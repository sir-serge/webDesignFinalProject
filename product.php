<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in - UPDATED CHECK
// We need to check for both old and new session structures
$isLoggedIn = isset($_SESSION['phone']) || isset($_SESSION['user_id']);
$userPhone = isset($_SESSION['phone']) ? $_SESSION['phone'] : 
             (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'client' ? $_SESSION['user_id'] : null);

// Debug session (uncomment to check session variables)
// echo "<pre>";
// var_dump($_SESSION);
// echo "</pre>";

if ($isLoggedIn) {
    error_log("User logged in with ID: " . $userPhone);
} else {
    error_log("No user session found");
    // You might want to redirect here if you want to force login
    // header("Location: index.html");
    // exit;
}

require_once 'config/db.php';

function getMedicinesByCategory() {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT medication_name, category, unit_price, image_path, ndc 
            FROM medicine 
            WHERE status = 'available'
            ORDER BY category, medication_name
        ");
        $stmt->execute();
        $medicines = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = $row['category'];
            if (!isset($medicines[$category])) {
                $medicines[$category] = [];
            }
            $medicines[$category][] = $row;
        }
        return $medicines;
    } catch (PDOException $e) {
        error_log("Error fetching medicines: " . $e->getMessage());
        return [];
    }
}

// Get prices from database
$medicinePrices = getMedicinesByCategory();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PharmaCare - Product Catalog</title>
  <style>
    * {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: #f5f7fa;
      color: #333;
    }

    header {
      background-color: #0a4275;
      color: white;
      padding: 15px 20px;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    header h1 {
      font-size: 24px;
      font-weight: bold;
    }

    header nav {
      display: flex;
      gap: 20px;
      align-items: center;
    }

    header nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
    }
        nav ul li a.active {
          color: #56d799;
          border-bottom: 2px solid #56d799;
        }
    header nav a:hover {
      color: #56d799;
    }

    .nav-container {
      max-width: 1100px;
      margin: auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
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

    .search-section {
      background-color: white;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .search-container {
      max-width: 1100px;
      margin: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: center;
      justify-content: space-between;
    }

    .search-container input, .search-container select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      flex: 1;
      min-width: 200px;
    }

    .search-container input:focus, .search-container select:focus {
      border-color: #0a4275;
      outline: none;
    }

    main {
      max-width: 1100px;
      margin: 40px auto;
      padding: 0 20px;
    }

    h2 {
      font-size: 28px;
      color: #0a4275;
      margin-bottom: 20px;
    }

    section.category {
      margin-bottom: 50px;
    }

    section.category h3 {
      font-size: 22px;
      color: #0a4275;
      margin-bottom: 20px;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
    }

    .product-card {
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: box-shadow 0.3s;
    }

    .product-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .product-card .details {
      padding: 15px;
    }

    .product-card h3 {
      font-size: 16px;
      margin-bottom: 10px;
    }

    .product-card .price {
      color: #0a4275;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .product-card button {
      background-color: #0a4275;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 5px;
      cursor: pointer;
    }

    .product-card button:hover {
      background-color: #08355d;
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

    footer {
      background-color: #0a4275;
      color: white;
      text-align: center;
      padding: 20px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <header>
    <div class="nav-container">
      <h1>💊 Pharma<span style="color:#56d799;">Care</span></h1>
      <nav>
        <a href="clientHomepage.php">Home</a>
        <a href="product.php" class="active">Products</a>
        <a href="#contact">Contact</a>
        <a href="cart.php">Cart</a>
        <?php if ($isLoggedIn): ?>
            <a href="handlers/logout.php" class="login-btn">Logout</a>
        <?php else: ?>
            <a href="index.html" class="login-btn">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Search & Filter -->
  <section class="search-section">
    <div class="search-container">
      <input type="text" id="search-input" placeholder="Search for medicines...">
      <select id="category-filter">
        <option value="">All Categories</option>
        <option>Men</option>
        <option>Women</option>
        <option>Children</option>
        <option>Allergy & Cold</option>
        <option>Pain Relief</option>
        <option>Vitamins & Supplements</option>
        <option>Skin Care</option>
        <option>Digestive Health</option>
        <option>Heart Health</option>
        <option>Diabetes Care</option>
      </select>
    </div>
  </section>

  <!-- Products -->
  <main>
    <h2>Our Products</h2>
    <?php
    $medicines = getMedicinesByCategory();
    foreach ($medicines as $category => $products): ?>
        <section class="category">
            <h3><?php echo htmlspecialchars($category); ?></h3>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['medication_name']); ?>">
                        <div class="details">
                            <h3><?php echo htmlspecialchars($product['medication_name']); ?></h3>
                            <p class="price">$<?php echo number_format($product['unit_price'], 2); ?></p>
                            <button onclick="addToCart('<?php echo htmlspecialchars($product['ndc']); ?>', 
                                                     '<?php echo htmlspecialchars($product['medication_name']); ?>', 
                                                     '<?php echo htmlspecialchars($product['unit_price']); ?>')" 
                                    class="add-cart-btn">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
  </main>

  <!-- Contact Section -->
  <section id="contact">
    <h3>Contact Us</h3>
    <p>Email: <a href="mailto:support@pharmacare.com">support@pharmacare.com</a> | Phone: <a href="tel:+12345678901">+1 234 567 890</a></p>
    <p>123 Wellness Ave, Healthy City, HC 12345</p>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 PharmaCare. All rights reserved.</p>
  </footer>

  <script>
    // Store session data as JavaScript variables
    const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    const clientPhone = <?php echo $userPhone ? '"'.$userPhone.'"' : 'null'; ?>;
    
    // For debugging
    console.log("Login status:", isLoggedIn);
    console.log("Client phone:", clientPhone);
    
    // Add to cart function
    function addToCart(ndc, medicationName, price) {
        // Check if user is logged in
        if (!isLoggedIn || !clientPhone) {
            alert("Please log in to add items to your cart");
            window.location.href = "index.html";
            return;
        }
        
        // Make AJAX request to add item to cart
        fetch("handlers/add_to_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `ndc=${encodeURIComponent(ndc)}&phone=${encodeURIComponent(clientPhone)}&quantity=1`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert("Item added to cart successfully!");
            } else {
                alert(data.message || "Failed to add item to cart. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while adding the item to cart");
        });
    }
    
    // Optional: Add search and filter functionality
    document.getElementById('search-input').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterProducts(searchTerm);
    });
    
    document.getElementById('category-filter').addEventListener('change', function() {
        const selectedCategory = this.value;
        filterCategories(selectedCategory);
    });
    
    function filterProducts(searchTerm) {
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            const productName = card.querySelector('h3').textContent.toLowerCase();
            if (productName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    function filterCategories(category) {
        const categorySections = document.querySelectorAll('section.category');
        
        if (category === '') {
            categorySections.forEach(section => {
                section.style.display = 'block';
            });
            return;
        }
        
        categorySections.forEach(section => {
            const sectionTitle = section.querySelector('h3').textContent;
            if (sectionTitle === category) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
  </script>
</body>
</html>