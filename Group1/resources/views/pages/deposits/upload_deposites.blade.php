<?php
include('db_connect.php'); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $csv_file = $_FILES["csv_file"]["tmp_name"];

    if (($handle = fopen($csv_file, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $memberNumber = $data[0];
            $amount = $data[1];
            $datedeposited = date("Y-m-d");
            $receiptnumber = $data[2];
            

            $query = "INSERT INTO deposit (memberNumber, amount, datedeposited, receiptnumber) 
                      VALUES ('$memberNumber', '$amount', '$datedeposited', '$receiptnumber')";

            $conn->query($query);
        }
        header("Location: payments.php"); // Redirect to the home page
        exit();
    } else {
        echo "Error opening CSV file!";
    }
}
?>
