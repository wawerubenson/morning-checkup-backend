<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " SET first_name=:first_name, last_name=:last_name, email=:email, password=:password, created_at=:created_at";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Hash the password
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        // Bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":created_at", $this->created_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        // Sanitize email
        $this->email = htmlspecialchars(strip_tags($this->email));
        // Bind email
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
  // Login user
  public function login() {
    // Query to check user credentials
    $query = "SELECT id, first_name, last_name, email, password FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
    // Prepare the query
    $stmt = $this->conn->prepare($query);
    // Sanitize input
    $this->email = htmlspecialchars(strip_tags($this->email));
    // Bind parameter
    $stmt->bindParam(':email', $this->email);

    // Execute the query
    $stmt->execute();

    // Check if email exists
    if ($stmt->rowCount() == 1) {
        // Retrieve user details
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $hashed_password = $row['password'];

        // Verify password
        if (password_verify($this->password, $hashed_password)) {
            return true; // Passwords match, login successful
        }
    }

    return false; // Login failed
}
}

