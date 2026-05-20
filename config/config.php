<?php
error_reporting(E_ERROR);
ini_set('display_errors', '0');

$dbhost   = getenv('DB_HOST') ?: 'localhost';
$dbuser   = getenv('DB_USER') ?: 'root';
$dbpass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$database = getenv('DB_NAME') ?: 'h360_op';

if (!defined('DB_HOST')) define('DB_HOST', $dbhost);
if (!defined('DB_USER')) define('DB_USER', $dbuser);
if (!defined('DB_PASS')) define('DB_PASS', $dbpass);
if (!defined('DB_NAME')) define('DB_NAME', $database);

$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
mysqli_select_db($conn, $database) or die(mysqli_error($conn));
if (!$conn) {
    echo 'Connected failure<br>';
}

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $database);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

mysqli_set_charset($conn, 'utf8');
mysqli_query($conn, "set character_set_results='utf8'");
mysqli_set_charset($conn, 'utf8mb4');
mysqli_query($conn, "set character_set_results='utf8mb4'");
date_default_timezone_set('Asia/Kolkata');
ini_set('date.timezone', 'Asia/Kolkata');

// $ipdomain = "http://vulcantunnel.com:8000/healthplix/";
$ipdomain = getenv('IP_DOMAIN') ?: "http://h360.in/health/";

$date        = time();
$datetime    = date('Y-m-d H:i:s');
$currentDate = date("Y-m-d");
