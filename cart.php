<?php
session_start();

// Check for both old and new session structures
$isLoggedIn = isset($_SESSION['phone']) || isset($_SESSION['user_id']);
$userPhone = isset($_SESSION['phone']) ? $_SESSION['phone'] : 
             (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'client' ? $_SESSION['user_id'] : null);

if (!$isLoggedIn) {
    header("Location: index.html");
    exit;
}

require_once 'config/db.php';

try {
    // Fetch cart items with medicine details
    $stmt = $conn->prepare("
        SELECT c.*, m.medication_name, m.unit_price, m.image_path 
        FROM cart c
        JOIN medicine m ON c.ndc = m.ndc
        WHERE c.client_phone = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$userPhone]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total items count for header display
    $cartCount = count($cartItems);
    
    // Get user name if available
    $userName = $_SESSION['name'] ?? $userPhone;
    
    // Calculate totals for passing to payment form
    $subtotal = array_reduce($cartItems, function($sum, $item) {
        return $sum + ($item['unit_price'] * $item['quantity']);
    }, 0);
    $shipping = 5.00;
    $tax = $subtotal * 0.07;
    $total = $subtotal + $shipping + $tax;
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - PharmaCare</title>
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
          /* padding: 10px 0; */
          position: sticky;
          top: 0;
          z-index: 100;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
          line-height: normal;
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
          /* padding: 10px 0; */
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
          /* margin-bottom: 20px; */
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
          margin-top: 15px;
          font-size: 16px;
          font-weight: bold;
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

        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .item-details {
            flex-grow: 1;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-controls button {
            padding: 5px 10px;
            background: #0a4275;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .quantity-controls span {
            padding: 0 10px;
            font-weight: bold;
        }
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #56d799;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .login-btn {
          background-color: #56d799;
          color: #0a4275;
          padding: 8px 16px;
          border-radius: 5px;
          text-decoration: none;
          font-weight: 600;
        }

        .login-btn:hover {
          background-color: #4cc290;
        }
        
        /* Modal Payment Form Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 200;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 600px;
            max-width: 90%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.4s;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            margin-right: 15px;
            margin-top: 10px;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        /* Payment Form Styles */
        .form-container {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 25px;
        }
        
        .form-container h2 {
            color: #2c3e50;
            margin-top: 0;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        .form-group input:focus, 
        .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
        
        .card-row {
            display: flex;
            gap: 15px;
        }
        
        .card-number {
            flex: 2;
        }
        
        .card-expiry, 
        .card-cvv {
            flex: 1;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .success-message {
            display: none;
            text-align: center;
            color: #27ae60;
            font-weight: bold;
            margin-top: 20px;
        }
        
        /* Body when modal is open */
        body.modal-open {
            overflow: hidden;
        }

        
    #contact {
      background-color: #0a427510;
      padding: 40px 20px;
      text-align: center;
    }

    #contact h3 {
      color: #0a4275;
      font-size: 24px;
      margin-bottom: 10px;
    }

    #contact a {
      color: #0a4275;
      text-decoration: none;
    }

    #contact a:hover {
      color: #56d799;
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
          <li><a href="#contact">Contact</a></li>
          <li><a href="cart.php" class="active">Cart (<?php echo $cartCount; ?>)</a></li>
          <li>
            <a href="handlers/logout.php" class="login-btn">Logout</a>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container">
    <div class="cart-container">
        <h1>Your Cart</h1>
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="product.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($item['medication_name']); ?>">
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['medication_name']); ?></h3>
                        <p>Price: $<?php echo number_format($item['unit_price'], 2); ?></p>
                        <div class="quantity-controls">
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease')">-</button>
                            <span><?php echo $item['quantity']; ?></span>
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase')">+</button>
                        </div>
                    </div>
                    <button onclick="removeItem(<?php echo $item['id']; ?>)" class="remove-btn">Remove</button>
                </div>
            <?php endforeach; ?>
            
            <div class="cart-summary">
                <h2>Order Summary</h2>
                <div class="summary-item">
                    <span>Subtotal (<?php echo $cartCount; ?> items)</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span>$<?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Tax</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <hr style="margin: 10px 0;">
                <div class="summary-item total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <button onclick="openCheckout()" class="checkout-btn">Proceed to Checkout</button>
                <a href="product.php" class="continue-shopping" style="display:block; text-align:center;">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
  </main>

  <!-- Payment Modal -->
  <div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="form-container">
            <h2>Payment Details</h2>
            <form id="paymentForm">
                <div class="form-group">
                    <label for="name">Cardholder Name</label>
                    <input type="text" id="name" placeholder="John Smith" required>
                    <div class="error" id="nameError">Please enter a valid name</div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="john@example.com" required>
                    <div class="error" id="emailError">Please enter a valid email address</div>
                </div>
                
                <div class="form-group">
                    <label for="amount">Payment Amount ($)</label>
                    <input type="number" id="amount" value="<?php echo number_format($total, 2); ?>" readonly>
                    <div class="error" id="amountError">Please enter a valid amount</div>
                </div>
                
                <div class="form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    <div class="error" id="cardNumberError">Please enter a valid card number</div>
                </div>
                
                <div class="form-group card-row">
                    <div class="card-expiry">
                        <label for="expiry">Expiry Date</label>
                        <input type="text" id="expiry" placeholder="MM/YY" maxlength="5" required>
                        <div class="error" id="expiryError">Invalid expiry date</div>
                    </div>
                    
                    <div class="card-cvv">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" placeholder="123" maxlength="4" required>
                        <div class="error" id="cvvError">Invalid CVV</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="paymentMethod">Payment Method</label>
                    <select id="paymentMethod" required>
                        <option value="" disabled selected>Select payment method</option>
                        <option value="visa">Visa</option>
                        <option value="mastercard">MasterCard</option>
                        <option value="amex">American Express</option>
                        <option value="discover">Discover</option>
                    </select>
                    <div class="error" id="paymentMethodError">Please select a payment method</div>
                </div>
                
                <button type="submit" id="submitBtn">Process Payment</button>
            </form>
            
            <div class="success-message" id="successMessage">
                Payment processed successfully!
            </div>
        </div>
    </div>
  </div>
  <section id="contact">
    <h3>Contact Us</h3>
    <p>Email: <a href="mailto:support@pharmacare.com">support@pharmacare.com</a> | Phone: <a href="tel:+12345678901">+1 234 567 890</a></p>
    <p>123 Wellness Ave, Healthy City, HC 12345</p>
  </section>

  <script>
    // Cart functions
    function updateQuantity(cartId, action) {
        fetch('handlers/update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_id: cartId,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error updating cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the cart');
        });
    }

    function removeItem(cartId) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch('handlers/remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_id: cartId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error removing item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item');
            });
        }
    }
    
    // Modal functions
    const modal = document.getElementById('paymentModal');
    
    function openCheckout() {
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
    }
    
    function closeModal() {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
    
    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }
    
    // Payment form handling
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('paymentForm');
        const successMessage = document.getElementById('successMessage');
        const cardNumberInput = document.getElementById('cardNumber');
        const expiryInput = document.getElementById('expiry');
        
        // Format card number with spaces
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            e.target.value = formattedValue;
        });
        
        // Format expiry date with slash
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/gi, '');
            
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            e.target.value = value;
        });
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            let isValid = true;
            
            // Validate name
            const name = document.getElementById('name');
            const nameError = document.getElementById('nameError');
            if (name.value.trim().length < 3) {
                nameError.style.display = 'block';
                isValid = false;
            } else {
                nameError.style.display = 'none';
            }
            
            // Validate email
            const email = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }
            
            // Validate amount
            const amount = document.getElementById('amount');
            const amountError = document.getElementById('amountError');
            if (parseFloat(amount.value) <= 0) {
                amountError.style.display = 'block';
                isValid = false;
            } else {
                amountError.style.display = 'none';
            }
            
            // Validate card number (using Luhn algorithm)
            const cardNumber = document.getElementById('cardNumber');
            const cardNumberError = document.getElementById('cardNumberError');
            const cardVal = cardNumber.value.replace(/\s/g, '');
            
            if (cardVal.length < 13 || cardVal.length > 19 || !luhnCheck(cardVal)) {
                cardNumberError.style.display = 'block';
                isValid = false;
            } else {
                cardNumberError.style.display = 'none';
            }
            
            // Validate expiry date
            const expiry = document.getElementById('expiry');
            const expiryError = document.getElementById('expiryError');
            const expiryParts = expiry.value.split('/');
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear() % 100; // Get last two digits
            const currentMonth = currentDate.getMonth() + 1; // January is 0
            
            if (expiryParts.length !== 2 || 
                !expiryParts[0] || !expiryParts[1] || 
                expiryParts[0] < 1 || expiryParts[0] > 12 ||
                (expiryParts[1] < currentYear || 
                (expiryParts[1] == currentYear && expiryParts[0] < currentMonth))) {
                expiryError.style.display = 'block';
                isValid = false;
            } else {
                expiryError.style.display = 'none';
            }
            
            // Validate CVV
            const cvv = document.getElementById('cvv');
            const cvvError = document.getElementById('cvvError');
            const cvvRegex = /^[0-9]{3,4}$/;
            if (!cvvRegex.test(cvv.value)) {
                cvvError.style.display = 'block';
                isValid = false;
            } else {
                cvvError.style.display = 'none';
            }
            
            // Validate payment method
            const paymentMethod = document.getElementById('paymentMethod');
            const paymentMethodError = document.getElementById('paymentMethodError');
            if (paymentMethod.value === '') {
                paymentMethodError.style.display = 'block';
                isValid = false;
            } else {
                paymentMethodError.style.display = 'none';
            }
            
            // If everything is valid, show success message
            if (isValid) {
                form.style.display = 'none';
                successMessage.style.display = 'block';
                
                // In a real application, you would send the form data to your server here
                console.log('Payment details submitted:', {
                    name: name.value,
                    email: email.value,
                    amount: amount.value,
                    cardNumber: cardVal,
                    expiry: expiry.value,
                    paymentMethod: paymentMethod.value
                });
                
                // After 3 seconds, redirect to a thank you page
                setTimeout(function() {
                    window.location.href = 'clientHomepage.php?order=success';
                }, 3000);
            }
        });
        
        // Luhn algorithm for validating credit card numbers
        function luhnCheck(cardNumber) {
            if (!cardNumber) return false;
            
            let nCheck = 0;
            let bEven = false;
            
            for (let n = cardNumber.length - 1; n >= 0; n--) {
                let cDigit = cardNumber.charAt(n);
                let nDigit = parseInt(cDigit, 10);
                
                if (bEven && (nDigit *= 2) > 9) {
                    nDigit -= 9;
                }
                
                nCheck += nDigit;
                bEven = !bEven;
            }
            
            return (nCheck % 10) === 0;
        }
    });
  </script>
</body>
</html>