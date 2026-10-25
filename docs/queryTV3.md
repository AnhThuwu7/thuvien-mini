

-- Câu 1: Lấy danh sách các phiếu mượn đang quá hạn chưa trả (Dùng WHERE)
SELECT id, ban_sao_id, nguoi_dung_id, muon_luc, han_tra
FROM phieu_muon
WHERE tra_luc IS NULL AND han_tra < CURDATE();

-- Câu 2: Xem chi tiết phiếu mượn kèm tên Độc giả, Tên sách và Mã bản sao (Dùng JOIN + WHERE)
SELECT pm.id AS ma_phieu, nd.ho_ten AS ten_doc_gia, s.ten_sach, bs.ma_ban_sao, pm.muon_luc, pm.han_tra
FROM phieu_muon pm
JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
JOIN ban_sao bs ON pm.ban_sao_id = bs.id
JOIN sach s ON bs.sach_id = s.id
WHERE pm.tra_luc IS NULL
ORDER BY pm.han_tra ASC;

-- Câu 3: Thống kê tổng số tiền phạt theo từng Độc giả (Dùng GROUP BY + JOIN + WHERE)
SELECT nd.id, nd.ho_ten, nd.email, SUM(pm.tien_phat) AS tong_tien_phat
FROM phieu_muon pm
JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
WHERE pm.tien_phat > 0
GROUP BY nd.id, nd.ho_ten, nd.email;