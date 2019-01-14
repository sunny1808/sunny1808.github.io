<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set user property values
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->password = $data->password;
$user->city = $data->city;
$user->state = $data->state;
$user->tradename = $data->tradename;

// default user role
if(empty($data->userrole)) {
    $user->userrole = "SUBSCRIBER";
} else {
    // Admin user first time
    $user->userrole = $data->userrole;
}

// check if user already exists
$email_exists = $user->emailExists();

// create the user
if(!$email_exists && $user->create()){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array(
                "status_code" => 200,
                "response_message" => "User created successfully."
            ));
} 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    if($email_exists) {
        // display message: user already exists
        echo json_encode(array(
                    "status_code" => 400,
                    "response_message" => "User already exists."
            ));
    } else {
        // display message: unable to create user
        echo json_encode(array(
                    "status_code" => 400,
                    "response_message" => "Unable to create user."
                ));
    }
}
?>