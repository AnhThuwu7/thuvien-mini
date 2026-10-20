# Quy tắc Validation & Kiểm tra Dữ liệu An toàn

Tài liệu quy định các quy tắc kiểm tra dữ liệu phía Server (Server-side Validation) và quy tắc nghiệp vụ cho các Form trong hệ thống.

---

## 1. Form Đăng nhập (`/auth/login`)

| Trường dữ liệu | Quy tắc Validation (Server-side) | Thông báo lỗi tương ứng |
|----------------|----------------------------------|-------------------------|
| `email` | - Bắt buộc (Required)<br>- Đúng định dạng email<br>- Độ dài tối đa 190 ký tự | - "Vui lòng nhập email."<br>- "Email không đúng định dạng." |
| `password` | - Bắt buộc (Required) | - "Vui lòng nhập mật khẩu." |
| **Nghệp vụ** | - Kiểm tra tài khoản tồn tại & khớp mật khẩu băm<br>- Kiểm tra trạng thái tài khoản | - "Email hoặc mật khẩu không đúng." (Thông báo chung chống dò email)<br>- "Tài khoản của bạn đã bị khóa. Lý do: [ly_do]." |

---

## 2. Form Khóa / Mở khóa Tài khoản (`/users/toggle-lock`)

| Trường dữ liệu | Quy tắc Validation (Server-side) | Thông báo lỗi tương ứng |
|----------------|----------------------------------|-------------------------|
| `csrf_token` | - Bắt buộc<br>- Khớp token trong Session | - "403 Forbidden - CSRF Token không hợp lệ." |
| `user_id` | - Bắt buộc<br>- Phải là số nguyên lớn hơn 0<br>- Phải tồn tại trong CSDL | - "Tài khoản không tồn tại." |
| `ly_do_khoa` | - Bắt buộc khi khóa tài khoản<br>- Tối đa 255 ký tự | - "Vui lòng nhập lý do khóa tài khoản."<br>- "Lý do khóa không được vượt quá 255 ký tự." |
| **Nghiệp vụ** | - Admin không được phép tự khóa tài khoản của chính mình | - "Bạn không thể tự khóa tài khoản của chính mình." |

---

## 3. Form Cấu hình Hệ thống (`/settings`)

| Trường dữ liệu | Quy tắc Validation (Server-side) | Thông báo lỗi tương ứng |
|----------------|----------------------------------|-------------------------|
| `so_sach_toi_da` | - Bắt buộc<br>- Là số nguyên (Integer)<br>- Khoảng giá trị: từ 1 đến 20 | - "Số sách mượn tối đa phải từ 1 đến 20 quyển." |
| `so_ngay_muon_toi_da`| - Bắt buộc<br>- Là số nguyên (Integer)<br>- Khoảng giá trị: từ 1 đến 90 | - "Số ngày mượn tối đa phải từ 1 đến 90 ngày." |
| `tien_phat_ngay` | - Bắt buộc<br>- Là số nguyên không âm ($\ge 0$) | - "Tiền phạt phải lớn hơn hoặc bằng 0 đ." |

---

## 4. Form Thêm / Sửa Sách (`/books/store`)

| Trường dữ liệu | Quy tắc Validation (Server-side) | Thông báo lỗi tương ứng |
|----------------|----------------------------------|-------------------------|
| `ten_sach` | - Bắt buộc<br>- Tối đa 255 ký tự | - "Tên sách không được để trống." |
| `isbn` | - Chuẩn định dạng ISBN-10 / ISBN-13<br>- Không trùng lặp trong CSDL | - "Mã ISBN không hợp lệ."<br>- "Mã ISBN này đã tồn tại." |
| `nam_xuat_ban` | - Số nguyên<br>- Giá trị $\le$ Năm hiện tại | - "Năm xuất bản không hợp lệ." |
| `so_luong` | - Số nguyên dương ($> 0$) | - "Số lượng sách phải lớn hơn 0." |

---

## 5. Form Tạo Phiếu mượn Sách (`/loans/store`)

| Trường dữ liệu | Quy tắc Validation (Server-side) | Thông báo lỗi tương ứng |
|----------------|----------------------------------|-------------------------|
| `ma_doc_gia` | - Bắt buộc<br>- Phải tồn tại trong hệ thống | - "Mã độc giả không tồn tại." |
| `ma_ban_sao` | - Bắt buộc<br>- Trạng thái bản sao phải là "Sẵn sàng" | - "Bản sao sách này hiện đang được mượn hoặc hư hỏng." |
| **Nghiệp vụ** | - Kiểm tra tài khoản độc giả có bị khóa không<br>- Kiểm tra số sách đang mượn có vượt `so_sach_toi_da` không<br>- Kiểm tra độc giả có sách quá hạn chưa trả không | - "Tài khoản độc giả đang bị khóa."<br>- "Độc giả đã vượt quá hạn mức mượn sách cho phép."<br>- "Độc giả còn sách quá hạn chưa trả, không thể mượn thêm." |