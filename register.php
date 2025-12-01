<?php
// register.php
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //  Kiểm tra mật khẩu nhập lại
    if ($password != $confirm_password) {
        $error = "Mật khẩu nhập lại không khớp!";
    } // Kiểm tra độ dài mật khẩu
    elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải ít nhất 6 kí tự bất kì";
    } 
    else {
        //  Kiểm tra email đã tồn tại chưa
        $check = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $error = "Email này đã được sử dụng, vui lòng chọn email khác!";
        } 
        else {
            //  Thêm người dùng mới (Role mặc định là 0 - Khách hàng)
            $sql = "INSERT INTO users (fullname, email, phone, password, role, status) 
                        VALUES ('$fullname', '$email', '$phone', '$password', 0, 1)";

            if ($conn->query($sql)) {
                $success = "Đăng ký thành công! Đang chuyển hướng...";
                echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
            } 
            else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}

// Nhúng Header (để có menu)
include 'includes/header.php';
?>

<style>
    .page-auth-bg {
        background: radial-gradient(circle at top, #e0f2fe 0, #f9fafb 45%, #f9fafb 100%);
    }

    .auth-wrapper {
        max-width: 1100px;
        margin: 0 auto;
    }

    .auth-card {
        border-radius: 24px;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
        background: #ffffff;
    }

    .auth-left {
        background: radial-gradient(circle at top left, #38bdf833, transparent 55%),
            radial-gradient(circle at bottom right, #22c55e33, transparent 60%),
            #0f172a;
        color: #e5e7eb;
        padding: 36px 32px;
        height: 100%;
    }

    .auth-right {
        padding: 36px 32px;
        background: #ffffff;
    }

    .auth-brand {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: .06em;
    }

    .auth-brand i {
        color: #22c55e;
    }

    .auth-left h3 {
        font-weight: 700;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .auth-left p {
        font-size: 14px;
        color: #9ca3af;
    }

    .auth-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.85);
        font-size: 12px;
        color: #e5e7eb;
        margin-top: 8px;
    }

    .auth-badge i {
        color: #22c55e;
    }

    .auth-feature-list {
        list-style: none;
        padding-left: 0;
        margin-top: 18px;
        font-size: 13px;
    }

    .auth-feature-list li {
        margin-bottom: 6px;
        color: #9ca3af;
    }

    .auth-feature-list i {
        color: #22c55e;
        margin-right: 6px;
    }

    .auth-title {
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .auth-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .form-control {
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
    }

    .btn-auth {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 0;
        font-size: 15px;
    }

    .small-link {
        font-size: 13px;
    }

    .input-group-text {
        border-radius: 10px 0 0 10px;
    }

    .password-toggle {
        cursor: pointer;
        border-radius: 0 10px 10px 0 !important;
    }

    @media (max-width: 768px) {
        .auth-left {
            display: none;
        }

        .auth-right {
            padding: 26px 20px;
        }

        .auth-card {
            border-radius: 18px;
        }
    }
</style>

<div class="page-auth-bg">
    <div class="container py-5">
        <div class="auth-wrapper">
            <div class="card auth-card">
                <div class="row g-0">
                    <!-- BÊN TRÁI: GIỚI THIỆU -->
                    <div class="col-md-5 d-none d-md-block">
                        <div class="auth-left d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fa-solid fa-laptop-code fa-lg me-2"></i>
                                    <span class="auth-brand text-uppercase">LaptopShop</span>
                                </div>

                                <div class="auth-badge mb-2">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>Đăng ký thành viên mới</span>
                                </div>

                                <h3>Tạo tài khoản trong vài bước ✨</h3>
                                <p>
                                    Trở thành thành viên của LaptopShop để nhận nhiều ưu đãi,
                                    xem lại lịch sử đơn hàng và bảo hành dễ dàng hơn.
                                </p>

                                <ul class="auth-feature-list">
                                    <li><i class="fa-solid fa-check"></i> Lưu sẵn thông tin giao hàng cho các lần sau</li>
                                    <li><i class="fa-solid fa-check"></i> Theo dõi chi tiết từng đơn hàng</li>
                                    <li><i class="fa-solid fa-check"></i> Được tư vấn cấu hình phù hợp miễn phí</li>
                                </ul>
                            </div>

                            <div class="mt-4 small" style="color:#9ca3af;">

                            </div>
                        </div>
                    </div>

                    <!-- BÊN PHẢI: FORM ĐĂNG KÝ -->
                    <div class="col-md-7">
                        <div class="auth-right">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h4 class="auth-title">Đăng ký tài khoản</h4>
                                    <p class="auth-subtitle mb-0">
                                        Điền đầy đủ thông tin bên dưới để tạo tài khoản mới.
                                    </p>
                                </div>
                                <a href="index_backup.php" class="small-link text-decoration-none text-secondary d-none d-md-inline">
                                    <i class="fa-solid fa-arrow-left-long me-1"></i> Về trang chủ
                                </a>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger text-center py-2 mb-3 small">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success text-center py-2 mb-3 small">
                                    <i class="fa-solid fa-circle-check me-1"></i> <?php echo $success; ?>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <input
                                        type="text"
                                        name="fullname"
                                        class="form-control"
                                        placeholder=""
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder=""
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        placeholder=""
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            class="form-control border-start-0"
                                            placeholder="Ít nhất 6 ký tự"
                                            required>
                                        <span class="input-group-text bg-light border-start-0 password-toggle" onclick="togglePassword('password','icon1')">
                                            <i class="fa-regular fa-eye" id="icon1"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Nhập lại mật khẩu</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fa-solid fa-key"></i>
                                        </span>
                                        <input
                                            type="password"
                                            id="confirm_password"
                                            name="confirm_password"
                                            class="form-control border-start-0"
                                            placeholder="Nhập lại mật khẩu"
                                            minlength="6"
                                            required>
                                        <span class="input-group-text bg-light border-start-0 password-toggle" onclick="togglePassword('confirm_password','icon2')">
                                            <i class="fa-regular fa-eye" id="icon2"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-auth">
                                        <i class="fa-solid fa-user-plus me-2"></i> Đăng ký ngay
                                    </button>
                                </div>

                                <div class="text-center small">
                                    <span>Đã có tài khoản?</span>
                                    <a href="login.php" class="text-decoration-none fw-semibold">Đăng nhập tại đây</a>
                                </div>

                                <div class="text-center mt-3 small d-md-none">
                                    <a href="index_backup.php" class="text-secondary text-decoration-none">
                                        &larr; Quay về trang chủ
                                    </a>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>