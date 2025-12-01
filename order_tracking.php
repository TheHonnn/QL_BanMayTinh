<?php
require_once 'config/db.php';
include 'includes/header.php';

$order = null;
$items = [];
$error = "";
$is_searched = false; // Biến kiểm tra xem đã tìm kiếm chưa

// --- XỬ LÝ TRA CỨU ---
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['order_id']) && isset($_GET['phone'])) {
    $order_id = intval($_GET['order_id']);
    $phone = $conn->real_escape_string($_GET['phone']);

    $sql = "SELECT * FROM orders WHERE order_id = $order_id AND phone = '$phone'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $is_searched = true; // Đánh dấu là tìm thấy

        $sql_items = "SELECT d.*, p.name, p.main_image
                      FROM order_details d 
                      JOIN product p ON d.product_id = p.product_id
                      WHERE d.order_id = $order_id";
        $items = $conn->query($sql_items);
    } else {
        $error = "❌ Không tìm thấy đơn hàng! Vui lòng kiểm tra lại.";
    }
}
?>

<style>
    /* ===== STYLE ĐẶC BIỆT CHO TRANG TRA CỨU ===== */
    .page-track-bg {
        background: radial-gradient(circle at top, #1f2937 0, #020617 45%, #020617 100%);
        min-height: 100vh;
    }

    .track-card-search {
        border-radius: 20px;
        border: none;
        transition: all 0.3s ease;
    }

    .track-card-result {
        border-radius: 24px;
        border: none;
    }

    .step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        font-weight: 600;
        font-size: 14px;
    }

    .step-active {
        background: #22c55e;
        color: #fff;
    }

    .step-inactive {
        background: #e5e7eb;
        color: #6b7280;
    }

    .timeline-bar {
        height: 5px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .timeline-bar-fill {
        height: 5px;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        transition: width .4s ease;
    }

    .product-item {
        border-bottom: 1px dashed #e5e7eb;
        padding-bottom: 12px;
    }

    .product-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .badge-status {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 999px;
    }

    /* Nút mở lại form tìm kiếm */
    .btn-toggle-search {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        padding: 10px 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-toggle-search:hover {
        background: rgba(255, 255, 255, 0.2);
    }
</style>

<div class="page-track-bg pb-5">
    <div class="container py-5">

        <div class="text-center text-white mb-4">
            <h2 class="fw-bold text-uppercase mb-2">Tra cứu đơn hàng</h2>
            <p class="text-white-50 mb-0">Theo dõi hành trình đơn hàng của bạn mọi lúc, mọi nơi.</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-md-6">

                <?php if ($is_searched): ?>
                    <div class="text-center mb-3">
                        <button class="btn-toggle-search" type="button" data-bs-toggle="collapse" data-bs-target="#searchFormCollapse" aria-expanded="false">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> Tra cứu đơn hàng khác
                        </button>
                    </div>
                <?php endif; ?>

                <div class="collapse <?php echo $is_searched ? '' : 'show'; ?>" id="searchFormCollapse">
                    <div class="card shadow-lg track-card-search p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-truck-fast text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Nhập thông tin đơn hàng</h5>
                                <small class="text-muted">Thông tin chính xác giúp tra cứu nhanh hơn</small>
                            </div>
                        </div>

                        <form method="GET">
                            <div class="mb-3">
                                <label class="fw-bold small text-muted text-uppercase">Mã đơn hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">#</span>
                                    <input type="number" name="order_id" class="form-control border-0 bg-light" placeholder="VD: 21" required
                                        value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-muted text-uppercase">Số điện thoại đặt hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="phone" class="form-control border-0 bg-light" placeholder="Nhập SĐT dùng khi đặt hàng" required
                                        value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>">
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 fw-bold mt-2">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Tra cứu ngay
                            </button>
                        </form>

                        <?php if ($error): ?>
                            <div class="alert alert-danger mt-3 text-center small mb-0">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($order): ?>
            <div class="row justify-content-center fade show">
                <div class="col-lg-10">
                    <div class="card shadow-lg track-card-result">

                        <div class="card-header border-0 text-white" style="background: linear-gradient(90deg,#0f172a,#1f2937);">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <div class="bg-success d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 46px; height: 46px;">
                                        <i class="fa-solid fa-receipt fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Đơn hàng #<?php echo $order['order_id']; ?></h5>
                                        <small class="text-white-50">
                                            Đặt lúc: <?php echo date('H:i d/m/Y', strtotime($order['order_date'])); ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <?php
                                    $status_text = '';
                                    $status_class = '';
                                    if ($order['status'] == 1) {
                                        $status_text = 'Chờ duyệt';
                                        $status_class = 'bg-warning text-dark';
                                    } elseif ($order['status'] == 2) {
                                        $status_text = 'Đang giao';
                                        $status_class = 'bg-info text-dark';
                                    } elseif ($order['status'] == 3) {
                                        $status_text = 'Hoàn thành';
                                        $status_class = 'bg-success';
                                    } else {
                                        $status_text = 'Đã hủy';
                                        $status_class = 'bg-danger';
                                    }
                                    ?>
                                    <span class="badge badge-status <?php echo $status_class; ?>">
                                        <i class="fa-solid fa-circle me-1" style="font-size: 8px;"></i> <?php echo $status_text; ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="mb-4">
                                <?php
                                $percent = 0;
                                if ($order['status'] == 1) $percent = 25;
                                elseif ($order['status'] == 2) $percent = 75;
                                elseif ($order['status'] == 3) $percent = 100;
                                ?>
                                <div class="timeline-bar mb-3">
                                    <div class="timeline-bar-fill" style="width: <?php echo $percent; ?>%;"></div>
                                </div>
                                <div class="d-flex justify-content-between text-center">
                                    <div>
                                        <div class="step-circle d-flex align-items-center justify-content-center <?php echo $order['status'] >= 1 ? 'step-active' : 'step-inactive'; ?>">1</div>
                                        <small class="fw-bold d-block mt-2">Chờ duyệt</small>
                                    </div>
                                    <div>
                                        <div class="step-circle d-flex align-items-center justify-content-center <?php echo ($order['status'] >= 2 && $order['status'] != 4) ? 'step-active' : 'step-inactive'; ?>">2</div>
                                        <small class="fw-bold d-block mt-2">Đang giao</small>
                                    </div>
                                    <div>
                                        <div class="step-circle d-flex align-items-center justify-content-center <?php echo $order['status'] == 3 ? 'step-active' : 'step-inactive'; ?>">3</div>
                                        <small class="fw-bold d-block mt-2">Hoàn thành</small>
                                    </div>
                                </div>
                                <?php if ($order['status'] == 4): ?>
                                    <div class="alert alert-danger fw-bold text-center mt-4 mb-0">
                                        <i class="fa-solid fa-circle-xmark me-2"></i> ĐƠN HÀNG ĐÃ BỊ HỦY
                                    </div>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <div class="row pt-3">
                                <div class="col-md-5 border-end mb-4 mb-md-0">
                                    <h6 class="text-uppercase text-muted fw-bold mb-3 small">Thông tin nhận hàng</h6>
                                    <p class="mb-1"><strong>Người nhận:</strong> <?php echo $order['fullname']; ?></p>
                                    <p class="mb-1"><strong>SĐT:</strong> <?php echo $order['phone']; ?></p>
                                    <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo $order['address']; ?></p>
                                    <p class="mb-2">
                                        <strong>Thanh toán:</strong>
                                        <?php if ($order['payment_method'] == "COD"): ?>
                                            <span class="badge bg-secondary ms-1">COD</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark ms-1">Chuyển khoản</span>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <div class="col-md-7">
                                    <h6 class="text-uppercase text-muted fw-bold mb-3 small">Sản phẩm đã mua</h6>
                                    <?php while ($i = $items->fetch_assoc()): ?>
                                        <div class="d-flex align-items-center product-item mb-3">
                                            <img src="assets/img/<?php echo $i['main_image']; ?>" style="width: 60px; height: 60px; border-radius: 10px; object-fit: cover;" class="me-3">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-dark"><?php echo $i['name']; ?></div>
                                                <div class="small text-muted"><?php echo number_format($i['price'], 0, ',', '.'); ?> ₫ × <?php echo $i['num']; ?></div>
                                            </div>
                                            <strong class="text-danger"><?php echo number_format($i['total_price'], 0, ',', '.'); ?> ₫</strong>
                                        </div>
                                    <?php endwhile; ?>
                                    <div class="text-end border-top pt-3 mt-2">
                                        <span class="fw-bold fs-5">Tổng cộng: <span class="text-danger"><?php echo number_format($order['final_money'], 0, ',', '.'); ?> ₫</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                            <a href="index_backup.php" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Về trang chủ
                            </a>
                            <a href="profile.php" class="btn btn-primary">
                                <i class="fa-solid fa-clock-rotate-left me-1"></i> Lịch sử đơn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>



<?php include 'includes/footer.php'; ?>