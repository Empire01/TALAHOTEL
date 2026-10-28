<?php
require_once "config/config.php";

class Reservation
{
  private $conn;
  private $table = "reservations";

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->connect();
  }

  public function createReservation($user_id, $room_id, $guest_name, $check_in, $check_out, $status)
  {
    $sql = "INSERT INTO " . $this->table . " (user_id, room_id, guest_name, check_in, check_out, status) 
              VALUES (:user_id, :room_id, :guest_name, :check_in, :check_out, :status)";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":room_id", $room_id);
    $stmt->bindParam(":guest_name", $guest_name);
    $stmt->bindParam(":check_in", $check_in);
    $stmt->bindParam(":check_out", $check_out);
    $stmt->bindParam(":status", $status);

    return $stmt->execute();
  }

  public function getReservation()
  {
    $sql = "SELECT id, user_id, room_id, guest_name, check_in, check_out, status, amount_paid, downpayment, remaining_balance, booking_id FROM reservations ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
