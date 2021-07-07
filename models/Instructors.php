<?php

class Instructors {
    
    // DB stuff:
    private $conn;

    // Instructor properties:
    public $id;
    public $name;
    public $field;
    public $title;
    public $email;
    public $phone;
    public $diploma;
    public $link;
    public $descr;
    public $img;

    // Constructor:
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get Instructors:
    public function read($approved_only) {
        if($approved_only){
            $approve_status = "approved";
            // Create query:
            $query = 'SELECT inst_id, inst_name, inst_field, inst_title, inst_email, inst_status, inst_img FROM instructors WHERE  inst_status = ?';
            // Prepare statement:
            $stmt = $this->conn->prepare($query);
            // Execute query:
            $stmt->execute([$approve_status]);
        } else {
            // Create query:
            $query = 'SELECT inst_id, inst_name, inst_field, inst_title, inst_email, inst_status, inst_img FROM instructors '; 
            // ORDER BY inst_status DESC
            // Prepare statement:
            $stmt = $this->conn->prepare($query);
            // Execute query:
            $stmt->execute();
        }
        return $stmt;
    }

    // Get One Instructors:
    public function read_one($id, $approved_only) {
        // Create query:
        $query = '';
        if($approved_only){
            $approve_status = "approved";
            $query = 'SELECT * FROM instructors WHERE inst_id = ? AND inst_status = ? ';
            // Prepare statement:
            $stmt = $this->conn->prepare($query);
            // Execute query:
            $stmt->execute([$id, $approve_status]);
        } else {
            $query = 'SELECT * FROM instructors WHERE inst_id = ? ';
            // Prepare statement:
            $stmt = $this->conn->prepare($query);
            // Execute query:
            $stmt->execute([$id]);
        }
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
        
    }

    // Create new instructor:
    public function create(){
        // Create query:
        $query = 'INSERT INTO instructors(inst_name, inst_field, inst_title, inst_descr, inst_img, inst_email, inst_phone, inst_link, inst_diploma) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([$this->name, $this->field, $this->title, $this->descr, $this->img, $this->email, $this->phone, $this->link, $this->diploma]);
        if ($result) {
            return true;
        } else {
            printf("Error %s. \n", $result->error);
            return false;
        }
    }

    // Update instructor:
    public function update($id){
        // Create query:
        $query = 'UPDATE instructors SET inst_name = ?, inst_field = ?, inst_title = ?, inst_descr = ?, inst_img = ?, inst_email = ?, inst_phone = ?, inst_link = ?, inst_diploma = ?  WHERE inst_id = ?';
        
        //  Prepare & execute query:
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->name, $this->field, $this->title, $this->descr, $this->img, $this->email, $this->phone, $this->link, $this->diploma, $id]);

        // Check result:
        $result = $stmt->rowCount();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    // select last_insert_id();
    public function get_last() {
        $query = 'select last_insert_id()';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result["last_insert_id()"];
    }

    // Delete instructor:
    function delete($id) {
        $query = 'DELETE FROM instructors WHERE inst_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        // Check result:
        $result = $stmt->rowCount();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    // Toggle approve of instructor:
    function toggle_approve($id, $is_approve) {
        $status = $is_approve ? "approved" : "not approved" ;
        $query = 'UPDATE instructors SET inst_status = ? WHERE inst_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status, $id]);
        
        // Check result:
        $result = $stmt->rowCount();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}
