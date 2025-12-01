<?php
// 1. Kết nối CSDL
require_once '../../../config/db.php'; 
include '../../includes/header.php'; 

// --- XỬ LÝ XÓA ---
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    
    // Không cho xóa tài khoản đang đăng nhập
    if ($id == $_SESSION['user_id']) {
        echo "<script>
                alert(' Không thể xóa tài khoản đang đăng nhập!');
                location.href='index.php';
              </script>";
    } else {
        try {
            $conn->query("DELETE FROM users WHERE user_id = $id");
            echo "<script>alert(' Đã xóa người dùng thành công!'); location.href='index.php';</script>";
        } catch (Throwable $e) { 
            if (strpos($e->getMessage(), 'foreign key') !== false) {
                echo "<script>alert(' KHÔNG THỂ XÓA! User này đã có đơn hàng.'); location.href='index.php';</script>";
            } else {
                echo "<script>alert(' Lỗi hệ thống: " . addslashes($e->getMessage()) . "'); location.href='index.php';</script>";
            }
        }
    }
}

// --- XỬ LÝ TÌM KIẾM ---
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT * FROM users";
if ($keyword !== '') {
    $kw = $conn->real_escape_string($keyword);
    $sql .= " WHERE fullname LIKE '%$kw%' OR email LIKE '%$kw%'";
}
$sql .= " ORDER BY user_id DESC";

$result = $conn->query($sql);
$total = $result ? $result->num_rows : 0;
?>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid">

    <!-- Header + Search -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <h3 class="fw-bold text-dark mb-0">Quản lý Người dùng</h3>
            <span class="text-muted small">
                Tổng số: <?php echo $total; ?> tài khoản
                <?php if ($keyword !== ''): ?>
                    (lọc theo: <strong><?php echo htmlspecialchars($keyword); ?></strong>)
                <?php endif; ?>
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Ô tìm kiếm -->
            <form class="d-flex me-2" method="get" action="user_index.php">
                <input type="text"
                       name="q"
                       class="form-control form-control-sm"
                       placeholder="Tìm theo tên hoặc email..."
                       value="<?php echo htmlspecialchars($keyword); ?>">
                <button class="btn btn-sm btn-outline-secondary ms-2" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <a href="add_user.php" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                <i class="fa-solid fa-user-plus me-1"></i> Thêm thành viên
            </a>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Thông tin cá nhân</th>
                            <th class="py-3">Vai trò (Role)</th>
                            <th class="py-3">Trạng thái</th>
                            <th class="py-3">Ngày tham gia</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Không tìm thấy người dùng nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#<?php echo $row['user_id']; ?></td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                            <?php echo strtoupper(mb_substr($row['fullname'], 0, 1, 'UTF-8')); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                            <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if($row['role'] == 1): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-shield-halved me-1"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                            <i class="fa-solid fa-user me-1"></i> Khách hàng
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($row['status'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Locked</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-muted small">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                </td>

                                <td class="text-end pe-4">
                                    <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" 
                                       class="btn btn-sm btn-light text-primary border me-1" 
                                       title="Sửa thông tin">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    <a href="index.php?del=<?php echo $row['user_id']; ?>" 
                                       class="btn btn-sm btn-light text-danger border" 
                                       onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')" 
                                       title="Xóa tài khoản">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
