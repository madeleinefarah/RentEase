<?php
session_start();
require __DIR__ . "/../includes/db_connect.php";

if (!isset($_POST['id_token'])) {
    echo "MISSING_TOKEN";
    exit;
}

$id_token = $_POST['id_token'];

$keys = json_decode(file_get_contents("https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com"), true);

if (!$keys) {
    echo "KEY_FETCH_ERROR";
    exit;
}

$jwt_parts = explode('.', $id_token);

if (count($jwt_parts) !== 3) {
    echo "INVALID_JWT";
    exit;
}

$signature = $jwt_parts[2];

$header = json_decode(base64_decode($jwt_parts[0]), true);
$kid = $header['kid'] ?? null;

if (!isset($keys[$kid])) {
    echo "INVALID_KEY_ID";
    exit;
}

$public_key = $keys[$kid];

$ok = openssl_verify(
    $jwt_parts[0] . '.' . $jwt_parts[1],
    base64_decode(strtr($signature, '-_', '+/')),
    $public_key,
    "sha256"
);

if ($ok !== 1) {
    echo "INVALID_SIGNATURE";
    exit;
}

$payload = json_decode(base64_decode($jwt_parts[1]), true);

$email = $payload["email"];
$name  = $payload["name"];

$stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $user = $res->fetch_assoc();
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["name"];
    echo "OK";
} else {
    $stmt2 = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt2->bind_param("ss", $name, $email);
    $stmt2->execute();
    $new_id = $conn->insert_id;

    $_SESSION["user_id"] = $new_id;
    $_SESSION["user_name"] = $name;
    echo "OK";
}
