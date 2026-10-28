<?php
class Database
{
  private $host = "127.0.0.1";
  private $dbname = "hotel_management";
  private $username = "root";
  private $password = "";
  private $conn;

  public function connect()
  {
    try {
      $this->conn = new PDO(
        "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
        $this->username,
        $this->password
      );
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $this->conn;
    } catch (PDOException $e) {
      die("Connection failed: " . $e->getMessage());
    }
  }
}
