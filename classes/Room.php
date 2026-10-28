<?php
require_once "config/config.php";

class Room
{
  private $conn;
  private $table = "rooms";

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->connect();
  }

  public function createRoom($room_name, $room_number, $room_type, $max_pax, $price, $is_available, $image_path, $promo_discount, $start_date, $end_date)
  {
    // Convert values to proper numeric types
    $max_pax = max(1, (int)$max_pax);
    $price = floatval($price); // Convert price to float
    $promo_discount = floatval($promo_discount); // Convert promo_discount to float

    // Calculate discounted price
    $discounted_price = $price - ($price * ($promo_discount / 100));

    // Check if room number already exists
    $checkSql = "SELECT COUNT(*) FROM " . $this->table . " WHERE room_number = :room_number";
    $checkStmt = $this->conn->prepare($checkSql);
    $checkStmt->bindParam(":room_number", $room_number);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() > 0) {
      return "Room number already exists!";
    }

    // Insert the room with the discounted price
    $sql = "INSERT INTO " . $this->table . " (room_name, room_number, room_type, max_pax, price, discounted_price, is_available, image_path, promo_discount, start_date, end_date) 
          VALUES (:room_name, :room_number, :room_type, :max_pax, :price, :discounted_price, :is_available, :image_path, :promo_discount, :start_date, :end_date)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":room_name", $room_name);
    $stmt->bindParam(":room_number", $room_number);
    $stmt->bindParam(":room_type", $room_type);
    $stmt->bindParam(":max_pax", $max_pax, PDO::PARAM_INT);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":discounted_price", $discounted_price);
    $stmt->bindParam(":is_available", $is_available, PDO::PARAM_BOOL);
    $stmt->bindParam(":image_path", $image_path);
    $stmt->bindParam(":promo_discount", $promo_discount, PDO::PARAM_STR);
    $stmt->bindParam(":start_date", $start_date);
    $stmt->bindParam(":end_date", $end_date);

    return $stmt->execute();
  }

  public function getAvailableRooms()
  {
    // Fetch all rooms, ordered by created_at in descending order to show the newest rooms first
    $sql = "SELECT id, room_name, room_number, room_type, max_pax, price, is_available, image_path, created_at, promo_discount, start_date, end_date FROM rooms ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function updateRoomAvailability()
  {
    $today = date('Y-m-d');

    // Mark rooms as available if the reservation has ended
    $query = "UPDATE rooms r 
    JOIN reservations res ON r.id = res.room_id 
    SET r.is_available = 1 
    WHERE res.check_out < ? AND r.is_available = 0";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(1, $today, PDO::PARAM_STR);
    $stmt->execute();
  }

  public function bookRoom($room_id)
  {
    $updateQuery = "UPDATE rooms SET is_available = 0 WHERE id = ?";
    $stmt = $this->conn->prepare($updateQuery);
    $stmt->bindValue(1, $room_id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  public function roomExists($room_number)
  {
    $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE room_number = :room_number";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":room_number", $room_number);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
  }

  public function getAllRooms()
  {
    global $conn;
    $query = "
          SELECT rooms.*, bookings.customer_name, bookings.payment_amount
          FROM rooms
          LEFT JOIN bookings ON rooms.id = bookings.room_id
          ORDER BY rooms.created_at DESC
      ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function removeExpiredPromos()
  {
    $currentDate = date('Y-m-d'); // Get today's date

    // Update rooms where promo has expired
    $sql = "UPDATE " . $this->table . " 
            SET discounted_price = price 
            WHERE end_date < :current_date AND promo_discount > 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":current_date", $currentDate, PDO::PARAM_STR);
    $stmt->execute();
  }

  public function getTotalReservations()
  {
    $db = new Database();
    $conn = $db->connect();

    $sql = "SELECT COUNT(*) AS total FROM reservations";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total'] ?? 0;
  }

  public function getTotalRooms()
  {
    $sql = "SELECT COUNT(*) AS total FROM rooms";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total']; // Return only the number
  }

  public function getTotalPendingCustomers()
  {
    $sql = "SELECT COUNT(*) AS total FROM reservations WHERE status = 'Pending'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total'] ?? 0;
  }

  public function markAsAvailable($id)
  {
    $sql = "UPDATE rooms SET is_available = 1 WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$id]);
  }

  // classes/Room.php
  public function getBookedRooms()
  {
    $sql = "SELECT * FROM rooms WHERE is_available = 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
