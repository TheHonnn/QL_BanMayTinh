<?php
require_once '../../../config/db.php'; 
include '../../includes/header.php'; 

// 1. Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = intval($_GET['id']);

// 2. Lấy thông tin sản phẩm
$product = $conn->query("SELECT * FROM product WHERE product_id = $id")->fetch_assoc();
if (!$product) {
    echo "<div class='alert alert-danger m-3'> Sản phẩm không tồn tại!</div>";
    include '../../includes/footer.php';
    exit();
}

// 3. Lấy lịch sử bán hàng (JOIN giữa order_details và orders)
// Chỉ lấy những đơn hàng KHÔNG bị hủy (status != 4) để tính doanh thu chuẩn
$sql = "SELECT o.order_id, o.fullname, o.phone, o.address, o.order_date, o.status, d.num, d.price as sold_price
        FROM order_details d
        JOIN orders o ON d.order_id = o.order_id
        WHERE d.product_id = $id
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);

// Tính toán tổng quan
$total_sold = 0;
$total_revenue = 0;
$history_data = []; // Lưu dữ liệu để loop sau

while ($row = $result->fetch_assoc()) {
    if ($row['status'] != 4) { // Không tính đơn hủy
        $total_sold += $row['num'];
        $total_revenue += ($row['num'] * $row['sold_price']);
    }
    $history_data[] = $row;
}
?>

<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css?v=1">

<style>
    .history-wrapper {
        background: #f3f4f6;
        min-height: calc(100vh - 80px);
    }

    .history-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .history-subtitle {
        font-size: 13px;
        color: #6b7280;
    }

    .history-back-btn {
        border-radius: 999px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .history-back-btn:hover {
        background: #f9fafb;
    }

    .history-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 16px 40px rgba(15,23,42,0.12);
        background: #ffffff;
        overflow: hidden;
    }

    .history-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #e0f2fe, #ffffff);
    }

    .history-card-header h6 {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .history-card-header small {
        font-size: 12px;
        color: #6b7280;
    }

    .history-product-img {
        max-height: 200px;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(15,23,42,.20);
        object-fit: cover;
    }

    .history-metric-label {
        text-transform: uppercase;
        font-size: 10px;
        color: #6b7280;
        letter-spacing: .09em;
        font-weight: 700;
    }

    .history-metric-value {
        font-size: 20px;
        font-weight: 800;
    }

    .history-metric-box {
        border-radius: 16px;
        border: 1px dashed #d1d5db;
        padding: 10px 12px;
        background: #f9fafb;
    }

    .history-table thead {
        background: #f9fafb;
    }

    .history-table thead th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        border-bottom-width: 1px;
    }

    .history-table tbody td {
        font-size: 13px;
        vertical-align: middle;
    }

    .badge-status {
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: .08em;
        border-radius: 999px;
        padding: 3px 7px;
    }

    .badge-status-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-status-warning {
        background: #fef3c7;
        color: #92400e;
    }
</style>

<div class="container-fluid history-wrapper py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 mt-1">
        <div>
            <div class="history-title">Lịch sử kinh doanh</div>
            <div class="history-subtitle mt-1">
                Sản phẩm: <strong><?php echo htmlspecialchars($product['name']); ?></strong>
            </div>
        </div>
        <a href="index_product.php" class="history-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <!-- Card bên trái: thông tin + tổng quan -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="history-card h-100">
                <div class="history-card-header text-center">
                    <h6><i class="fa-solid fa-laptop me-1"></i> Thông tin sản phẩm</h6>
                    <small>Một số chỉ số kinh doanh tổng quan.</small>
                </div>
                <div class="card-body p-4 text-center">
                    <img src="../../../assets/img/<?php echo htmlspecialchars($product['main_image']); ?>" 
                         class="img-fluid history-product-img mb-3" 
                         alt="Ảnh sản phẩm">

                    <h5 class="fw-bold text-primary mb-1">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h5>
                    <div class="text-muted small mb-3">
                        Giá hiện tại: <strong><?php echo number_format($product['price']); ?> ₫</strong>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="history-metric-box">
                                <div class="history-metric-label mb-1">Đã bán</div>
                                <div class="history-metric-value text-success">
                                    <?php echo $total_sold; ?>
                                </div>
                                <div class="text-muted small">Sản phẩm</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="history-metric-box">
                                <div class="history-metric-label mb-1">Doanh thu</div>
                                <div class="history-metric-value text-warning">
                                    <?php echo number_format($total_revenue); ?>
                                </div>
                                <div class="text-muted small">VNĐ (net)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-muted small">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Đơn đã hủy <span class="fst-italic">không</span> được tính vào doanh thu.
                    </div>
                </div>
            </div>
        </div>

        <!-- Card bên phải: nhật ký giao dịch -->
        <div class="col-md-8 col-lg-9 mb-4">
            <div class="history-card">
                <div class="history-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">
                            <i class="fa-solid fa-list-ul me-2"></i> Nhật ký giao dịch
                        </h6>
                        <small>Danh sách các đơn hàng có chứa sản phẩm này.</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 history-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Ngày mua</th>
                                    <th>Khách hàng</th>
                                    <th>Giao đến</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end pe-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($history_data) > 0): ?>
                                    <?php foreach ($history_data as $row): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">
                                                <?php echo date('d/m/Y', strtotime($row['order_date'])); ?>
                                            </div>
                                            <small class="text-muted">
                                                #<?php echo $row['order_id']; ?> 
                                                <?php 
                                                    if($row['status']==4) {
                                                        echo '<span class="badge-status badge-status-danger ms-1">Đã hủy</span>';
                                                    } elseif($row['status']==3) {
                                                        echo '<span class="badge-status badge-status-success ms-1">Hoàn thành</span>';
                                                    } else {
                                                        echo '<span class="badge-status badge-status-warning ms-1">Đang xử lý</span>';
                                                    }
                                                ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($row['phone']); ?></small>
                                        </td>
                                        <td class="small text-muted" style="max-width: 260px;">
                                            <?php echo htmlspecialchars($row['address']); ?>
                                        </td>
                                        <td class="text-center fw-bold">
                                            x<?php echo $row['num']; ?>
                                        </td>
                                        <td class="text-end pe-4 text-danger fw-bold">
                                            <?php echo number_format($row['num'] * $row['sold_price']); ?> ₫
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-box-open me-2"></i> Chưa có giao dịch nào.
                                        </td>
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

<?php include '../../includes/footer.php'; ?>
