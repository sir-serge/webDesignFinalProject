# Pharmacy Management System Documentation

## Overview
The Pharmacy Management System is a web-based application designed to manage pharmacy operations, including user management, inventory control, prescription handling, and order processing. The system supports two types of users: Pharmacists and Clients (patients).

## System Architecture

### Technology Stack
- Backend: PHP
- Database: MySQL
- Frontend: HTML, CSS, JavaScript
- Server: XAMPP (Apache)

### Directory Structure
```
├── config/             # Configuration files
├── handlers/           # PHP request handlers
├── js/                 # JavaScript files
├── uploads/           # File uploads directory
├── database/          # Database related files
└── various PHP/HTML files for different functionalities
```

## Database Schema

### Core Tables

1. **clientUser**
   - Primary key: phone
   - Stores client/patient information
   - Fields: username, email, address, city, state, country, password

2. **pharmacistUser**
   - Primary key: license_number
   - Stores pharmacist information
   - Fields: username, email, phone, address, pharmacy details, TIN

3. **medicine**
   - Primary key: ndc (National Drug Code)
   - Stores medication inventory
   - Fields: medication_name, category, stock, price, expiry_date

4. **cart**
   - Manages shopping cart functionality
   - Links clients with medicines
   - Tracks quantities

5. **orders**
   - Manages order processing
   - Tracks order status and total amounts

6. **order_items**
   - Stores individual items in orders
   - Links orders with medicines

## Key Features

### User Management
- Separate registration and login for pharmacists and clients
- Secure password hashing
- Input validation and error handling

### Pharmacist Features
- Inventory management
- Prescription handling
- Order processing
- Report generation
- Stock management with reorder points

### Client Features
- Browse medications
- Shopping cart functionality
- Order placement and tracking
- Prescription submission
- Profile management

## API Endpoints

### Authentication
- `/handlers/register.php`
  - POST request
  - Handles both pharmacist and client registration
  - Validates required fields
  - Checks for duplicate entries

### Security Features
- Password hashing using PHP's password_hash()
- PDO prepared statements for SQL injection prevention
- Input validation and sanitization
- Error logging and handling

## Setup Instructions

1. **Prerequisites**
   - XAMPP server
   - PHP 7.0 or higher
   - MySQL 5.7 or higher

2. **Installation**
   - Clone the repository to XAMPP's htdocs directory
   - Import the database schema from `database.sql`
   - Configure database connection in `config/db.php`
   - Ensure proper permissions for the uploads directory

3. **Configuration**
   - Update database credentials in `config/db.php`
   - Configure error reporting settings
   - Set up file upload limits if needed

## Error Handling

The system implements comprehensive error handling:
- Database connection errors
- Input validation errors
- Duplicate entry checks
- File upload errors
- API response formatting

## Security Considerations

1. **Data Protection**
   - Passwords are hashed using PASSWORD_DEFAULT
   - Sensitive data is validated and sanitized
   - SQL injection prevention using prepared statements

2. **Access Control**
   - Role-based access control
   - Session management
   - Input validation

## Maintenance

### Regular Tasks
- Monitor error logs
- Check database performance
- Update inventory levels
- Review expired medications
- Backup database regularly

### Troubleshooting
- Check error logs in PHP error log
- Verify database connectivity
- Ensure proper file permissions
- Validate input data

## Future Enhancements
- Implement real-time inventory updates
- Add payment gateway integration
- Enhance reporting capabilities
- Implement email notifications
- Add mobile responsiveness improvements

## Support
For technical support or questions, please contact the system administrator.

### registter ####
when check box is false return the we should create the insert all data in table clientUser where we should use the primary key is the phone number. all inputs must valid and are required 

when the check box is true the user inputs must be stored in pharmacistUser where the pharmacy licens number should be the primary key all inputs are required and the must be valid.

nb:check if the primary key already exist and return a pop up in the right corner of  in red with text "user already exist"

### login ###

when use click login and the check box is false return we must check in clientUser has the email with  that password in our database are on the same row the page should direct you to the clientHomepage.html   



when use click login and the check box is tru return we must check in pharmacistUser has the email with  that password in our database are on the same row the page should direct you to the pharmacist.html 


nb:check if there is no primary key already with those information return a pop up in the right corner of  in red with text "invalid email or password"



### add invetory ### 
when user click  add invetory  the  must pop up  a form that has this inputs "Medication	NDC	Category	Stock	Reorder Point	Unit Price	Expiring image(mpg,jpg)" that will be stored in the  sotred in the table medcine where ndc will be numbers and will be the primary key  and when the medicine is less than 20 units it will mark low stock and when the medecine is less than the 5 it will mark the  critical and when the units are 50 it will mark status as normal and when it is above 100 units it will mark overstock  and  if its Expiring date is less than 5days it will create a div that has  

the  background color should be #ffebee and border on the left of 3px #ff5252
<li class="expiry-item expiry-warning">
                            <h4>{Medication}</h4>
                            <div class="expiry-details">
                                <div>Lot:${ndc}</div>
                            <div class="expiry-date">Expires: {date it will expire }</div>
                            </div>

if its Expiring date is less than 10days it will create a div that has  

the  background color should be #fff8e1 and border on the left of 3px #ffc107

<li class="expiry-item expiry-warning">
                            <h4>{Medication}</h4>
                            <div class="expiry-details">
                                <div>Lot:${ndc}</div>
                            <div class="expiry-date">Expires: {date it will expire }</div>

when the pharmasits finish adding the medecine the product if it doesn't exist on the product.html create the product card on the product page .

#### add to cart ###
 when user click add to cart on any of the page on the client pages the product that was clicked will be added on the list of cart items list  on the cart.html page and the amount on the product should be added to the total and when the user click the  and when the user  clicks the remove button the product should be removed from the cart and the total should be updated. when the user clicks on the checkout button it should redirect him to the payment.html page and when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when user click checkout  you should return a pop payment form that will include fields for credit card information, or with mobile phone number and the amount to be paid. when the user clicks on the pay button it should check if the payment is successful or not and if it is successful it should redirect him to the success.html page and if it is not successful it should redirect him to the error.html page. when the user clicks on the cancel button it should redirect him to the clientHomepage.html page. when the user clicks on the print button it should print the invoice and when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when the user clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on




 #### requirements for client registration ####
- The registration form should include the following fields:
  - First Name
  - Last Name
  - Email
  - Phone Number (Primary Key)
  - Password
  - Address
  - City
  - State
  - Date of Birth
  - Insurance Number
  - Insurance Provider

when user click login and the check box is false return we must check in clientUser has the email with that password in our database are on the same row the page should direct you to the clientHomepage.html

when user click login and the check box is tru return we must check in pharmacistUser has the email with  that password in our database are on the same row the page should direct you to the pharmacist.html
nb:check if there is no primary key already with those information return a pop up in the right corner of  in red with text "invalid email or password"
### requirements for pharmacist registration ####   
- The registration form should include the following fields:
  - First Name
  - Last Name
  - Email
  - Pharmacy License Number (Primary Key)
  - Password
  - Pharmacy Name
  - Address
  - City
  - State
  - Phone Number
  - Date of Birth
  - NPI Number
  - DEA Number
  - Pharmacy Type
  - Pharmacy Address
  - Pharmacy City

## Function Examples and UI Components

### 1. Registration Forms

#### Client Registration Form
```html
<form id="clientRegistrationForm" class="registration-form">
    <div class="form-group">
        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstName" required>
    </div>
    <div class="form-group">
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required>
    </div>
    <!-- Additional fields -->
    <button type="submit">Register</button>
</form>
```

#### Pharmacist Registration Form
```html
<form id="pharmacistRegistrationForm" class="registration-form">
    <div class="form-group">
        <label for="licenseNumber">Pharmacy License Number</label>
        <input type="text" id="licenseNumber" name="licenseNumber" required>
    </div>
    <div class="form-group">
        <label for="pharmacyName">Pharmacy Name</label>
        <input type="text" id="pharmacyName" name="pharmacyName" required>
    </div>
    <!-- Additional fields -->
    <button type="submit">Register</button>
</form>
```

### 2. Add Inventory Form
```html
<div class="inventory-form">
    <h2>Add New Medication</h2>
    <form id="addInventoryForm">
        <div class="form-group">
            <label for="medicationName">Medication Name</label>
            <input type="text" id="medicationName" required>
        </div>
        <div class="form-group">
            <label for="ndc">NDC Number</label>
            <input type="text" id="ndc" required>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" required>
                <option value="prescription">Prescription</option>
                <option value="otc">Over-the-Counter</option>
            </select>
        </div>
        <div class="form-group">
            <label for="stock">Stock Quantity</label>
            <input type="number" id="stock" required>
        </div>
        <div class="form-group">
            <label for="reorderPoint">Reorder Point</label>
            <input type="number" id="reorderPoint" required>
        </div>
        <div class="form-group">
            <label for="unitPrice">Unit Price</label>
            <input type="number" step="0.01" id="unitPrice" required>
        </div>
        <div class="form-group">
            <label for="expiryDate">Expiry Date</label>
            <input type="date" id="expiryDate" required>
        </div>
        <div class="form-group">
            <label for="medicationImage">Medication Image</label>
            <input type="file" id="medicationImage" accept="image/*" required>
        </div>
        <button type="submit">Add Medication</button>
    </form>
</div>
```

### 3. Inventory Status Display
```html
<!-- Low Stock Warning -->
<div class="stock-warning low-stock">
    <i class="fas fa-exclamation-triangle"></i>
    <span>Low Stock Alert: {medication_name} has only {current_stock} units remaining</span>
</div>

<!-- Critical Stock Warning -->
<div class="stock-warning critical-stock">
    <i class="fas fa-exclamation-circle"></i>
    <span>Critical Stock Alert: {medication_name} has only {current_stock} units remaining</span>
</div>

<!-- Expiry Warning -->
<div class="expiry-warning" style="background-color: #ffebee; border-left: 3px solid #ff5252;">
    <h4>{Medication}</h4>
    <div class="expiry-details">
        <div>Lot: {ndc}</div>
        <div class="expiry-date">Expires: {expiry_date}</div>
    </div>
</div>
```

### 4. Shopping Cart Interface
```html
<div class="cart-container">
    <h2>Shopping Cart</h2>
    <div class="cart-items">
        <!-- Cart Item Template -->
        <div class="cart-item">
            <img src="{medication_image}" alt="{medication_name}">
            <div class="item-details">
                <h3>{medication_name}</h3>
                <p class="price">${unit_price}</p>
                <div class="quantity-controls">
                    <button class="decrease">-</button>
                    <input type="number" value="{quantity}" min="1">
                    <button class="increase">+</button>
                </div>
            </div>
            <button class="remove-item">Remove</button>
        </div>
    </div>
    
    <div class="cart-summary">
        <div class="subtotal">
            <span>Subtotal:</span>
            <span>${subtotal}</span>
        </div>
        <div class="total">
            <span>Total:</span>
            <span>${total}</span>
        </div>
        <div class="cart-actions">
            <button class="checkout-btn">Proceed to Checkout</button>
            <button class="continue-shopping">Continue Shopping</button>
        </div>
    </div>
</div>
```

### 5. Payment Form
```html
<div class="payment-form">
    <h2>Payment Information</h2>
    <form id="paymentForm">
        <div class="payment-methods">
            <label>
                <input type="radio" name="paymentMethod" value="creditCard" checked>
                Credit Card
            </label>
            <label>
                <input type="radio" name="paymentMethod" value="mobileMoney">
                Mobile Money
            </label>
        </div>

        <div id="creditCardFields">
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" pattern="[0-9]{16}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="expiryDate">Expiry Date</label>
                    <input type="text" id="expiryDate" placeholder="MM/YY" required>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" pattern="[0-9]{3}" required>
                </div>
            </div>
        </div>

        <div id="mobileMoneyFields" style="display: none;">
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="tel" id="phoneNumber" required>
            </div>
        </div>

        <div class="amount-display">
            <h3>Total Amount: ${total}</h3>
        </div>

        <div class="payment-actions">
            <button type="submit" class="pay-btn">Pay Now</button>
            <button type="button" class="cancel-btn">Cancel</button>
        </div>
    </form>
</div>
```

### 6. Success/Error Messages
```html
<!-- Success Message -->
<div class="alert success">
    <i class="fas fa-check-circle"></i>
    <span>Operation completed successfully!</span>
</div>

<!-- Error Message -->
<div class="alert error">
    <i class="fas fa-times-circle"></i>
    <span>An error occurred. Please try again.</span>
</div>

<!-- User Exists Warning -->
<div class="alert warning">
    <i class="fas fa-exclamation-triangle"></i>
    <span>User already exists!</span>
</div>
```

### 7. CSS Styling Examples
```css
/* Form Styling */
.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Alert Styling */
.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert.success {
    background-color: #e8f5e9;
    border-left: 4px solid #4caf50;
}

.alert.error {
    background-color: #ffebee;
    border-left: 4px solid #f44336;
}

.alert.warning {
    background-color: #fff3e0;
    border-left: 4px solid #ff9800;
}

/* Cart Item Styling */
.cart-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.cart-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    margin-right: 1rem;
}

/* Inventory Status Styling */
.stock-warning {
    padding: 0.5rem;
    margin: 0.5rem 0;
    border-radius: 4px;
}

.low-stock {
    background-color: #fff3e0;
    border-left: 4px solid #ff9800;
}

.critical-stock {
    background-color: #ffebee;
    border-left: 4px solid #f44336;
}
```

These code snippets demonstrate the UI components and their styling for the main functions of the system. Each component is designed to be responsive and user-friendly, with clear visual feedback for different states and actions.