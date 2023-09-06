<?php include 'db_connect.php' ?>
<style>
   
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../css/statistics.css">

<div class="container-fluid" style="background-color:lavender;">

	<!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->  

			<div class="col-lg-12">
			<div class="card dashboard">
				<div class="card-body">
			
						<marquee behavior="alternate" style="color:red; font-weight: 400; font-size: 16px;" scrolldelay="300"> AS UPRISE SACCO , WE ARE MOTIVATED TO SECURE YOUR FUTURE AND TOGETHER WE CAN</marquee>
				</div>

				<hr>
				<div class="row ml-2 mr-2">
				<div class="col-md-4">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 ">Deposits Today</div>
                                        <div class="text-lg font-weight-bold">
                                        	<?php 
                                        	$payment = $conn->query("SELECT sum(amount) as total FROM deposit where date(datedeposited) = '".date("Y-m-d")."'");
                                        	echo $payment->num_rows > 0 ? number_format($payment->fetch_array()['total'],2) : "0.00";
                                        	 ?>
                                        		
                                    	</div>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class=" text-white stretched-link" href="index.php?page=payments">View Deposits</a>
                                <div class=" text-white">
                                	
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="col-md-4">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 ">Clients</div>
                                        <div class="text-lg font-weight-bold">
                                        	<?php 
                                        	$user = $conn->query("SELECT * FROM user");
                                        	echo $user->num_rows > 0 ? $user->num_rows : "0";
                                        	 ?>
                                        		
                                    	</div>
                                    </div>
                                   
                                </div>
                            </div>
                            <!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->  
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class=" text-white stretched-link" href="index.php?page=borrowers">View Clients</a>
                                <div class=" text-white">
                                	
                                </div>
                            </div>
                        </div>
                    </div>

                  <div class="col-md-4">
                        <div class="card bg-warning text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 ">Active Loans</div>
                                        <div class="text-lg font-weight-bold">
                                        	<?php 
                                        	$loans = $conn->query("SELECT * FROM acceptedloan ");
                                        	echo $loans->num_rows > 0 ? $loans->num_rows : "0";
                                        	 ?>
                                        		
                                    	</div>
                                    </div>
                                   
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="text-white stretched-link" href="index.php?page=accepted_loans">View Loan List</a>
                                <div class=" text-white">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="card bg-info text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75 ">Total Deposits</div>
                                        <div class="text-lg font-weight-bold">
                                        	<?php 
                                        	$payment = $conn->query("SELECT sum(amount) as total FROM deposit");
                                        	echo $payment->num_rows > 0 ? number_format($payment->fetch_array()['total'],2) : "0.00";
                                        	 ?>
                                        		
                                    	</div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="text-white stretched-link" href="index.php?page=payments">View Deposit List</a>
                                <div class="text-white">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php


                    // Fetch the number of pending requests from the loanstatus table
                    $query = "SELECT COUNT(*) as total_pending FROM loanstatus WHERE status != 'Approved'";
                    $result = $conn->query($query);
                    $totalPending = $result->fetch_assoc()['total_pending'];
                    ?>

                    <div class="col-md-4">
                        <div class="card bg-danger text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="mr-3">
                                        <div class="text-white-75">Pending Requests</div>
                                        <div class="text-lg font-weight-bold">
                                            <?php echo $totalPending; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <!-- Link to pending_requests.php to view the pending requests -->
                                <a class="text-white stretched-link" href="pending_requests.php">View Pending Requests</a>
                            </div>
                        </div>
                    </div>
                    <div class="diagram_div" style="height:500px; width:500px;">
                        <canvas id="myChart" ></canvas>
                    </div>
                    <div class="card-body" style="height:500px; width:500px;">
                        <canvas id="comparisonChart"></canvas>
                    </div>


		</div>
        <div style="flex-direction:row;">

        
        <?php
        $query = "SELECT date(datedeposited) as deposit_date, sum(amount) as total_amount FROM deposit  GROUP BY date(datedeposited)";
        $result = $conn->query($query);

        $total_amount = array();
        $deposit_date = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $total_amount[] = $row["total_amount"];
            $deposit_date[] = $row["deposit_date"];
        }
        ?>

        

        <script>
            const data = {
                labels: <?php echo json_encode($deposit_date) ?>,
                datasets: [
                    {
                        label: 'Deposit Amount:',
                        backgroundColor:[
                            'green',
                        ], 
                        borderColor: '#04aa1a',
                        data: <?php echo json_encode($total_amount) ?>,
                        borderWidth: 1,
                    }
                ]
            };

            var chartEl = document.getElementById("myChart");
            chartEl.height = 250;

            const config = {
                type: 'bar',
                data: data,
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Deposit Statistics'
                        },
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            };

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        </script>

        <!-- Add your previous code here -->


        
        

<script>
    const comparisonData = {
        labels: ['Clients', 'Pending Requests', 'Active Loans'],
        datasets: [
            {
                label: 'Count',
                backgroundColor: [
                    'blue',// Clients colo 
                    'red', // Pending Requests color
                    'green',  // Active Loans color
                ],
                borderColor: [
                    '#04aa1a', // Clients border color
                    '#ff6384', // Pending Requests border color
                    '#36a2eb'  // Active Loans border color
                ],
                data: [
                    <?php echo $user->num_rows ?>, // Number of clients
                    <?php echo $totalPending ?>,   // Number of pending requests
                    <?php echo $loans->num_rows ?> // Number of active loans
                ],
                borderWidth: 1,
            }
        ]
    };

    var comparisonChartEl = document.getElementById("comparisonChart");
    comparisonChartEl.height = 250;

    const comparisonConfig = {
        type: 'pie',
        data: comparisonData,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Clients Statistics'
                },
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    };

    const comparisonChart = new Chart(
        document.getElementById('comparisonChart'),
        comparisonConfig
    );
</script>

<!-- Rest of your HTML and PHP code -->





