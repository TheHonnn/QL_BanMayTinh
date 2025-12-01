<?php
require_once '../../../config/db.php'; 
include '../../includes/header.php'; 

if (!isset($_GET['id'])) {
    header("Location: user_index.php");
    exit();
}
$id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE user_id = $id")->fetch_assoc();

if (!$user) {
    echo "<div class='alert alert-danger m-3'>Người dùng không tồn tại!</div>";
    include '../../includes/footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $new_pass = $_POST['password'];

    $sql_pass = "";
    if (!empty($new_pass)) {
        $sql_pass = ", password='$new_pass'";
    }

    $sql = "UPDATE users SET fullname='$fullname', phone='$phone', address='$address', role=$role, status=$status $sql_pass WHERE user_id=$id";
    
    if ($conn->query($sql)) {
        echo "<script>alert(' Cập nhật thành công!'); location.href='user_index.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
}
?>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h3 class="fw-bold text-dark mb-0">Cập nhật thông tin</h3>
        <a href="user_index.php" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST">
                <h6 class="text-uppercase text-muted fw-bold mb-3 small">Thông tin tài khoản</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email đăng nhập</label>
                        <input type="email" class="form-control bg-light" value="<?php echo $user['email']; ?>" disabled>
                        <div class="form-text text-muted small">Không thể thay đổi email.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary">Mật khẩu mới</label>
                        <input type="text" name="password" class="form-control" placeholder="Để trống nếu không muốn đổi">
                        <div class="form-text text-muted small">Chỉ nhập vào đây nếu muốn đổi mật khẩu cho user này.</div>
                    </div>
                </div>

                <h6 class="text-uppercase text-muted fw-bold mb-3 mt-3 small">Thông tin cá nhân</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo $user['fullname']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Địa chỉ liên hệ</label>
                        <input type="text" name="address" class="form-control" value="<?php echo $user['address']; ?>">
                    </div>
                </div>

                <h6 class="text-uppercase text-muted fw-bold mb-3 mt-3 small">Phân quyền & Trạng thái</h6>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Vai trò (Role)</label>
                        <select name="role" class="form-select">
                            <option value="0" <?php if($user['role']==0) echo 'selected'; ?>>Khách hàng</option>
                            <option value="1" <?php if($user['role']==1) echo 'selected'; ?>>Admin (Quản trị viên)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Trạng thái tài khoản</label>
                        <select name="status" class="form-select">
                            <option value="1" <?php if($user['status']==1) echo 'selected'; ?>>Hoạt động</option>
                            <option value="0" <?php if($user['status']==0) echo 'selected'; ?>>Khóa (Banned)</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <a href="user_index.php" class="btn btn-light border me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Cập nhật ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>