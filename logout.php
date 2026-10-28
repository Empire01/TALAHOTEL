<?php
session_start();
require_once 'config/config.php';

if (isset($_SESSION['user_id'])) {
  $userId = (int) $_SESSION['user_id'];

  try {
    $database = new Database();
    $conn = $database->connect();
    $stmt = $conn->prepare("UPDATE users SET last_seen = NULL WHERE id = ?");
    $stmt->execute([$userId]);
  } catch (Throwable $e) {
    // Ignore DB errors during logout so the user is still logged out.
  }
}

$_SESSION = [];
if (session_id() !== '') {
  session_unset();
  session_destroy();
}

header('Location: customer_login.php');
exit;
