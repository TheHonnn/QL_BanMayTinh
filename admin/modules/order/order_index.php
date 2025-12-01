<?php
// 1. Kết nối CSDL
require_once '../../../config/db.php';

// 2. Nhúng Header
include '../../includes/header.php';

// --- XỬ LÝ CẬP NHẬT TRẠNG THÁI ---
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id     = intval($_GET['id']);
    $status = intval($_GET['status']);

    $sql = "UPDATE orders SET status = $status WHERE order_id = $id";
    if ($conn->query($sql)) {
        echo "<script>alert(' Cập nhật trạng thái đơn hàng thành công!'); location.href='order_index.php';</script>";
    } else {
        echo "<script>alert(' Lỗi: " . addslashes($conn->error) . "');</script>";
    }
}

// --- BỘ LỌC & TÌM KIẾM ---
$keyword       = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$status_filter = isset($_GET['status_filter']) ? intval($_GET['status_filter']) : 0;

$where = "WHERE 1=1";

if ($keyword !== '') {
    $kw = $conn->real_escape_string($keyword);
    $where .= " AND (fullname LIKE '%$kw%' 
                 OR phone LIKE '%$kw%' 
                 OR CAST(order_id AS CHAR) LIKE '%$kw%')";
}

if ($status_filter > 0) {
    $where .= " AND status = $status_filter";
}

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM orders $where ORDER BY order_date DESC";
$result = $conn->query($sql);

// Đếm tổng số (không theo filter)
$total_all = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
?>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css?v=1">

<div class="container-fluid">

    <!-- HEADER TRANG -->
    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
        <!-- BÊN TRÁI: TIÊU ĐỀ + THỐNG KÊ -->
        <div>
            <h3 class="fw-bold text-dark mb-1">Quản lý Đơn hàng</h3>
            <div class="text-muted small">
                Tổng đơn trong hệ thống:
                <span class="fw-bold text-primary"><?php echo $total_all; ?></span> •
                Hiển thị: <span class="fw-bold"><?php echo $result->num_rows; ?></span> đơn theo bộ lọc hiện tại
            </div>
        </div>

        <!-- BÊN PHẢI: BADGE + NÚT TẠO ĐƠN -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                <i class="fa-solid fa-receipt me-1"></i> Đơn hàng
            </span>

            <a href="add.php" class="badge bg-warning rounded-pill fs-6 px-3 py-2 fw-bold text-decoration-none">
                <i class="fa-solid fa-plus me-1"></i>Tạo đơn
            </a>

        </div>
    </div>


    <!-- FORM TÌM KIẾM & LỌC -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <label class="form-label mb-1 small text-muted fw-semibold">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="keyword"
                            class="form-control bg-light border-0"
                            placeholder="Nhập tên khách, SĐT hoặc mã đơn hàng..."
                            value="<?php echo htmlspecialchars($keyword); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-1 small text-muted fw-semibold">Trạng thái</label>
                    <select name="status_filter" class="form-select bg-light border-0">
                        <option value="0">Tất cả trạng thái</option>
                        <option value="1" <?php if ($status_filter == 1) echo 'selected'; ?>>Chờ duyệt</option>
                        <option value="2" <?php if ($status_filter == 2) echo 'selected'; ?>>Đang giao</option>
                        <option value="3" <?php if ($status_filter == 3) echo 'selected'; ?>>Hoàn thành</option>
                        <option value="4" <?php if ($status_filter == 4) echo 'selected'; ?>>Đã hủy</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fa-solid fa-filter me-1"></i> Áp dụng
                    </button>

                    <?php if ($keyword !== '' || $status_filter > 0): ?>
                        <a href="order_index.php" class="btn btn-outline-secondary px-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> Bỏ lọc
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG ĐƠN HÀNG -->
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Mã ĐH</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3">Ngày đặt</th>
                            <th class="py-3">Tổng tiền</th>
                            <th class="py-3">Trạng thái</th>
                            <th class="py-3 text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php $st = (int)$row['status']; ?>
                                <tr>
                                    <!-- MÃ ĐƠN -->
                                    <td class="ps-4 fw-bold text-primary">
                                        #<?php echo $row['order_id']; ?>
                                    </td>

                                    <!-- KHÁCH HÀNG -->
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?php echo htmlspecialchars($row['fullname']); ?>
                                        </div>
                                        <small class="text-muted d-block">
                                            <i class="fa-solid fa-phone me-1"></i>
                                            <?php echo htmlspecialchars($row['phone']); ?>
                                        </small>
                                    </td>

                                    <!-- NGÀY ĐẶT -->
                                    <td class="text-muted small">
                                        <i class="fa-regular fa-calendar me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($row['order_date'])); ?><br>
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?php echo date('H:i', strtotime($row['order_date'])); ?>
                                    </td>

                                    <!-- TỔNG TIỀN -->
                                    <td class="text-danger fw-bold">
                                        <?php echo number_format($row['final_money'], 0, ',', '.'); ?> ₫
                                    </td>

                                    <!-- TRẠNG THÁI -->
                                    <td>
                                        <?php
                                        if ($st == 1) {
                                            echo '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                                        <i class="fa-solid fa-hourglass-half me-1"></i> Chờ duyệt
                                                      </span>';
                                        } elseif ($st == 2) {
                                            echo '<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                        <i class="fa-solid fa-truck me-1"></i> Đang giao
                                                      </span>';
                                        } elseif ($st == 3) {
                                            echo '<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                        <i class="fa-solid fa-circle-check me-1"></i> Hoàn thành
                                                      </span>';
                                        } else {
                                            echo '<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                                        <i class="fa-solid fa-circle-xmark me-1"></i> Đã hủy
                                                      </span>';
                                        }
                                        ?>
                                    </td>

                                    <!-- ACTIONS -->
                                    <td class="text-end pe-4">
                                        <!-- Chi tiết -->
                                        <a href="detail.php?id=<?php echo $row['order_id']; ?>"
                                            class="btn btn-sm btn-outline-info me-1"
                                            title="Xem chi tiết">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </a>

                                        <?php if ($st == 1): ?>
                                            <!-- Duyệt đơn -->
                                            <a href="order_index.php?id=<?php echo $row['order_id']; ?>&status=2"
                                                class="btn btn-sm btn-primary px-2"
                                                title="Duyệt đơn (chuyển sang Đang giao)">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                            <!-- Hủy đơn -->
                                            <a href="order_index.php?id=<?php echo $row['order_id']; ?>&status=4"
                                                class="btn btn-sm btn-outline-danger px-2"
                                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')"
                                                title="Hủy đơn">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                        <?php elseif ($st == 2): ?>
                                            <!-- Xác nhận hoàn thành -->
                                            <a href="order_index.php?id=<?php echo $row['order_id']; ?>&status=3"
                                                class="btn btn-sm btn-success px-2"
                                                title="Xác nhận đã giao xong">
                                                <i class="fa-solid fa-box-open me-1"></i> Xong
                                            </a>
                                        <?php endif; ?>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-inbox fa-2x mb-3"></i><br>
                                    Không tìm thấy đơn hàng nào phù hợp với bộ lọc.
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