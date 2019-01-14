<?php

// required to decode jwt
include_once 'config/core.php';
include_once 'libs/php-jwt/src/BeforeValidException.php';
include_once 'libs/php-jwt/src/ExpiredException.php';
include_once 'libs/php-jwt/src/SignatureInvalidException.php';
include_once 'libs/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;

function isValidToken($token_data) {
    // get jwt
    $jwt = isset($token_data) ? $token_data : "";

    // if jwt is not empty
    if($jwt){
        // if decode succeed, show user details
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, AUTH_KEY, array('HS256'));
            return $decoded->data;
    
        }
        // if decode fails, it means jwt is invalid
        catch (Exception $e){
            return false;
        }
    } 
    // show error message if jwt is empty
    else{
        return false;
    }
}
?>