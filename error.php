<?php
session_start();
$message = $_SESSION['message'] ?? 'An unknown error occurred';
$type = $_SESSION['message_type'] ?? 'error';
echo "<h2>Error</h2>";
echo "<p>{$message}</p>";