<?php
class Details {
    private $conn;
    private $table_pb = 'pbx_checkups';
    private $table_sy = 'synology_checkups';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getPBXCheckups($startDate, $endDate) {
        $query = 'SELECT * FROM ' . $this->table_pb . ' WHERE date BETWEEN :start_date AND :end_date ORDER BY date';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSynologyCheckups($startDate, $endDate) {
        $query = 'SELECT * FROM ' . $this->table_sy . ' WHERE date BETWEEN :start_date AND :end_date ORDER BY date';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

