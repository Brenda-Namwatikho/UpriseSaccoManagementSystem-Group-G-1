<?php include 'db_connect.php' ?>
<!DOCTYPE html>
<html>
<head>
    <title>Loans Payments</title>
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
                <h3 class="card-title">Loan Payments</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <thead>
                        <tr>
                            <th class="text-center"> Reciept Number</th>
                            <th class="text-center">Amount Paid</th>
                            <th class="text-center">Payment Date</th>
                            <th class="text-center">Loan Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                            $qry = $conn->query("SELECT * FROM loanpayment ");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['receiptNumber'] ?></td>
                            <td><?php echo number_format($row['amountPaid'], 2) ?></td>
                            <td><?php echo $row['paymentDate'] ?></td>
                            <td><?php echo $row['loanNumber'] ?></td>
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
