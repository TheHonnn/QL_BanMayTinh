<?php
require_once 'config/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    header("Location: index_backup.php");
    exit();
}

$order_id = intval($_GET['id']);

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM orders WHERE order_id = $order_id";
$query = $conn->query($sql);
$order = $query->fetch_assoc();

if (!$order) {
    die("Đơn hàng không tồn tại");
}

// Cấu hình tài khoản nhận chuyển khoản
$ngan_hang = "MB";               // Mã ngân hàng (MB, VCB, TCB, BIDV...)
$so_tk    = "0394316963";        // Số tài khoản
$chu_tk   = "BUI THE HOANG";     // Tên chủ tài khoản
$so_tien  = $order['final_money'];
$noi_dung = "THANHTOAN DH" . $order_id;

// Link ảnh QR VietQR
$link_qr  = "https://img.vietqr.io/image/$ngan_hang-$so_tk-compact.jpg?amount=$so_tien&addInfo=$noi_dung&accountName=$chu_tk";
?>

<style>
/* ==== ORDER SUCCESS STYLE ==== */
.order-success-wrapper {
    background: radial-gradient(circle at top, #1f2937 0, #020617 45%, #020617 100%);
    padding: 40px 0 60px;
}
.order-success-card {
    border-radius: 22px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    overflow: hidden;
}
.order-success-header {
    background: radial-gradient(circle at top left, #22c55e, #0f172a 55%);
    color: #ecfdf5;
}
.order-id-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.7);
    font-size: .8rem;
}
.order-id-badge span {
    font-weight: 700;
}
.order-success-title {
    font-size: 1.5rem;
    letter-spacing: .06em;
}
.order-info-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    border: 1px dashed rgba(148, 163, 184, 0.7);
    font-size: .75rem;
    color: #e5e7eb;
}
.order-qr-box {
    background: #f9fafb;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
}
.order-qr-meta p {
    font-size: .9rem;
}
.order-qr-meta p strong {
    font-weight: 600;
}
.cod-alert {
    background: #ecfeff;
    border-color: #06b6d4;
}
.order-actions .btn {
    border-radius: 999px;
}
.badge-method {
    font-size: .75rem;
    border-radius: 999px;
    padding: 4px 10px;
}
</style>

<div class="order-success-wrapper">
    <div class="container">
        <div class="card shadow-xl order-success-card mx-auto" style="max-width: 780px;">
            
            <!-- HEADER -->
            <div class="order-success-header px-4 px-md-5 py-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div class="d-flex gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-emerald-500 rounded-circle" 
                             style="width: 58px; height: 58px; background:#22c55e;">
                            <i class="fa-solid fa-circle-check fa-2x" style="color:#022c22;"></i>
                        </div>
                        <div>
                            <div class="order-id-badge mb-2">
                                <i class="fa-solid fa-receipt"></i>
                                <span>#<?php echo $order_id; ?></span>
                            </div>
                            <h2 class="order-success-title fw-bold text-uppercase mb-1">
                                Đặt hàng thành công
                            </h2>
                            <div class="small text-slate-200">
                                Cảm ơn bạn đã tin tưởng LaptopShop. Chúng mình đã ghi nhận đơn hàng của bạn.
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="order-info-chip mb-2">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <span><?php echo number_format($so_tien, 0, ',', '.'); ?> ₫</span>
                        </div>
                        <div class="d-block small text-slate-200">
                            Thanh toán:
                            <?php if ($order['payment_method'] == 'BANK'): ?>
                                <span class="badge-method bg-emerald-100 text-emerald-800">Chuyển khoản ngân hàng</span>
                            <?php else: ?>
                                <span class="badge-method bg-amber-100 text-amber-800">Thanh toán khi nhận hàng (COD)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="card-body px-4 px-md-5 py-4">
                <?php if ($order['payment_method'] == 'BANK'): ?>

                    <div class="row g-4">
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <div class="text-center order-qr-box p-3">
                                <h5 class="fw-bold text-primary mb-2">Quét mã VietQR để thanh toán</h5>
                                <p class="text-muted small mb-3">
                                    Sử dụng ứng dụng Mobile Banking bất kỳ để quét mã.
                                </p>
                                <img src="<?php echo $link_qr; ?>" 
                                     alt="QR Code" 
                                     class="img-fluid border rounded-3 bg-white p-1"
                                     style="max-width: 260px;">
                                <p class="mt-2 mb-0 text-muted small fst-italic">
                                    Mã QR đã được tạo sẵn <strong>số tiền</strong> và <strong>nội dung chuyển khoản</strong>.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Thông tin chuyển khoản</h5>
                            <div class="order-qr-meta">
                                <p class="mb-1">
                                    Số tiền: 
                                    <strong class="text-danger fs-5">
                                        <?php echo number_format($so_tien, 0, ',', '.'); ?> ₫
                                    </strong>
                                </p>
                                <p class="mb-1">
                                    Ngân hàng: 
                                    <strong><?php echo $ngan_hang; ?></strong>
                                </p>
                                <p class="mb-1">
                                    Số tài khoản: 
                                    <strong><?php echo $so_tk; ?></strong>
                                </p>
                                <p class="mb-1">
                                    Chủ tài khoản: 
                                    <strong><?php echo $chu_tk; ?></strong>
                                </p>
                                <p class="mb-1">
                                    Nội dung chuyển khoản: 
                                    <strong class="text-primary"><?php echo $noi_dung; ?></strong>
                                </p>
                            </div>

                            <hr>

                            <ul class="text-muted small ps-3 mb-2">
                                <li>Vui lòng chuyển khoản <strong>đúng số tiền</strong> và <strong>đúng nội dung</strong>.</li>
                                <li>Sau khi nhận được tiền, shop sẽ <strong>xác nhận & đóng gói đơn</strong>.</li>
                                <li>Thời gian xử lý: thường trong vòng <strong>15–30 phút</strong> giờ hành chính.</li>
                            </ul>

                            <div class="alert alert-warning small mb-0">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                Nếu bạn chuyển khoản ngoài giờ hành chính, đơn sẽ được ưu tiên xử lý vào đầu giờ làm việc tiếp theo.
                            </div>
                        </div>
                    </div>

                <?php else: ?>

                    <div class="alert cod-alert border small mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fa-solid fa-box-open fa-2x text-cyan-600"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Thanh toán khi nhận hàng (COD)</h5>
                                <p class="mb-1">
                                    Đơn hàng của bạn sẽ được giao tận nơi. Vui lòng chuẩn bị:
                                    <strong class="text-danger"><?php echo number_format($so_tien, 0, ',', '.'); ?> ₫</strong>
                                </p>
                                <p class="mb-0 text-muted">
                                    Shipper sẽ liên hệ qua số điện thoại bạn cung cấp trước khi giao.  
                                    Bạn có thể kiểm tra máy trước khi thanh toán.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="small text-muted">
                        • Thời gian giao hàng dự kiến: <strong>1–3 ngày làm việc</strong> (tùy khu vực). <br>
                        • Vui lòng giữ liên lạc qua số điện thoại để việc giao nhận thuận lợi hơn.
                    </div>

                <?php endif; ?>
            </div>

            <!-- FOOTER BUTTONS -->
            <div class="card-footer bg-white border-0 px-4 px-md-5 pb-4 pt-0">
                <div class="order-actions d-flex flex-wrap justify-content-center gap-3 mt-3">
                    <a href="index_backup.php" class="btn btn-primary px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> Tiếp tục mua sắm
                    </a>
                    <a href="profile.php" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Xem lịch sử đơn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
