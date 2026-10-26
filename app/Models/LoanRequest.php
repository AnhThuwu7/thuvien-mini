<?php

class LoanRequest extends Model
{
    /**
     * Lấy danh sách yêu cầu mượn đang chờ duyệt.
     */
    public function getPendingRequests(): array
    {
        $sql = "
            SELECT
                yc.id,
                nd.ma_doc_gia,
                nd.ho_ten,
                s.ten_sach,
                yc.yeu_cau_luc,
                yc.ghi_chu,
                yc.trang_thai
            FROM yeu_cau_muon AS yc
            INNER JOIN nguoi_dung AS nd
                ON nd.id = yc.nguoi_dung_id
            INNER JOIN sach AS s
                ON s.id = yc.sach_id
            WHERE yc.trang_thai = 'cho_duyet'
            ORDER BY yc.yeu_cau_luc ASC, yc.id ASC
        ";

        return $this->fetchAll($sql);
    }

    /**
     * Duyệt yêu cầu và giữ một bản sao sách cho độc giả.
     */
    public function approveRequest(int $requestId, int $staffId): void
    {
        if ($requestId <= 0 || $staffId <= 0) {
            throw new RuntimeException('Mã yêu cầu hoặc mã nhân viên không hợp lệ.');
        }

        $this->transaction(function () use ($requestId, $staffId): void {
            // Khóa yêu cầu để tránh hai người cùng duyệt một yêu cầu.
            $stmt = $this->db->prepare("
                SELECT id, sach_id, trang_thai
                FROM yeu_cau_muon
                WHERE id = ?
                FOR UPDATE
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new RuntimeException('Không tìm thấy yêu cầu mượn sách.');
            }

            if ($request['trang_thai'] !== 'cho_duyet') {
                throw new RuntimeException('Yêu cầu này không còn ở trạng thái chờ duyệt.');
            }

            // Đọc số ngày giữ sách từ bảng cấu hình.
            $stmt = $this->db->prepare("
                SELECT gia_tri
                FROM cau_hinh
                WHERE khoa = 'so_ngay_giu_sach'
                LIMIT 1
            ");
            $stmt->execute();
            $config = $stmt->fetch(PDO::FETCH_ASSOC);

            $holdDays = isset($config['gia_tri'])
                ? (int) $config['gia_tri']
                : 2;

            if ($holdDays < 1) {
                $holdDays = 2;
            }

            // Chọn một bản sao còn trống của đúng đầu sách.
            $stmt = $this->db->prepare("
                SELECT id
                FROM ban_sao
                WHERE sach_id = ?
                  AND trang_thai = 'con_trong'
                  AND xoa_luc IS NULL
                ORDER BY id ASC
                LIMIT 1
                FOR UPDATE
            ");
            $stmt->execute([(int) $request['sach_id']]);
            $copy = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$copy) {
                throw new RuntimeException(
                    'Sách hiện không còn bản sao trống để giữ chỗ.'
                );
            }

            $copyId = (int) $copy['id'];

            // Chuyển bản sao sang trạng thái đang giữ chỗ.
            $stmt = $this->db->prepare("
                UPDATE ban_sao
                SET trang_thai = 'dang_giu_cho'
                WHERE id = ?
                  AND trang_thai = 'con_trong'
                  AND xoa_luc IS NULL
            ");
            $stmt->execute([$copyId]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Bản sao vừa được thay đổi bởi thao tác khác. Hãy thử lại.'
                );
            }

            // Duyệt yêu cầu, gán bản sao và đặt hạn đến lấy.
            // $holdDays đã được ép kiểu số nguyên trước khi đưa vào SQL.
            $sql = "
                UPDATE yeu_cau_muon
                SET trang_thai = 'da_duyet',
                    ban_sao_id = ?,
                    nguoi_xu_ly_id = ?,
                    xu_ly_luc = NOW(),
                    han_den_lay = DATE_ADD(NOW(), INTERVAL "
                        . (int) $holdDays . " DAY)
                WHERE id = ?
                  AND trang_thai = 'cho_duyet'
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$copyId, $staffId, $requestId]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Không thể cập nhật trạng thái yêu cầu mượn.'
                );
            }
        });
    }

    /**
     * Từ chối yêu cầu mượn sách.
     */
    public function rejectRequest(
        int $requestId,
        int $staffId,
        string $reason = 'Yêu cầu không được chấp thuận.'
    ): void {
        if ($requestId <= 0 || $staffId <= 0) {
            throw new RuntimeException('Mã yêu cầu hoặc mã nhân viên không hợp lệ.');
        }

        $reason = trim($reason);

        if ($reason === '') {
            $reason = 'Yêu cầu không được chấp thuận.';
        }

        // Cột ly_do_tu_choi có độ dài tối đa 255 ký tự.
        if (function_exists('mb_substr')) {
            $reason = mb_substr($reason, 0, 255, 'UTF-8');
        } else {
            $reason = substr($reason, 0, 255);
        }

        $this->transaction(function () use (
            $requestId,
            $staffId,
            $reason
        ): void {
            // Khóa yêu cầu trước khi xử lý.
            $stmt = $this->db->prepare("
                SELECT id, trang_thai
                FROM yeu_cau_muon
                WHERE id = ?
                FOR UPDATE
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new RuntimeException('Không tìm thấy yêu cầu mượn sách.');
            }

            if ($request['trang_thai'] !== 'cho_duyet') {
                throw new RuntimeException('Yêu cầu này không còn ở trạng thái chờ duyệt.');
            }

            $stmt = $this->db->prepare("
                UPDATE yeu_cau_muon
                SET trang_thai = 'tu_choi',
                    nguoi_xu_ly_id = ?,
                    xu_ly_luc = NOW(),
                    ly_do_tu_choi = ?
                WHERE id = ?
                  AND trang_thai = 'cho_duyet'
            ");
            $stmt->execute([$staffId, $reason, $requestId]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Không thể cập nhật trạng thái từ chối yêu cầu.'
                );
            }
        });
    }
}