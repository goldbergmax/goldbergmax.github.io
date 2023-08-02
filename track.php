<?php 
$data = file_get_contents('php://input');
$json = json_decode($data);

if ($json->_type !== 'location') {
    die('JSON not formatted correctly');
}

$db = new mysqli('localhost:3306', 'otr', 'sshhh', 'otr');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

$stmt = $db->prepare('INSERT INTO recordings (user, device, acc, alt, batt, bs, conn, created_at, lat, lon, t, tid, tst, vac, vel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
if ($stmt === false) {
    die('failed to prepare query: ' . $db->error);
}

if ($stmt->bind_param('ssiiiisiddssiii', $user, $device, $json->acc, $json->alt, $json->batt, $json->bs, $json->conn, $json->created_at, $json->lat, $json->lon, $json->t, $json->tid, $json->tst, $json->vac, $json->vel) === false) {
    die('failed to bind params: ' . $stmt->error);
}

$user = $_SERVER['HTTP_X_LIMIT_U'];
$device = $_SERVER['HTTP_X_LIMIT_D'];

if ($stmt->execute() === false) {
    die('failed to insert: ' . $stmt->error);
}

$stmt->close();
$db->close();
