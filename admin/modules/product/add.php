<?php
require_once '../../../config/db.php'; 
include '../../includes/header.php'; 

// Xử lý Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = trim($_POST['name']);
    $price   = (int)$_POST['price'];
    $stock   = (int)$_POST['stock'];
    $cat_id  = (int)$_POST['category_id'];
    $sup_id  = (int)$_POST['supplier_id'];
    $desc    = trim($_POST['description']);
    $specs   = trim($_POST['specifications']); // [MỚI] Thông số kỹ thuật
    
    // Xử lý ảnh
    $img = $_FILES['image']['name'] ?? '';
    if (!empty($img)) {
        $target = "../../../assets/img/" . basename($img);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    } else {
        $img = "no-image.png"; // Ảnh mặc định nếu không chọn
    }

    // [MỚI] Thêm cột specifications vào câu lệnh SQL
    $sql = "INSERT INTO product 
                (name, price, stock, category_id, supplier_id, description, specifications, main_image) 
            VALUES 
                ('$name', $price, $stock, $cat_id, $sup_id, '$desc', '$specs', '$img')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Thêm sản phẩm thành công!'); location.href='index_product.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
}

// Lấy danh mục & NCC
$cats = $conn->query("SELECT * FROM category");
$sups = $conn->query("SELECT * FROM suppliers");
?>

<style>
    .product-create-page {
        background: #f3f4f6;
        min-height: calc(100vh - 80px);
    }

    .product-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        background: #ffffff;
        animation: pc-fadeup .35s ease-out;
    }

    @keyframes pc-fadeup {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .page-title-main {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.03em;
    }

    .page-title-sub {
        color: #6b7280;
        font-size: 13px;
    }

    .btn-back-soft {
        border-radius: 999px;
        padding: 8px 16px;
        font-size: 13px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back-soft:hover {
        background: #f3f4f6;
    }

    .section-box {
        border-radius: 14px;
        border: 1px dashed #e5e7eb;
        padding: 16px 18px 4px;
        margin-bottom: 18px;
        background: #f9fafb;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .section-icon {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 13px;
    }

    .section-title-text {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #374151;
    }

    .form-label span.text-danger {
        font-weight: 700;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        font-size: 14px;
        padding: 9px 12px;
        transition: all .2s;
    }

    .form-control:focus,
    .form-select:focus {
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,.25);
    }

    .input-group-text {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
        font-size: 13px;
        color: #6b7280;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 90px;
    }

    .upload-wrapper {
        border-radius: 14px;
        border: 1px dashed #d1d5db;
        padding: 16px;
        background: #f9fafb;
        transition: all .2s;
    }

    .upload-wrapper:hover {
        border-color: #4f46e5;
        background: #f3f4ff;
    }

    .upload-text-main {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .upload-text-sub {
        font-size: 12px;
        color: #6b7280;
    }

    .product-preview-img {
        width: 100%;
        max-height: 260px;
        object-fit: contain;
        border-radius: 12px;
        margin-top: 10px;
        display: none;
        box-shadow: 0 8px 24px rgba(15,23,42,.25);
        background: #ffffff;
    }

    .btn-cancel-soft {
        border-radius: 999px;
        padding: 8px 18px;
        font-size: 13px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
    }

    .btn-cancel-soft:hover {
        background: #f3f4f6;
    }

    .btn-save-primary {
        border-radius: 999px;
        padding: 9px 26px;
        font-size: 14px;
        border: none;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #ffffff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 10px 25px rgba(79,70,229,.45);
        transition: all .18s;
    }

    .btn-save-primary:hover {
        transform: translateY(-1px) scale(1.01);
        opacity: .96;
    }

    .badge-required {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .1em;
        border-radius: 999px;
        padding: 2px 7px;
        background: #fee2e2;
        color: #b91c1c;
        font-weight: 600;
    }
</style>
<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">

<div class="container-fluid product-create-page py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-1">
        <div>
            <div class="page-title-main">Thêm sản phẩm mới</div>
            <div class="page-title-sub mt-1">
                Nhập đầy đủ thông tin để sản phẩm hiển thị đẹp trên trang bán hàng.
            </div>
        </div>
        <a href="index_product.php" class="btn-back-soft">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="product-card p-4 p-md-5 mb-4">
                <form method="POST" enctype="multipart/form-data">
                    
                    <!-- 1. Thông tin cơ bản -->
                    <div class="section-box">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <div>
                                <div class="section-title-text">Thông tin cơ bản</div>
                                <div class="page-title-sub">Tên, giá và số lượng tồn kho của sản phẩm.</div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tên sản phẩm 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Macbook Air M1 2020..." required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    Giá bán (VNĐ) 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="price" class="form-control" placeholder="0" required>
                                    <span class="input-group-text">₫</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    Tồn kho 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="stock" class="form-control" value="10" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Phân loại & Nguồn gốc -->
                    <div class="section-box">
                        <div class="section-header">
                            <div class="section-icon" style="background:#ecfeff;color:#0891b2;">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <div class="section-title-text">Phân loại & Thương hiệu</div>
                                <div class="page-title-sub">Chọn danh mục và nhà cung cấp để dễ quản lý.</div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Danh mục sản phẩm</label>
                                <select name="category_id" class="form-select">
                                    <?php while($c = $cats->fetch_assoc()): ?>
                                        <option value="<?php echo $c['category_id']; ?>">
                                            <?php echo $c['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nhà cung cấp / Thương hiệu</label>
                                <select name="supplier_id" class="form-select">
                                    <?php while($s = $sups->fetch_assoc()): ?>
                                        <option value="<?php echo $s['supplier_id']; ?>">
                                            <?php echo $s['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Hình ảnh & Mô tả + Thông số kỹ thuật -->
                    <div class="section-box mb-3">
                        <div class="section-header">
                            <div class="section-icon" style="background:#fef3c7;color:#d97706;">
                                <i class="fa-solid fa-image"></i>
                            </div>
                            <div>
                                <div class="section-title-text">Hình ảnh & Chi tiết sản phẩm</div>
                                <div class="page-title-sub">Ảnh thiết bị.</div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">
                                    Ảnh đại diện 
                                    <span class="text-danger">*</span>
                                </label>

                                <label class="upload-wrapper w-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="section-icon" style="background:#eef2ff;color:#4f46e5;">
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                        </div>
                                        <div>
                                            <div class="upload-text-main">Chọn ảnh từ máy</div>
                                            <div class="upload-text-sub">
                                                Hỗ trợ JPG, PNG. Nên dùng ảnh ngang, rõ nét.
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" name="image" id="imageInput" class="form-control mt-2" accept="image/*" required>
                                </label>

                                <img id="previewImg" class="product-preview-img" alt="Xem trước ảnh">
                            </div>

                            <div class="col-md-7 mb-3">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Mô tả chi tiết (Giới thiệu)</label>
                                        <textarea name="description" class="form-control" rows="4" placeholder="Nhập cấu hình tổng quan, tính năng nổi bật, bảo hành."></textarea>
                                    </div>
                                    <div class="col-12 mb-1">
                                        <label class="form-label text-primary">
                                            Thông số kỹ thuật (Cấu hình chi tiết)
                                        </label>
                                        <textarea 
                                            name="specifications" 
                                            class="form-control" 
                                            rows="4"
                                            placeholder="- CPU: Core i5
- RAM: 8GB
- SSD: 512GB
- Màn hình: 15.6 inch..."></textarea>
                                        <div class="form-text small">
                                           Chỉ nhập thông số chi tiết.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <div class="text-muted small">
                            <span class="badge-required">Lưu ý</span>
                            &nbsp;Các trường có dấu <span class="text-danger">*</span> là bắt buộc.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="index_product.php" class="btn-cancel-soft">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn-save-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu sản phẩm
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview ảnh sản phẩm
    const imageInput = document.getElementById('imageInput');
    const previewImg = document.getElementById('previewImg');

    if (imageInput && previewImg) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                previewImg.src = URL.createObjectURL(file);
                previewImg.style.display = 'block';
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
        });
    }
</script>

<?php include '../../includes/footer.php'; ?>
