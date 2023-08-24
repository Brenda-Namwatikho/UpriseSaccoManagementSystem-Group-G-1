<?php
// Include your database connection here
include 'db_connect.php';

if(isset($_POST['referenceNumber']) && isset($_POST['adminComment'])){
    $referenceNumber = $_POST['referenceNumber'];
    $adminComment = $_POST['adminComment'];
    
    // Update the admin comment for the specified reference number
    $query = "UPDATE claim SET adminComment = ? WHERE referenceNumber = ?";
    
    // Prepare the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $adminComment, $referenceNumber);
    
    if($stmt->execute()){
        echo 1;
    } else {
        echo 0;
    }
    $stmt->close();
} else {
    echo 0; // Data not received
}
?>
