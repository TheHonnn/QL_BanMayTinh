<?php
require_once 'config/db.php';

// KHỞI TẠO GIỎ HÀNG NẾU CHƯA CÓ
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. THÊM VÀO GIỎ (từ trang chi tiết – POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $id  = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity']));

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
    header("Location: cart.php");
    exit();
}

// 2. CẬP NHẬT SỐ LƯỢNG
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

// 3. XÓA 1 SẢN PHẨM
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// 4. THÊM NHANH TỪ TRANG CHỦ (GET ?add_quick=ID)
if (isset($_GET['add_quick'])) {
    $id = intval($_GET['add_quick']);
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    header("Location: cart.php");
    exit();
}

// --- LẤY DỮ LIỆU SẢN PHẨM TRONG GIỎ ---
$cart_ids   = array_keys($_SESSION['cart']);
$cart_empty = empty($cart_ids);
$total_money = 0;

if (!$cart_empty) {
    $ids_string = implode(',', array_map('intval', $cart_ids));
    $sql   = "SELECT * FROM product WHERE product_id IN ($ids_string)";
    $result = $conn->query($sql);
}
include 'includes/header.php';

?>

<style>
/* ========== CART PAGE ========== */
.cart-page-title {
    font-size: 1.8rem;
    font-weight: 700;
    text-transform: uppercase;
}

.cart-empty-icon {
    opacity: 0.5;
}

.cart-table th {
    background: #0f172a;
    color: #f9fafb;
    border: none !important;
}

.cart-table td {
    vertical-align: middle;
}

.cart-product-name a {
    font-weight: 600;
}

.cart-product-name a:hover {
    color: #2563eb;
}

.cart-summary-card {
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.cart-summary-header {
    background: #0f172a;
    color: #f9fafb;
}

.cart-summary-header h5 {
    font-size: 1rem;
    letter-spacing: .05em;
}

.cart-summary-body {
    background: #ffffff;
}

.cart-badge-small {
    font-size: .8rem;
    padding: 0.15rem .5rem;
    border-radius: 999px;
}

.cart-total-row span:last-child {
    font-size: 1.15rem;
}

.cart-actions .btn {
    border-radius: 999px;
}

.cart-product-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
    .cart-page-title {
        font-size: 1.4rem;
    }
}
</style>

<div class="container py-5">

    <?php
    $cart_count = 0;
    if (!$cart_empty && isset($result)) {
        // đếm tổng số item (số lượng)
        foreach ($_SESSION['cart'] as $qty_tmp) {
            $cart_count += $qty_tmp;
        }
    }
    ?>

    <!-- HEADER GIỎ HÀNG -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="cart-page-title mb-1">
                <i class="fa-solid fa-cart-shopping me-2 text-primary"></i> Giỏ hàng của bạn
            </h2>
            <div class="text-muted small">
                <?php if ($cart_empty): ?>
                    Hiện chưa có sản phẩm nào. Hãy chọn vài chiếc laptop ưng ý nhé!
                <?php else: ?>
                    Có <span class="fw-semibold text-primary"><?php echo $cart_count; ?></span> sản phẩm trong giỏ.
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$cart_empty): ?>
            <div class="text-end">
                <a href="index_backup.php" class="btn btn-outline-secondary btn-sm cart-actions me-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Tiếp tục mua sắm
                </a>
                <button type="submit" form="cartForm" name="update_cart"
                        class="btn btn-warning btn-sm text-dark fw-semibold cart-actions">
                    <i class="fa-solid fa-rotate me-1"></i> Cập nhật giỏ
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($cart_empty): ?>
        <!-- GIỎ HÀNG RỖNG -->
        <div class="text-center py-5">
            <i class="fa-solid fa-cart-arrow-down fa-4x text-secondary cart-empty-icon mb-3"></i>
            <p class="lead mb-3">Giỏ hàng của bạn đang trống.</p>
            <p class="text-muted mb-4">Khám phá ngay những mẫu laptop mới nhất với nhiều ưu đãi hấp dẫn.</p>
            <a href="index_backup.php" class="btn btn-primary btn-lg px-4">
                <i class="fa-solid fa-laptop me-2"></i> Bắt đầu mua sắm
            </a>
        </div>
    <?php else: ?>

        <form action="cart.php" method="POST" id="cartForm">
            <div class="row g-4">

                <!-- DANH SÁCH SẢN PHẨM -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0 cart-table align-middle text-center">
                                    <thead>
                                        <tr>
                                            <th class="text-start ps-4">Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th style="width: 130px;">Số lượng</th>
                                            <th>Thành tiền</th>
                                            <th style="width: 70px;">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_money = 0;
                                        // reset pointer nếu cần
                                        $result->data_seek(0);
                                        while ($row = $result->fetch_assoc()):
                                            $id   = $row['product_id'];
                                            $qty  = $_SESSION['cart'][$id];
                                            $price = (int)$row['price'];
                                            $subtotal = $price * $qty;
                                            $total_money += $subtotal;
                                        ?>
                                            <tr>
                                                <td class="text-start ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/img/<?php echo $row['main_image']; ?>"
                                                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                                                             class="cart-product-img me-3">
                                                        <div>
                                                            <div class="cart-product-name mb-1">
                                                                <a href="product_detail.php?id=<?php echo $id; ?>"
                                                                   class="text-decoration-none text-dark">
                                                                    <?php echo htmlspecialchars($row['name']); ?>
                                                                </a>
                                                            </div>
                                                            <div class="text-muted small">
                                                                Mã: #<?php echo $id; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="fw-semibold">
                                                        <?php echo number_format($price, 0, ',', '.'); ?> ₫
                                                    </span>
                                                </td>

                                                <td>
                                                    <input type="number"
                                                           name="qty[<?php echo $id; ?>]"
                                                           value="<?php echo $qty; ?>"
                                                           min="1"
                                                           class="form-control form-control-sm text-center mx-auto"
                                                           style="max-width: 80px;">
                                                </td>

                                                <td>
                                                    <span class="fw-bold text-danger">
                                                        <?php echo number_format($subtotal, 0, ',', '.'); ?> ₫
                                                    </span>
                                                </td>

                                                <td>
                                                    <a href="cart.php?del=<?php echo $id; ?>"
                                                       class="btn btn-sm btn-outline-danger rounded-circle"
                                                       onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này khỏi giỏ?')">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- NÚT DƯỚI BẢNG (CHO MOBILE) -->
                    <div class="d-flex justify-content-between align-items-center mt-3 d-lg-none">
                        <a href="index_backup.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i> Mua thêm
                        </a>
                        <button type="submit" name="update_cart" class="btn btn-warning btn-sm text-dark fw-semibold">
                            <i class="fa-solid fa-rotate me-1"></i> Cập nhật giỏ
                        </button>
                    </div>
                </div>

                <!-- TÓM TẮT ĐƠN HÀNG -->
                <div class="col-lg-4">
                    <div class="cart-summary-card shadow-sm">
                        <div class="cart-summary-header px-4 py-3">
                            <h5 class="mb-0 text-uppercase">
                                <i class="fa-solid fa-receipt me-2"></i> Tóm tắt đơn hàng
                            </h5>
                        </div>
                        <div class="cart-summary-body px-4 py-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính</span>
                                <span class="fw-semibold">
                                    <?php echo number_format($total_money, 0, ',', '.'); ?> ₫
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển</span>
                                <span class="text-success fw-semibold">Miễn phí</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Ưu đãi</span>
                                <span class="text-muted small">
                                    <span class="badge bg-success-subtle text-success cart-badge-small">
                                        Đơn trên 20 triệu được giảm thêm
                                    </span>
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3 cart-total-row">
                                <span class="fw-bold">Tổng cộng</span>
                                <span class="fw-bold text-danger">
                                    <?php echo number_format($total_money, 0, ',', '.'); ?> ₫
                                </span>
                            </div>

                            <p class="small text-muted mb-3">
                                Bằng việc tiếp tục, bạn đồng ý với chính sách bảo hành & đổi trả của LaptopShop.
                            </p>

                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-danger btn-lg text-uppercase fw-bold">
                                    <i class="fa-solid fa-credit-card me-2"></i> Tiến hành đặt hàng
                                </a>
                                <a href="index_backup.php" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Xem thêm sản phẩm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end row -->
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
