<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    // set response code
    http_response_code(405);
 
    // tell the user access denied
    echo json_encode(array(
                "status_code" => 405,
                "response_message" => $_SERVER['REQUEST_METHOD'] . " Method Not Allowed."
            ));
    die;
}

// required to decode jwt
include_once 'config/core.php';
include_once 'libs/php-jwt/src/BeforeValidException.php';
include_once 'libs/php-jwt/src/ExpiredException.php';
include_once 'libs/php-jwt/src/SignatureInvalidException.php';
include_once 'libs/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = isset($data->token) ? $data->token : "";
 
// if jwt is not empty
if($jwt){
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, AUTH_KEY, array('HS256'));
 
        // set response code
        http_response_code(200);
 
        // show user details
        $response_data['data'] = $decoded->data;
        echo json_encode(array(
                    "status_code" => 200,
                    "response_message" => "Access granted.",
                    "response_data" => $response_data
        ));
 
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
        $response_data['error'] = $e->getMessage();
        // tell the user access denied  & show error message
        echo json_encode(array(
                    "status_code" => 401,
                    "response_message" => "Access denied.",
                    "response_data" => $response_data
        ));
    }
} 
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied  & show error message
    echo json_encode(array(
                "status_code" => 401,
                "response_message" => "Access denied.",
                "response_data" => $response_data
    ));
}
?>