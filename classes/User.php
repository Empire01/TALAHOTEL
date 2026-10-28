<?php
require_once "config/config.php";

class User
{
  private $conn;
  private $table = "users";

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->connect();
  }

  public function createUser($fullname, $email, $phone, $password, $role)
  {
    // Hash the password before inserting it into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Updated SQL query to remove the username column
    $sql = "INSERT INTO " . $this->table . " (fullname, email, phone, password, role) 
            VALUES (:fullname, :email, :phone, :password, :role)";
    $stmt = $this->conn->prepare($sql);

    // Bind the parameters to the SQL query
    $stmt->bindParam(":fullname", $fullname);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":role", $role);

    // Execute the query and return whether it was successful
    return $stmt->execute();
  }

  // Get user role (used for checking if user is an admin)
  public function getUserRole($userId)
  {
    $sql = "SELECT role FROM " . $this->table . " WHERE id = :userId";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":userId", $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['role'] : null;
  }

  // Check if user is admin
  public function isAdmin($userId)
  {
    return $this->getUserRole($userId) === 'admin';
  }

  public function getHotelLocation($userId)
  {
    if ($this->isAdmin($userId)) {
      $sql = "SELECT present_address FROM " . $this->table . " WHERE id = :userId";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(":userId", $userId);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      return $user ? $user['present_address'] : null;
    } else {
      return null;
    }
  }
}
