<?php
session_start();
include('../config/connect.php');

// Check if 'request_id' is passed
if (isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    if (is_numeric($request_id)) {
        // Prepare the DELETE query to remove the request from pending_requests
        $delete_sql = "DELETE FROM pending_requests WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);

        if ($delete_stmt === false) {
            echo "Error preparing DELETE statement: " . $conn->error;
            exit();
        }

        $delete_stmt->bind_param("i", $request_id);

        // Execute the DELETE query
        if ($delete_stmt->execute()) {
            if ($delete_stmt->affected_rows > 0) {
                header("Location: admin_pendingRequest.php?message=Request rejected and deleted.");
                exit();
            } else {
                echo "Request with ID $request_id does not exist or could not be deleted.";
                exit();
            }
        } else {
            echo "Error deleting the request: " . $delete_stmt->error;
            exit();
        }
    } else {
        echo "Invalid request ID.";
        exit();
    }
} else {
    echo "Request ID not set.";
    exit();
}

$conn->close();
?>
