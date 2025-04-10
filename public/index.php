<?php
// public/index.php
require_once '../vendor/autoload.php';

// Load các file cấu hình
require_once '../app/config/config.php';

// Autoload các class
spl_autoload_register(function ($className) {
    // Chuyển đổi namespace thành đường dẫn file
    $className = str_replace('\\', '/', $className);
    $className = str_replace('App/', '', $className);

    $file = ROOT_PATH . '/app/' . $className . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Khởi tạo session
session_name(SESSION_NAME);
session_start();

// Khởi chạy ứng dụng
$app = new App\Core\App();
