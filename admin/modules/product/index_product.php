<?php
// 1. Kết nối CSDL
require_once '../../../config/db.php';
include '../../includes/header.php';

// --- XỬ LÝ XÓA ---
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);

    try {
        $conn->query("DELETE FROM product WHERE product_id = $id");
        echo "<script>alert('✅ Đã xóa sản phẩm thành công!'); location.href='index_product.php';</script>";
    } catch (Throwable $e) {
        if (strpos($e->getMessage(), 'foreign key') !== false) {
            echo "<script>
                alert('⚠️ KHÔNG THỂ XÓA!\\n\\nSản phẩm này đang nằm trong Đơn hàng hoặc Giỏ hàng.\\nHãy sửa Số lượng về 0 để ẩn sản phẩm.'); 
                location.href='index_product.php';
            </script>";
        } else {
            echo "<script>alert('❌ Lỗi hệ thống: " . addslashes($e->getMessage()) . "'); location.href='index_product.php';</script>";
        }
    }
}

// --- XỬ LÝ TÌM KIẾM ---
$keyword = '';
$where   = "";

if (!empty($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $where   = "WHERE p.name LIKE '%$keyword%'";
}

// Lấy danh sách sản phẩm
$sql = "SELECT p.*, c.name as cat_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        $where
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);
?>

<!-- CSS dùng chung cho admin + form sản phẩm -->
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css?v=1">
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin_product_form.css?v=1">

<div class="container-fluid">

    <!-- HEADER TRANG -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">Quản lý Sản phẩm</h3>
            <span class="text-muted small">
                Tổng: <strong><?php echo $result->num_rows; ?></strong> sản phẩm
                <?php if ($keyword): ?>
                    • Từ khóa: "<span class="text-primary fw-semibold"><?php echo htmlspecialchars($keyword); ?></span>"
                <?php endif; ?>
            </span>
        </div>
        <a href="add.php" class="btn admin-btn-main">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>

    <!-- FORM TÌM KIẾM -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 bg-white rounded">
            <form action="" method="GET" class="row g-2 align-items-center">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="keyword"
                            class="form-control bg-light border-0"
                            placeholder="Nhập tên sản phẩm cần tìm..."
                            value="<?php echo htmlspecialchars($keyword); ?>">
                    </div>
                </div>

                <div class="col-md-2">
                    <?php if ($keyword): ?>
                        <a href="index_product.php" class="btn admin-btn-secondary w-100">
                            <i class="fa-solid fa-xmark me-1"></i> Hủy lọc
                        </a>
                    <?php else: ?>
                        <button type="submit" class="btn admin-btn-primary w-100">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG DANH SÁCH SẢN PHẨM -->
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Sản phẩm</th>
                            <th class="py-3">Danh mục</th>
                            <th class="py-3">Giá bán</th>
                            <th class="py-3">Tồn kho</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <!-- Tên + ảnh -->
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img
                                                src="../../../assets/img/<?php echo $row['main_image']; ?>"
                                                alt="<?php echo htmlspecialchars($row['name']); ?>"
                                                style="width: 55px; height: 55px; object-fit: cover; border-radius: 10px;"
                                                class="me-3 border shadow-sm">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars($row['name']); ?>
                                                </div>
                                                <small class="text-muted">
                                                    ID: #<?php echo $row['product_id']; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Danh mục -->
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">
                                            <?php echo htmlspecialchars($row['cat_name']); ?>
                                        </span>
                                    </td>

                                    <!-- Giá -->
                                    <td class="fw-bold text-danger">
                                        <?php echo number_format($row['price'], 0, ',', '.'); ?> ₫
                                    </td>

                                    <!-- Tồn kho -->
                                    <td>
                                        <?php if ($row['stock'] > 0): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                Còn <?php echo $row['stock']; ?> cái
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                                Hết hàng
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Hành động -->
                                    <td class="text-end pe-4">
                                        <a href="edit.php?id=<?php echo $row['product_id']; ?>"
                                            class="btn btn-sm btn-light text-primary border me-1"
                                            title="Sửa sản phẩm">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <a href="index_product.php?del=<?php echo $row['product_id']; ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')"
                                            class="btn btn-sm btn-light text-danger border"
                                            title="Xóa sản phẩm">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <a href="history.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-light text-info border me-1" title="Lịch sử bán hàng">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </a>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fa-2x mb-3"></i><br>
                                    Không tìm thấy sản phẩm nào.
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