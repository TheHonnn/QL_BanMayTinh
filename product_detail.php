<?php
require_once 'config/db.php';
include 'includes/header.php';

// 1. Kiểm tra tham số ID trên URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="container py-5 text-center">
            <img src="assets/img/404.png" style="width: 200px;" class="mb-3">
            <h3>Sản phẩm không tồn tại!</h3>
            <a href="index_backup.php" class="btn btn-primary mt-3">Về trang chủ</a>
          </div>';
    include 'includes/footer.php';
    exit();
}

$id = intval($_GET['id']);

// 2. Lấy thông tin sản phẩm
$sql = "SELECT p.*, c.name as cat_name, s.name as sup_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        WHERE p.product_id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo '<div class="container py-5 text-center"><h3>Sản phẩm không tồn tại!</h3></div>';
    include 'includes/footer.php';
    exit();
}

$product = $result->fetch_assoc();

// 3. Lấy ảnh phụ (Gallery)
$sql_img = "SELECT * FROM product_images WHERE product_id = $id";
$result_img = $conn->query($sql_img);

// Xử lý giá
$price = number_format($product['price'], 0, ',', '.');
?>

<style>
    /* NỀN MỀM MỊN CHO TRANG CHI TIẾT */
    .product-page-wrapper {
        background: #f3f4f6;
    }

    /* CARD CHÍNH */
    .product-main-card {
        border-radius: 24px;
        border: none;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
    }

    /* CARD GALLERY BÊN TRÁI */
    .product-gallery-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.10);
    }

    /* ẢNH CHÍNH */
    #mainImage {
        max-height: 360px;
        object-fit: contain;
    }

    /* ẢNH THUMB */
    .thumb-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 3px;
        background: #ffffff;
        transition: all .2s ease;
    }

    .thumb-img:hover {
        border-color: #f97316;
        transform: translateY(-2px);
    }

    /* BADGE MỀM MỊN */
    .badge-soft {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-soft-primary {
        background: #e0f2fe;
        color: #0369a1;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* BOX ƯU ĐÃI */
    .offer-box {
        border-radius: 16px;
        background: linear-gradient(135deg, #fff7ed, #fffbeb);
        border: 1px dashed #fdba74;
    }

    /* NAV TAB MÔ TẢ / THÔNG SỐ */
    .nav-pills .nav-link {
        border-radius: 999px;
    }

    .nav-pills .nav-link.active {
        background: #111827;
    }

    /* BREADCRUMB */
    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
    }
</style>

<div class="product-page-wrapper">
    <div class="container py-5">

        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item">
                    <a href="index_backup.php" class="text-decoration-none text-muted">Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="category.php" class="text-decoration-none text-muted">
                        <?php echo $product['cat_name']; ?>
                    </a>
                </li>
                <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">
                    <?php echo $product['name']; ?>
                </li>
            </ol>
        </nav>

        <!-- CARD CHÍNH -->
        <div class="card product-main-card bg-white p-4 mb-5">
            <div class="row g-5 align-items-start">

                <!-- CỘT ẢNH -->
                <div class="col-md-5">
                    <div class="card product-gallery-card bg-white overflow-hidden mb-3">
                        <div class="p-4 text-center">
                            <img src="assets/img/<?php echo $product['main_image']; ?>"
                                class="img-fluid"
                                id="mainImage"
                                alt="<?php echo $product['name']; ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <img src="assets/img/<?php echo $product['main_image']; ?>"
                            onclick="changeImage(this.src)"
                            class="thumb-img">

                        <?php while ($img = $result_img->fetch_assoc()): ?>
                            <img src="assets/img/<?php echo $img['image_url']; ?>"
                                onclick="changeImage(this.src)"
                                class="thumb-img">
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- CỘT THÔNG TIN -->
                <div class="col-md-7">
                    <div class="d-flex flex-wrap align-items-center mb-2 gap-2">
                        <span class="badge-soft badge-soft-primary">
                            <i class="fa-solid fa-layer-group me-1"></i> <?php echo $product['cat_name']; ?>
                        </span>
                        <?php if (!empty($product['sup_name'])): ?>
                            <span class="badge-soft badge-soft-success">
                                Thương hiệu: <?php echo $product['sup_name']; ?>
                            </span>
                        <?php endif; ?>
                        <span class="badge-soft <?php echo ($product['stock'] > 0) ? 'badge-soft-success' : 'badge-soft-danger'; ?>">
                            <?php echo ($product['stock'] > 0) ? 'Còn hàng: ' . $product['stock'] . ' chiếc' : 'Tạm hết hàng'; ?>
                        </span>
                    </div>

                    <h2 class="fw-bold text-dark mb-3"><?php echo $product['name']; ?></h2>

                    <div class="d-flex align-items-baseline mb-3">
                        <span class="text-danger fw-bold display-6 me-3"><?php echo $price; ?> ₫</span>
                        <!-- Nếu sau này có giá cũ thì thêm vào đây -->
                        <!-- <span class="text-muted text-decoration-line-through">29.990.000 ₫</span> -->
                    </div>

                    <!-- ƯU ĐÃI -->
                    <div class="offer-box p-3 mb-4">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-gift text-amber-500 text-danger me-2"></i> ƯU ĐÃI ĐẶC BIỆT
                        </h6>
                        <ul class="mb-0 small list-unstyled">
                            <li class="mb-1">
                                <i class="fa-solid fa-check text-success me-2"></i> Tặng Balo Laptop cao cấp chống sốc.
                            </li>
                            <li class="mb-1">
                                <i class="fa-solid fa-check text-success me-2"></i> Tặng Chuột không dây chính hãng.
                            </li>
                            <li class="mb-1">
                                <i class="fa-solid fa-check text-success me-2"></i> Voucher giảm giá 10% cho lần mua sau.
                            </li>
                            <li>
                                <i class="fa-solid fa-check text-success me-2"></i> Hỗ trợ cài đặt phần mềm miễn phí trọn đời.
                            </li>
                        </ul>
                    </div>

                    <!-- FORM MUA -->
                    <form action="cart.php" method="POST" class="mt-3">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                        <div class="d-flex align-items-center mb-4 flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <label class="me-3 fw-semibold mb-0">Số lượng:</label>
                                <div class="input-group" style="width: 140px;">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                        -
                                    </button>
                                    <input type="number"
                                        name="quantity"
                                        value="1"
                                        min="1"
                                        max="<?php echo $product['stock']; ?>"
                                        class="form-control text-center border-secondary">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                        +
                                    </button>
                                </div>
                            </div>

                            <div class="small text-muted">
                                <i class="fa-solid fa-shield-halved me-1"></i> Bảo hành chính hãng 24 tháng
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <?php if ($product['stock'] > 0): ?>
                                <button type="submit" class="btn btn-danger btn-lg px-5 fw-bold flex-grow-1 shadow-sm">
                                    <i class="fa-solid fa-cart-plus me-2"></i> THÊM VÀO GIỎ
                                </button>
                                <button type="submit" formaction="cart.php?action=buy_now" class="btn btn-primary btn-lg px-4 fw-bold shadow-sm">
                                    MUA NGAY
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                    <i class="fa-solid fa-bell-slash me-2"></i> TẠM HẾT HÀNG
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- MÔ TẢ / THÔNG SỐ -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <ul class="nav nav-pills" id="myTab">
                            <li class="nav-item me-2">
                                <button class="nav-link active fw-bold px-4" data-bs-toggle="tab" data-bs-target="#desc">
                                    Mô tả sản phẩm
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold px-4" data-bs-toggle="tab" data-bs-target="#spec">
                                    Thông số kỹ thuật
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="desc">
                                <div class="lh-lg text-secondary">
                                    <?php echo nl2br($product['description']); ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="spec">
                                <?php if (!empty($product['specifications'])): ?>
                                    <div class="bg-light p-4 rounded-3">
                                        <?php echo nl2br($product['specifications']); ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted fst-italic mb-0">
                                        Chưa có thông số kỹ thuật chi tiết cho sản phẩm này.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // 1. Hàm đổi ảnh khi click vào gallery
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // 2. [MỚI THÊM] Xử lý nút Tăng/Giảm số lượng
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy các phần tử
        const minusBtn = document.querySelector('.btn-outline-secondary:first-of-type');
        const plusBtn = document.querySelector('.btn-outline-secondary:last-of-type');
        const qtyInput = document.querySelector('input[name="quantity"]');

        // Lấy giới hạn tồn kho từ PHP (đã in sẵn trong thuộc tính max của input)
        const maxStock = parseInt(qtyInput.getAttribute('max'));

        // Xử lý nút TRỪ (-)
        minusBtn.addEventListener('click', function() {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue > 1) {
                qtyInput.value = currentValue - 1;
            }
        });

        // Xử lý nút CỘNG (+)
        plusBtn.addEventListener('click', function() {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue < maxStock) {
                qtyInput.value = currentValue + 1;
            } else {
                alert('Bạn chỉ có thể mua tối đa ' + maxStock + ' sản phẩm!');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/footer.php'; ?>