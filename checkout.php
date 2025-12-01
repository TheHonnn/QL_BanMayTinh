<?php
require_once 'config/db.php';
// Kiểm tra giỏ hàng có trống không
if (empty($_SESSION['cart'])) {
    header("Location: index_backup.php");
    exit();
}

$error = '';
$success = '';

// --- XỬ LÝ KHI BẤM NÚT "ĐẶT HÀNG" ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_order'])) {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];
    $payment_method = $_POST['payment_method'];
    
    // Lấy user_id nếu đã đăng nhập, nếu không thì để NULL
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';

    // Tính tổng tiền
    $total_money = 0;
    $cart_ids = implode(',', array_keys($_SESSION['cart']));
    $sql_cart = "SELECT * FROM product WHERE product_id IN ($cart_ids)";
    $res_cart = $conn->query($sql_cart);
    
    // Mảng tạm để lưu thông tin sản phẩm
    $cart_items = []; 
    while ($row = $res_cart->fetch_assoc()) {
        $id = $row['product_id'];
        $qty = $_SESSION['cart'][$id];
        $total_money += $row['price'] * $qty;
        $cart_items[] = [
            'id' => $id,
            'price' => $row['price'],
            'qty' => $qty
        ];
    }
    
    // 1. INSERT VÀO BẢNG ORDERS
    $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, note, payment_method, total_money, final_money, status) 
                  VALUES ($user_id, '$fullname', '$phone', '$address', '$note', '$payment_method', $total_money, $total_money, 1)";
    
    if ($conn->query($sql_order)) {
        $order_id = $conn->insert_id;

        // 2. INSERT VÀO BẢNG ORDER_DETAILS & TRỪ TỒN KHO
        foreach ($cart_items as $item) {
            $p_id = $item['id'];
            $price = $item['price'];
            $num = $item['qty'];
            $total = $price * $num;

            $conn->query("INSERT INTO order_details (order_id, product_id, price, num, total_price) 
                          VALUES ($order_id, $p_id, $price, $num, $total)");
            
            $conn->query("UPDATE product SET stock = stock - $num WHERE product_id = $p_id");
        }

        // 3. XÓA GIỎ HÀNG
        unset($_SESSION['cart']);

        // Chuyển sang trang thông báo
        header("Location: order_success.php?id=$order_id");
        exit();

    } else {
        $error = "Lỗi đặt hàng: " . $conn->error;
    }
}
include 'includes/header.php';
?>

<style>
/* ===== CHECKOUT PAGE STYLE ===== */
.checkout-wrapper {
    background: radial-gradient(circle at top, #1f2937 0, #020617 45%, #020617 100%);
    padding: 32px 0 40px;
}
.checkout-title {
    color: #f9fafb;
}
.checkout-sub {
    color: #9ca3af;
    font-size: .9rem;
}
.checkout-steps {
    display: inline-flex;
    gap: 10px;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.7);
    color: #e5e7eb;
    font-size: .8rem;
}
.checkout-step-badge {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    font-weight: 600;
    background: #22c55e;
    color: #022c22;
}
.checkout-card {
    border-radius: 18px;
    border: 1px solid #e5e7eb;
}
.checkout-card-header {
    border-radius: 18px 18px 0 0 !important;
}
.checkout-label {
    font-size: .85rem;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 600;
}
.checkout-summary-header {
    background: linear-gradient(135deg, #0f172a, #1e293b);
}
.checkout-summary-title {
    font-size: .9rem;
    letter-spacing: .05em;
}
.checkout-total-line {
    font-size: 1rem;
}
.checkout-product-name {
    font-size: .9rem;
}
.checkout-product-qty {
    font-size: .8rem;
}
.btn-checkout-main {
    border-radius: 999px;
    font-weight: 700;
    letter-spacing: .05em;
}
</style>

<div class="checkout-wrapper">
    <div class="container">

        <!-- HEADER -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h2 class="checkout-title fw-bold mb-2 text-uppercase">
                    Thanh toán đơn hàng
                </h2>
                <p class="checkout-sub mb-2">
                    Hoàn tất thông tin giao hàng & phương thức thanh toán để chúng mình xử lý đơn cho bạn nhanh nhất.
                </p>
                <div class="checkout-steps mt-1">
                    <span class="checkout-step-badge">1</span> Giỏ hàng
                    <i class="fa-solid fa-angle-right mx-1"></i>
                    <span class="checkout-step-badge" style="background:#facc15; color:#422006;">2</span> Thanh toán
                    <i class="fa-solid fa-angle-right mx-1"></i>
                    <span class="badge rounded-pill bg-slate-700 border text-white border-slate-500">3</span> Hoàn tất
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="cart.php" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại giỏ hàng
                </a>
            </div>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger shadow-sm border-0 mb-4">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="row g-4">

                <!-- CỘT TRÁI: THÔNG TIN GIAO HÀNG + THANH TOÁN -->
                <div class="col-lg-7">

                    <!-- Thông tin giao hàng -->
                    <div class="card checkout-card mb-4 shadow-sm">
                        <div class="card-header bg-white checkout-card-header border-0 px-4 py-3 d-flex align-items-center">
                            <div>
                                <div class="text-primary text-uppercase fw-bold small mb-1">
                                    <i class="fa-solid fa-location-dot me-1"></i> Thông tin giao hàng
                                </div>
                                <div class="text-muted small">
                                    Vui lòng nhập chính xác để giao đến đúng người nhận.
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4 pt-3">
                            <?php 
                                $u_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : '';
                            ?>
                            <div class="mb-3">
                                <label class="checkout-label mb-1">Họ và tên người nhận *</label>
                                <input type="text" name="fullname" class="form-control" 
                                       value="<?php echo htmlspecialchars($u_name); ?>" 
                                       required placeholder="Nguyễn Văn A">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="checkout-label mb-1">Số điện thoại *</label>
                                    <input type="text" name="phone" class="form-control" required placeholder="Ví dụ: 0394 316 963">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="checkout-label mb-1">Email (không bắt buộc)</label>
                                    <input type="email" name="email_fake" class="form-control" placeholder="Để nhận hóa đơn điện tử (nếu có)">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="checkout-label mb-1">Địa chỉ nhận hàng *</label>
                                <input type="text" name="address" class="form-control" required placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">
                            </div>

                            <div class="mb-0">
                                <label class="checkout-label mb-1">Ghi chú cho shipper</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Gọi trước khi giao, giao giờ hành chính..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="card checkout-card shadow-sm">
                        <div class="card-header bg-white checkout-card-header border-0 px-4 py-3 d-flex align-items-center">
                            <div>
                                <div class="text-info text-uppercase fw-bold small mb-1">
                                    <i class="fa-solid fa-wallet me-1"></i> Phương thức thanh toán
                                </div>
                                <div class="text-muted small">
                                    Bạn có thể chọn thanh toán khi nhận hàng hoặc chuyển khoản ngân hàng.
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4 pt-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm_cod" value="COD" checked>
                                <label class="form-check-label" for="pm_cod">
                                    <strong>Thanh toán khi nhận hàng (COD)</strong><br>
                                    <span class="text-muted small">Phù hợp nếu bạn muốn kiểm tra hàng trước khi thanh toán.</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm_bank" value="BANK">
                                <label class="form-check-label" for="pm_bank">
                                    <strong>Chuyển khoản ngân hàng</strong><br>
                                    <span class="text-muted small">Chi tiết số tài khoản và mã đơn sẽ hiển thị ở trang xác nhận.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- CỘT PHẢI: ĐƠN HÀNG -->
                <div class="col-lg-5">
                    <div class="card checkout-card shadow-lg border-0">
                        <div class="card-header checkout-summary-header border-0 px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-uppercase text-gray-300 checkout-summary-title mb-1">
                                        Đơn hàng của bạn
                                    </div>
                                    <div class="text-gray-300 small">
                                        <?php echo count($_SESSION['cart']); ?> sản phẩm trong giỏ
                                    </div>
                                </div>
                                <div class="text-end text-gray-300 small">
                                    <i class="fa-solid fa-shield-halved me-1 text-emerald-400"></i> Được bảo vệ bởi LaptopShop
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-4 pb-3 pt-3 bg-white">
                            <ul class="list-group list-group-flush mb-3">
                                <?php
                                $total_checkout = 0;
                                $cart_ids_show = implode(',', array_keys($_SESSION['cart']));
                                $res_show = $conn->query("SELECT * FROM product WHERE product_id IN ($cart_ids_show)");
                                
                                while ($p = $res_show->fetch_assoc()):
                                    $qty = $_SESSION['cart'][$p['product_id']];
                                    $sub = $p['price'] * $qty;
                                    $total_checkout += $sub;
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <div class="checkout-product-name fw-semibold">
                                                <?php echo htmlspecialchars($p['name']); ?>
                                            </div>
                                            <div class="checkout-product-qty text-muted">
                                                x <?php echo $qty; ?> • 
                                                <?php echo number_format($p['price'], 0, ',', '.'); ?> ₫
                                            </div>
                                        </div>
                                        <span class="text-muted">
                                            <?php echo number_format($sub, 0, ',', '.'); ?> ₫
                                        </span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>

                            <div class="d-flex justify-content-between mb-2 checkout-total-line">
                                <span class="text-muted">Tạm tính</span>
                                <span><?php echo number_format($total_checkout, 0, ',', '.'); ?> ₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 checkout-total-line">
                                <span class="text-muted">Phí vận chuyển</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Tổng thanh toán</span>
                                <span class="fw-bold text-danger fs-5">
                                    <?php echo number_format($total_checkout, 0, ',', '.'); ?> ₫
                                </span>
                            </div>
                            <p class="text-muted small mb-0">
                                Bằng cách bấm "Xác nhận đặt hàng", bạn đồng ý với 
                                <a href="#" class="text-decoration-none">Điều khoản mua hàng</a> của LaptopShop.
                            </p>
                        </div>

                        <div class="card-footer bg-white border-0 px-4 pb-4 pt-2">
                            <button type="submit" name="btn_order" class="btn btn-success w-100 btn-lg btn-checkout-main">
                                <i class="fa-solid fa-lock me-2"></i> Xác nhận đặt hàng
                            </button>
                            <div class="text-center mt-2 text-muted" style="font-size: .8rem;">
                                <i class="fa-solid fa-circle-info me-1"></i> Bạn sẽ được chuyển đến trang xác nhận đơn & hướng dẫn thanh toán.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
