<?php
// profile.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

// Chưa đăng nhập → đá về login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// --- CẬP NHẬT THÔNG TIN HỒ SƠ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone    = $conn->real_escape_string($_POST['phone']);
    $address  = $conn->real_escape_string($_POST['address']);
    $password = $_POST['password'];

    // Xử lý mật khẩu (nếu nhập)
    $sql_pass = "";
    if (!empty($password)) {
        $sql_pass = ", password='$password'";
    }

    // Xử lý avatar
    $sql_avatar = "";
    if (!empty($_FILES['avatar']['name'])) {
        $img_name   = time() . "_" . basename($_FILES['avatar']['name']);
        $target_dir = "assets/img/avatars/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $img_name;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            $sql_avatar = ", avatar='$img_name'";
        } else {
            $msg = "<div class='alert alert-warning mb-3 small'>Không thể tải ảnh lên, vui lòng thử lại.</div>";
        }
    }

    $sql = "UPDATE users SET fullname='$fullname', phone='$phone', address='$address' $sql_pass $sql_avatar WHERE user_id=$user_id";

    if ($conn->query($sql)) {
        $_SESSION['fullname'] = $fullname;
        $msg = "<div class='alert alert-success alert-dismissible fade show mb-3 small'>
                    <i class='fa-solid fa-check-circle me-2'></i> Cập nhật hồ sơ thành công!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    } else {
        $msg = "<div class='alert alert-danger mb-3 small'>Lỗi: " . $conn->error . "</div>";
    }
}

// --- HỦY ĐƠN HÀNG ---
if (isset($_GET['cancel_order'])) {
    $order_id = intval($_GET['cancel_order']);
    $check    = $conn->query("SELECT status FROM orders WHERE order_id=$order_id AND user_id=$user_id");
    if ($check->num_rows > 0) {
        $st = $check->fetch_assoc()['status'];
        if ($st == 1) { // chỉ cho hủy khi Chờ duyệt
            $conn->query("UPDATE orders SET status=4 WHERE order_id=$order_id");
            header("Location: profile.php");
            exit();
        } else {
            echo "<script>alert('❌ Đơn hàng này không thể hủy!'); window.location.href='profile.php';</script>";
        }
    }
}

// Lấy thông tin user & đơn hàng
$user   = $conn->query("SELECT * FROM users WHERE user_id=$user_id")->fetch_assoc();
$orders = $conn->query("SELECT * FROM orders WHERE user_id=$user_id ORDER BY order_date DESC");

$avatar_url = (!empty($user['avatar']) && file_exists("assets/img/avatars/" . $user['avatar']))
    ? "assets/img/avatars/" . $user['avatar']
    : "";

include 'includes/header.php';
?>

<style>
    .profile-bg {
        background: #7a8590ff;
        border-radius: 1%;
        border: #6b7280;
        /* hoặc #ffffff nếu bạn muốn trắng hoàn toàn */
    }

    .profile-card {
        border-radius: 22px;
        border: none;
    }

    .profile-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
    }

    .profile-input {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        padding: 8px 10px;
        background: #f9fafb;
    }

    .profile-input:focus {
        background: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 1px rgba(37, 99, 235, .2);
    }

    .profile-btn-save {
        border-radius: 999px;
        padding: 9px 18px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 10px 30px rgba(37, 99, 235, .35);
    }

    .profile-btn-save:hover {
        opacity: .96;
    }

    .order-status-badge {
        font-size: 11px;
        padding: 6px 12px;
        border-radius: 999px;
    }

    .table-orders tbody tr:hover {
        background: #f9fafb;
    }
</style>

<div class="profile-bg">
    <div class="container py-5">

        <!-- Hero nhỏ -->
        <div class="mb-4 text-white">
            <h3 class="fw-bold mb-1">
                <i class="fa-solid fa-user-circle me-2"></i> Hồ sơ của bạn
            </h3>
            <p class="mb-0 text-white-50 small">
                Quản lý thông tin cá nhân và xem lịch sử đơn hàng tại LaptopShop.
            </p>
        </div>

        <div class="row g-4">
            <!-- Cột HỒ SƠ -->
            <div class="col-lg-4">
                <div class="card profile-card shadow-lg">
                    <div class="card-body p-4">
                        <form action="" method="POST" enctype="multipart/form-data">

                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <?php if ($avatar_url): ?>
                                        <img src="<?php echo $avatar_url; ?>"
                                            class="rounded-circle shadow-sm object-fit-cover"
                                            style="width: 100px; height: 100px;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                                            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <label for="avatarInput"
                                        class="position-absolute bottom-0 end-0 bg-white border rounded-circle shadow-sm p-1"
                                        style="cursor:pointer;">
                                        <i class="fa-solid fa-camera text-primary p-1 small"></i>
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                </div>

                                <h5 class="fw-bold mt-3 mb-1"><?php echo htmlspecialchars($user['fullname']); ?></h5>
                                <p class="text-muted small mb-0">
                                    <i class="fa-solid fa-envelope me-1"></i> <?php echo htmlspecialchars($user['email']); ?>
                                </p>
                            </div>

                            <?php echo $msg; ?>

                            <div class="mb-3">
                                <label class="profile-label">Họ và tên</label>
                                <input type="text" name="fullname" class="form-control profile-input"
                                    value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="profile-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control profile-input"
                                    value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="profile-label">Địa chỉ nhận hàng</label>
                                <textarea name="address" rows="2" class="form-control profile-input"><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="profile-label">Đổi mật khẩu mới</label>
                                <input type="password" name="password" class="form-control profile-input" placeholder="Bỏ trống nếu không đổi">
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="profile-btn-save">
                                    <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cột LỊCH SỬ ĐƠN HÀNG -->
            <div class="col-lg-8">
                <div class="card profile-card shadow-lg h-100">
                    <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>
                                    Lịch sử đơn hàng
                                </h5>
                                <p class="text-muted small mb-0">
                                    Xem trạng thái đơn hàng và chi tiết các lần mua trước đây.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <?php if ($orders->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-orders">
                                    <thead class="bg-light small text-muted text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3">Mã ĐH</th>
                                            <th>Ngày đặt</th>
                                            <th>Thanh toán</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th class="text-end pe-4">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($ord = $orders->fetch_assoc()):
                                            $st = $ord['status'];
                                            if ($st == 1) {
                                                $st_text  = 'Chờ duyệt';
                                                $st_class = 'bg-secondary bg-opacity-10 text-secondary';
                                            } elseif ($st == 2) {
                                                $st_text  = 'Đang giao';
                                                $st_class = 'bg-primary bg-opacity-10 text-primary';
                                            } elseif ($st == 3) {
                                                $st_text  = 'Hoàn thành';
                                                $st_class = 'bg-success bg-opacity-10 text-success';
                                            } else {
                                                $st_text  = 'Đã hủy';
                                                $st_class = 'bg-danger bg-opacity-10 text-danger';
                                            }
                                        ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-primary">#<?php echo $ord['order_id']; ?></td>
                                                <td class="text-muted small">
                                                    <?php echo date('H:i d/m/Y', strtotime($ord['order_date'])); ?>
                                                </td>
                                                <td class="small">
                                                    <?php if ($ord['payment_method'] == 'COD'): ?>
                                                        <span class="badge bg-light text-dark border">COD</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info bg-opacity-10 text-info">Chuyển khoản</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-danger fw-bold">
                                                    <?php echo number_format($ord['final_money'], 0, ',', '.'); ?> ₫
                                                </td>
                                                <td>
                                                    <span class="order-status-badge <?php echo $st_class; ?>">
                                                        <i class="fa-solid fa-circle me-1" style="font-size:7px;"></i>
                                                        <?php echo $st_text; ?>
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="order_tracking.php?order_id=<?php echo $ord['order_id']; ?>&phone=<?php echo urlencode($user['phone']); ?>"
                                                            class="btn btn-outline-secondary rounded-pill px-3">
                                                            <i class="fa-regular fa-eye me-1"></i> Chi tiết
                                                        </a>
                                                        <?php if ($st == 1): ?>
                                                            <a href="profile.php?cancel_order=<?php echo $ord['order_id']; ?>"
                                                                class="btn btn-outline-danger rounded-pill px-3"
                                                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                                                                Hủy
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fa-solid fa-box-open fa-3x text-muted mb-3 opacity-50"></i>
                                <p class="text-muted mb-2">Bạn chưa có đơn hàng nào.</p>
                                <a href="index_backup.php" class="btn btn-primary rounded-pill px-4">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Mua sắm ngay
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // tìm img trong khối avatar
                var wrapper = input.parentElement;
                var img = wrapper.querySelector('img');
                var div = wrapper.querySelector('div.rounded-circle');

                if (img) {
                    img.src = e.target.result;
                } else if (div) {
                    var newImg = document.createElement('img');
                    newImg.src = e.target.result;
                    newImg.className = "rounded-circle shadow-sm object-fit-cover";
                    newImg.style.width = "100px";
                    newImg.style.height = "100px";
                    div.parentNode.replaceChild(newImg, div);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>