<?php
$conn = new mysqli('localhost', 'root', '', 'smsdb');
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);
$result = $conn->query('SELECT student_id, name, first_name, class_id FROM student LIMIT 5');
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo 'ID: ' . $row['student_id'] . ' - Name: ' . $row['name'] . ' - First: ' . $row['first_name'] . ' - Class: ' . $row['class_id'] . "\n";
    }
} else {
    echo 'No students found' . "\n";
}
$conn->close();
?>