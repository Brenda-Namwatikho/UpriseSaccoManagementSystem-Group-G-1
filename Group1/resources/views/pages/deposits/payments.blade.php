<?php include 'db_connect.php' ?>



<!Doctype>
<html>
	<head>

	</head>
	<body style="background-color:lavender;">
		<div class="container-fluid">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<large class="card-title">
							<b>General Deposits </b>
							<form action="upload_deposites.php" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="csv_file">Upload CSV File:</label>
								<input type="file" class="form-control-file" id="csv_file" name="csv_file">
							</div>
							<button type="submit" class="btn btn-primary">Upload CSV</button>
							</form>
							
						</large>
						
					</div>
					<div class="card-body">
						
						<div class="card-body">
						
					</div>

					</div>
					<div class="card-body">
						<table class="table table-bordered" id="loan-list">
							<colgroup>
								<col width="10%">
								<col width="25%">
								<col width="25%">
								<col width="20%">
								<col width="10%">
								<col width="10%">
							</colgroup>
							<thead>
								<tr>
									<th class="text-center">Member Number</th>
									<th class="text-center">Amount Deposited</th>
									<th class="text-center">Date Deposited</th>
									<th class="text-center">Reciept Number</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$i = 1;
								$qry = $conn->query("SELECT * FROM deposit ");
								while ($row = $qry->fetch_assoc()):
								?>
								<tr>
									
									<td>
										<?php echo $row['memberNumber'] ?>
									</td>
									
									<td>
										<?php echo number_format($row['amount'], 2); ?>
									</td>
									<td class="text-center">
										<?php echo $row['datedeposited'] ?>
									</td>
									<td class="text-center">
									<?php echo $row['receiptnumber'] ?>
									</td>
								</tr>
								<?php endwhile; ?>



							
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>



		<div class="container-fluid">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<large class="card-title">
						<b> Deposits Today</b>
					</large>
				</div>
				<div class="card-body">
					<table class="table table-bordered" id="loan-list">
						<!-- Table headers -->
						<thead>
							<tr>
								<th class="text-center">Member Number</th>
								<th class="text-center">Amount Deposited</th>
								<th class="text-center">Date Deposited</th>
								<th class="text-center">Receipt Number</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$dateToday = date("Y-m-d"); // Get the current date
							$payment = $conn->query("SELECT * FROM deposit WHERE date(datedeposited) = '$dateToday'");
							while ($row = $payment->fetch_assoc()):
							?>
							<tr>
								<td>
									<?php echo $row['memberNumber'] ?>
								</td>
								<td>
									<?php echo number_format($row['amount'], 2) ?>
								</td>
								<td class="text-center">
									<?php echo $row['datedeposited'] ?>
								</td>
								<td class="text-center">
									<?php echo $row['receiptnumber'] ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

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
		$('#loan-list').dataTable()
		$('#new_payments').click(function(){
			uni_modal("New Payement","manage_payment.php",'mid-large')
		})
		$('.edit_payment').click(function(){
			uni_modal("Edit Payement","manage_payment.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.delete_payment').click(function(){
			_conf("Are you sure to delete this data?","delete_payment",[$(this).attr('data-id')])
		})
	function delete_payment($id){
			start_load()
			$.ajax({
				url:'ajax.php?action=delete_payment',
				method:'POST',
				data:{id:$id},
				success:function(resp){
					if(resp==1){
						alert_toast("Payment successfully deleted",'success')
						setTimeout(function(){
							location.reload()
						},1500)

					}
				}
			})
		}
	</script>


	</body>
</html>



<?php
include('db_connect.php'); // Include your database connection file

// Fetch data from the deposit table
$query = "SELECT * FROM deposit";
$result = $conn->query($query);

// Define CSV file name (you can customize this)
$csvFileName = "deposits.csv";

// Open a file handle for writing
$handle = fopen($csvFileName, 'w');

// Write CSV header
$header = ['Member Number', 'Amount Deposited', 'Date Deposited', 'Receipt Number'];
fputcsv($handle, $header);

// Write data rows
while ($row = $result->fetch_assoc()) {
    $data = [
        $row['memberNumber'],
        $row['amount'],
        $row['datedeposited'],
        $row['receiptnumber']
    ];
    fputcsv($handle, $data);
}

// Close the file handle
fclose($handle);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposits List</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color:lavender;">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <large class="card-title">
                        <b>Deposits List</b>

                        <a class="btn btn-success col-md-2 float-right" href="<?php echo $csvFileName; ?>" download><i class="fa fa-download"></i> Export CSV</a>
                        <!-- Rest of your existing buttons -->
                    </large>
                </div>
                <!-- Rest of your card body and table structure -->
            </div>
        </div>
    </div>
    <style>
        /* Your CSS styles */
    </style>
    <!-- Add Bootstrap JS and jQuery links -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Your JavaScript code -->
</body>
</html>
