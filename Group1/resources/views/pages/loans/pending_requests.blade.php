<?php
include 'db_connect.php'; // Include the database connection file
 
function getTimeDifference($timestamp1, $timestamp2) {
    $diff = abs(strtotime($timestamp1) - strtotime($timestamp2));
    return round($diff / (60 * 60), 2); // Convert to hours and round to 2 decimal places
}

// Fetch data from the loanstatus table where status is 'pending'
$query = "SELECT * FROM loanstatus WHERE status = 'pending'";
$result = $conn->query($query);

$pendingRequests = array(); // Initialize an empty array to store the data

// Fetch each row from the result and add it to the $pendingRequests array
while ($row = $result->fetch_assoc()) {
    $pendingRequests[] = $row;
}

// Handle form submissions (Approve and Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        // Get the applicationID to be approved
        $applicationID = $_POST['applicationID'];
        
        // Update the status in the database to 'approved'
        $updateQuery = "UPDATE loanstatus SET status = 'approved' WHERE applicationID = '$applicationID'";
        if ($conn->query($updateQuery)) {
            // Status updated successfully
            header('Location: pending_requests.php'); // Redirect to the same page to refresh the table
            exit;
        } else {
            echo "Error updating status: " . $conn->error;
        }
    } elseif (isset($_POST['reject'])) {
        // Get the applicationID to be rejected
        $applicationID = $_POST['applicationID'];
        
        // Update the status in the database to 'rejected'
        $updateQuery = "UPDATE loanstatus SET status = 'rejected' WHERE applicationID = '$applicationID'";
        if ($conn->query($updateQuery)) {
            // Status updated successfully
            header('Location: pending_requests.php'); // Redirect to the same page to refresh the table
            exit;
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Requests</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Add Uprise Sacco favicon -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add red star to indicate unaddressed references exceeding 5 hours */
        .red-star {
            color: red;
        }
    </style>
</head>
<body>
    <h2 style="color: red;">Pending Requests</h2>
    <div class="table-responsive">
        <a href="http://127.0.0.1:8000/"><button type="submit" class="btn btn-success" >BACK</button></a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Application ID</th>
                    <th>Amount Requested</th>
                    <th>Amount Given</th>
                    <th>Amount to Return</th>
                    <th>Amount to Return Monthly</th>
                    <th>Receive Date</th>
                    <th>Payment Period</th>
                    <th>Status</th>
                    <th>Member Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    foreach ($pendingRequests as $request):
                        $receiveDate = $request['receiveDate'];
                        $currentDate = date("Y-m-d H:i:s");
                        $timeDifference = getTimeDifference($currentDate, $receiveDate);
                ?>
                <tr>
                    <td>
                        <?php echo $i++; ?>
                        <?php if ($timeDifference > 5): ?>
                        <i class="fas fa-star text-danger"></i>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $request['applicationID']; ?></td>
                    <td><?php echo number_format($request['amountRequested'], 2); ?></td>
                    <td><?php echo number_format($request['amountGiven'], 2); ?></td>
                    <td><?php echo number_format($request['amountToReturn'], 2); ?></td>
                    <td><?php echo number_format($request['amountToReturnMonthly'], 2); ?></td>
                    <td><?php echo $receiveDate; ?></td>
                    
                    <td><?php echo $request['paymentPeriod']; ?></td>
                    <td><?php echo $request['status']; ?></td>
                    <td><?php echo $request['memberNumber']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="applicationID" value="<?php echo $request['applicationID']; ?>">
                            <button type="submit" class="btn btn-success" name="approve">Approve</button>
                            <button type="submit" class="btn btn-danger" name="reject">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </table>
    </div>

    <!-- Add Bootstrap JS and jQuery links here -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#borrower-list').dataTable();

        $('.approve_request').click(function () {
            var applicationID = $(this).data('applicationid');
            var confirmApprove = confirm("Are you sure you want to approve this loan request?");
            if (confirmApprove) {
                perform_loan_action(applicationID, 'approve');
            }
        });

        $('.reject_request').click(function () {
            var applicationID = $(this).data('applicationid');
            var confirmReject = confirm("Are you sure you want to reject this loan request?");
            if (confirmReject) {
                perform_loan_action(applicationID, 'reject');
            }
        });

        function perform_loan_action(applicationID, action) {
        start_load();
        $.ajax({
            url: 'pending_requests.php', // Adjust the URL if needed
            method: 'POST',
            data: { applicationID: applicationID, action: action },
            success: function (resp) {
                if (resp == 1) {
                    var actionText = (action === 'approve') ? 'approved' : 'rejected';
                    alert_toast("Loan request " + actionText + " successfully.", 'success');
                    setTimeout(function () {
                        window.location.href = 'http://127.0.0.1:8000/'; // Redirect to home.php
                    }, 1500);
                } else {
                    alert_toast("Failed to " + action + " loan request.", 'error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                alert_toast("Error: Failed to " + action + " loan request.", 'error');
            }
        });
    }








    </script>
</body>
</html>