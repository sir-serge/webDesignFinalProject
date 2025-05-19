<?php
try {
    $conn = new PDO("mysql:host=hopper.proxy.rlwy.net;port=25604;dbname=railway","bAcBhBEsJARRCPScSlaUXXZZsoiBUxIV");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}
?>