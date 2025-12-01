<?php
// config/db.php

$servername = "localhost";
$username = "root";     // Mặc định của XAMPP
$password = "";         // Mặc định XAMPP không có mật khẩu
$dbname = "LaptopShop"; // Tên database bạn vừa tạo

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập bảng mã tiếng Việt để không bị lỗi font
$conn->set_charset("utf8");

// Khởi tạo Session (để dùng cho đăng nhập/giỏ hàng sau này)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>