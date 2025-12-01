<?php
// 1. Kết nối CSDL
require_once 'config/db.php';

// 2. Nhúng Header
include 'includes/header.php';

// 3. Lấy 8 sản phẩm mới nhất
$sql = "SELECT p.*, c.name as category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        ORDER BY p.created_at DESC 
        LIMIT 8";
$result = $conn->query($sql);
?>

<!-- HERO BANNER -->
<section class="hero-section">
    <div class="hero-overlay"></div>

    <div class="container h-100 position-relative">
        <div class="row align-items-center h-100">

            <!-- TEXT -->
            <div class="col-lg-6 text-white pe-lg-5">
                <h1 class="hero-title mb-3">
                    Nâng Tầm Trải Nghiệm<br> Laptop Của Bạn
                </h1>

                <p class="hero-subtitle mb-4">
                    Laptop Gaming – Văn phòng – Macbook chính hãng.
                    Ưu đãi đặc biệt cho sinh viên & nhân viên văn phòng.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="#products" class="btn btn-danger btn-lg px-4">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Mua ngay
                    </a>
                    <a href="category.php" class="btn btn-outline-light btn-lg px-4">
                        Xem tất cả sản phẩm
                    </a>
                </div>

                <div class="hero-badges mt-4">
                    <span><i class="fa-solid fa-shield-halved me-2"></i>Bảo hành chính hãng</span>
                    <span><i class="fa-solid fa-truck-fast me-2"></i>Giao hàng toàn quốc</span>
                </div>
            </div>

            <!-- IMAGE -->
            <div class="col-lg-6 d-none d-lg-flex justify-content-center">
                <div class="hero-image-wrapper">
                    <img src="assets/img/pexels-junior-teixeira-1064069-2047905.jpg"
                        alt="Laptop Banner"
                        class="hero-image">
                </div>
            </div>

        </div>
    </div>
</section>


<!-- SECTION: LỰA CHỌN NHANH -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-header mb-4">
            <h3 class="section-title">Lựa chọn theo nhu cầu</h3>
            <p class="section-subtitle">Chọn nhanh dòng laptop phù hợp với mục đích sử dụng của bạn.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="category.php?cat_id=1" class="quick-card gaming">
                    <div class="quick-card-body">
                        <h5>Laptop Gaming</h5>
                        <p>Hiệu năng mạnh, card rời, tối ưu cho game & đồ họa.</p>
                        <span>Xem ngay <i class="fa-solid fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="category.php?cat_id=2" class="quick-card office">
                    <div class="quick-card-body">
                        <h5>Laptop Văn phòng</h5>
                        <p>Mỏng nhẹ, pin trâu, phù hợp học tập & làm việc.</p>
                        <span>Khám phá <i class="fa-solid fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="category.php?cat_id=3" class="quick-card macbook">
                    <div class="quick-card-body">
                        <h5>Macbook</h5>
                        <p>Thiết kế cao cấp, hiệu năng ổn định, hệ sinh thái Apple.</p>
                        <span>Xem Macbook <i class="fa-solid fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SECTION: SẢN PHẨM MỚI -->
<section class="py-5 bg-light" id="products">
    <div class="container">
        <div class="section-header mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="section-title mb-1">
                    <i class="fa-solid fa-fire text-danger me-2"></i> Sản phẩm mới
                </h3>
                <p class="section-subtitle mb-0">Những mẫu máy vừa cập bến tại LaptopShop.</p>
            </div>
            <a href="category.php" class="section-link">
                Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $img_url = "assets/img/" . $row['main_image'];
                    if (empty($row['main_image']) || !file_exists($img_url)) {
                        $img_url = "https://via.placeholder.com/300x200?text=No+Image";
                    }
                    $price = number_format($row['price'], 0, ',', '.');
            ?>
                    <div class="col">
                        <div class="card product-card h-100">
                            <div class="product-badge">New</div>
                            <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="product-img-wrap">
                                <img src="<?php echo $img_url; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-secondary-subtle text-secondary-emphasis mb-2">
                                    <?php echo $row['category_name']; ?>
                                </span>
                                <h6 class="product-title">
                                    <a href="product_detail.php?id=<?php echo $row['product_id']; ?>">
                                        <?php echo $row['name']; ?>
                                    </a>
                                </h6>

                                <div class="mt-auto">
                                    <div class="product-price mb-2"><?php echo $price; ?> ₫</div>
                                    <div class="d-flex gap-2">
                                        <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fa-regular fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="cart.php?add_quick=<?php echo $row['product_id']; ?>" class="btn btn-danger btn-sm flex-fill">
                                            <i class="fa-solid fa-cart-plus me-1"></i> Giỏ hàng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5"><p class="text-muted">Chưa có sản phẩm nào trong hệ thống.</p></div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- SECTION: LỢI ÍCH -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fa-solid fa-truck-fast fa-3x text-primary mb-3"></i>
                    <h5>Giao hàng nhanh & miễn phí</h5>
                    <p class="text-muted">Miễn phí nội thành cho đơn trên 20 triệu, hỗ trợ ship COD toàn quốc.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fa-solid fa-shield-halved fa-3x text-primary mb-3"></i>
                    <h5>Bảo hành chính hãng</h5>
                    <p class="text-muted">Sản phẩm nguyên seal, bảo hành tại trung tâm ủy quyền chính hãng.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fa-solid fa-headset fa-3x text-primary mb-3"></i>
                    <h5>Hỗ trợ 1-1</h5>
                    <p class="text-muted">Tư vấn cấu hình, cài đặt phần mềm & vệ sinh máy trọn đời.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// 4. Nhúng Footer
include 'includes/footer.php';
?>