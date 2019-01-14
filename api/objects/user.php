<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $userrole;
    public $email;
    public $password;
    public $city;
    public $state;
    public $tradename;
    
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    firstname = :firstname,
                    lastname = :lastname,
                    userrole = :userrole,
                    email = :email,
                    password = :password,
                    city = :city,
                    state = :state,
                    tradename = :tradename";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->userrole = htmlspecialchars(strip_tags($this->userrole));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->city = htmlspecialchars(strip_tags($this->city));
        $this->state = htmlspecialchars(strip_tags($this->state));
        $this->tradename = htmlspecialchars(strip_tags($this->tradename));
        
        // check for empty values
        if(empty($this->firstname) || empty($this->lastname) || 
            empty($this->userrole) || empty($this->email) ||
            empty($this->city) || empty($this->state) || 
            empty($this->tradename) || empty($this->password)) {
            return false;
        }

        // bind the values
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':userrole', $this->userrole);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':tradename', $this->tradename);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
    
    // check if given email exist in the database
    function emailExists(){
    
        // query to check if email exists
        $query = "SELECT ID, firstname, lastname, password, userrole
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['ID'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
            $this->password = $row['password'];
            $this->userrole = $row['userrole'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }
}