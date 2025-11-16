<?php
include '../config/connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['member_id']) && isset($data['fines'])) {
    $member_id = $data['member_id'];
    $fines = (int)$data['fines']; // Ensure fines is treated as an integer

    $sql = "UPDATE user_info SET fines = ? WHERE member_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $fines, $member_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update fines.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
}

$conn->close();
?>
