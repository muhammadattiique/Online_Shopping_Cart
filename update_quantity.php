<?php
// Connect to DB
$conn = new PDO("mysql:host=localhost;dbname=your_db", "username", "password");

// Get JSON POST data
$data = json_decode(file_get_contents('php://input'), true);
$itemId = $data['id'];

// Update quantity
$stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
$stmt->execute([$itemId]);

// Fetch updated quantity
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ?");
$stmt->execute([$itemId]);
$newQuantity = $stmt->fetchColumn();

// Return JSON
echo json_encode(['newQuantity' => $newQuantity]);
?>
