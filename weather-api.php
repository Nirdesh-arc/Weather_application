<?php  
// Student name: NIrdesh Khadka
// Student Id:2509474
header('Content-Type: application/json'); // Ensure proper JSON output

$serverName = "localhost";
$userName = "root";
$password = "";

// Connect to MySQL
$conn = mysqli_connect($serverName, $userName, $password);
if (!$conn) {
    echo json_encode(["error" => "Failed to connect: " . mysqli_connect_error()]);
    exit;
}

// Create the database if it doesn't exist
$createDatabase = "CREATE DATABASE IF NOT EXISTS prototype3";
if (!mysqli_query($conn, $createDatabase)) {
    echo json_encode(["error" => "Failed to create database: " . mysqli_error($conn)]);
    exit;
}

// Select the database
mysqli_select_db($conn, 'prototype3');

// Create the table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS weather (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(50) NOT NULL,
    temperature FLOAT NOT NULL,
    humidity FLOAT NOT NULL,
    wind FLOAT NOT NULL,
    pressure FLOAT NOT NULL,
    icon_code VARCHAR(10) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!mysqli_query($conn, $createTable)) {
    echo json_encode(["error" => "Failed to create table: " . mysqli_error($conn)]);
    exit;
}

// Retrieve the city name from the URL parameter
$cityName = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : "Mobile";

// Check if data for the city exists in the database
$selectAllData = "SELECT * FROM weather WHERE city = '$cityName'";
$result = mysqli_query($conn, $selectAllData);

if (!$result || mysqli_num_rows($result) == 0) {
    // Fetch data from OpenWeatherMap API if not in the database
    $apiKey = "2080be2b79da2b21ef4e6f4951960ecd";
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$cityName&units=metric&appid=$apiKey";

    $response = file_get_contents($url);
    if ($response === false) {
        echo json_encode(["error" => "Failed to fetch data from OpenWeatherMap API"]);
        exit;
    }

    $data = json_decode($response, true);

    if (isset($data['main'])) {
        $temperature = $data['main']['temp'];
        $humidity = $data['main']['humidity'];
        $wind = $data['wind']['speed'];
        $pressure = $data['main']['pressure'];
        $mainCondition = $data['weather'][0]['main']; 
        $description = $data['weather'][0]['description'];
        $iconCode = $data['weather'][0]['icon'];  

        // Insert data into the database
        $insertData = "INSERT INTO weather (city, temperature, humidity, wind, pressure, icon_code, main_condition, description)
               VALUES ('$cityName', '$temperature', '$humidity', '$wind', '$pressure', '$iconCode', '$mainCondition', '$description')";

        if (!mysqli_query($conn, $insertData)) {
            echo json_encode(["error" => "Failed to insert data: " . mysqli_error($conn)]);
            exit;
        }
    } else {
        echo json_encode(["error" => "City not found or invalid API response"]);
        exit;
    }
}

// Fetch updated data from the database
$selectAllData = "SELECT * FROM weather WHERE city = '$cityName'";
$result = mysqli_query($conn, $selectAllData);
if (!$result) {
    echo json_encode(["error" => "Database query failed: " . mysqli_error($conn)]);
    exit;
}

// Add the icon URL for the weather
$rows = [];
$iconBaseUrl = "http://openweathermap.org/img/wn/"; // Base URL for weather icons
while ($row = mysqli_fetch_assoc($result)) {
    $iconCode = $row['icon_code'];  

    // Create the icon URL
    $iconUrl = $iconBaseUrl . $iconCode . "@2x.png"; // Full icon URL

    

    // Add the icon URL to the data
    $row['temperature'] = floatval($row['temperature']);
    $row['humidity'] = floatval($row['humidity']);
    $row['wind'] = floatval($row['wind']);
    $row['pressure'] = floatval($row['pressure']);
    $row['main_condition'] = $row['main_condition']; // Add main condition
    $row['description'] = $row['description']; // Add weather description
    $row['icon_url'] = $iconUrl;
    $rows[] = $row;

}

// Return data as JSON, including the icon URL
echo json_encode($rows);
mysqli_close($conn);
?>
