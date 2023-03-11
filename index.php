<?php

ob_start();
session_start();
include 'users.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['PATH_INFO'] == '/get_usrs') {
    echo json_encode($users);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['PATH_INFO'] == '/login') {
    $input = (json_decode(file_get_contents("php://input"), true));

    foreach ($users as $user) {
        if ($user['email'] == $input['email']) {
            // Define the header and payload as arrays
            $header = ['alg' => 'HS256', 'typ' => 'JWT'];
            $payload = ['sub' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'iat' => 1516239022];

            // Encode the header and payload as base64
            $encoded_header = base64_encode(json_encode($header));
            $encoded_payload = base64_encode(json_encode($payload));

            // Generate the signature using HMAC-SHA256 and the secret key
            $secret_key = 'techunico';
            $signature = hash_hmac('sha256', "$encoded_header.$encoded_payload", $secret_key, true);
            $encoded_signature = base64_encode($signature);

            // Combine the encoded header, payload, and signature to form the JWT
            $jwt = "$encoded_header.$encoded_payload.$encoded_signature";

            $_SESSION['jwt'] = $jwt;

            echo $jwt;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['PATH_INFO'] == '/profile') {
    $headers = getallheaders();
    $jwt = $headers['Authorization'];

    $secret_key = "techunico"; // Replace this with your secret key
// Split the JWT into three parts
    $jwt_parts = explode(".", $jwt);
    
   
// Decode the JWT header and payload
    $header = base64_decode($jwt_parts[0]);
    $payload = base64_decode($jwt_parts[1]);


// Convert the payload from JSON to an object
    $payload_obj = json_decode($payload);
    
    echo $payload_obj->name;
    echo '\n';
    echo $payload_obj->email;
    echo '\n';
    echo $payload_obj->iat;

// Verify the JWT signature
    $signature = hash_hmac('sha256', "$jwt_parts[0].$jwt_parts[1]", $secret_key, true);
    $signature_base64 = base64_encode($signature);
    
    echo $signature;


//    if ($signature_base64 == $jwt_parts[2]) {
//        echo "JWT is valid.";
//
//        // Access the JWT claims as follows
//        $user_id = $payload_obj->sub;
//        $username = $payload_obj->name;
//        $issued_at = $payload_obj->iat;
//    } else {
//        echo "JWT is not valid.";
//    }
}

