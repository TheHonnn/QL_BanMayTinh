<?php
session_start();
require_once 'config/db.php';

// Nếu đã đăng nhập rồi thì chuyển hướng luôn, không cần nhập lại
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 1) {
        header("Location: admin/index_dashboard.php");
    } else {
        header("Location: index_backup.php");
    }
    exit();
}

$error = '';

// XỬ LÝ KHI BẤM NÚT ĐĂNG NHẬP
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // (Bài tập lớn: chưa mã hoá để đơn giản)

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['status'] == 0) {
            $error = "Tài khoản của bạn đã bị khóa!";
        } else {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['fullname']  = $user['fullname'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['email']     = $user['email'];

            if ($user['role'] == 1) {
                header("Location: admin/index_dashboard.php");
            } else {
                header("Location: index_backup.php");
            }
            exit();
        }
    } else {
        $error = "Email hoặc Mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - LaptopShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
            background: radial-gradient(circle at top, #1e293b 0, #020617 45%, #020617 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #111827;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            padding: 16px;
        }

        .login-card {
            border-radius: 24px;
            border: none;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.6);
            background: #0b1120;
        }

        .login-left {
            background: radial-gradient(circle at top left, #22c55e22, transparent 60%),
                        radial-gradient(circle at bottom right, #38bdf822, transparent 55%),
                        #020617;
            color: #e5e7eb;
            padding: 40px 36px;
        }

        .login-right {
            background: #ffffff;
            padding: 40px 36px;
        }

        .brand-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: .06em;
        }

        .brand-title i {
            color: #22c55e;
        }

        .login-left h2 {
            font-weight: 700;
            margin-top: 24px;
            margin-bottom: 12px;
        }

        .login-left p {
            font-size: 14px;
            color: #9ca3af;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: #111827;
            color: #e5e7eb;
            font-size: 12px;
            gap: 6px;
        }

        .login-badge i {
            color: #22c55e;
        }

        .login-feature-list {
            list-style: none;
            padding-left: 0;
            margin-top: 18px;
            font-size: 13px;
        }

        .login-feature-list li {
            margin-bottom: 6px;
            color: #9ca3af;
        }

        .login-feature-list i {
            color: #22c55e;
            margin-right: 6px;
        }

        .login-title {
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .login-subtitle {
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

        .btn-login {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 0;
            font-size: 15px;
        }

        .small-link {
            font-size: 13px;
        }

        .badge-demo {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            margin-top: 8px;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
        }

        .password-toggle {
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .login-left {
                display: none;
            }

            .login-right {
                padding: 28px 22px;
            }

            .login-card {
                border-radius: 18px;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="card login-card">
        <div class="row g-0">
            <!-- BÊN TRÁI: INTRO / BRAND -->
            <div class="col-md-5 login-left d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-laptop-code fa-lg me-2"></i>
                        <span class="brand-title text-uppercase">LaptopShop</span>
                    </div>

                    <div class="login-badge mb-3">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Cổng đăng nhập bảo mật</span>
                    </div>

                    <h2>Chào mừng bạn quay lại 👋</h2>
                    <p>
                        Đăng nhập để tiếp tục mua sắm, quản lý đơn hàng và trải nghiệm những ưu đãi dành riêng cho bạn.
                    </p>

                    <ul class="login-feature-list">
                        <li><i class="fa-solid fa-check"></i> Theo dõi trạng thái đơn hàng theo thời gian thực</li>
                        <li><i class="fa-solid fa-check"></i> Lưu lịch sử mua hàng & bảo hành</li>
                        <li><i class="fa-solid fa-check"></i> Ưu đãi đặc biệt cho thành viên thân thiết</li>
                    </ul>
                </div>

                <div class="mt-4 small text-gray-400">
                    <span class="text-gray-400" style="color:#9ca3af;">
                      2025
                    </span>
                </div>
            </div>

            <!-- BÊN PHẢI: FORM ĐĂNG NHẬP -->
            <div class="col-md-7 login-right">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="login-title">Đăng nhập tài khoản</h4>
                        <p class="login-subtitle mb-0">Nhập thông tin để truy cập hệ thống LaptopShop.</p>
                    </div>
                    <a href="index_backup.php" class="small-link text-decoration-none text-secondary d-none d-md-inline">
                        <i class="fa-solid fa-arrow-left-long me-1"></i> Về trang chủ
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger text-center py-2 mb-3 small">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Tên tài khoản"
                            required
                        >
                    </div>

                    <div class="mb-2">
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
                                placeholder="Nhập mật khẩu"
                                required
                            >
                            <span class="input-group-text bg-light border-start-0 password-toggle" onclick="togglePassword()">
                                <i class="fa-regular fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                            <label class="form-check-label" for="rememberMe">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>
                        <span class="text-muted fst-italic">Quên mật khẩu? </span>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng nhập
                        </button>
                    </div>

                    <div class="text-center small">
                        <span>Chưa có tài khoản?</span>
                        <a href="register.php" class="text-decoration-none fw-semibold">Đăng ký ngay</a>
                    </div>

                    <div class="text-center mt-3 small">
                        <a href="index_backup.php" class="text-secondary text-decoration-none">
                            &larr; Quay về trang chủ
                        </a>
                    </div>

                    <div class="text-center">
                        <span class="badge-demo">
                           admin@gmail.com  123456
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggleIcon');
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

</body>
</html>
