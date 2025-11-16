<?php
session_start();
include('../config/connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['accept']) && $data['accept'] === true && isset($_SESSION['member_id'])) {
        $member_id = $_SESSION['member_id'];

        $stmt = $conn->prepare("UPDATE user_info SET has_accepted_policy = TRUE WHERE member_id = ?");
        $stmt->bind_param("i", $member_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
}

$conn->close();
?>
