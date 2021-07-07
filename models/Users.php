<?php

class Users {
    
    // DB stuff:
    private $conn;

    // user properties:
    public $user_id;
    public $user_name;
    public $user_password;
    public $user_special;

    // Constructor:
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get One users:
    public function get_user($user) {
        // Create query:
        $query = 'SELECT * FROM user_table WHERE user = ? ';
        // Prepare statement:
        $stmt = $this->conn->prepare($query);
        // Execute query:
        $stmt->execute([$user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->user_id        = $result['user_id'];
        $this->user_name      = $result['user'];
        $this->user_password  = $result['password'];
        $this->user_special   = $result['special'];
    }

    public function check_authkey($authkey) {
        // Create query: 
        $query = 'SELECT * FROM user_table WHERE special = ? ';
        // Prepare statement:
        $stmt = $this->conn->prepare($query);
        // Execute query:
        $stmt->execute([$authkey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // Check result and return true or false:
        if($result){
           return true;
        }else {
            return false;
        }
    }
}
