USE pharmacydb;

-- Drop existing tables
DROP TABLE IF EXISTS clientUser;
DROP TABLE IF EXISTS pharmacistUser;

-- Create clientUser table
CREATE TABLE clientUser (
    phone VARCHAR(20) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    insurance_number VARCHAR(50) NOT NULL,
    insurance_provider VARCHAR(100) NOT NULL
);

-- Create pharmacistUser table
CREATE TABLE pharmacistUser (
    license_number VARCHAR(50) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    pharmacy_name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_of_birth DATE NOT NULL,
    npi_number VARCHAR(50) NOT NULL,
    dea_number VARCHAR(50) NOT NULL,
    pharmacy_type VARCHAR(50) NOT NULL,
    pharmacy_address TEXT NOT NULL,
    pharmacy_city VARCHAR(100) NOT NULL
);

-- Create indexes for better performance
CREATE INDEX idx_client_email ON clientUser(email);
CREATE INDEX idx_pharmacist_email ON pharmacistUser(email);