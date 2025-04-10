# Movie Streaming Platform

Ứng dụng xem phim trực tuyến với tính năng xem phim cùng nhau và trò chuyện.

## Tính năng

- **Xem phim trực tuyến**: Người dùng có thể mua và xem phim trực tuyến.
- **Phòng xem phim**: Admin tạo phòng xem phim và người dùng có thể tham gia và xem cùng nhau.
- **Trò chuyện thời gian thực**: Người dùng có thể trò chuyện với nhau trong phòng xem phim.
- **Hệ thống thanh toán**: Hỗ trợ nhiều phương thức thanh toán khác nhau (MoMo, Thẻ, Chuyển khoản, v.v.).
- **Trang quản trị**: Giao diện quản trị cho admin để quản lý phim, phòng, người dùng và thanh toán.

## Yêu cầu

- PHP 7.4+ 
- MySQL 5.7+
- Composer
- Web server (Apache/Nginx)
- WebSocket server (Ratchet)
- SSL/TLS (cho môi trường production)

## Cài đặt

### 1. Clone dự án

```bash
git clone https://github.com/your-username/movie-streaming.git
cd movie-streaming
```

### 2. Cài đặt dependencies

```bash
composer install
```

### 3. Cấu hình cơ sở dữ liệu

- Tạo database bằng cách import file `database.sql`
- Cấu hình kết nối trong file `.env` (xem file `.env.example`)

### 4. Cấu hình web server

#### Apache

```apache
<VirtualHost *:80>
    ServerName movie-streaming.local
    DocumentRoot /path/to/movie-streaming/public
    
    <Directory /path/to/movie-streaming/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name movie-streaming.local;
    root /path/to/movie-streaming/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Cấu hình thư mục uploads

```bash
mkdir -p public/assets/uploads/movies
mkdir -p public/assets/uploads/thumbnails
chmod -R 755 public/assets/uploads
```

### 6. Chạy WebSocket server

```bash
php server.php
```

Nên sử dụng Supervisor hoặc PM2 để giữ WebSocket server chạy liên tục trong môi trường production.

## Cấu trúc dự án

```
movie-streaming/
├── app/                  # Thư mục chính của ứng dụng
│   ├── config/           # Cấu hình
│   ├── controllers/      # Controllers
│   ├── core/             # Core classes (MVC framework)
│   ├── models/           # Models
│   ├── socket/           # WebSocket handlers
│   └── views/            # Views
├── public/               # Public files
│   ├── assets/           # CSS, JS, images
│   ├── index.php         # Entry point
│   └── .htaccess         # Apache rewrite rules
├── vendor/               # Composer dependencies (auto-generated)
├── .env                  # Environment configuration
├── .gitignore            # Git ignore file
├── composer.json         # Composer configuration
├── database.sql          # Database schema
├── README.md             # This file
└── server.php            # WebSocket server entry point
```

## Tài khoản mặc định

### Admin
- Username: admin
- Password: admin123

## Hướng dẫn sử dụng

### Dành cho Admin

1. **Đăng nhập**: Truy cập `/auth/admin-login` và đăng nhập với tài khoản admin.
2. **Quản lý phim**: Thêm, sửa, xóa phim qua menu "Quản lý phim".
3. **Tạo phòng xem phim**: Tạo phòng xem phim qua menu "Quản lý phòng".
4. **Mở phòng**: Nhấn nút "Mở phòng" để bắt đầu buổi xem phim.
5. **Điều khiển phim**: Sử dụng các nút điều khiển để phát/tạm dừng/tua phim.

### Dành cho người dùng

1. **Đăng ký/Đăng nhập**: Tạo tài khoản hoặc đăng nhập vào hệ thống.
2. **Nạp tiền**: Nạp tiền vào tài khoản qua các phương thức thanh toán.
3. **Mua phim**: Chọn phim và thanh toán để mua.
4. **Tham gia phòng**: Xem danh sách phòng đang mở và tham gia.
5. **Xem phim và trò chuyện**: Xem phim đồng thời trò chuyện với người xem khác.

## Tùy chỉnh

### Cấu hình thanh toán

Cập nhật các thông tin cổng thanh toán trong file `.env`:

```
# Cấu hình thanh toán
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
```

### Thêm phương thức thanh toán mới

1. Thêm hằng số vào file `.env`
2. Cập nhật model `Payment.php`
3. Thêm controller xử lý trong `PaymentController.php`
4. Thêm giao diện trong view `payment.php`

## License

MIT License