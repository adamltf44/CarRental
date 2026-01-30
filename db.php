<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$query_complete_rentals = "UPDATE rentals SET rental_status = 'Completed' 
                           WHERE end_date < CURDATE() 
                           AND rental_status != 'Completed'";
$conn->query($query_complete_rentals);

// 2. Set cars to 'Available' if they don't have any current 'Pending' or 'Active' rentals
$query_reset_cars = "UPDATE cars 
                     SET status = 'Available' 
                     WHERE car_id NOT IN (
                         SELECT car_id FROM rentals 
                         WHERE end_date >= CURDATE() 
                         AND rental_status IN ('Pending', 'Active')
                     )";
$conn->query($query_reset_cars);

?>