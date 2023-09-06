<?php include 'db_connect.php' ?>
<!DOCTYPE html>
<html>
<head>
    <title> Loans Status</title>
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
                <h3 class="card-title"> Loans Status</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <thead>
                        <tr>
                            <th class="text-center"> ID</th>
                            <th class="text-center">Loan Group</th>
                            <th class="text-center">Requested</th>
                            <th class="text-center">Given</th>
                            <th class="text-center">To Return</th>
                            <th class="text-center">Monthly Payments</th>
                            <th class="text-center">Receive Date</th>
                            <th class="text-center">Period</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Member Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                            $qry = $conn->query("SELECT * FROM loanstatus  ");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            
                            <td><?php echo $row['applicationID'] ?></td>
                            <td><?php echo $row['loanGroup'] ?></td>
                            <td><?php echo $row['amountRequested'] ?></td>
                            <td><?php echo $row['amountGiven'] ?></td>
                            <td><?php echo $row['amountToReturn'] ?></td>
                            <td><?php echo $row['amountToReturnMonthly'] ?></td>
                            <td><?php echo $row['receiveDate'] ?></td>
                            <td><?php echo $row['paymentPeriod'] ?></td>
                            <td><?php echo $row['status'] ?></td>
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
