<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if (!isset($_POST['hashedPath'])) {
    echo json_encode(['error' => 'No hashed path provided']);
    exit();
}

function hashFileName($fileName) {
    return hash('sha256', $fileName . $_SESSION['member_id']);
}

function getRealFilePath($hashedName) {
    $directory = '../files/';
    $files = scandir($directory);
    foreach ($files as $file) {
        if (hashFileName($file) === $hashedName) {
            return $directory . $file;
        }
    }
    return false;
}

$hashedPath = $_POST['hashedPath'];
$realPath = getRealFilePath($hashedPath);

if ($realPath) {
    echo json_encode(['path' => $realPath]);
} else {
    echo json_encode(['error' => 'File not found']);
}
