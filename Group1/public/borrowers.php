<?php include 'db_connect.php' ?>
<html>
<body style="background-color:lavender;">
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<large class="card-title">
					<b>Clients List</b>
				</large>
				<button class="btn btn-primary col-md-2 float-right" type="button" id="new_borrower"><i class="fa fa-plus"></i> New client</button>
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
							<th class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$qry = $conn->query("SELECT * FROM user order by memberNumber desc");
						while($row = $qry->fetch_assoc()):
							$memberNumber = $row['memberNumber'];
							
							// Calculate total deposits for the member
							$totalDeposits = 0;
							$deposit_query = $conn->query("SELECT sum(amount) as total FROM deposit WHERE memberNumber = '$memberNumber'");
							$deposit_row = $deposit_query->fetch_assoc();
							if ($deposit_row['total'] !== null) {
								$totalDeposits = $deposit_row['total'];
							}

							// Update the member's totalDeposits in the database
							$update_query = "UPDATE user SET totalDeposits = '$totalDeposits' WHERE memberNumber = '$memberNumber'";
							$conn->query($update_query);
						 ?>
						 <tr>
						 	<td><p><b><?php echo $row['memberNumber'] ?></b></p></td>
						 	<td><p><b><?php echo $row['name'] ?></b></p></td>
						 	<td><p><b><?php echo $row['address'] ?></b></p></td>
						 	<td><p><b><?php echo $row['contact'] ?></b></p></td>
						 	<td><p><b><?php echo $row['email'] ?></b></p></td>
						 	<td><p><b><?php echo $row['dateJoined'] ?></b></p></td>
						 	<td><p><b><?php echo number_format($totalDeposits, 2) ?></b></p></td>
						 	<td><p><b><?php echo $row['amountExpectedToDepositMonthly'] ?></b></p></td>
						 	<td class="text-center">
								<button class="btn btn-primary edit_borrower" type="button" data-id="<?php echo $row['memberNumber'] ?>"><i class="fa fa-edit"></i></button>
								<button class="btn btn-danger delete_borrower" type="button" data-id="<?php echo $row['memberNumber'] ?>"><i class="fa fa-trash"></i></button>
						 	</td>
						 </tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include custom JavaScript file -->
<script src="{{ asset('js/custom.js') }}"></script>


<!-- Rest of your HTML code -->

<script>
    $(document).ready(function () {
        $('#borrower-list').dataTable();
        
        // New borrower button click event
        $('#new_borrower').click(function () {
            uni_modal("New borrower", "manage_borrower.php", 'mid-large');
        });

        // Edit borrower button click event
        $('.edit_borrower').click(function () {
            uni_modal("Edit borrower", "manage_borrower.php?id=" + $(this).attr('data-id'), 'mid-large');
        });

        // Delete borrower button click event
        $('.delete_borrower').click(function () {
            _conf("Are you sure to delete this borrower?", "delete_borrower", [$(this).attr('data-id')]);
        });
    });

    function delete_borrower($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_borrower',
            method: 'POST',
            data: { id: $id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Borrower successfully deleted", 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>

