<?php
$host = 'localhost';
$username = 'root'; 
$password = ''; 

$medical_db = 'neustmrdb';
global $conn_medical;
$conn_medical = new mysqli($host, $username, $password, $medical_db);
if ($conn_medical->connect_error) {
    die("Connection to Medical DB failed: " . $conn_medical->connect_error);
}

$student_db = 'neust_student_details';
global $conn_student;
$conn_student = new mysqli($host, $username, $password, $student_db);
if ($conn_student->connect_error) {
    die("Connection to Student DB failed: " . $conn_student->connect_error);
}
?>
