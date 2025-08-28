<?php
session_start();

// Razorpay credentials
$keyId = "YOUR_RAZORPAY_KEY_ID";
$keySecret = "YOUR_RAZORPAY_KEY_SECRET";

// Ensure session has order details
if (!isset($_SESSION['order_number']) || !isset($_POST['razorpay_payment_id'])) {
    die("Invalid access.");
}

$orderNumber = $_SESSION['order_number'];
$model       = $_SESSION['model'];
$name        = $_SESSION['name'];

// Razorpay response
$payment_id   = $_POST['razorpay_payment_id'];
$order_id     = $_POST['razorpay_order_id'];
$signature    = $_POST['razorpay_signature'];

// Step 1: Verify signature
$generated_signature = hash_hmac('sha256', $order_id . "|" . $payment_id, $keySecret);

if ($generated_signature !== $signature) {
    die("Payment verification failed.");
}

// Step 2: Store record in a text file
$timestamp = date("Y-m-d H:i:s");
$line = $orderNumber . " | " . $payment_id . " | " . $order_id . " | " . $model . " | " . $timestamp . PHP_EOL;

$file = __DIR__ . "/orders.txt";
file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

// Step 3: Clear session
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Booking Successful</title>
  <style>
    body {font-family: Arial, sans-serif; background:#f7f7f7; text-align:center; padding:50px;}
    .box {background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,.1); display:inline-block;}
    h2 {color:#2e7d32;}
    p {margin:10px 0;}
  </style>
</head>
<body>
  <div class="box">
    <h2>✅ Payment Successful!</h2>
    <p><strong>Order Number:</strong> <?php echo htmlspecialchars($orderNumber); ?></p>
    <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
    <p><strong>Bicycle Model:</strong> <?php echo htmlspecialchars($model); ?></p>
    <p>Thank you <?php echo htmlspecialchars($name); ?>, your bicycle has been pre-booked successfully.</p>
  </div>
</body>
</html>
