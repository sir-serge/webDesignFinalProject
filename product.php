<?php
session_start();

// Get user information from session if logged in
$userName = $_SESSION['name'] ?? 'Guest';
$isLoggedIn = isset($_SESSION['user_id']);

require_once 'config/db.php';
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
        <a href="clientHomepage.html">Home</a>
        <a href="#contact">Contact</a>
        <a href="cart.html">Cart</a>
        <a href="index.html" class="login-btn">Login</a>
      </nav>
    </div>
  </header>

  <!-- Search & Filter -->
  <section class="search-section">
    <div class="search-container">
      <input type="text" placeholder="Search for medicines...">
      <select>
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
    <script>
      const categories = {
        "Men": ["Men's Multivitamin", "Hair Growth Serum", "Energy Booster", "Beard Oil", "Protein Powder", "Men's Shampoo", "Testosterone Support", "Joint Support", "Men's Daily Pack", "Hair & Beard Combo"],
        "Women": ["Women's Wellness Pack", "Prenatal Vitamins", "Hair & Skin Capsules", "Iron Supplement", "Calcium Chews", "Multivitamin Gummies", "Collagen Booster", "Hormonal Balance", "Skin Brightening Cream", "Women's Daily Pack"],
        "Children": ["Children's Multivitamins", "Kids Cold Relief Syrup", "Vitamin C Gummies", "Cough Syrup", "Diaper Cream", "Allergy Tablets", "Kids Probiotic", "Children's Pain Relief", "Baby Shampoo", "Teething Gel"],
        "Allergy & Cold": ["Antihistamine Tablets", "Cold Relief Syrup", "Decongestant Spray", "Allergy Relief Capsules", "Nasal Drops", "Sinus Relief", "Hay Fever Tablets", "Allergy Nasal Spray", "Kids Allergy Syrup", "Allergy Eye Drops"],
        "Pain Relief": ["Ibuprofen", "Paracetamol", "Aspirin", "Pain Relief Gel", "Arthritis Cream", "Migraine Tablets", "Back Pain Patches", "Muscle Relaxant", "Headache Roll-on", "Knee Pain Balm"],
        "Vitamins & Supplements": ["Vitamin D3", "Vitamin C", "Vitamin B Complex", "Magnesium Supplement", "Zinc Tablets", "Fish Oil", "Calcium Tablets", "Probiotic Capsules", "Immune Booster", "Antioxidant Blend"],
        "Skin Care": ["Acne Cream", "Anti-Aging Serum", "Moisturizing Lotion", "Sunscreen SPF 50", "Dry Skin Cream", "Ointment for Eczema", "Face Wash", "Aloe Vera Gel", "Scar Remover", "Night Cream"],
        "Digestive Health": ["Probiotic Supplement", "Digestive Enzyme", "Antacid Tablets", "Gas Relief Capsules", "Bloating Relief", "Laxative", "IBS Support", "Fiber Gummies", "Stomach Soothing Tea", "Diarrhea Relief"],
        "Heart Health": ["Omega-3 Fish Oil", "Cholesterol Support", "Heart Multivitamin", "Blood Pressure Supplement", "CoQ10", "Aspirin (Low Dose)", "Cardio Formula", "Heartburn Relief", "Potassium Supplement", "Heart Tonic"],
        "Diabetes Care": ["Glucose Monitor", "Blood Sugar Strips", "Insulin Pen Needles", "Diabetic Multivitamin", "Foot Cream", "Low GI Snacks", "Diabetes Test Kit", "Sugar-Free Syrup", "Chromium Supplement", "Glucose Tablets"]
      };

      Object.entries(categories).forEach(([category, products]) => {
        document.write(`<section class="category"><h3>${category}</h3><div class="products-grid">`);
        products.forEach(product => {
          const price = (Math.random() * 20 + 5).toFixed(2);
          document.write(`
            <div class="product-card">
              <img src="images.jpeg" alt="${product}">
              <div class="details">
                <h3>${product}</h3>
                <p class="price">$${price}</p>
                <button>Add to Cart</button>
              </div>
            </div>
          `);
        });
        document.write(`</div></section>`);
      });
    </script>
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

</body>
</html>
