<?php include 'db_connect.php' ?>
<!DOCTYPE html>
<html>
<head>
    <title>Accepted Loans</title>
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
                <h3 class="card-title">Accepted Loans</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <thead>
                        <tr>
                            <th class="text-center"> ID</th>
                            <th class="text-center">Amount Requested</th>
                            <th class="text-center">Amount Given</th>
                            <th class="text-center">Amount To Return</th>
                            <th class="text-center">Monthly Payment </th>
                            <th class="text-center">Receive Date</th>
                            <th class="text-center">Duration</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Member Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                            $qry = $conn->query("SELECT * FROM acceptedloan");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            
                            <td><?php echo $row['loanNumber'] ?></td>
                            <td><?php echo number_format($row['amountRequested'], 2) ?></td>
                            <td><?php echo number_format($row['amountGiven'], 2) ?></td>
                            <td><?php echo number_format($row['amountToReturn'], 2) ?></td>
                            <td><?php echo number_format($row['amountToReturnMonthly'], 2) ?></td>
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
