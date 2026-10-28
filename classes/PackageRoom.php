<?php
require_once "config/config.php";

class PackageRoom
{
  private $conn;
  private $table = "packages";  // Your packages table name

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->connect();
  }

  // Create a new package room
  public function createPackageRoom($package_name, $included_rooms, $max_pax, $package_price, $package_discount, $start_date, $end_date, $image_path, $attachment_1, $attachment_2, $attachment_3, $package_description)
  {
    // Convert price and discount to appropriate types
    $max_pax = max(1, (int)$max_pax);
    $package_price = floatval($package_price);
    $package_discount = floatval($package_discount);

    // Insert the package into the database
    $sql = "INSERT INTO " . $this->table . " (package_name, included_rooms, max_pax, package_price, package_discount, package_start, package_end, package_image, attachment_1, attachment_2, attachment_3, package_description)
          VALUES (:package_name, :included_rooms, :max_pax, :package_price, :package_discount, :package_start, :package_end, :package_image, :attachment_1, :attachment_2, :attachment_3, :package_description)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":package_name", $package_name);
    $stmt->bindParam(":included_rooms", $included_rooms);
    $stmt->bindParam(":max_pax", $max_pax, PDO::PARAM_INT);
    $stmt->bindParam(":package_price", $package_price);
    $stmt->bindParam(":package_discount", $package_discount);
    $stmt->bindParam(":package_start", $start_date);
    $stmt->bindParam(":package_end", $end_date);
    $stmt->bindParam(":package_image", $image_path);
    $stmt->bindParam(":attachment_1", $attachment_1);
    $stmt->bindParam(":attachment_2", $attachment_2);
    $stmt->bindParam(":attachment_3", $attachment_3);
    $stmt->bindParam(":package_description", $package_description);

    return $stmt->execute();
  }

  public function getAllPackageRooms()
  {
    $sql = "SELECT id, package_name, included_rooms, max_pax, package_price, package_discount, package_start, package_end, package_image, attachment_1, attachment_2, attachment_3, created_at, package_description, room_status 
                FROM " . $this->table . " 
                ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Packages visible to customers: exclude packages whose promo already ended
  public function getCustomerVisiblePackageRooms()
  {
    $currentDate = date('Y-m-d');
    $sql = "SELECT id, package_name, included_rooms, max_pax, package_price, package_discount, package_start, package_end, package_image, attachment_1, attachment_2, attachment_3, created_at, package_description, room_status
                FROM " . $this->table . "
                WHERE room_status = 'available' AND NOT (package_discount > 0 AND package_end < :current_date)
                ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':current_date', $currentDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getPackageRoomById($id)
  {
    $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getPackageReservation()
  {
    $sql = "SELECT id, booking_id, package_id, user_id, guest_name, check_in, check_in_time, check_out, check_out_time, guest_count, amount_paid, downpayment, status FROM package_reservation ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function removeExpiredPromos()
  {
    $currentDate = date('Y-m-d');

    // When a package promo has ended (package_end < today), clear the discount
    // and ensure the promo no longer displays. Some DBs may not have
    // discounted_price for packages, so we update package_discount instead.
    $sql = "UPDATE " . $this->table . " 
                SET package_discount = 0 
                WHERE package_end < :current_date AND package_discount > 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":current_date", $currentDate, PDO::PARAM_STR);
    $stmt->execute();
  }

  public function deletePackageRoom($id)
  {
    $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
  }

  public function getTotalPackageReservations()
  {
    $db = new Database();
    $conn = $db->connect();

    $sql = "SELECT COUNT(*) AS total FROM package_reservation";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total'] ?? 0;
  }

  public function getTotalPackage()
  {
    $sql = "SELECT COUNT(*) AS total FROM packages";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total']; // Return only the number
  }

  public function getTotalPendingCustomers()
  {
    $sql = "SELECT COUNT(*) AS total FROM package_reservation WHERE status = 'pending'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total'] ?? 0;
  }

  public function markAsAvailable($id)
  {
    $sql = "UPDATE packages SET room_status = 'available' WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$id]);
  }

  // classes/PackageRoom.php
  public function getBookedPackageRooms()
  {
    $sql = "SELECT * FROM packages WHERE room_status = 'booked'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
