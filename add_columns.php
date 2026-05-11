<?php
$conn = new mysqli('localhost', 'root', '', 'smsdb');
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$columns = [
    'first_name' => 'VARCHAR(255) DEFAULT NULL',
    'middle_name' => 'VARCHAR(255) DEFAULT NULL',
    'last_name' => 'VARCHAR(255) DEFAULT NULL',
    'fmobile' => 'VARCHAR(20) DEFAULT NULL',
    'standard' => 'VARCHAR(50) DEFAULT NULL',
    'medium' => 'VARCHAR(50) DEFAULT NULL',
    'board' => 'VARCHAR(50) DEFAULT NULL',
    'aadhar_card' => 'VARCHAR(255) DEFAULT NULL',
    'marksheet' => 'VARCHAR(255) DEFAULT NULL'
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