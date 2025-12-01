<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopShop - Hệ thống bán Laptop uy tín</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/QL_BanMayTinh/assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg shadow-sm" style="background:#0a1220;">
        <div class="container py-2">

            <!-- Logo -->
            <a class=" fw-bold d-flex align-items-center " style=" color:azure; font-size: 30px" href="index_backup.php">
                <i class="fa-solid fa-laptop-code me-2 " ></i> LaptopShop
            </a>


            <!-- Mobile button -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">

                <!-- LEFT MENU -->
                <ul class="navbar-nav ms-3">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold px-3" href="index_backup.php">Trang chủ</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link text-white fw-semibold px-3 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            Danh mục
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item" href="category.php?cat_id=1">Laptop Gaming</a></li>
                            <li><a class="dropdown-item" href="category.php?cat_id=2">Laptop Văn phòng</a></li>
                            <li><a class="dropdown-item" href="category.php?cat_id=3">Macbook</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- SEARCH: width giảm xuống 30% cho đẹp -->
                <form class="d-flex mx-auto" action="category.php" method="GET" style="width: 30%;">
                    <div class="input-group">
                        <input type="search" name="keyword" class="form-control search-box" placeholder="Tìm laptop, hãng…">
                        <button class="btn btn-primary px-3">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- RIGHT MENU -->
                <ul class="navbar-nav ms-auto d-flex align-items-center">

                    <li class="nav-item mx-2">
                        <a class="nav-link text-white d-flex align-items-center" href="order_tracking.php">
                            <i class="fa-solid fa-truck-fast me-1"></i> Tra cứu
                        </a>
                    </li>

                    <li class="nav-item mx-2 position-relative">
                        <a class="nav-link text-white" href="cart.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="cart-badge">
                                <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                            </span>
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link text-white fw-semibold dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-user-circle me-1"></i> <?php echo $_SESSION['fullname']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="profile.php">
                                        <i class="fa-solid fa-clock-rotate-left me-2"></i> Thông tin
                                    </a></li>

                                <?php if ($_SESSION['role'] == 1): ?>
                                    <li><a class="dropdown-item text-danger" href="admin/index_dashboard.php">
                                            <i class="fa-solid fa-screwdriver-wrench me-2"></i> Trang quản trị
                                        </a></li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li><a class="dropdown-item" href="logout.php">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item mx-2"><a class="nav-link text-white" href="login.php">Đăng nhập</a></li>
                        <li class="nav-item mx-2"><a class="nav-link text-white" href="register.php">Đăng ký</a></li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>


    <div class="container mt-4" style="min-height: 600px;">