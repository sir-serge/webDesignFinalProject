CREATE TABLE PATIENT (
    PatientID VARCHAR(10) PRIMARY KEY,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Phone VARCHAR(15),
    Email VARCHAR(100),
    Gender CHAR(1) CHECK (Gender IN ('M', 'F')),
    DateOfBirth DATE,
    Age INT,
    Address VARCHAR(100),
    InsuranceProvider VARCHAR(50),
    PolicyNumber VARCHAR(20)
);