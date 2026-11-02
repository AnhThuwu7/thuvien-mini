
USE thuvien_mini;

-- Cau 3: Tra cuu yeu cau muon sach
SELECT
    yc.id AS ma_yeu_cau,
    nd.ho_ten AS ten_doc_gia,
    s.ten_sach,
    yc.trang_thai,
    yc.yeu_cau_luc,
    nv.ho_ten AS ten_nguoi_xu_ly,
    yc.han_den_lay,
    yc.ly_do_tu_choi
FROM yeu_cau_muon yc
JOIN nguoi_dung nd
    ON yc.nguoi_dung_id = nd.id
JOIN sach s
    ON yc.sach_id = s.id
LEFT JOIN nguoi_dung nv
    ON yc.nguoi_xu_ly_id = nv.id
WHERE yc.trang_thai IN (
    'cho_duyet',
    'da_duyet',
    'tu_choi',
    'het_han'
)
ORDER BY yc.yeu_cau_luc DESC;