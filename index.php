<?php
// index.php
// Start session for CSRF token
session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bicycle Pre-Booking</title>
  <style>
    :root { --maxw: 860px; }
    *{box-sizing:border-box}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;margin:0;background:#f7f7fb;color:#222}
    header{background:#101827;color:#fff;padding:24px}
    header h1{margin:0;font-size:1.4rem}
    main{max-width:var(--maxw);margin:24px auto;padding:16px}
    form{background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.06);padding:20px}
    fieldset{border:0;margin:0 0 18px;padding:0}
    legend{font-weight:700;margin-bottom:10px}
    label{display:block;font-size:.92rem;margin:6px 0}
    input[type="text"], input[type="email"], input[type="tel"], input[type="number"], select, textarea{
      width:100%;padding:12px;border:1px solid #d1d5db;border-radius:10px;background:#fff
    }
    textarea{min-height:90px;resize:vertical}
    .row{display:grid;grid-template-columns:1fr;gap:12px}
    @media (min-width:720px){ .row{grid-template-columns:1fr 1fr} }
    .help{font-size:.82rem;color:#6b7280}
    .cta{display:flex;gap:12px;align-items:center;margin-top:10px}
    button{cursor:pointer;border:0;background:#0ea5e9;color:#fff;padding:12px 18px;border-radius:12px;font-weight:700}
    button:hover{filter:brightness(.95)}
    .note{font-size:.9rem;color:#374151}
    .hidden-hp{position:absolute;left:-5000px;top:auto;width:1px;height:1px;overflow:hidden}
    .pill{display:inline-block;background:#eef2ff;color:#3730a3;padding:4px 10px;border-radius:999px;font-size:.78rem;margin-left:6px}
    .req{color:#ef4444;margin-left:3px}
  </style>
</head>
<body>
  <header>
    <h1>Pre-Book Your Bicycle <span class="pill">Advance only</span></h1>
  </header>

  <main>
    <form method="POST" action="create_order.php" autocomplete="on" novalidate onsubmit="return validateAmount();">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />
      <!-- basic honeypot -->
      <div class="hidden-hp" aria-hidden="true">
        <label>Leave this field empty
          <input type="text" name="website" tabindex="-1" autocomplete="off" />
        </label>
      </div>

      <fieldset>
        <legend>Customer Details</legend>
        <div class="row">
          <label>Full Name <span class="req">*</span>
            <input type="text" name="full_name" required maxlength="80" />
          </label>
          <label>Email <span class="req">*</span>
            <input type="email" name="email" required maxlength="120" />
          </label>
        </div>
        <div class="row">
          <label>Phone <span class="req">*</span>
            <input type="tel" name="phone" required pattern="[0-9]{10}" placeholder="10-digit number" />
          </label>
          <label>City
            <input type="text" name="city" maxlength="60" />
          </label>
        </div>
        <label>Address
          <textarea name="address" maxlength="240" placeholder="House/Street, Area, Landmark"></textarea>
        </label>
        <div class="row">
          <label>State
            <input type="text" name="state" maxlength="60" />
          </label>
          <label>Pincode
            <input type="text" name="pincode" maxlength="10" />
          </label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Choose Your Bicycle</legend>
        <div class="row">
          <label>Model <span class="req">*</span>
            <select name="model" required>
              <option value="" disabled selected>Select a model</option>
              <option value="CityCommuter 700C">CityCommuter 700C</option>
              <option value="TrailBlazer 29">TrailBlazer 29</option>
              <option value="RoadPro 105">RoadPro 105</option>
              <option value="KidsFun 20">KidsFun 20</option>
            </select>
          </label>
          <label>Variant
            <select name="variant">
              <option value="" selected>Standard</option>
              <option value="Disc Brakes">Disc Brakes</option>
              <option value="Alloy Frame">Alloy Frame</option>
              <option value="Suspension Fork">Suspension Fork</option>
            </select>
          </label>
        </div>
        <div class="row">
          <label>Color
            <select name="color">
              <option value="Black">Black</option>
              <option value="Red">Red</option>
              <option value="Blue">Blue</option>
              <option value="Silver">Silver</option>
            </select>
          </label>
          <label>Accessories (optional)
            <select name="accessories[]" multiple size="4">
              <option value="Helmet">Helmet</option>
              <option value="Lock">Lock</option>
              <option value="Bottle Cage">Bottle Cage</option>
              <option value="Front Light">Front Light</option>
              <option value="Rear Light">Rear Light</option>
              <option value="Mudguards">Mudguards</option>
            </select>
            <div class="help">Hold Ctrl/⌘ to select multiple.</div>
          </label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Pre-Booking Payment</legend>
        <label>Advance Amount (INR) <span class="req">*</span>
          <input type="number" name="amount" id="amount" required min="99" step="1" placeholder="e.g., 499" />
        </label>
        <div class="help">You’ll pay just the advance now. Balance at delivery.</div>
      </fieldset>

      <div class="cta">
        <button type="submit">Continue to Payment</button>
        <span class="note">On the next page we’ll create your order and take you to payment.</span>
      </div>
    </form>
  </main>

  <script>
    function validateAmount(){
      var amt = document.getElementById('amount').value;
      if(!amt || isNaN(amt) || Number(amt) < 99){
        alert('Please enter a valid advance amount (₹99 or more).');
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
