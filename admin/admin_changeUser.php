<?php
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['member_id']) && isset($input['status'])) {
    $memberId = $input['member_id'];
    $status = $input['status'];

    $host = "localhost";
    $username = "oubomnof_book_borrowing_db";
    $password = "michaeljamesochea1234567890";
    $database = "oubomnof_library_db";

    // Use your custom connection method
    $connection = mysqli_connect($host, $username, $password, $database);

    // Check connection
    if (!$connection) {
        echo json_encode(['success' => false, 'message' => 'Connection failed: ' . mysqli_connect_error()]);
        exit();
    }

    // Prepare the SQL query
    $stmt = $connection->prepare("UPDATE user_info SET status = ? WHERE member_id = ?");
    $stmt->bind_param("si", $status, $memberId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }

    $stmt->close();
    mysqli_close($connection); // Close connection
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
