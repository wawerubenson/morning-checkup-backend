<?php
class Checkup {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch PBX checkups by date
    public function getPBXCheckupsByDate($date) {
        $query = "SELECT * FROM pbx_checkups WHERE date = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date);
        $stmt->execute();
        return $stmt;
    }

    // Fetch Synology checkups by date
    public function getSynologyCheckupsByDate($date) {
        $query = "SELECT * FROM synology_checkups WHERE date = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date);
        $stmt->execute();
        return $stmt;
    }

    // Fetch PBX checkup storage remaining for previous day
    public function getPBXStorageRemainingPreviousDay($date) {
        $previous_day = date('Y-m-d', strtotime($date . ' -1 day'));
        $query = "SELECT pbx_name, storage_remaining FROM pbx_checkups WHERE date = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $previous_day);
        $stmt->execute();
        return $stmt;
    }
}
?>
