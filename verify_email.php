<?php
include('./config/connect.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists and is not verified using prepared statements
    $query = "SELECT * FROM user_info WHERE verification_token = ? AND email_verified = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $token); // Bind the token parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Fetch current password before verification update using prepared statements
        $queryCheckPassword = "SELECT password FROM user_info WHERE verification_token = ?";
        $stmtCheckPassword = mysqli_prepare($conn, $queryCheckPassword);
        mysqli_stmt_bind_param($stmtCheckPassword, 's', $token);
        mysqli_stmt_execute($stmtCheckPassword);
        $passwordResult = mysqli_stmt_get_result($stmtCheckPassword);
        $currentPassword = mysqli_fetch_assoc($passwordResult)['password'];

        // Verify the user using prepared statements
        $updateQuery = "UPDATE user_info SET email_verified = 1, verification_token = NULL WHERE verification_token = ?";
        $stmtUpdate = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmtUpdate, 's', $token);

        if (mysqli_stmt_execute($stmtUpdate)) {
            // After successful update, check password again using prepared statements
            $queryCheckPasswordAfter = "SELECT password FROM user_info WHERE verification_token IS NULL";
            $stmtCheckPasswordAfter = mysqli_prepare($conn, $queryCheckPasswordAfter);
            mysqli_stmt_execute($stmtCheckPasswordAfter);
            $passwordAfterResult = mysqli_stmt_get_result($stmtCheckPasswordAfter);
            $updatedPassword = mysqli_fetch_assoc($passwordAfterResult)['password'];

            echo "<script>
                alert('Email verified successfully. You can now log in.');
                window.location.href = 'login_form';
            </script>";
        } else {
            echo "Verification failed: " . mysqli_error($conn);
        }
    } else {
        echo "<script>
            alert('Invalid or already used verification token.');
            window.location.href = 'login_form';
        </script>";
    }
} else {
    header('Location: login_form');
}
?>
