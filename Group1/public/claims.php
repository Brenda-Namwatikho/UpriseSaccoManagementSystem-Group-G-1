
<?php
include('db_connect.php'); // Include your database connection file

// Retrieve data from the claim table
$claims = $conn->query("SELECT * FROM claim");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Claim Management</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color:lavender ;
        }
        
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="col-lg-12">
        
            <?php if (isset($successMessage)) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errorMessage)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <centre><h1>Claim Management</h1></centre>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="claim-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Reference Number</th>
                                    <th class="text-center">Claim Type</th>
                                    <th class="text-center">Claim Description</th>
                                    <th class="text-center">Contact</th>
                                    <th class="text-center">Member Number</th>
                                    <th class="text-center">Admin Comment</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($claim = $claims->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo $claim["referenceNumber"]; ?></td>
                                        <td><?php echo $claim["claimType"]; ?></td>
                                        <td><?php echo $claim["claimDescription"]; ?></td>
                                        <td><?php echo $claim["contact"]; ?></td>
                                        <td><?php echo $claim["memberNumber"]; ?></td>
                                        <td><?php echo $claim["adminComment"]; ?></td>
                                        <td>
                                            <button class="btn btn-primary update_claim">Update</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS and jQuery links -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
        $('.update_claim').click(function () {
            var adminComment = prompt("Enter the updated Admin Comment:");

            if (adminComment !== null) {
                var row = $(this).closest('tr');
                var referenceNumber = row.find('td:eq(0)').text(); // Get the reference number from the first cell
                update_claim(referenceNumber, adminComment);
            }
        });

        function update_claim(referenceNumber, adminComment) {
            console.log("Updating claim with referenceNumber:", referenceNumber);
            console.log("New adminComment:", adminComment);
            start_load();
            $.ajax({
                url: 'update_claim.php',
                method: 'POST',
                data: { referenceNumber: referenceNumber, adminComment: adminComment },
                success: function (resp) {
                    console.log("Response from update_claim.php:", resp);
                    if (resp == 1) {
                        alert("Claim data updated successfully.");
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        alert("Failed to update claim data.");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    alert("Error: Failed to update claim data.");
                }
            });
        }
    });


    </script>
</body>
</html>
