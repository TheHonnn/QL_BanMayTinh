<?php
require_once '../../../config/db.php'; 
include '../../includes/header.php'; 

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$row = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $id")->fetch_assoc();

// Nếu không tồn tại
if (!$row) {
    echo "<div class='alert alert-danger m-3'> Nhà cung cấp không tồn tại!</div>";
    include '../../includes/footer.php';
    exit();
}

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $email   = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE suppliers 
            SET name='$name', phone='$phone', email='$email', address='$address' 
            WHERE supplier_id=$id";
    
    if ($conn->query($sql)) {
        echo "<script>alert(' Cập nhật nhà cung cấp thành công!'); location.href='index_lier.php';</script>";
    } else {
        echo "<script>alert(' Lỗi: " . $conn->error . "');</script>";
    }
}
?>

<style>
    .sup-edit-page {
        background: #f3f4f6;
        min-height: calc(100vh - 80px);
    }

    .sup-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .sup-subtitle {
        font-size: 13px;
        color: #6b7280;
    }

    .sup-back-btn {
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

    .sup-back-btn:hover {
        background: #f9fafb;
    }

    .sup-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 16px 40px rgba(15,23,42,0.12);
        overflow: hidden;
    }

    .sup-card-header {
        padding: 18px 22px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fef9c3, #ffffff);
    }

    .sup-card-header-title {
        font-size: 16px;
        font-weight: 700;
        color: #92400e;
    }

    .sup-card-header-sub {
        font-size: 12px;
        color: #6b7280;
    }

    .sup-chip-edit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #fef3c7;
        color: #92400e;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .sup-form-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .sup-form-control,
    .sup-form-textarea {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        padding: 8px 11px;
    }

    .sup-form-control:focus,
    .sup-form-textarea:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 1px rgba(245,158,11,0.25);
    }

    .sup-required {
        color: #ef4444;
    }

    .sup-btn-update {
        border-radius: 999px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 10px 30px rgba(248,113,113,.35);
    }

    .sup-btn-update:hover {
        opacity: .97;
        transform: translateY(-1px);
    }

    .sup-note {
        font-size: 11px;
        color: #9ca3af;
    }
</style>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">
<div class="container-fluid sup-edit-page py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 mt-1">
        <div>
            <div class="sup-title">Cập nhật nhà cung cấp</div>
            <div class="sup-subtitle mt-1">
                Chỉnh sửa thông tin cho: <strong><?php echo htmlspecialchars($row['name']); ?></strong>
            </div>
        </div>
        <a href="index.php" class="sup-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="sup-card bg-white">
                <div class="sup-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="sup-card-header-title">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa thông tin NCC
                        </div>
                        <div class="sup-card-header-sub">
                            Hãy đảm bảo thông tin chính xác để tiện cho việc nhập hàng & liên hệ.
                        </div>
                    </div>
                    <div class="sup-chip-edit">
                        <i class="fa-solid fa-circle-info"></i>
                        Đang chỉnh sửa
                    </div>
                </div>

                <div class="card-body px-4 pb-4 pt-3">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="sup-form-label">
                                Tên nhà cung cấp <span class="sup-required">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control sup-form-control" 
                                   value="<?php echo htmlspecialchars($row['name']); ?>" 
                                   required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="sup-form-label">
                                    Số điện thoại <span class="sup-required">*</span>
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control sup-form-control" 
                                       value="<?php echo htmlspecialchars($row['phone']); ?>" 
                                       required>
                            </div>
                            <div class="col-6">
                                <label class="sup-form-label">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control sup-form-control" 
                                       value="<?php echo htmlspecialchars($row['email']); ?>">
                                <div class="sup-note mt-1">
                                    Nếu NCC không sử dụng email, có thể để trống.
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="sup-form-label">Địa chỉ kho / văn phòng</label>
                            <textarea name="address" 
                                      class="form-control sup-form-textarea" 
                                      rows="3"
                                      placeholder="Cập nhật địa chỉ mới nếu có thay đổi..."><?php echo htmlspecialchars($row['address']); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-2">
                            <a href="index_lier.php " class="btn btn-light border-0 text-sm px-3">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="sup-btn-update">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Cập nhật ngay
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
