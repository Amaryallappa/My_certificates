<?php
// create_order.php
session_start();

// Razorpay credentials
$keyId = "YOUR_RAZORPAY_KEY_ID";
$keySecret = "YOUR_RAZORPAY_KEY_SECRET";

// Basic validation
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['model'])) {
    die("Invalid request.");
}

// Generate unique order number
$orderNumber = "ORD" . time() . rand(1000, 9999);

// Amount is fixed 500 INR (in paisa for Razorpay = 50000)
$amount = 500 * 100; // in paisa

// Customer data
$name  = htmlspecialchars($_POST['full_name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$model = htmlspecialchars($_POST['model']);

// Create Razorpay Order using API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders");
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'amount' => $amount,
    'currency' => 'INR',
    'receipt' => $orderNumber,
    'payment_capture' => 1
]));
$response = curl_exec($ch);
if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}
curl_close($ch);

$orderData = json_decode($response, true);
if (!isset($orderData['id'])) {
    die("Razorpay Order creation failed. Response: " . $response);
}

// Store order info temporarily in session
$_SESSION['order_number'] = $orderNumber;
$_SESSION['model'] = $model;
$_SESSION['name'] = $name;

// Pass to Razorpay Checkout
?>
<!DOCTYPE html>
<html>
<head>
  <title>Checkout - Bicycle Prebooking</title>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
  <h2>Processing Pre-booking for <?php echo $model; ?></h2>
  <p>Order No: <?php echo $orderNumber; ?></p>
  <p>Advance Amount: ₹500</p>

  <button id="rzp-button">Pay with Razorpay</button>

  <form name="razorpayform" action="success.php" method="POST">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
  </form>

  <script>
    var options = {
        "key": "<?php echo $keyId; ?>",
        "amount": "<?php echo $amount; ?>",
        "currency": "INR",
        "name": "Bicycle Pre-booking",
        "description": "Advance Payment",
        "order_id": "<?php echo $orderData['id']; ?>",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.razorpayform.submit();
        },
        "prefill": {
            "name": "<?php echo $name; ?>",
            "email": "<?php echo $email; ?>",
            "contact": "<?php echo $phone; ?>"
        },
        "theme": {
            "color": "#3399cc"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
  </script>
</body>
</html>
