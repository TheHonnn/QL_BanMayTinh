<?php
require_once '../../../config/db.php'; 
include '../../includes/header.php'; 

// --- XỬ LÝ LỌC THỜI GIAN ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where_sql = "WHERE o.status = 3"; // Chỉ lấy đơn đã hoàn thành (tiền đã về)

if ($filter == 'today') {
    $where_sql .= " AND DATE(o.order_date) = CURDATE()";
} elseif ($filter == 'month') {
    $where_sql .= " AND MONTH(o.order_date) = MONTH(CURRENT_DATE()) AND YEAR(o.order_date) = YEAR(CURRENT_DATE())";
}

// Label tiếng Việt cho filter
$filter_label = 'Tất cả';
if ($filter == 'today') $filter_label = 'Hôm nay';
elseif ($filter == 'month') $filter_label = 'Tháng này';

// 1. Truy vấn Thống kê tổng quan
$sql_stats = "SELECT SUM(d.num) as total_items, SUM(d.total_price) as total_revenue 
              FROM order_details d 
              JOIN orders o ON d.order_id = o.order_id 
              $where_sql";
$stats = $conn->query($sql_stats)->fetch_assoc();

// 2. Truy vấn Chi tiết lịch sử bán hàng
$sql_list = "SELECT d.*, p.name as product_name, p.main_image, o.fullname, o.order_date, o.order_id
             FROM order_details d 
             JOIN product p ON d.product_id = p.product_id
             JOIN orders o ON d.order_id = o.order_id
             $where_sql
             ORDER BY o.order_date DESC LIMIT 100"; // Lấy 100 giao dịch mới nhất
$result = $conn->query($sql_list);
?>

<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">

<style>
    .revenue-history-wrapper {
        background: #f3f4f6;
        min-height: calc(100vh - 80px);
    }

    .rev-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .rev-subtitle {
        font-size: 13px;
        color: #6b7280;
    }

    .rev-filter-group .btn {
        border-radius: 999px !important;
        font-size: 12px;
        font-weight: 600;
        border-color: #e5e7eb;
        background: #ffffff;
        color: #4b5563;
    }

    .rev-filter-group .btn.active {
        background: #111827 !important;
        color: #f9fafb !important;
        border-color: #111827 !important;
    }

    .rev-card-stat {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 16px 40px rgba(15,23,42,0.12);
        overflow: hidden;
        position: relative;
    }

    .rev-card-stat-body {
        padding: 18px 22px;
        position: relative;
        z-index: 1;
    }

    .rev-stat-label {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .09em;
        opacity: .8;
    }

    .rev-stat-value {
        font-size: 24px;
        font-weight: 800;
    }

    .rev-card-icon {
        position: absolute;
        right: 18px;
        bottom: -6px;
        opacity: 0.18;
    }

    .rev-card-table {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 16px 40px rgba(15,23,42,0.12);
        overflow: hidden;
        background: #ffffff;
    }

    .rev-card-table-header {
        background: linear-gradient(135deg, #e0f2fe, #ffffff);
        border-bottom: 1px solid #e5e7eb;
        padding: 14px 20px;
    }

    .rev-card-table-header h6 {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .rev-card-table-header small {
        font-size: 12px;
        color: #6b7280;
    }

    .rev-table thead {
        background: #f9fafb;
    }

    .rev-table thead th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        border-bottom-width: 1px;
    }

    .rev-table tbody td {
        font-size: 13px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid revenue-history-wrapper py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4 mt-1">
        <div>
            <div class="rev-title">Lịch sử doanh thu</div>
            <div class="rev-subtitle">
                Nhật ký bán hàng chi tiết (chỉ tính đơn đã hoàn thành).
            </div>
        </div>
        <div class="btn-group shadow-sm rev-filter-group">
            <a href="history_all.php?filter=all" 
               class="btn <?php echo $filter=='all'?'active':''; ?>">
                Tất cả
            </a>
            <a href="history_all.php?filter=today" 
               class="btn <?php echo $filter=='today'?'active':''; ?>">
                Hôm nay
            </a>
            <a href="history_all.php?filter=month" 
               class="btn <?php echo $filter=='month'?'active':''; ?>">
                Tháng này
            </a>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="rev-card-stat bg-success text-white">
                <div class="rev-card-stat-body">
                    <div class="rev-stat-label mb-2">
                        Tổng doanh thu (<?php echo $filter_label; ?>)
                    </div>
                    <div class="rev-stat-value">
                        <?php echo number_format($stats['total_revenue'] ?? 0); ?> ₫
                    </div>
                    <div class="small mt-1 opacity-75">
                        Đã loại trừ các đơn hủy / chưa hoàn thành.
                    </div>
                    <i class="fa-solid fa-coins fa-4x rev-card-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="rev-card-stat bg-primary text-white">
                <div class="rev-card-stat-body">
                    <div class="rev-stat-label mb-2">
                        Số máy đã bán (<?php echo $filter_label; ?>)
                    </div>
                    <div class="rev-stat-value">
                        <?php echo number_format($stats['total_items'] ?? 0); ?> 
                        <small class="fs-6">sản phẩm</small>
                    </div>
                    <div class="small mt-1 opacity-75">
                        Tính theo tổng số lượng trong các đơn đã hoàn thành.
                    </div>
                    <i class="fa-solid fa-boxes-packing fa-4x rev-card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="rev-card-table">
        <div class="rev-card-table-header d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i> Giao dịch gần đây
                </h6>
                <small>Hiển thị tối đa 100 giao dịch mới nhất theo bộ lọc thời gian.</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 rev-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Thời gian</th>
                            <th>Sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end pe-4">Thành tiền</th>
                            <th>Khách hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 text-muted small">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    <?php echo date('H:i d/m/Y', strtotime($row['order_date'])); ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../../../assets/img/<?php echo $row['main_image']; ?>" 
                                             style="width: 45px; height: 45px; object-fit: cover; border-radius: 10px; border: 1px solid #e5e7eb;" 
                                             class="me-2">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                <?php echo htmlspecialchars($row['product_name']); ?>
                                            </div>
                                            <div class="text-muted small">
                                                Đơn giá: <?php echo number_format($row['price']); ?> ₫
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        x<?php echo $row['num']; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 text-success fw-bold">
                                    + <?php echo number_format($row['total_price']); ?> ₫
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center me-2" 
                                             style="width: 30px; height: 30px; font-size: 12px;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <span class="small"><?php echo htmlspecialchars($row['fullname']); ?></span>
                                        <a href="../order/detail.php?id=<?php echo $row['order_id']; ?>" 
                                           class="ms-2 text-muted" 
                                           title="Xem đơn">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open me-2"></i> Chưa có giao dịch hoàn thành nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
