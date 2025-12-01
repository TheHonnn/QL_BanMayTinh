<?php
require_once 'config/db.php';
include 'includes/header.php';

$title = "Tất cả sản phẩm";
$keyword = "";
$sql = "SELECT * FROM product WHERE 1=1"; // 1=1 là mẹo để nối điều kiện

// Lọc theo danh mục
if (isset($_GET['cat_id'])) {
    $cat_id = intval($_GET['cat_id']);
    $sql .= " AND category_id = $cat_id";

    $cat_res = $conn->query("SELECT name FROM category WHERE category_id = $cat_id");
    if ($cat_res && $cat_res->num_rows > 0) {
        $cat_name = $cat_res->fetch_assoc()['name'];
        $title = "Danh mục: " . $cat_name;
    }
}

// Tìm kiếm theo từ khóa
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
    if ($keyword !== '') {
        $keyword_escaped = $conn->real_escape_string($keyword);
        $sql .= " AND name LIKE '%$keyword_escaped%'";
        $title = "Kết quả tìm kiếm: \"" . htmlspecialchars($keyword) . "\"";
    }
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
$total = $result ? $result->num_rows : 0;
?>

<style>
/* ===== CATEGORY / SEARCH PAGE ===== */
.section-header {
    background: #0f172a;
    border-radius: 18px;
    padding: 18px 22px;
    color: #e5e7eb;
    margin-bottom: 24px;
}
.section-header-title {
    font-size: 1.4rem;
    font-weight: 700;
}
.section-header-sub {
    font-size: .9rem;
}
.section-header-badge {
    background: rgba(248, 250, 252, 0.1);
    border-radius: 999px;
    padding: 5px 14px;
    font-size: .8rem;
}

.product-card {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    transition: all .2s ease;
    background: #ffffff;
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(15, 23, 42, .10);
}
.product-thumb-wrap {
    position: relative;
    overflow: hidden;
    background: #020617;
}
.product-thumb-wrap img {
    transition: transform .25s ease;
}
.product-card:hover .product-thumb-wrap img {
    transform: scale(1.03);
}
.product-chip {
    position: absolute;
    left: 10px;
    top: 10px;
    background: rgba(15, 23, 42, .7);
    color: #e5e7eb;
    font-size: .75rem;
    padding: 3px 10px;
    border-radius: 999px;
}
.product-price {
    font-size: 1.1rem;
}
.product-footer .btn {
    border-radius: 999px;
}
.product-meta {
    font-size: .8rem;
    color: #94a3b8;
}

/* Empty state */
.empty-state-icon {
    opacity: .6;
}
</style>

<div class="container py-4">

    <!-- HEADER -->
    <div class="section-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center mb-1 flex-wrap gap-2">
                <h3 class="section-header-title mb-0">
                    <?php echo $title; ?>
                </h3>
                <span class="section-header-badge">
                    <i class="fa-solid fa-box-open me-1"></i>
                    <?php echo $total; ?> sản phẩm
                </span>
            </div>

            <div class="section-header-sub">
                <?php if ($keyword !== ''): ?>
                    Từ khóa tìm kiếm: <span class="fw-semibold text-white"><?php echo htmlspecialchars($keyword); ?></span>
                <?php elseif (isset($cat_name)): ?>
                    Đang xem danh mục: <span class="fw-semibold text-white"><?php echo htmlspecialchars($cat_name); ?></span>
                <?php else: ?>
                    Hiển thị toàn bộ sản phẩm hiện có trong hệ thống LaptopShop.
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="index_backup.php" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-house me-1"></i> Trang chủ
            </a>
            <a href="category.php" class="btn btn-light btn-sm text-dark">
                <i class="fa-solid fa-rotate-left me-1"></i> Bỏ lọc
            </a>
        </div>
    </div>

    <?php if ($total > 0): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $img = "assets/img/" . $row['main_image'];
                    if (empty($row['main_image']) || !file_exists($img)) {
                        $img = "https://via.placeholder.com/400x260?text=LaptopShop";
                    }
                    $price = number_format($row['price'], 0, ',', '.');
                ?>
                <div class="col">
                    <div class="product-card h-100 d-flex flex-column">
                        <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="text-decoration-none">
                            <div class="product-thumb-wrap">
                                <img src="<?php echo $img; ?>" class="w-100" style="height: 190px; object-fit: cover;"
                                     alt="<?php echo htmlspecialchars($row['name']); ?>">
                                <span class="product-chip">
                                    Mã: #<?php echo $row['product_id']; ?>
                                </span>
                            </div>
                        </a>

                        <div class="card-body d-flex flex-column">
                            <h6 class="mb-1 text-truncate">
                                <a href="product_detail.php?id=<?php echo $row['product_id']; ?>"
                                   class="text-decoration-none text-dark fw-semibold">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </h6>
                            <div class="product-meta mb-2">
                                <?php if (!empty($row['category_id'])): ?>
                                    <!-- <i class="fa-solid fa-tag me-1"></i> ID danh mục: <?php echo $row['category_id']; ?> -->
                                <?php else: ?>
                                    <span>&nbsp;</span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto product-footer">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-danger fw-bold product-price">
                                        <?php echo $price; ?> ₫
                                    </span>
                                    <span class="text-muted small">
                                        <i class="fa-solid fa-layer-group mt-1"></i>
                                        SL  : <?php echo (int)$row['stock']; ?>
                                    </span>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="product_detail.php?id=<?php echo $row['product_id']; ?>"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fa-regular fa-eye me-1"></i> Xem chi tiết
                                    </a>
                                    <a href="cart.php?add_quick=<?php echo $row['product_id']; ?>"
                                       class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    <?php else: ?>

        <div class="text-center py-5">
            <i class="fa-solid fa-face-frown-open fa-4x text-secondary empty-state-icon mb-3"></i>
            <h4 class="fw-bold mb-2">Rất tiếc, không tìm thấy sản phẩm nào.</h4>
            <?php if ($keyword !== ''): ?>
                <p class="text-muted mb-3">
                    Không có sản phẩm nào phù hợp với từ khóa
                    <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong>.
                </p>
            <?php else: ?>
                <p class="text-muted mb-3">
                    Hiện chưa có sản phẩm trong danh mục này. Bạn hãy thử danh mục khác nhé.
                </p>
            <?php endif; ?>
            <a href="index_backup.php" class="btn btn-primary px-4 me-2">
                <i class="fa-solid fa-house me-1"></i> Về trang chủ
            </a>
            <a href="category.php" class="btn btn-outline-secondary px-4">
                Xem tất cả sản phẩm
            </a>
        </div>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
