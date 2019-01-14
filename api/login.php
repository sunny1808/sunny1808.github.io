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
 
// set user values
$user->email = $data->email;
$email_exists = $user->emailExists();
 
// generate json web token
include_once 'config/core.php';
include_once 'libs/php-jwt/src/BeforeValidException.php';
include_once 'libs/php-jwt/src/ExpiredException.php';
include_once 'libs/php-jwt/src/SignatureInvalidException.php';
include_once 'libs/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;
 
// check if email exists and if password is correct
if($email_exists && password_verify($data->password, $user->password)){
    $token = array(
       "iss" => ISSUSER, // Issuer
       "aud" => AUDIENCE, // Audience
       "iat" => ISSUED_AT, // Issued at: time when the token was generated
       "nbf" => NOT_BEFORE, // Not before
       "data" => array(
           "id" => $user->id,
           "firstname" => $user->firstname
       )
    );
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, AUTH_KEY);
    $response_data['profile_id'] = $user->id;
    $response_data['user_role'] = $user->userrole;
    $response_data['token'] = $jwt;
    echo json_encode(array(
                "status_code" => 200,
                "response_data" => $response_data,
                "response_message" => "Successful login."
            ));
}
// login failed
else{
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array(
            "status_code" => 401,
            "response_message" => "Login failed."
        ));
}
?>