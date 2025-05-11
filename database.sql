-- Create and use the database
CREATE DATABASE IF NOT EXISTS pharmacydb;
USE pharmacydb;

-- Create clientUser table
CREATE TABLE clientUser (
    phone VARCHAR(20) PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create pharmacistUser table
CREATE TABLE pharmacistUser (
    license_number VARCHAR(50) PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pharmacy_name VARCHAR(255) NOT NULL,
    tin VARCHAR(50) NOT NULL,
    country VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create medicine table
CREATE TABLE medicine (
    ndc VARCHAR(20) PRIMARY KEY,
    medication_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    stock INT NOT NULL,
    reorder_point INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    expiry_date DATE NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_phone VARCHAR(20),
    ndc VARCHAR(20),
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_phone) REFERENCES clientUser(phone),
    FOREIGN KEY (ndc) REFERENCES medicine(ndc)
);

-- Create orders table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    client_phone VARCHAR(20),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (client_phone) REFERENCES clientUser(phone)
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    ndc VARCHAR(20),
    quantity INT NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (ndc) REFERENCES medicine(ndc)
);

-- Create indexes for better performance
CREATE INDEX idx_medicine_status ON medicine(status);
CREATE INDEX idx_medicine_expiry ON medicine(expiry_date);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_client_email ON clientUser(email);
CREATE INDEX idx_pharmacist_email ON pharmacistUser(email);