<?php
// admin/modules/user/user_add.php (ví dụ)

require_once '../../../config/db.php';
include '../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    $role     = (int)$_POST['role'];
    $status   = (int)$_POST['status'];

    // Kiểm tra email trùng
    $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email đã tồn tại!');</script>";
    } else {
        $sql = "INSERT INTO users (fullname, email, password, phone, address, role, status)
                VALUES ('$fullname', '$email', '$password', '$phone', '$address', $role, $status)";

        if ($conn->query($sql)) {
            echo "<script>alert('Thêm thành công!'); location.href='user_index.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
}
?>

<!-- CSS trang thêm user (chỉ vài dòng nhỏ) -->
<style>
    .page-header-custom{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:1.5rem;
    }
    .page-header-custom h3{
        margin:0;
        font-weight:600;
    }
    .page-header-custom .subtext{
        font-size:0.9rem;
        color:#6c757d;
    }
    .badge-page{
        background:linear-gradient(135deg,#4e73df,#1cc88a);
        color:#fff;
        padding:0.4rem 0.75rem;
        border-radius:999px;
        font-size:0.8rem;
        font-weight:500;
    }
    .card-user{
        border-radius:1rem;
        overflow:hidden;
    }
    .card-user .card-header{
        background:linear-gradient(135deg,#4e73df,#224abe);
        color:#fff;
        border-bottom:none;
        padding:0.9rem 1.25rem;
    }
    .card-user .card-header h5{
        margin:0;
        font-size:1rem;
        font-weight:600;
        display:flex;
        align-items:center;
        gap:0.5rem;
    }
    .card-user .card-header small{
        font-size:0.8rem;
        opacity:0.9;
    }
    .btn-save{
        background:linear-gradient(135deg,#1cc88a,#17a673);
        border:none;
        font-weight:600;
        padding:0.45rem 1.4rem;
    }
    .btn-save:hover{
        filter:brightness(1.05);
    }
</style>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid py-3">

    <div class="page-header-custom">
        <div>
            <h3>Thêm người dùng mới</h3>
            <div class="subtext">Tạo tài khoản để quản lý đơn hàng và hệ thống tốt hơn</div>
        </div>
        <span class="badge-page">
            <i class="fa-solid fa-user-plus me-1"></i> Tạo tài khoản
        </span>
    </div>

    <div class="card shadow-sm border-0 card-user">
        <div class="card-header">
            <h5>
                <i class="fa-solid fa-id-card"></i>
                Thông tin tài khoản
                <small class="ms-2">Điền đầy đủ các trường bắt buộc (*)</small>
            </h5>
        </div>

        <div class="card-body">
            <form method="POST">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Họ và tên *</label>
                        <input type="text" name="fullname" class="form-control" required
                               placeholder="Họ và tên">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required
                               placeholder="Email của khách">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mật khẩu *</label>
                        <input type="password" name="password" class="form-control" required
                               minlength="6" autocomplete="new-password" placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control"
                               placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="0">Khách hàng</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="user_index.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-save text-white">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu người dùng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
