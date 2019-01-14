<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

// files needed to connect to database
include_once 'config/database.php';
include_once 'common/helper.php';

// get request headers
$headers = apache_request_headers();
if(empty($headers['Authorization']) || !isValidToken($headers['Authorization'])) {
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array(
                "status_code" => 401,
                "response_message" => "Unauthorized: Access denied."
            ));
    die;
}
// get user id from token
else if(!empty($headers['Authorization']) && isValidToken($headers['Authorization'])) {
    $token_user_data = isValidToken($headers['Authorization']);
    $token_user_id = $token_user_data->id;
}

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

// get database connection
$database = new Database();
$conn = $database->getConnection();
$table_name = "refund";

// add new refund application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get posted data
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->refund_id) && !empty($data->refund_status)) {
        // query to check if email exists
        $update_query = "UPDATE " . $table_name . " SET refund_status = :refund_status WHERE refund_id = :refund_id";

        // prepare the query
        $stmt = $conn->prepare($update_query);

        // sanitize
        $refund_status = htmlspecialchars(strip_tags($data->refund_status));
        $refund_id = htmlspecialchars(strip_tags($data->refund_id));

        // bind given email value
        $stmt->bindParam(':refund_status', $refund_status);
        $stmt->bindParam(':refund_id', $refund_id);

        // execute the query
        if($stmt->execute()){ 
            // display success message
            echo json_encode(array(
                        "status_code" => 200,
                        "response_message" => "Application form updated successfully."
                    ));
        } else {
            // display error message
            echo json_encode(array(
                        "status_code" => 400,
                        "response_message" => "Unable to update the application form."
                    ));
        }
    }
    else {
        // set response code
        http_response_code(400);
        echo json_encode(array(
                    "status_code" => 400,
                    "message" => "Unable to update the application form."
                ));
    }
} 

?>