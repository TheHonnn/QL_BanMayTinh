<?php
require_once '../../../config/db.php';
include '../../includes/header.php';

// --- XỬ LÝ XÓA ---
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    try {
        $conn->query("DELETE FROM suppliers WHERE supplier_id = $id");
        echo "<script>alert(' Đã xóa nhà cung cấp!'); location.href='index.php';</script>";
    } catch (Throwable $e) {
        echo "<script>alert('KHÔNG THỂ XÓA! Nhà cung cấp này đang có sản phẩm trong kho.'); location.href='index_lier.php';</script>";
    }
}

$result = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
$total_suppliers = $result->num_rows;
?>

<style>
    .supplier-page {
        background: #f3f4f6;
        min-height: calc(100vh - 80px);
    }

    .sp-page-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .sp-page-sub {
        font-size: 13px;
        color: #6b7280;
    }

    .sp-badge-count {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .sp-btn-add {
        border-radius: 999px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 8px 22px rgba(79,70,229,.4);
    }

    .sp-btn-add:hover {
        opacity: .96;
        transform: translateY(-1px);
    }

    .sp-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .sp-table thead {
        background: #f9fafb;
    }

    .sp-table thead th {
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #6b7280 !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .sp-table tbody td {
        font-size: 14px;
        border-color: #e5e7eb !important;
    }

    .sp-id {
        font-weight: 700;
        color: #6b7280;
    }

    .sp-name {
        font-weight: 600;
        color: #111827;
    }

    .sp-contact-line {
        font-size: 12px;
        color: #6b7280;
    }

    .sp-address {
        font-size: 13px;
        color: #4b5563;
        max-width: 260px;
    }

    .sp-badge-email {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 11px;
        margin-top: 3px;
    }

    .sp-action-btn {
        border-radius: 999px;
        padding: 6px 9px;
        font-size: 12px;
    }

    .sp-empty {
        padding: 40px 0;
        text-align: center;
        color: #9ca3af;
        font-size: 14px;
    }

    .sp-empty-icon {
        width: 46px;
        height: 46px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4f46e5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        font-size: 20px;
    }
</style>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid supplier-page py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 mt-1">
        <div>
            <div class="sp-page-title">Nhà cung cấp</div>
            <div class="sp-page-sub mt-1">
                Quản lý danh sách đối tác cung cấp laptop, linh kiện cho cửa hàng.
            </div>
            <div class="mt-2">
                <span class="sp-badge-count">
                    <i class="fa-solid fa-building"></i>
                    <?php echo $total_suppliers; ?> nhà cung cấp
                </span>
            </div>
        </div>

        <a href="add.php" class="sp-btn-add">
            <i class="fa-solid fa-plus"></i>
            Thêm nhà cung cấp
        </a>
    </div>

    <div class="sp-card bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sp-table">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Nhà cung cấp</th>
                            <th class="py-3">Liên hệ</th>
                            <th class="py-3">Địa chỉ</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_suppliers > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 sp-id">#<?php echo $row['supplier_id']; ?></td>

                                    <td>
                                        <div class="sp-name"><?php echo $row['name']; ?></div>
                                    </td>

                                    <td>
                                        <?php if (!empty($row['phone'])): ?>
                                            <div class="sp-contact-line">
                                                <i class="fa-solid fa-phone me-1 text-success"></i>
                                                <?php echo $row['phone']; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($row['email'])): ?>
                                            <div class="sp-badge-email mt-1">
                                                <i class="fa-solid fa-envelope"></i>
                                                <?php echo $row['email']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="sp-address">
                                            <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                            <?php echo $row['address']; ?>
                                        </div>
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="edit.php?id=<?php echo $row['supplier_id']; ?>" 
                                           class="btn btn-sm btn-light text-primary border sp-action-btn me-1" 
                                           title="Sửa thông tin">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="index_lier.php?del=<?php echo $row['supplier_id']; ?>" 
                                           class="btn btn-sm btn-light text-danger border sp-action-btn" 
                                           onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?')" 
                                           title="Xóa nhà cung cấp">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
                                    <div class="sp-empty">
                                        <div class="sp-empty-icon">
                                            <i class="fa-solid fa-truck-field"></i>
                                        </div><br>
                                        Hiện chưa có nhà cung cấp nào trong hệ thống.<br>
                                        Hãy nhấn nút <strong>“Thêm nhà cung cấp”</strong> để tạo mới.
                                    </div>
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
