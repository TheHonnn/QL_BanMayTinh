<?php
require_once '../../../config/db.php';
include '../../includes/header.php';

// Lấy ID sản phẩm
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy dữ liệu sản phẩm
$res = $conn->query("SELECT * FROM product WHERE product_id = $id");
if (!$res || $res->num_rows == 0) {
    echo "<div class='alert alert-danger m-3'> Sản phẩm không tồn tại!</div>";
    include '../../includes/footer.php>';
    exit();
}
$p = $res->fetch_assoc();

// Lấy danh mục & nhà cung cấp
$cats = $conn->query("SELECT * FROM category");
$sups = $conn->query("SELECT * FROM suppliers");

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $conn->real_escape_string(trim($_POST['name']));
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $desc  = $conn->real_escape_string(trim($_POST['description']));

    $cat_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $sup_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $specs  = $conn->real_escape_string(trim($_POST['specifications'] ?? ''));

    $img_sql = "";
    // Nếu có chọn ảnh mới
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . '_' . basename($_FILES['image']['name']);
        $target   = "../../../assets/img/" . $img_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $img_sql = ", main_image='$img_name'";
            $p['main_image'] = $img_name; // Cập nhật lại để hiển thị preview
        }
    }

    // Cập nhật thêm category_id, supplier_id, specifications
    $sql = "UPDATE product 
            SET name='$name', 
                price=$price, 
                stock=$stock, 
                description='$desc',
                category_id=$cat_id,
                supplier_id=$sup_id,
                specifications='$specs'
                $img_sql 
            WHERE product_id=$id";

    if ($conn->query($sql)) {
        echo "<script>alert('Cập nhật sản phẩm thành công!'); location.href='index_product.php';</script>";
        exit();
    } else {
        echo "<script>alert(' Lỗi: " . addslashes($conn->error) . "');</script>";
    }

    // Cập nhật lại dữ liệu trên form
    $p['name']           = $name;
    $p['price']          = $price;
    $p['stock']          = $stock;
    $p['description']    = $desc;
    $p['category_id']    = $cat_id;
    $p['supplier_id']    = $sup_id;
    $p['specifications'] = $specs;
}
?>

<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css?v=1">

<style>
    .product-edit-wrapper {
        background: #f3f4f6;
        min-height: calc(100vh - 80px);
    }

    .pe-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .pe-subtitle {
        font-size: 13px;
        color: #6b7280;
    }

    .pe-back-btn {
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

    .pe-back-btn:hover {
        background: #f9fafb;
    }

    .pe-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }

    .pe-card-header {
        padding: 18px 22px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #e0f2fe, #ffffff);
    }

    .pe-card-header-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .pe-card-header-sub {
        font-size: 12px;
        color: #6b7280;
    }

    .pe-chip-id {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .pe-form-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .pe-required {
        color: #ef4444;
    }

    .pe-control,
    .pe-textarea {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        padding: 8px 11px;
    }

    .pe-control:focus,
    .pe-textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.25);
    }

    .pe-price-addon {
        border-radius: 0 10px 10px 0;
        border: 1px solid #e5e7eb;
        border-left: 0;
        background: #f9fafb;
        font-size: 13px;
        color: #6b7280;
        padding: 0 10px;
        display: flex;
        align-items: center;
    }

    .pe-preview-box {
        border-radius: 14px;
        border: 1px dashed #cbd5f5;
        background: #f9fafb;
        padding: 10px;
        text-align: center;
    }

    .pe-preview-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .18);
    }

    .pe-note {
        font-size: 11px;
        color: #9ca3af;
    }

    .pe-btn-save {
        border-radius: 999px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 10px 30px rgba(37, 99, 235, .35);
    }

    .pe-btn-save:hover {
        opacity: .97;
        transform: translateY(-1px);
    }
</style>

<div class="container-fluid product-edit-wrapper py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 mt-1">
        <div>
            <div class="pe-title">Chỉnh sửa sản phẩm</div>
            <div class="pe-subtitle mt-1">
                Đang sửa: <strong><?php echo htmlspecialchars($p['name']); ?></strong>
            </div>
        </div>
        <a href="index_product.php" class="pe-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Quay lại danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="pe-card bg-white">
                <div class="pe-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="pe-card-header-title">
                            <i class="fa-solid fa-laptop me-1"></i> Thông tin sản phẩm
                        </div>
                        <div class="pe-card-header-sub">
                            Cập nhật giá bán, tồn kho, danh mục, nhà cung cấp, mô tả, thông số kỹ thuật và hình ảnh.
                        </div>
                    </div>
                    <div class="pe-chip-id">
                        <i class="fa-solid fa-hashtag"></i> ID: <?php echo $p['product_id']; ?>
                    </div>
                </div>

                <div class="card-body px-4 pb-4 pt-3">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Hàng 1: Tên, giá, kho -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="pe-form-label">
                                    Tên sản phẩm <span class="pe-required">*</span>
                                </label>
                                <input type="text"
                                    name="name"
                                    class="form-control pe-control"
                                    value="<?php echo htmlspecialchars($p['name']); ?>"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="pe-form-label">
                                    Giá bán <span class="pe-required">*</span>
                                </label>
                                <div class="d-flex">
                                    <input type="number"
                                        name="price"
                                        class="form-control pe-control"
                                        value="<?php echo (int)$p['price']; ?>"
                                        required>
                                    <span class="pe-price-addon">₫</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="pe-form-label">
                                    Tồn kho <span class="pe-required">*</span>
                                </label>
                                <input type="number"
                                    name="stock"
                                    class="form-control pe-control"
                                    value="<?php echo (int)$p['stock']; ?>"
                                    required>
                            </div>
                        </div>

                        <!-- Hàng 1.5: Danh mục & Nhà cung cấp -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="pe-form-label">Danh mục</label>
                                <select name="category_id" class="form-select pe-control">
                                    <?php while ($c = $cats->fetch_assoc()): ?>
                                        <option value="<?php echo $c['category_id']; ?>"
                                            <?php if ((int)$c['category_id'] === (int)$p['category_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($c['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="pe-form-label">Nhà cung cấp</label>
                                <select name="supplier_id" class="form-select pe-control">
                                    <?php while ($s = $sups->fetch_assoc()): ?>
                                        <option value="<?php echo $s['supplier_id']; ?>"
                                            <?php if ((int)$s['supplier_id'] === (int)$p['supplier_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($s['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Hàng 2: Ảnh + Mô tả -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="pe-form-label">Ảnh hiển thị</label>
                                <div class="pe-preview-box mb-2">
                                    <img src="../../../assets/img/<?php echo htmlspecialchars($p['main_image']); ?>"
                                        alt="Ảnh sản phẩm"
                                        class="pe-preview-img mb-2">
                                    <div class="pe-note">
                                        Ảnh hiện tại trên website. Có thể chọn ảnh mới bên dưới.
                                    </div>
                                </div>
                                <input type="file"
                                    name="image"
                                    class="form-control pe-control"
                                    accept="image/*">
                                <div class="pe-note mt-1">
                                    Nên dùng ảnh ngang, rõ, không mờ.
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="pe-form-label">Mô tả chi tiết</label>
                                <textarea name="description"
                                    class="form-control pe-textarea"
                                    rows="6"
                                    placeholder="Thông tin giới thiệu, ưu điểm nổi bật..."><?php echo htmlspecialchars($p['description']); ?></textarea>
                                <div class="pe-note mt-1">
                                    Mô tả càng chi tiết thì khách càng dễ quyết định mua hàng.
                                </div>
                            </div>
                        </div>

                        <!-- Hàng 3: Thông số kỹ thuật -->
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="pe-form-label">Thông số kỹ thuật</label>
                                <textarea name="specifications"
                                    class="form-control pe-textarea"
                                    rows="6"
                                    placeholder="- CPU: ...
- RAM: ...
- Ổ cứng: ...
- Màn hình: ...
- Trọng lượng: ..."><?php echo htmlspecialchars($p['specifications'] ?? ''); ?></textarea>
                                <div class="pe-note mt-1">
                                    Nên nhập mỗi thông số trên 1 dòng để dễ đọc.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-2">
                            <a href="index_product.php" class="btn btn-light border-0 text-sm px-3">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="pe-btn-save">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Lưu thay đổi
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>