<?php include 'db_connect.php' ?>
<!DOCTYPE html>
<html>
<head>
    <title>Rejected Loans Reasons </title>
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
                <h3 class="card-title">Rejected Loan Reasons</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <thead>
                        <tr>
                            <th class="text-center">Duration</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Member Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                            $qry = $conn->query("SELECT * FROM loanreasons");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['ApplicationID'] ?></td>
                            <td><?php echo $row['memberNumber'] ?></td>
                            <td><?php echo $row['reason'] ?></td>
                            
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
