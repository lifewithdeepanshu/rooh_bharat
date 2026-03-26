<?php
require_once 'config/db.php';
$conn->query("INSERT IGNORE INTO gigs (id, title, description, reward_points, reward_money, status) VALUES 
(1, 'Survey Potholes in Sector 4', 'Take photos and geolocate potholes in your neighborhood. Help the municipality prioritize repairs.', 50, 200.00, 'open'),
(2, 'Community Clean-up Drive Volunteer', 'Assist in organizing and executing the weekend clean-up drive at MG Road Park.', 100, 0.00, 'open'),
(3, 'Verify Senior Citizen Requests', 'Visit requested addresses to verify civic assistance requirements for senior citizens.', 75, 300.00, 'open');");
echo "Mock gigs inserted successfully.";
?>
