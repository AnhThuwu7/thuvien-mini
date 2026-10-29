-- Câu 1: Tra cứu sách theo tên và danh mục
SELECT s.id, s.ten_sach, s.isbn, d.ten AS danh_muc
FROM sach s
LEFT JOIN danh_muc d ON s.danh_muc_id = d.id
WHERE s.ten_sach LIKE '%toán%';

-- Câu 2: Đếm tổng số bản sách và số bản còn
SELECT
    s.id,
    s.ten_sach,
    COUNT(b.id) AS tong_so_ban,
    SUM(
        CASE
            WHEN b.trang_thai = 'con' THEN 1
            ELSE 0
        END
    ) AS so_ban_con
FROM sach s
LEFT JOIN ban_sao b ON s.id = b.sach_id
GROUP BY s.id, s.ten_sach;

-- Câu 3: Xem lịch sử mượn của độc giả
SELECT
    nd.ho_ten,
    s.ten_sach,
    pm.muon_luc,
    pm.han_tra,
    pm.tra_luc
FROM phieu_muon pm
JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
JOIN ban_sao b ON pm.ban_sao_id = b.id
JOIN sach s ON b.sach_id = s.id
WHERE nd.id = 1;
