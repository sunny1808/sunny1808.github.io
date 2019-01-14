<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
                "response_message" => "POST Unauthorized: Access denied."
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

$upload_success_msg;
$upload_error_msg;

// add new refund application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['refundType']) && !empty($_POST['refundAmount']) &&
        !empty($_POST['bankAccNumber']) && !empty($_FILES["fileToUpload"]["name"]) &&
        !empty($_POST['declaration_status']) && !empty($_POST['profile_id'])) {
        
        // upload user document
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = false;

        $pdfFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        if(!empty($_FILES["fileToUpload"]["type"])) {
            $check = filesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = true;
            } else {
                $upload_error_msg = "File is not an PDF.";
                $uploadOk = false;
            }
            clearstatcache();
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            $upload_error_msg = "Sorry, file already exists.";
            $uploadOk = false;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 1e+6) {
            $upload_error_msg = "Sorry, your file is too large.";
            $uploadOk = false;
        }
        // Allow certain file formats
        if($pdfFileType != "pdf") {
            $upload_error_msg = "Sorry, only PDF files are allowed.";
            $uploadOk = false;
        }

        // insert form data in the database
        // insert query
        $query = "INSERT INTO " . $table_name . "
                SET
                    refund_ARN = :refund_ARN,
                    refund_type = :refund_type,
                    refund_amount = :refund_amount,
                    bank_account_number = :bank_account_number,
                    declaration_status = :declaration_status,
                    file_name = :file_name,
                    ID = :ID,
                    refund_status = :refund_status";

        // prepare the query
        $stmt = $conn->prepare($query);

        // sanitize
        $refund_type = htmlspecialchars(strip_tags($_POST['refundType']));
        $refund_amount = htmlspecialchars(strip_tags($_POST['refundAmount']));
        $bank_account_number = htmlspecialchars(strip_tags($_POST['bankAccNumber']));
        $declaration_status = htmlspecialchars(strip_tags($_POST['declaration_status'] ? 1 : 0));
        $file_name = basename($_FILES["fileToUpload"]["name"]);
        $refund_ARN = generateARN();

        // default status 
        $refund_status = "SUBMITTED";

        // bind the values
        $stmt->bindParam(':refund_ARN', $refund_ARN);
        $stmt->bindParam(':refund_type', $refund_type);
        $stmt->bindParam(':refund_amount', $refund_amount);
        $stmt->bindParam(':bank_account_number', $bank_account_number);
        $stmt->bindParam(':declaration_status', $declaration_status);
        $stmt->bindParam(':file_name', $file_name);
        $stmt->bindParam(':refund_status', $refund_status);

        // check if user exists in database
        if(validUserID($conn, $_POST['profile_id'], $token_user_id)) {
            $ID = htmlspecialchars(strip_tags($_POST['profile_id']));
            $stmt->bindParam(':ID', $ID);

            $upload_permission_error_msg = "";
            // if everything is ok, try to upload file
            if($uploadOk) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $upload_success_msg = "The file ". basename($_FILES["fileToUpload"]["name"]). " has been uploaded.";
                } else {
                    $upload_permission_error_msg = "Sorry, there was an error uploading your file.";
                }
            }
        }     

        // execute the query, also check if query was successful
        if($uploadOk && $stmt->execute()){
            // set response code
            http_response_code(200);
        
            // display success message
            echo json_encode(array(
                        "status_code" => 200,
                        "response_data" => $refund_ARN,
                        "response_message" => "Refund claim submitted successfully."
                ));
        }
        // message if unable to submit form
        else{    
            // set response code
            http_response_code(400);
        
            // display error message
            if(!empty($upload_error_msg) || !empty($upload_permission_error_msg)) {
                echo json_encode(array(
                        "status_code" => 400,
                        "response_message" => $upload_error_msg . $upload_permission_error_msg
                    ));
            } else {
                echo json_encode(array(
                        "status_code" => 400,
                        "response_message" => "Unable to submit refund claim"
                    ));
            }
        }
    } else {
        // set response code
        http_response_code(400);
        echo json_encode(array(
                    "status_code" => 400,
                    "response_message" => "Unable to submit refund claim"
                ));
    }
}

function generateARN() {
    $c = uniqid (mt_rand() + microtime(),true);
    $md5c = md5($c);

    return strtoupper($md5c);
}

function validUserID($conn, $profile_id, $token_user_id) {
    $user_table = "users";
    // query to check if email exists
    $user_query = "SELECT *
            FROM " . $user_table . "
            WHERE ID = ?
            LIMIT 0,1";

    // prepare the query
    $stmt = $conn->prepare($user_query);

    // bind given email value
    $stmt->bindParam(1, $profile_id);

    // execute the query
    $stmt->execute();

    // get number of rows
    $num = $stmt->rowCount();

    // if user id exists
    if($num>0 && $profile_id == $token_user_id){
        // get record details / values
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // assign values to variables
        $user_id = $row['ID'];
        $user_firstname = $row['firstname'];

        // return true because user exists in the database
        return true;
    }

    // return false if user does not exist in the database
    return false;
}
?>