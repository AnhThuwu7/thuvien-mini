-- 1. Thống kê Top 5 cuốn sách được mượn nhiều nhất hệ thống (Dùng JOIN + GROUP BY + ORDER BY)
SELECT 
    s.id AS sach_id, 
    s.ten_sach, 
    GROUP_CONCAT(t.ten SEPARATOR ', ') AS tac_gia, 
    COUNT(pm.id) AS so_luot_muon
FROM phieu_muon pm
JOIN ban_sao b ON pm.ban_sao_id = b.id
JOIN sach s ON b.sach_id = s.id
LEFT JOIN sach_tac_gia stg ON s.id = stg.sach_id
LEFT JOIN tac_gia t ON stg.tac_gia_id = t.id
GROUP BY s.id, s.ten_sach
ORDER BY so_luot_muon DESC
LIMIT 5;
-- 2. Báo cáo danh sách độc giả quá hạn kèm số ngày bị chậm trễ (Dùng JOIN + WHERE + DATEDIFF)
SELECT 
    nd.id AS nguoi_dung_id,
    nd.ho_ten, 
    nd.email, 
    nd.so_dien_thoai, 
    s.ten_sach, 
    pm.han_tra,
    DATEDIFF(CURDATE(), pm.han_tra) AS so_ngay_qua_han
FROM phieu_muon pm
JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
JOIN ban_sao b ON pm.ban_sao_id = b.id
JOIN sach s ON b.sach_id = s.id
WHERE pm.tinh_trang_tra = 'dang_muon' AND pm.han_tra < CURDATE();
-- 3. Thống kê tổng số lượt mượn sách phân theo từng tháng trong năm (Dùng GROUP BY + WHERE)
SELECT 
    MONTH(pm.muon_luc) AS thang, 
    COUNT(*) AS tong_luot_muon
FROM phieu_muon pm
WHERE YEAR(pm.muon_luc) = YEAR(CURDATE())
GROUP BY MONTH(pm.muon_luc)
ORDER BY thang ASC;