<?php
session_start();

echo "<h1>Session Test</h1>";

if (isset($_SESSION['test'])) {
    echo "<p>Session variable 'test' is set to: " . $_SESSION['test'] . "</p>";
} else {
    echo "<p>Session variable 'test' is not set.</p>";
    $_SESSION['test'] = "Hello, session!";
}

echo "<pre>";
var_dump($_SESSION);
echo "</pre>";
?>