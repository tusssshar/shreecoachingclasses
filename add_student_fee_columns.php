<?php
$conn = new mysqli('localhost', 'root', '', 'smsdb');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$columns = [
    'total_fees' => 'VARCHAR(255) DEFAULT NULL',
    'payment_done' => 'VARCHAR(255) DEFAULT NULL'
];
foreach ($columns as $name => $type) {
    $result = $conn->query("SHOW COLUMNS FROM student LIKE '$name'");
    if ($result->num_rows === 0) {
        if ($conn->query("ALTER TABLE student ADD COLUMN $name $type") === TRUE) {
            echo "$name added\n";
        } else {
            echo "Error adding $name: " . $conn->error . "\n";
        }
    } else {
        echo "$name already exists\n";
    }
}
$conn->close();
?>