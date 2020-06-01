<?php
function genToken($param){
      // Create token header as a JSON string
$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

// Create token payload as a JSON string
$payload = json_encode($param);

// Encode Header to Base64Url String
$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

// Encode Payload to Base64Url String
$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

// Create Signature Hash
$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

// Encode Signature to Base64Url String
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

// Create JWT
$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

return $jwt;



}



function sendToken($token, $row){
   
  
   
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.sendinblue.com/v3/smtp/email",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    // alternative string if validating and redirecting through verify.php
    // CURLOPT_POSTFIELDS => "{\"sender\":{\"name\":\"NTNU marketplace\",\"email\":\"eirik.kvistad@gmail.com\"},\"to\":[{\"email\":\"".$row['email']."\",\"name\":\"".$row['email']."\"}],\"textContent\":\"Please click the provided link to login to the marketplace: "."http://192.168.64.3/php-aws-codepipeline/verify.php?token=".$token." \",\"subject\":\"NTNU Marketplace login\",\"replyTo\":{\"email\":\"eirik.kvistad@gmail.com\",\"name\":\"do not reply\"}}",
    CURLOPT_POSTFIELDS => "{\"sender\":{\"name\":\"NTNU marketplace\",\"email\":\"".$row['email']."\"},\"to\":[{\"email\":\"".$row['email']."\",\"name\":\"".$row['email']."\"}],\"textContent\":\"Please click the provided link to login to the marketplace: "."http://localhost:3000/".$token." \",\"subject\":\"NTNU Marketplace login\",\"replyTo\":{\"email\":\"eirik.kvistad@gmail.com\",\"name\":\"do not reply\"}}",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "api-key: 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",
        "content-type: application/json"
    ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
    echo "cURL Error #:" . $err;
    } else {
    echo $response;
    }
}
?>