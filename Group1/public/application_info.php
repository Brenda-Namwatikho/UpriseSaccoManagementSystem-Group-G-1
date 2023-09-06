<?php include 'db_connect.php' ?>
<!DOCTYPE html>
<html>
<head>
    <title>Loan Applications</title>
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body style="background-color:lavender;">

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card dashboard">
            <div class="card-body">
                <!-- Add any content you want above the table -->
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Loan Application List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <thead>
                        <tr>
                            <th class="text-center">Application ID</th>
                            <th class="text-center">Loan Amount </th>
                            <th class="text-center">Payment Period</th>
                            <th class="text-center">Application Date</th>
                            <th class="text-center">Loan Group</th>
                            <th class="text-center">Member Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $qry = $conn->query("SELECT * FROM loanapplication");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            
                            <td><?php echo $row['applicationID'] ?></td>
                            <td><?php echo number_format($row['loanAmount'], 2) ?></td>
                            <td><?php echo $row['paymentPeriod'] ?></td>
                            <td><?php echo $row['applicationDate'] ?></td>
                            <td><?php echo $row['loanGroup'] ?></td>
                            <td><?php echo $row['memberNumber'] ?></td>
                            
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>

