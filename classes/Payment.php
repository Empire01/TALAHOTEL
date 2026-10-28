<?php
require_once "config/config.php";

class Payment
{
  private $conn;
  private $table = "payments";

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->connect();
  }

  public function makePayment($reservation_id, $user_id, $amount, $payment_method, $payment_status)
  {
    // Debugging: Check if reservation_id exists in reservations table
    $check_sql = "SELECT id FROM reservations WHERE id = :reservation_id";
    $stmt_check = $this->conn->prepare($check_sql);
    $stmt_check->bindParam(":reservation_id", $reservation_id);
    $stmt_check->execute();

    if ($stmt_check->rowCount() == 0) {
      die("Error: reservation_id does not exist in reservations table.");
    }

    $sql = "INSERT INTO " . $this->table . " (reservation_id, user_id, amount, payment_method, payment_status) 
              VALUES (:reservation_id, :user_id, :amount, :payment_method, :payment_status)";
    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":reservation_id", $reservation_id);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":amount", $amount);
    $stmt->bindParam(":payment_method", $payment_method);
    $stmt->bindParam(":payment_status", $payment_status);

    return $stmt->execute();
  }

  public function makePackagePayment($package_id, $user_id, $amount, $payment_method, $payment_status)
  {
    $sql = "INSERT INTO payments (reservation_id, user_id, amount, payment_method, payment_status) 
            VALUES (:reservation_id, :user_id, :amount, :payment_method, :payment_status)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':reservation_id', $package_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':payment_status', $payment_status);

    return $stmt->execute();
  }

  public function getTotalPayments()
  {
    $sql = "SELECT SUM(amount) as total FROM " . $this->table;
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total'] ?? 0;
  }
}
