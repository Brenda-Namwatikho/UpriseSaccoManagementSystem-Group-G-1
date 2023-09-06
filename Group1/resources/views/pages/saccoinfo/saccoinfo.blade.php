<?php
include('db_connect.php'); // Include your database connection file

// Retrieve data from the saccoinfo table
$saccoinfo = $conn->query("SELECT * FROM saccoinfo");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sacco Information</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color:lavender;">
    <div class="container-fluid">
        <div class="col-lg-12">
            <h1 >Sacco Information</h1>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="saccoinfo-table">
                            <thead>
                                <tr>
                                    
                                    <th class="text-center">Sacco ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Available Funds</th>
                                    <th class="text-center">Contribution Performance</th>
                                    <th class="text-center">Loan Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $paymentResult = $conn->query("SELECT sum(amount) as total FROM deposit");
                                $paymentArray = $paymentResult->fetch_assoc();
                                $payment = $paymentArray['total'];
                                
                                $qry = $conn->query("SELECT * FROM saccoinfo");
                                while($row = $qry->fetch_assoc()):
                            ?>    
                                <tr>
                                    <td><?php echo $row["saccoID"]; ?></td>
                                    <td><?php echo $row["name"]; ?></td>
                                    <td><?php echo $row["availableFunds"] + $payment; ?></td>
                                    <td><?php echo $row["contributionPerformance"]; ?></td>
                                    <td><?php echo $row["loanPerformance"]; ?></td>
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
        // Your JavaScript code goes here, if needed
    </script>
    <?php 
        require 'PHPMailerAutoload.php';
        require 'credentials.php'; // Make sure you have this file with your email credentials

        if (isset($_POST['send_emails' ])) {
            $qry = $conn->query("SELECT * FROM user WHERE dateJoined <= DATE_SUB(NOW(), INTERVAL 6 MONTH) ORDER BY memberNumber DESC");
            
            while ($row = $qry->fetch_assoc()) {
                $to = $row['email'];
                $subject = "Reminder: Important Information";
                $message = "Hello " . $row['name'] . ",\n\nThis is a reminder about some important information...\n";
                
                // Initialize PHPMailer
                $mail = new PHPMailer;
                $mail->SMTPDebug = 4; 
                
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = 'smtp1.gmail.com'; // Your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'alemibless@gmail.com'; // Your email address
                $mail->Password = 'Aab0780245409B@'; // Your email password
                $mail->SMTPSecure = 'tls'; // Encryption type
                $mail->Port = 587; // Port for SMTP
                
                // Set email content
                $mail->setFrom('alemibless@gmail.com'); // Your email and name
                $mail->addAddress('alemibless@gmail.com'); // Recipient's email and name
                $mail->addReplyTo('alemibless@gmail.com'); // Reply-to email and name
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body = $message;
                
                // Send email
                if ($mail->send()) {
                    echo "Email sent successfully to " . $to . "<br>";
                } else {
                    echo "Email sending failed to " . $to . "<br>";
                    echo "Mailer Error: " . $mail->ErrorInfo . "<br>";
                }
            }
    
        }
?>
<!-- The rest of your HTML and JavaScript code remains the same -->



    <div style="margin-top:30px;">
    <body style="background-color:lavender;">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card">
                <h><b><center>Active Users List</center></b></h>
                <div class="card-header">
                    <large class="card-title">
                    </large>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="borrower-list">
                        <colgroup>
                            <col width="10%">
                            <col width="35%">
                            <col width="30%">
                            <col width="15%">
                            <col width="10%">
                            <col width="35%">
                            <col width="30%">
                            <col width="15%">
                            <col width="10%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">memberID</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Address</th>
                                <th class="text-center">Contact</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">dateJoined</th>
                                <th class="text-center">TotalDeposits</th>
                                <th class="text-center">Monthly deposit</th>
                                
                            </tr>
                        </thead><!--  Author Name: Mayuri K. 
    for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
    Visit website : www.mayurik.com -->  
                        <tbody>
                            <?php
                            $i = 1;
                                $qry = $conn->query("SELECT * FROM user WHERE dateJoined <= DATE_SUB(NOW(), INTERVAL 6 MONTH) ORDER BY memberNumber DESC");
                                while($row = $qry->fetch_assoc()):

                            ?>
                            <tr>
                                
                                <td><p><b><?php echo $row['memberNumber'] ?></b></p></td>
                                <td><p><b><?php echo $row['name'] ?></b></p></td>
                                <td><p><b><?php echo $row['address'] ?></b></p></td>
                                <td><p><b><?php echo $row['contact'] ?></b></p></td>
                                <td><p><b><?php echo $row['email'] ?></b></p></td>
                                <td><p><b><?php echo $row['dateJoined'] ?></b></p></td>
                                <td><p><b><?php echo $row['totalDeposits'] ?></b></p></td>
                                <td><p><b><?php echo $row['amountExpectedToDepositMonthly'] ?></b></p></td>
                            </tr>

                            <?php endwhile; ?>
                        </tbody>
                        <button id="send-emails-btn"  class="btn btn-primary">Send Emails to Users</button>
                        <form id="send-emails-form" method="post">
                            <button type="submit" name="send_emails" style="display: none; background-color:blue; color:white;">Email User</button>
                        </form>
                    </table>
                    
                </div>
            </div>
        </div>
    </div><!--  Author Name: Mayuri K. 
    for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
    Visit website : www.mayurik.com -->  
    <style>
        td p {
            margin:unset;
        }
        td img {
            width: 8vw;
            height: 12vh;
        }
        td{
            vertical-align: middle !important;
        }
    </style>
    <script>
        console.log("Script loaded.");
        document.getElementById("send-emails-btn").addEventListener("click", function() {
            if (confirm("Are you sure you want to send emails to all users?")) {
                document.getElementById("send-emails-form").submit();
            }
        });
    </script>


	
    
</body>
</html>


