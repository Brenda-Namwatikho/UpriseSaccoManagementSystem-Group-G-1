<?php
// Include your database connection here
include 'db_connect.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    
    // Update the user's password to 'suspended'
    $query = "UPDATE users SET password = 'suspended' WHERE id = $id";
    
    if($conn->query($query)){
        echo 1;
    } else {
        echo 0;
    }
} else {
    echo 0; // Data not received
}
?>
