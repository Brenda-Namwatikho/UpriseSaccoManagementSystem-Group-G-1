<?php
include('db_connect.php'); // Include your database connection file
include('header.php'); // Include your header file
include('navbar.php');
// Check if form is submitted
if (isset($_POST['csvFileName'])) {
    $csvFileName = $_POST['csvFileName'];
    
    // Open a file handle for writing (create the file if not exists)
    $handle = fopen($csvFileName, 'w');

    // Fetch data from the deposit table
    $query = "SELECT * FROM deposit";
    $result = $conn->query($query);

    // Write CSV header
    $header = ['Member Number', 'Amount Deposited', 'Date Deposited', 'Receipt Number'];
    fputcsv($handle, $header);

    // Write data rows
    while ($row = $result->fetch_assoc()) {
        $data = [
            $row['memberNumber'],
            $row['amount'],
            $row['datedeposited'],
            $row['receiptnumber']
        ];
        fputcsv($handle, $data);
    }

    // Close the file handle
    fclose($handle);
    header("Location: payments.php"); // Redirect to the home page
        exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposits List</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color:lavender;">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <large class="card-title">
                        <b>Deposits List</b>
                    </large>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="csvFileName">Enter CSV File Name:</label>
                            <input type="text" class="form-control" id="csvFileName" name="csvFileName" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Export CSV</button>
                    </form>
                </div>
                <!-- Rest of your card body and table structure -->
            </div>
        </div>
    </div>
    <style>
        /* Your CSS styles */
    </style>
    <!-- Add Bootstrap JS and jQuery links -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Your JavaScript code -->
</body>
</html>
