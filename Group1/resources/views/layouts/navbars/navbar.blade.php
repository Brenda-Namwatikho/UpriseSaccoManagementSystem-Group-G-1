<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Other styles and scripts -->
</head>
<body>
    <!-- Your content here -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Other scripts -->
	<nav id="sidebar" class='mx-lt-5 bg-white' >
    <div class="sidebar-list">
        <a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span>Home</a>
        <div class="nav-item nav-loans">
            <button class="nav-link dropdown-toggle" data-toggle="dropdown">
                <span class='icon-field'><i class="fa fa-file-invoice-dollar"></i></span> Loans
			</button>
            <div class="dropdown-menu">
                <a href="index.php?page=accepted_loans" class="dropdown-item">Accepted Loans</a>
				<a href="index.php?page=application_info" class="dropdown-item">Applications</a>
            </div>
        </div>
        <a href="index.php?page=payments" class="nav-item nav-payments"><span class='icon-field'><i class="fa fa-money-bill"></i></span> Deposits</a>
        <a href="index.php?page=borrowers" class="nav-item nav-borrowers"><span class='icon-field'><i class="fa fa-user-friends"></i></span>Manage Client</a>
        <a href="index.php?page=plan" class="nav-item nav-plan"><span class='icon-field'><i class="fa fa-list-alt"></i></span> Loan Plans</a>    
        <a href="index.php?page=loan_type" class="nav-item nav-loan_type"><span class='icon-field'><i class="fa fa-th-list"></i></span> Loan type</a>        
        <?php if($_SESSION['login_type'] == 1): ?>
        <a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Manage Admin</a>
        <a href="index.php?page=saccoinfo" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-list-alt"></i></span> Sacco Info</a>
        <a href="ajax.php?action=logout" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-lock"></i></span> Logout</a>
        <?php endif; ?>
    </div>
</nav>
<script>
    $('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>

</body>
</html>
