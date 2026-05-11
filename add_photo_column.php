<?php
$conn = new mysqli('localhost', 'root', '', 'smsdb');
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$columns = [
    'student_photo' => 'VARCHAR(255) DEFAULT NULL'
];

foreach ($columns as $col => $type) {
    $sql = "ALTER TABLE student ADD COLUMN $col $type";
    if ($conn->query($sql) === TRUE) {
        echo "$col added\n";
    } else {
        echo "Error adding $col: " . $conn->error . "\n";
    }
}

$conn->close();
?>