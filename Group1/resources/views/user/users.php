<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <!-- Add these lines to include Bootstrap CSS and JavaScript files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include jQuery before Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="background-color:lavender;">

<!--  Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->  
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <large class="card-title">
                    <b>Users</b>
                </large>
                <button class="btn btn-primary float-right" id="new_user"><i class="fa fa-plus"></i> New user</button>
            </div>
            <div class="card-body">
                <table class="table-striped table-bordered col-md-12">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            include 'db_connect.php';
                            $users = $conn->query("SELECT * FROM users order by name asc");
                            $i = 1;
                            while($row= $users->fetch_assoc()):
                         ?>
                         <tr>
                            <td>
                                <?php echo $i++ ?>
                            </td>
                            <td>
                                <?php echo $row['name'] ?>
                            </td>
                            <td>
                                <?php echo $row['username'] ?>
                            </td>
                            <td>  
                                <div class="btn-group">
                                    <!-- Edit User Button -->
                                    <button type="button" class="btn btn-primary edit_user" data-id="<?php echo $row['id'] ?>">Edit</button>
                                    
                                    <!-- Suspend User Button -->
                                    <!-- Suspend User Button -->
									<button type="button" class="btn btn-danger suspend_user" data-id="<?php echo $row['id'] ?>">Suspend</button>

                                </div>
                            </td>

                         </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// ... Other code ...

if(isset($_GET['action']) && $_GET['action'] == 'suspend_user'){
    $id = $_POST['id'];
    $query = "UPDATE users SET password = 'suspended' WHERE id = $id";
    if($conn->query($query)){
        echo 1;
    } else {
        echo 0;
    }
    exit;
}
?>

<script>
$(document).ready(function() {
    $('#new_user').click(function(){
        uni_modal('New User','manage_user.php');
    });

    $('.edit_user').click(function(){
        uni_modal('Edit User','manage_user.php?id=' + $(this).attr('data-id'));
    });

    $('.suspend_user').click(function() {
        var userId = $(this).data('id');
        _conf("Are you sure you want to suspend this user?", function() {
            suspend_user(userId);
        });
    });

    function suspend_user(userId) {
        start_load();
        $.ajax({
            url: 'suspend_user.php',
            method: 'POST',
            data: {id: userId},
            success: function (resp) {
                if (resp == 1) {
                    alert("User suspended successfully.");
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    alert("Failed to suspend user.");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
                alert("Error: Failed to suspend user.");
            }
        });
    }

    // Confirmation function
    function _conf(message, callback) {
        var conf = confirm(message);
        if (conf) {
            callback();
        }
    }
});
</script>


<body style="background-color:lavender;">
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<large class="card-title">
					<b>clients List</b>
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
						 	<td class="text-center">
								<button class="btn btn-primary edit_borrower" type="button" data-id="<?php echo $row['memberNumber'] ?>"><i class="fa fa-edit"></i></button>
								<!-- <button class="btn btn-danger delete_borrower" type="button" data-id="<?php echo $row['memberNumber'] ?>"><i class="fa fa-trash"></i></button> -->
						 	</td>

						 </tr>

						<?php endwhile; ?>
					</tbody>
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
	$('#borrower-list').dataTable()
	$('#new_borrower').click(function(){
		uni_modal("New borrower","manage_borrower.php",'mid-large')
	})
	$('.edit_borrower').click(function(){
		uni_modal("Edit borrower","manage_borrower.php?id="+$(this).attr('data-id'),'mid-large')
	})
	$('.delete_borrower').click(function(){
		_conf("Are you sure to delete this borrower?","delete_borrower",[$(this).attr('data-id')])
	})
	function delete_borrower($id){
			start_load()
			$.ajax({
				url:'ajax.php?action=delete_borrower',
				method:'POST',
				data:{id:$id},
				success:function(resp){
					if(resp==1){
						alert_toast("borrower successfully deleted",'success')
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
