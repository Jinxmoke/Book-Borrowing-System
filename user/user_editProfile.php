<?php
session_start();

if (isset($_SESSION['name']) && isset($_SESSION['member_id'])) {
    $name = $_SESSION['name'];
    $member_id = $_SESSION['member_id'];
} else {
    header("Location: /e-book/login_form.php");
    exit;
}

include('../config/connect.php');
include 'navbar.php';

$stmt = $conn->prepare("SELECT * FROM user_info WHERE member_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get the fines value
$fines = $user['fines'];

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $fines = $_POST['fines']; // This might not be necessary if fines are just displayed and not updated
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        // Max file size (5MB)
        $max_file_size = 5 * 1024 * 1024;

        if (in_array($file_extension, $allowed_types) && $_FILES['profile_picture']['size'] <= $max_file_size) {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $new_filename;

                // Update the user's profile picture in the database
                $stmt = $conn->prepare("UPDATE user_info SET profile_picture = ? WHERE member_id = ?");
                $stmt->bind_param("si", $profile_picture, $member_id);
                if ($stmt->execute()) {
                    $success_message = "Profile picture updated successfully!";
                } else {
                    $error_message = "Error updating profile picture: " . $conn->error;
                }
                $stmt->close();

                // Delete the old profile picture if it exists
                if (!empty($user['profile_picture']) && $user['profile_picture'] !== $new_filename) {
                    $old_file = $target_dir . $user['profile_picture'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error_message = "Invalid file type or size. Please upload a JPG, JPEG, PNG, or GIF file under 5MB.";
        }
    }

    // Update other user information
    $stmt = $conn->prepare("UPDATE user_info SET name = ?, contact = ?, address = ? WHERE member_id = ?");
    $stmt->bind_param("sssi", $name, $contact, $address, $member_id);
    if ($stmt->execute()) {
        $success_message .= " User information updated successfully!";
    } else {
        $error_message .= " Error updating user information: " . $conn->error;
    }
    $stmt->close();

    if (!empty($current_password) && !empty($new_password)) {
        $stmt = $conn->prepare("SELECT password FROM user_info WHERE member_id = ?");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();

        $hashed_current_password = md5($current_password);

        if ($hashed_current_password === $user_data['password']) {
            $new_hashed_password = md5($new_password);

            $stmt = $conn->prepare("UPDATE user_info SET password = ? WHERE member_id = ?");
            $stmt->bind_param("si", $new_hashed_password, $member_id);
            if ($stmt->execute()) {
                $success_message .= " Password updated successfully!";
            } else {
                $error_message .= " Error updating password: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error_message .= " Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Section</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --primary-color: #063E29;
        --secondary-color: #0A5C3D;
        --tertiary-color: #063A26;
        --background-color: #f5f7fa;
        --text-color: #333;
        --card-background: #ffffff;
        --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.1), 0 6px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body, html {
        font-family: 'Poppins', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        line-height: 1.6;
        overflow-x: hidden;
    }
    .profile-section {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .header {
        background: linear-gradient(135deg, #063E29, #0A5C3D);
        color: white;
        padding: 2rem 0 4rem;
        position: relative;
        overflow: hidden;
    }
    .header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(10,92,61,0.2) 0%, rgba(6,62,41,0.1) 80%);
        transform: rotate(30deg);
        animation: shimmer 20s linear infinite;
    }
    @keyframes shimmer {
        0% { transform: rotate(30deg) translateY(0); }
        100% { transform: rotate(30deg) translateY(-50%); }
    }
    .container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .header-text {
        flex: 1;
        min-width: 200px;
        margin-right: 1rem;
    }
    h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }
    .header-text p {
        font-size: 1rem;
        margin-bottom: 1.5rem;
        max-width: 600px;
        opacity: 0.9;
    }
    .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background-color: #0A5C3D;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: var(--transition);
        border: 2px solid transparent;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .btn:hover {
        background-color: transparent;
        border-color: #0A5C3D;
        color: #0A5C3D;
        transform: translateY(-3px);
        box-shadow: 0 6px 8px rgba(0,0,0,0.15);
    }
    .avatar-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 1rem auto;
    }
    .avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        object-fit: cover;
    }
    .avatar-container::after {
        content: '';
        position: absolute;
        top: 5%;
        left: 5%;
        right: 5%;
        bottom: 5%;
        border-radius: 50%;
        border: 3px solid #0A5C3D;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
    .content {
        margin-top: -2rem;
        padding-bottom: 2rem;
    }
    .card {
        background-color: var(--card-background);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: var(--transition);
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15), 0 10px 10px rgba(0,0,0,0.1);
    }
    .card-body {
        padding: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
        display: block;
    }
    .grid-pair {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .grid-pair > div {
        flex: 1 1 300px;
    }
    input[type="text"], input[type="tel"], input[type="password"], input[type="file"] {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: var(--transition);
    }
    .toggle-password {
        margin-top: 0.5rem;
        background: none;
        border: none;
        color: var(--primary-color);
        cursor: pointer;
        font-size: 0.9rem;
    }
    button[type="submit"] {
        display: inline-block;
        padding: 0.75rem 2rem;
        background-color: var(--secondary-color);
        color: white;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
    }
    button[type="submit"]:hover {
        background-color: var(--primary-color);
    }
    .disabled-input {
        background-color: #f0f0f0;
        border-color: #ddd;
        cursor: not-allowed;
    }
    .password-container {
        position: relative;
    }
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .header {
            padding: 1.5rem 0 3rem;
        }
        h1 {
            font-size: 2rem;
        }
        .header-text p {
            font-size: 0.9rem;
        }
        .avatar-container {
            width: 120px;
            height: 120px;
        }
        .card-body {
            padding: 1rem;
        }
        .grid-pair > div {
            flex: 1 1 100%;
        }
    }
        /* Add these new styles */
        .fines-container {
            display: flex;
            align-items: center;
        }
        .question-mark {
            margin-left: 5px;
            cursor: pointer;
            color: #ffffff;
            background-color: #0A5C3D;
            margin-top: -25px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
        }
        .modal5 {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<section class="profile-section">
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-text">
                    <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
                    <p>Library ID: <?php echo htmlspecialchars($member_id); ?></p>
                    <div class="fines-container">
                        <p>User Fines: <?php echo htmlspecialchars($fines); ?></p>
                        <span class="question-mark" id="finesInfo">?</span>
                    </div>
                    <form method="POST" action="">
                        <button type="button" id="editProfileBtn" class="btn">Edit Profile</button>
                    </form>
                </div>
                <div class="avatar-container">
                    <img class="avatar" src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data" id="profileForm">
                        <div class="form-group grid-pair">
                            <div>
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" disabled class="disabled-input">
                            </div>
                            <div>
                                <label for="current_password">Current Password</label>
                                <div class="password-container">
                                    <input type="password" id="current_password" name="current_password" placeholder="Enter your current password" disabled class="disabled-input">
                                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('current_password', this)" disabled>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group grid-pair">
                            <div>
                                <label for="contact">Contact Number</label>
                                <input type="tel" id="contact" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" disabled class="disabled-input">
                            </div>
                            <div>
                                <label for="new_password">New Password</label>
                                <div class="password-container">
                                    <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" disabled class="disabled-input">
                                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_password', this)" disabled>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group grid-pair">
                            <div>
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" disabled class="disabled-input">
                            </div>
                            <div>
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" disabled class="disabled-input">
                            </div>
                        </div>

                        <button type="submit" id="saveChangesBtn" class="btn" disabled>Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="finesModal" class="modal5">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>User Fines Information</h2>
            <p>Fines are penalties that are applied to your account when you have overdue books. These charges accumulate based on the number of overdue books in your possession. Once your account accumulates three fines your account will be automatically disabled as a consequence. This system ensures that overdue books are returned in a timely manner and encourages responsible borrowing habits.</p>
        </div>
    </div>

<script>
    // Display success or error messages using SweetAlert
    <?php if(!empty($success_message)): ?>
        Swal.fire({
            title: 'Success!',
            text: "<?php echo $success_message; ?>",
            icon: 'success',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    <?php if(!empty($error_message)): ?>
        Swal.fire({
            title: 'Error!',
            text: "<?php echo $error_message; ?>",
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
    <?php endif; ?>

    document.getElementById('editProfileBtn').addEventListener('click', function() {
        // Enable all form fields and show Save Changes button
        const formFields = document.querySelectorAll('#profileForm input');
        formFields.forEach(function(field) {
            field.disabled = false;
            field.classList.remove('disabled-input');
        });
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(function(button) {
            button.disabled = false;
        });
        document.getElementById('saveChangesBtn').disabled = false;
        document.getElementById('saveChangesBtn').classList.remove('disabled');
    });

    function togglePasswordVisibility(inputId, button) {
        const inputField = document.getElementById(inputId);
        const eyeIcon = button.querySelector('i');

        if (inputField.type === 'password') {
            inputField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            inputField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    // Add this new JavaScript for the modal
        var modal = document.getElementById("finesModal");
        var btn = document.getElementById("finesInfo");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
</script>

</body>
</html>