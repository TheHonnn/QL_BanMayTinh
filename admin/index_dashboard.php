<?php
// 1. Kết nối CSDL
require_once '../config/db.php';
include 'includes/header.php';

// 2. THỐNG KÊ SỐ LIỆU
// Đếm đơn hàng
$count_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
// Đếm sản phẩm
$count_products = $conn->query("SELECT COUNT(*) as total FROM product")->fetch_assoc()['total'];
// Đếm khách hàng
$count_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE role=0")->fetch_assoc()['total'];
// Tính doanh thu (Chỉ tính đơn đã hoàn thành: status=3)
$revenue = $conn->query("SELECT SUM(final_money) as total FROM orders WHERE status = 3")->fetch_assoc()['total'];

// 3. LẤY 5 ĐƠN HÀNG MỚI NHẤT
$sql_recent = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";
$recent_orders = $conn->query($sql_recent);
?>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Dashboard</h2>
            <p class="text-muted small mb-0">Chào mừng trở lại, <strong><?php echo $_SESSION['fullname']; ?></strong>!</p>
        </div>
        <div class="date text-muted">
            <i class="fa-regular fa-calendar"></i> <?php echo date("d/m/Y"); ?>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card text-white bg-warning h-100 shadow-sm overflow-hidden position-relative border-0">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase fw-bold mb-3 opacity-75">Doanh thu</h6>
                    <h3 class="fw-bold mb-0"><?php echo number_format($revenue ?? 0, 0, ',', '.'); ?> ₫</h3>
                    <i class="fa-solid fa-sack-dollar fa-4x position-absolute" style="right: 15px; bottom: 10px; opacity: 0.2; transform: rotate(-15deg);"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-primary h-100 shadow-sm overflow-hidden position-relative border-0">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase fw-bold mb-3 opacity-75">Đơn hàng</h6>
                    <h3 class="fw-bold mb-0"><?php echo $count_orders; ?></h3>
                    <small class="opacity-75">Tổng số đơn</small>
                    <i class="fa-solid fa-cart-shopping fa-4x position-absolute" style="right: 15px; bottom: 10px; opacity: 0.2; transform: rotate(-15deg);"></i>
                </div>
                <a href="modules/order/order_index.php" class="card-footer bg-transparent border-0 text-white text-end small text-decoration-none">Xem chi tiết &rarr;</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success h-100 shadow-sm overflow-hidden position-relative border-0">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase fw-bold mb-3 opacity-75">Sản phẩm</h6>
                    <h3 class="fw-bold mb-0"><?php echo $count_products; ?></h3>
                    <small class="opacity-75">Đang kinh doanh</small>
                    <i class="fa-solid fa-laptop fa-4x position-absolute" style="right: 15px; bottom: 10px; opacity: 0.2; transform: rotate(-15deg);"></i>
                </div>
                <a href="modules/product/index_product.php" class="card-footer bg-transparent border-0 text-white text-end small text-decoration-none">Xem chi tiết &rarr;</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-secondary h-100 shadow-sm overflow-hidden position-relative border-0" style="background: linear-gradient(45deg, #64748b, #475569);">
                <div class="card-body p-4">
                    <h6 class="card-title text-uppercase fw-bold mb-3 opacity-75">Khách hàng</h6>
                    <h3 class="fw-bold mb-0"><?php echo $count_users; ?></h3>
                    <small class="opacity-75">Tài khoản thành viên</small>
                    <i class="fa-solid fa-users fa-4x position-absolute" style="right: 15px; bottom: 10px; opacity: 0.2; transform: rotate(-15deg);"></i>
                </div>
                <a href="modules/user/user_index.php" class="card-footer bg-transparent border-0 text-white text-end small text-decoration-none">Xem chi tiết &rarr;</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i> Đơn hàng mới nhất</h5>
                    <a href="modules/order/order_index.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th class="text-end pe-4">Hành động</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php if ($recent_orders->num_rows > 0): ?>
                                    <?php while ($row = $recent_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">#<?php echo $row['order_id']; ?></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                                <small class="text-muted"><?php echo $row['phone']; ?></small>
                                            </td>
                                            <td class="fw-bold text-danger">
                                                <?php echo number_format($row['final_money'], 0, ',', '.'); ?> ₫
                                            </td>
                                            <td>
                                                <?php
                                                $st = $row['status'];
                                                if ($st == 1) echo '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Chờ duyệt</span>';
                                                elseif ($st == 2) echo '<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Đang giao</span>';
                                                elseif ($st == 3) echo '<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Hoàn thành</span>';
                                                else echo '<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Đã hủy</span>';
                                                ?>
                                            </td>
                                            <td class="text-muted small">
                                                <?php echo date('H:i - d/m', strtotime($row['order_date'])); ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="modules/order/detail.php?id=<?php echo $row['order_id']; ?>" class="btn btn-sm btn-light text-primary border" title="Xem chi tiết">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                           
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>