<?php
// 1. Kết nối & Header
require_once '../../../config/db.php';
include '../../includes/header.php';

// 2. Kiểm tra ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$order_id = intval($_GET['id']);

// --- [QUAN TRỌNG] PHẦN NÀY MỚI THÊM VÀO ĐỂ XỬ LÝ CẬP NHẬT ---
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['status'])) {
    $status = intval($_GET['status']);

    // Cập nhật trạng thái
    $sql_update = "UPDATE orders SET status = $status WHERE order_id = $order_id";
    if ($conn->query($sql_update)) {
        // Cập nhật xong -> Load lại chính trang này để hiện trạng thái mới
        echo "<script>location.href='detail.php?id=$order_id';</script>";
        exit();
    } else {
        echo "<script>alert('Lỗi SQL: " . $conn->error . "');</script>";
    }
}
// ------------------------------------------------------------

// 3. Lấy thông tin Đơn hàng
$sql_order = "SELECT * FROM orders WHERE order_id = $order_id";
$order = $conn->query($sql_order)->fetch_assoc();

// 4. Lấy chi tiết sản phẩm
$sql_items = "SELECT d.*, p.name, p.main_image 
              FROM order_details d 
              JOIN product p ON d.product_id = p.product_id 
              WHERE d.order_id = $order_id";
$items = $conn->query($sql_items);
?>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <a href="order_index.php" class="text-decoration-none text-secondary mb-2 d-inline-block">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
            </a>
            <h3 class="fw-bold text-dark mb-0">Chi tiết đơn hàng #<?php echo $order_id; ?></h3>
        </div>
        <div>
            <a href="print.php?id=<?php echo $order_id; ?>" target="_blank" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-print"></i> In hóa đơn 
                </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary text-uppercase">
                        <i class="fa-solid fa-user me-2"></i> Thông tin khách hàng
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-0 pb-0">
                            <small class="text-muted text-uppercase fw-bold">Họ và tên</small>
                            <div class="fw-bold text-dark fs-6"><?php echo $order['fullname']; ?></div>
                        </li>
                        <li class="list-group-item px-0 border-0 pb-0 mt-3">
                            <small class="text-muted text-uppercase fw-bold">Số điện thoại</small>
                            <div class="fw-bold text-dark"><?php echo $order['phone']; ?></div>
                        </li>
                        <li class="list-group-item px-0 border-0 pb-0 mt-3">
                            <small class="text-muted text-uppercase fw-bold">Địa chỉ giao hàng</small>
                            <div class="text-dark"><?php echo $order['address']; ?></div>
                        </li>
                        <li class="list-group-item px-0 border-0 pb-0 mt-3">
                            <small class="text-muted text-uppercase fw-bold">Ghi chú</small>
                            <div class="text-dark fst-italic"><?php echo $order['note'] ? $order['note'] : 'Không có'; ?></div>
                        </li>
                        <li class="list-group-item px-0 border-0 pb-0 mt-3">
                            <small class="text-muted text-uppercase fw-bold">Ngày đặt hàng</small>
                            <div class="text-dark"><?php echo date('d/m/Y - H:i', strtotime($order['order_date'])); ?></div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-warning text-uppercase">
                        <i class="fa-solid fa-star me-2"></i> Trạng thái đơn hàng
                    </h6>
                </div>
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        <?php
                        $st = $order['status'];
                        if ($st == 1) echo '<span class="badge bg-secondary bg-opacity-10 text-secondary fs-6 px-4 py-2 rounded-pill">Chờ duyệt</span>';
                        elseif ($st == 2) echo '<span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-4 py-2 rounded-pill">Đang giao hàng</span>';
                        elseif ($st == 3) echo '<span class="badge bg-success bg-opacity-10 text-success fs-6 px-4 py-2 rounded-pill">Giao thành công</span>';
                        else echo '<span class="badge bg-danger bg-opacity-10 text-danger fs-6 px-4 py-2 rounded-pill">Đã hủy</span>';
                        ?>
                    </div>

                    <?php if ($st == 1): ?>
                        <div class="d-grid gap-2">
                            <a href="detail.php?id=<?php echo $order_id; ?>&action=update&status=2" class="btn btn-primary fw-bold">
                                <i class="fa-solid fa-check me-2"></i> DUYỆT ĐƠN NÀY
                            </a>
                            <a href="detail.php?id=<?php echo $order_id; ?>&action=update&status=4" class="btn btn-outline-danger fw-bold" onclick="return confirm('Hủy đơn này?')">
                                HỦY ĐƠN
                            </a>
                        </div>
                    <?php elseif ($st == 2): ?>
                        <div class="d-grid">
                            <a href="detail.php?id=<?php echo $order_id; ?>&action=update&status=3" class="btn btn-success fw-bold text-white">
                                <i class="fa-solid fa-box-open me-2"></i> XÁC NHẬN ĐÃ GIAO
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark text-uppercase">
                        <i class="fa-solid fa-bag-shopping me-2"></i> Sản phẩm đã đặt
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive border-0 shadow-none">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted text-uppercase small">
                                <tr>
                                    <th class="ps-4">Sản phẩm</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end pe-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $items->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="../../../assets/img/<?php echo $row['main_image']; ?>"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #f1f5f9;"
                                                    class="me-3 shadow-sm">
                                                <div>
                                                    <div class="fw-bold text-dark"><?php echo $row['name']; ?></div>
                                                    <small class="text-muted">ID: <?php echo $row['product_id']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted">
                                            <?php echo number_format($row['price'], 0, ',', '.'); ?> ₫
                                        </td>
                                        <td class="text-center fw-bold">x<?php echo $row['num']; ?></td>
                                        <td class="text-end pe-4 text-dark fw-bold">
                                            <?php echo number_format($row['total_price'], 0, ',', '.'); ?> ₫
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end py-4">
                                        <span class="text-muted text-uppercase small fw-bold me-2">Tổng tiền thanh toán:</span>
                                    </td>
                                    <td class="text-end pe-4 py-4">
                                        <span class="h4 fw-bold text-dan    ger mb-0">
                                            <?php echo number_format($order['final_money'], 0, ',', '.'); ?> ₫
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>