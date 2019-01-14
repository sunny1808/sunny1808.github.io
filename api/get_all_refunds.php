<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
    echo json_encode(array("message" => "Access denied."));
    die;
}
// get user id from token
else if(!empty($headers['Authorization']) && isValidToken($headers['Authorization'])) {
    $token_user_data = isValidToken($headers['Authorization']);
    $token_user_id = $token_user_data->id;
}

// get database connection
$database = new Database();
$conn = $database->getConnection();
$table_name = "refund";

if($token_user_id) {
    // query to get all application details
    $search_query = "SELECT refund_id, refund_ARN, refund_type, users.firstname, users.lastname, refund_status, refund.created_at FROM ". 
                    $table_name . ", users WHERE refund.ID = users.ID";

    // prepare the query
    $stmt = $conn->prepare($search_query);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();
    
    // if ARN exists return data
    if($num>0){
        // get record details / values
        $result_arr = $stmt->fetchAll();
        
        $arr_data = array();
        foreach ($result_arr as $row) {
            $arr = array();
            $arr['refund_id'] = $row['refund_id'];
            $arr['refund_ARN'] = $row['refund_ARN'];
            $arr['refund_type'] = $row['refund_type'];
            $arr['firstname'] = $row['firstname'];
            $arr['lastname'] = $row['lastname'];
            $arr['refund_status'] = $row['refund_status'];
            $arr['created_at'] = $row['created_at'];
            $arr_data[] = $arr;
        }

        // display success message
        echo json_encode(array(
                "status_code" => 200,
                "response_data" => $arr_data,
                "response_message" => "All applications details fetched successfully."
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
            "response_message" => "Unable to fetch refund details."
        ));
}
?>