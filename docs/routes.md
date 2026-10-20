# Danh sách route dự kiến (Theo quy ước `/controller/method/tham-so`)

Trạng thái: ✅ đã có | ⬜ chưa làm. 
Method viết `camelCase` trong code, URL dùng dấu gạch ngang (Ví dụ: `toggle-lock`).

| Route | Method | Controller::method | Vai trò | Phụ trách | Trạng thái |
|---|---|---|---|---|---|
| `/` | GET | HomeController::index | Tất cả | TV1 | ✅ |
| `/auth/login` | GET, POST | AuthController::login | Khách | TV1 | ✅ |
| `/auth/logout` | POST | AuthController::logout | Đã đăng nhập | TV1 | ✅ |
| `/auth/profile` | GET | AuthController::profile | Đã đăng nhập | TV1 | ✅ |
| `/users` | GET | UsersController::index | Admin | TV1 | ✅ |
| `/users/toggle-lock` | POST | UsersController::toggleLock | Admin | TV1 | ✅ |
| `/users/change-role` | POST | UsersController::changeRole | Admin | TV1 | ✅ |
| `/users/create`, `/users/store` | GET, POST | UsersController | Admin | TV1 | ⬜ |
| `/settings` | GET, POST | SettingsController::index | Admin | TV1 | ✅ |
| `/api/users?q=` | GET | ApiController::users | Thủ thư, Admin | TV1 | ✅ |
| `/catalog` | GET | CatalogController::index | Đã đăng nhập | TV4 | ⬜ |
| `/catalog/show/{id}` | GET | CatalogController::show | Đã đăng nhập | TV4 | ⬜ |
| `/api/suggest-books?q=` | GET | ApiController::suggestBooks | Đã đăng nhập | TV4 | ⬜ |
| `/books` | GET | BooksController::index | Thủ thư, Admin | TV2 | ⬜ |
| `/books/create`, `/books/store` | GET, POST | BooksController | Thủ thư, Admin | TV2 | ⬜ |
| `/books/edit/{id}`, `/books/update/{id}` | GET, POST | BooksController | Thủ thư, Admin | TV2 | ⬜ |
| `/books/delete/{id}` | POST | BooksController::delete | Thủ thư, Admin | TV2 | ⬜ |
| `/categories` | GET, POST | CategoriesController | Admin | TV2 | ⬜ |
| `/loans` | GET | LoansController::index | Thủ thư, Admin | TV3 | ⬜ |
| `/loans/create`, `/loans/store` | GET, POST | LoansController | Thủ thư, Admin | TV3 | ⬜ |
| `/loans/return-book/{id}` | POST | LoansController::returnBook | Thủ thư, Admin | TV3 | ⬜ |
| `/loans/mine` | GET | LoansController::mine | Độc giả | TV3 | ⬜ |
| `/reports/overdue` | GET | ReportsController::overdue | Thủ thư, Admin | TV5 | ⬜ |
| `/reports/stats` | GET | ReportsController::stats | Thủ thư, Admin | TV5 | ⬜ |