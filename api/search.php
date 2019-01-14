<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/api/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

// files needed to connect to database
include_once 'config/database.php';
include_once 'common/helper.php';

// get request headers
$headers = apache_request_headers();
if(empty($_POST['Authorization']) || !isValidToken($_POST['Authorization'])) {
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
else if(!empty($_POST['Authorization']) && isValidToken($_POST['Authorization'])) {
    $token_user_data = isValidToken($_POST['Authorization']);
    $token_user_id = $token_user_data->id;
}

// get database connection
$database = new Database();
$conn = $database->getConnection();
$table_name = "refund";

$parts = parse_url($url);
parse_str($parts['query'], $query);

if(!empty($query['q'])) {
    // query to application details
    $search_query = "SELECT refund_id, refund_ARN, firstname, lastname, refund_type, refund.created_at, refund_status FROM " 
                    . $table_name . 
                    ", users WHERE refund_ARN = :refund_ARN 
                    AND refund.ID = :userID LIMIT 0,1";

    // prepare the query
    $stmt = $conn->prepare($search_query);

    // sanitize
    $refund_ARN = htmlspecialchars(strip_tags($query['q']));

    // bind given email value
    $stmt->bindParam(':refund_ARN', $refund_ARN);
    $stmt->bindParam(':userID', $token_user_id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if ARN exists return data
    if($num>0){
        // get record details / values
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // display success message
        echo json_encode(array(
                "status_code" => 200,
                "response_data" => $row,
                "response_message" => "Application details fetched successfully."
            ));
    } else{    
        // set response code
        http_response_code(400);
        echo json_encode(array(
                "status_code" => 400,
                "response_data" => array(),
                "response_message" => "Unable to fetch appliation details."
            ));
    }

} else {
    // set response code
    http_response_code(400);
    echo json_encode(array(
            "status_code" => 400,
            "response_data" => array(),
            "response_message" => "Unable to fetch appliation details."
        ));
}
?>