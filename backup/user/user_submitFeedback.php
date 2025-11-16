<?php
include('../config/connect.php');
session_start();

$response = ['status' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['member_id'])) {
        $member_id = $_SESSION['member_id'];
        $feedback = trim($_POST['feedback']);

        if (!empty($feedback)) {
            $profileQuery = "SELECT name, profile_picture FROM user_info WHERE member_id = ?";
            if ($profileStmt = $conn->prepare($profileQuery)) {
                $profileStmt->bind_param("i", $member_id);
                $profileStmt->execute();
                $profileStmt->store_result();
                $profileStmt->bind_result($name, $profile_picture);
                $profileStmt->fetch();
                $profileStmt->close();

                if (!$profile_picture) {
                    $profile_picture = NULL;
                }

                $insertQuery = "INSERT INTO testimonial (name, content, profile_picture, member_id) VALUES (?, ?, ?, ?)";
                if ($insertStmt = $conn->prepare($insertQuery)) {
                    $insertStmt->bind_param("sssi", $name, $feedback, $profile_picture, $member_id);
                    if ($insertStmt->execute()) {
                        $response['status'] = 'success';
                        $response['message'] = 'Thank you for your feedback!';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'There was an error while submitting your feedback.';
                    }
                    $insertStmt->close();
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Error preparing statement.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error preparing profile query.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Please enter your feedback content.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'You must be logged in to submit feedback.';
    }
}

$conn->close(); // Close the database connection

// Return the response as JSON
echo json_encode($response);
?>
