<?php
require_once '../../../config/db.php';
include '../../includes/header.php';

// Lấy danh sách Khách hàng & Sản phẩm
$users = $conn->query("SELECT * FROM users WHERE role = 0");
$products = $conn->query("SELECT * FROM product WHERE stock > 0 ORDER BY product_id DESC");

// --- XỬ LÝ LƯU ĐƠN HÀNG ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];

    // Mảng sản phẩm từ form
    $prod_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
    $qtys    = isset($_POST['qty'])        ? $_POST['qty']        : [];

    if (empty($prod_ids)) {
        echo "<script>alert('❌ Vui lòng chọn ít nhất 1 sản phẩm!');</script>";
    } else {
        
        // [SỬA LỖI TẠI ĐÂY] Xử lý user_id: Nếu rỗng (Khách lẻ) thì gán là NULL
        if (empty($user_id)) {
            $sql_user_id = "NULL"; // Lưu giá trị NULL vào SQL
        } else {
            $sql_user_id = "'$user_id'"; // Lưu ID user vào SQL (có dấu nháy)
        }

        // 1. Tạo đơn hàng (Lưu ý biến $sql_user_id không cần bao quanh bởi dấu nháy nữa vì đã xử lý ở trên)
        $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, note, status, payment_method, order_date) 
                      VALUES ($sql_user_id, '$fullname', '$phone', '$address', '$note', 1, 'CASH', NOW())";

        if ($conn->query($sql_order)) {
            $order_id    = $conn->insert_id;
            $total_money = 0;

            // 2. Insert chi tiết
            for ($i = 0; $i < count($prod_ids); $i++) {
                $p_id = intval($prod_ids[$i]);
                $num  = intval($qtys[$i]);
                if ($num <= 0) continue;

                $p_info = $conn->query("SELECT price FROM product WHERE product_id = $p_id")->fetch_assoc();
                $price  = $p_info['price'];
                $total  = $price * $num;
                $total_money += $total;

                $conn->query("INSERT INTO order_details (order_id, product_id, price, num, total_price) 
                              VALUES ($order_id, $p_id, $price, $num, $total)");

                $conn->query("UPDATE product SET stock = stock - $num WHERE product_id = $p_id");
            }

            // 3. Update tổng tiền
            $conn->query("UPDATE orders SET total_money = $total_money, final_money = $total_money WHERE order_id = $order_id");

            echo "<script>alert(' Tạo đơn hàng thành công! Mã đơn: #$order_id'); location.href='order_index.php';</script>";
        } else {
            echo "<script>alert(' Lỗi tạo đơn: " . $conn->error . "');</script>";
        }
    }
}
?>

<link rel="stylesheet" href="/QL_BanMayTinh/assets/css/admin.css">

<style>
    .order-wrapper { background: #f3f4f6; border-radius: 12px; padding: 16px 18px 20px; margin-top: 10px; }
    .order-header-title { font-size: 22px; font-weight: 700; }
    .order-header-sub { font-size: 13px; color: #6b7280; }
    .order-step-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 10px; padding: 4px 10px; border-radius: 999px; letter-spacing: .08em; text-transform: uppercase; font-weight: 700; }
    .order-step-badge i { font-size: 11px; }
    .order-card-header { border-bottom: 1px solid #e5e7eb; }
    .order-card-header h6 { font-size: 14px; }
    .order-table thead { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; }
    .order-table td { vertical-align: middle; }
    .order-empty-row { font-size: 13px; }
    .order-submit-bar { border-top: 1px solid #e5e7eb; }
    .order-submit-btn { border-radius: 999px; font-weight: 700; }
    .order-qty-input { width: 70px; margin: 0 auto; font-size: 13px; }
    #productModal .modal-header { border-bottom: 1px solid #e5e7eb; }
    #productModal .modal-title { font-size: 16px; font-weight: 700; }
    #searchProductInput { font-size: 13px; }
</style>

<div class="container-fluid order-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="order-header-title text-dark mb-1">Tạo Đơn Hàng Mới</div>
            <div class="order-header-sub">Nhập thông tin khách hàng và chọn sản phẩm để xuất kho trực tiếp từ hệ thống LaptopShop.</div>
        </div>
        <a href="index.php" class="btn btn-light border fw-semibold shadow-sm"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách</a>
    </div>

    <form method="POST" id="orderForm" onsubmit="return validateOrder()">
        <div class="row g-3">

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white order-card-header py-3">
                        <div class="d-flex flex-column">
                            <span class="order-step-badge bg-primary bg-opacity-10 text-primary mb-1"><i class="fa-solid fa-user"></i> Bước 1 · Khách hàng</span>
                            <h6 class="mb-0 fw-bold text-dark">Thông tin người mua / người nhận</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Chọn khách đã đăng ký</label>
                            <select name="user_id" class="form-select" onchange="fillCustomerInfo(this)">
                                <option value="">Khách lẻ</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['user_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($u['fullname']); ?>"
                                        data-phone="<?php echo htmlspecialchars($u['phone']); ?>"
                                        data-address="<?php echo htmlspecialchars($u['address']); ?>">
                                        <?php echo htmlspecialchars($u['fullname']); ?> (<?php echo htmlspecialchars($u['phone']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <hr class="border-light">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Họ tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" id="fullname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ghi chú đơn hàng</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white order-card-header py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <span class="order-step-badge bg-success bg-opacity-10 text-success mb-1"><i class="fa-solid fa-box-open"></i> Bước 2 · Sản phẩm</span>
                            <h6 class="mb-0 fw-bold text-dark">Chọn sản phẩm để xuất kho</h6>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="fa-solid fa-magnifying-glass-plus me-2"></i> Tìm & chọn sản phẩm
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle order-table">
                                <thead class="bg-light small text-muted">
                                    <tr>
                                        <th style="width: 50%;">Sản phẩm</th>
                                        <th style="width: 20%; text-align: center;">Giá bán</th>
                                        <th style="width: 15%; text-align: center;">Số lượng</th>
                                        <th style="width: 15%; text-align: center;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItems">
                                    <tr id="emptyRow">
                                        <td colspan="4" class="text-center py-4 text-muted fst-italic order-empty-row">
                                            <i class="fa-solid fa-cart-arrow-down me-2"></i>
                                            Chưa có sản phẩm nào. Bấm nút <b>“Tìm & chọn sản phẩm”</b> để thêm vào đơn.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white order-submit-bar d-flex justify-content-between align-items-center py-3">
                        <div class="text-muted small"></div>
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow-sm order-submit-btn">
                            <i class="fa-solid fa-check-circle me-2"></i> TẠO ĐƠN HÀNG
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-laptop me-2"></i> Kho sản phẩm LaptopShop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3 sticky-top" style="top: -1rem; z-index: 10;">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="searchProductInput" class="form-control border-start-0 ps-0" placeholder="Gõ tên laptop để tìm nhanh..." onkeyup="filterProducts()">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="productTable">
                        <thead class="table-dark small">
                            <tr>
                                <th style="width: 60px;">Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Kho</th>
                                <th class="text-end">Chọn</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $products->fetch_assoc()): ?>
                                <tr class="product-row">
                                    <td><img src="../../../assets/img/<?php echo $p['main_image']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"></td>
                                    <td class="fw-bold text-dark product-name"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td class="text-danger fw-bold"><?php echo number_format($p['price']); ?> ₫</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?php echo $p['stock']; ?></span></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="selectProduct(<?php echo $p['product_id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, <?php echo $p['stock']; ?>)">
                                            <i class="fa-solid fa-plus"></i> Chọn
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. HÀM QUAN TRỌNG NHẤT: Kiểm tra đơn hàng rỗng
    function validateOrder() {
        var emptyRow = document.getElementById('emptyRow');
        // Nếu dòng "Chưa có sản phẩm" vẫn còn -> Nghĩa là chưa chọn gì
        if (emptyRow) {
            alert("HÃY CHỌN ÍT NHẤT MỘT SẢN PHẨM.   ");
            return false; // Chặn không cho gửi form, giữ nguyên trang
        }
        return true; // Cho phép gửi
    }

    // 2. Điền thông tin khách hàng tự động
    function fillCustomerInfo(select) {
        var option = select.options[select.selectedIndex];
        if (select.value !== "") {
            document.getElementById('fullname').value = option.getAttribute('data-name') || '';
            document.getElementById('phone').value = option.getAttribute('data-phone') || '';
            document.getElementById('address').value = option.getAttribute('data-address') || '';
        } else {
            document.getElementById('fullname').value = "";
            document.getElementById('phone').value = "";
            document.getElementById('address').value = "";
        }
    }

    // 3. Tìm kiếm sản phẩm trong Modal
    function filterProducts() {
        var input = document.getElementById("searchProductInput");
        var filter = input.value.toUpperCase();
        var table = document.getElementById("productTable");
        var tr = table.getElementsByTagName("tr");
        for (var i = 1; i < tr.length; i++) {
            var td = tr[i].getElementsByClassName("product-name")[0];
            if (td) {
                var txtValue = td.textContent || td.innerText;
                tr[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
            }
        }
    }

    // 4. Chọn sản phẩm đưa vào bảng chính
    function selectProduct(id, name, price, maxStock) {
        var tbody = document.getElementById('orderItems');
        var emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        var existingRow = document.getElementById('row_' + id);
        if (existingRow) {
            var inputQty = existingRow.querySelector('input[name="qty[]"]');
            var newQty = parseInt(inputQty.value) + 1;
            if (newQty <= maxStock) {
                inputQty.value = newQty;
            } else {
                alert("Đã đạt giới hạn tồn kho!");
            }
        } else {
            var newRow = document.createElement('tr');
            newRow.id = 'row_' + id;
            newRow.innerHTML = `
                <td>
                    <div class="fw-bold text-dark">${name}</div>
                    <input type="hidden" name="product_id[]" value="${id}">
                </td>
                <td class="text-center text-muted">${new Intl.NumberFormat().format(price)} ₫</td>
                <td class="text-center">
                    <input type="number" name="qty[]" class="form-control form-control-sm text-center order-qty-input" value="1" min="1" max="${maxStock}" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(this)">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
        }
    }

    // 5. Xóa dòng
    function removeRow(btn) {
        btn.closest('tr').remove();
        var tbody = document.getElementById('orderItems');
        if (tbody.rows.length === 0) {
            var empty = document.createElement('tr');
            empty.id = 'emptyRow';
            empty.innerHTML = `
                <td colspan="4" class="text-center py-4 text-muted fst-italic order-empty-row">
                    <i class="fa-solid fa-cart-arrow-down me-2"></i>
                    Chưa có sản phẩm nào. Bấm nút <b>“Tìm & chọn sản phẩm”</b> để thêm vào đơn.
                </td>`;
            tbody.appendChild(empty);
        }
    }
</script>

<?php include '../../includes/footer.php'; ?>