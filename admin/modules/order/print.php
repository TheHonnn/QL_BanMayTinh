<?php
// 1. Kết nối CSDL
require_once '../../../config/db.php'; 

// 2. Kiểm tra ID
if (!isset($_GET['id'])) { die("Không tìm thấy đơn hàng"); }
$order_id = intval($_GET['id']);

// 3. Lấy dữ liệu
$sql_order = "SELECT * FROM orders WHERE order_id = $order_id";
$order = $conn->query($sql_order)->fetch_assoc();

$sql_items = "SELECT d.*, p.name FROM order_details d JOIN product p ON d.product_id = p.product_id WHERE d.order_id = $order_id";
$items = $conn->query($sql_items);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn bán hàng #<?php echo $order_id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 1.5; color: #000; background: #fff; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        
        /* Header Hóa đơn */
        .invoice-header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px; }
        .shop-info h2 { margin: 0; text-transform: uppercase; font-size: 24px; font-weight: 700; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0; color: #333; font-size: 28px; text-transform: uppercase; }
        
        /* Thông tin khách hàng */
        .info-section { display: flex; margin-bottom: 30px; }
        .info-col { width: 50%; }
        .info-label { font-weight: bold; display: inline-block; width: 100px; }
        
        /* Bảng sản phẩm */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; text-transform: uppercase; font-size: 12px; font-weight: bold; text-align: center; }
        .col-num { text-align: center; }
        .col-money { text-align: right; }
        
        /* Tổng tiền */
        .total-section { text-align: right; margin-top: 20px; }
        .total-row { font-size: 16px; margin-bottom: 5px; }
        .final-total { font-size: 20px; font-weight: bold; color: #000; margin-top: 10px; border-top: 1px solid #000; display: inline-block; padding-top: 10px; }
        
        /* Chữ ký */
        .signature-section { display: flex; justify-content: space-between; margin-top: 50px; text-align: center; }
        .sig-box { width: 30%; }
        .sig-title { font-weight: bold; text-transform: uppercase; margin-bottom: 60px; }
        
        /* Nút in (sẽ ẩn khi in thật) */
        .print-btn { position: fixed; bottom: 20px; right: 20px; background: #2563eb; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .print-btn:hover { background: #1d4ed8; }
        
        @media print {
            .print-btn { display: none; }
            body { margin: 0; padding: 0; }
            .container { width: 100%; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="invoice-header">
        <div class="shop-info">
            <h2>LAPTOP SHOP</h2>
            <p>Đ/C: 123 Đường Cầu Giấy, Hà Nội</p>
            <p>Hotline: 0988.888.888</p>
            <p>Email: contact@laptopshop.com</p>
        </div>
        <div class="invoice-title">
            <h1>HÓA ĐƠN BÁN HÀNG</h1>
            <p>Mã đơn: <strong>#<?php echo $order_id; ?></strong></p>
            <p>Ngày: <?php echo date('d/m/Y', strtotime($order['order_date'])); ?></p>
        </div>
    </div>

    <div class="info-section">
        <div class="info-col">
            <p><span class="info-label">Khách hàng:</span> <?php echo $order['fullname']; ?></p>
            <p><span class="info-label">Điện thoại:</span> <?php echo $order['phone']; ?></p>
            <p><span class="info-label">Địa chỉ:</span> <?php echo $order['address']; ?></p>
        </div>
        <div class="info-col" style="text-align: right;">
            <p><span class="info-label">Thu ngân:</span> Admin</p>
            <p><span class="info-label">Hình thức:</span> <?php echo ($order['payment_method']=='COD')?'Tiền mặt':'Chuyển khoản'; ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">STT</th>
                <th>Tên sản phẩm</th>
                <th style="width: 100px;">Đơn giá</th>
                <th style="width: 80px;">SL</th>
                <th style="width: 120px;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1; 
            $total_qty = 0;
            while($item = $items->fetch_assoc()): 
                $total_qty += $item['num'];
            ?>
            <tr>
                <td class="col-num"><?php echo $i++; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td class="col-money"><?php echo number_format($item['price']); ?></td>
                <td class="col-num"><?php echo $item['num']; ?></td>
                <td class="col-money"><?php echo number_format($item['total_price']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">Tổng số lượng: <strong><?php echo $total_qty; ?></strong></div>
        <div class="total-row">Tạm tính: <?php echo number_format($order['final_money']); ?> đ</div>
        <div class="total-row">Phí vận chuyển: 0 đ</div>
        <div class="final-total">TỔNG CỘNG: <?php echo number_format($order['final_money']); ?> VNĐ</div>
        <p style="font-style: italic; font-size: 12px; margin-top: 5px;">(Bằng chữ: <?php echo "................................................................................"; ?>)</p>
    </div>

    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-title">Người mua hàng</div>
            <small>(Ký, ghi rõ họ tên)</small>
        </div>
        <div class="sig-box">
            <div class="sig-title">Người bán hàng</div>
            <small>(Ký, ghi rõ họ tên)</small>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 50px; font-style: italic;">
        Cảm ơn quý khách đã mua hàng tại Laptop Shop!
    </div>
</div>

<button onclick="window.print()" class="print-btn">🖨️ IN HÓA ĐƠN NGAY</button>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html> 