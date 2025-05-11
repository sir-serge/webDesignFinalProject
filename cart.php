<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get user information from session
$userName = $_SESSION['name'] ?? 'Guest';
$userType = $_SESSION['user_type'] ?? '';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PharmaCare - Shopping Cart</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      line-height: 1.6;
      color: #333;
      background-color: #f5f7fa;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Header Styles */
    header {
      background-color: #0a4275;
      color: white;
      padding: 10px 0;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 20px;
      font-weight: bold;
      display: flex;
      align-items: center;
    }

    .logo span {
      color: #56d799;
      margin-left: 5px;
    }

    nav ul {
      display: flex;
      list-style: none;
    }

    nav ul li {
      margin-left: 20px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      padding: 10px 0;
      display: block;
    }

    nav ul li a:hover {
      color: #56d799;
    }

    nav ul li a.active {
      color: #56d799;
      border-bottom: 2px solid #56d799;
    }

    /* Cart Section */
    .cart-section {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      margin-top: 20px;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .cart-header h1 {
      font-size: 24px;
      color: #0a4275;
    }

    .cart-table {
      width: 100%;
      border-collapse: collapse;
    }

    .cart-table th,
    .cart-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .cart-table th {
      background-color: #f8f9fa;
      color: #666;
      font-weight: 500;
    }

    .cart-table td img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 4px;
    }

    .cart-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }

    .cart-actions .btn {
      padding: 10px 20px;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      border: none;
      transition: background-color 0.2s;
    }

    .btn-primary {
      background-color: #0a4275;
      color: white;
    }

    .btn-primary:hover {
      background-color: #0d5291;
    }

    .btn-secondary {
      background-color: #f0f7ff;
      color: #0a4275;
    }

    .btn-secondary:hover {
      background-color: #e0ecff;
    }

    .total {
      font-size: 18px;
      font-weight: bold;
      color: #0a4275;
    }

    /* Cart Item Styles */
    .cart-item {
      display: flex;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }

    .cart-item img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 15px;
    }

    .item-details {
      flex: 1;
    }

    .item-details h3 {
      font-size: 18px;
      margin-bottom: 5px;
    }

    .item-details .price {
      font-size: 16px;
      color: #0a4275;
      margin-bottom: 10px;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .qty-btn {
      background-color: #f0f7ff;
      color: #0a4275;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .qty-btn:hover {
      background-color: #e0ecff;
    }

    .remove-btn {
      background-color: #ffdddd;
      color: #d9534f;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .remove-btn:hover {
      background-color: #f2b2b2;
    }

    /* Cart Summary Styles */
    .cart-summary {
      margin-top: 20px;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .cart-summary h2 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #0a4275;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .summary-item.total {
      font-weight: bold;
      font-size: 18px;
      color: #0a4275;
    }

    .checkout-btn {
      background-color: #0a4275;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s;
      width: 100%;
    }

    .checkout-btn:hover {
      background-color: #0d5291;
    }

    /* Empty Cart Styles */
    .empty-cart {
      text-align: center;
      padding: 50px 0;
    }

    .empty-cart p {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .empty-cart .btn {
      background-color: #0a4275;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .empty-cart .btn:hover {
      background-color: #0d5291;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container header-content">
      <div class="logo">
        💊 Pharma<span>Care</span>
      </div>
      <nav>
        <ul>
          <li><a href="clientHomepage.php">Home</a></li>
          <li><a href="product.php">Products</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a href="cart.php" class="active">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
          <li>
            <div class="user-menu">
              <span><?php echo htmlspecialchars($userName); ?></span>
              <a href="logout.php">Logout</a>
            </div>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container">
    <h1>Your Shopping Cart</h1>
    
    <div class="cart-container">
      <?php if (empty($_SESSION['cart'])): ?>
          <div class="empty-cart">
              <p>Your cart is empty</p>
              <a href="product.php" class="btn">Continue Shopping</a>
          </div>
      <?php else: ?>
          <div class="cart-items">
              <?php foreach ($_SESSION['cart'] as $item): ?>
                  <div class="cart-item">
                      <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                      <div class="item-details">
                          <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                          <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                          <div class="quantity-controls">
                              <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')">-</button>
                              <input type="number" value="<?php echo $item['quantity']; ?>" min="1" 
                                     onchange="updateQuantity(<?php echo $item['id']; ?>, 'set', this.value)">
                              <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')">+</button>
                          </div>
                          <button class="remove-btn" onclick="removeItem(<?php echo $item['id']; ?>)">Remove</button>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
          
          <div class="cart-summary">
              <h2>Order Summary</h2>
              <div class="summary-item">
                  <span>Subtotal:</span>
                  <span>$<?php echo number_format(calculateSubtotal(), 2); ?></span>
              </div>
              <div class="summary-item">
                  <span>Shipping:</span>
                  <span>$5.00</span>
              </div>
              <div class="summary-item total">
                  <span>Total:</span>
                  <span>$<?php echo number_format(calculateSubtotal() + 5, 2); ?></span>
              </div>
              <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
          </div>
      <?php endif; ?>
    </div>
  </main>

  <script src="js/cart.js"></script>
</body>
</html>
<?php
function calculateSubtotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}
?>