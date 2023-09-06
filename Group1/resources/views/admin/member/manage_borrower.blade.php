
@extends('layouts.app') <!-- Assuming you have a layout called 'app.blade.php' -->

@section('content')
<?php
include('db_connect.php');
if(isset($_GET['memberNumber'])){
    $user = $conn->query("SELECT * FROM user where memberNumber =".$_GET['memberNumber']);
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
}
?>

<div class="container-fluid">
    <div class="col-lg-12">
        <form id="manage-borrower">
            @csrf <!-- Add CSRF token for Laravel form protection -->
            <input type="hidden" name="memberNumber" value="{{ isset($_GET['memberNumber']) ? $_GET['memberNumber'] : '' }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="name">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ isset($name) ? $name : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" required value="{{ isset($username) ? $username : '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required value="{{ isset($meta['password']) ? $meta['password'] : '' }}">
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <label for="contact">Contact</label>
                    <input type="text" class="form-control" name="contact" value="<?php echo isset($contact) ? $contact : '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="gender">Gender</label>
                    <textarea name="gender" class="form-control"><?php echo isset($gender) ? $gender : '' ?></textarea>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo isset($email) ? $email : '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="dateJoined">Date Joined</label>
                    <input type="date" class="form-control" name="dateJoined" value="<?php echo isset($dateJoined) ? $dateJoined : '' ?>">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4">
                    <label for="totalDeposits">Total Deposits</label>
                    <input type="text" class="form-control" name="totalDeposits" value="<?php echo isset($totalDeposits) ? $totalDeposits : '' ?>">
                </div>
                <div class="col-md-4">
                    <label for="amountExpectedToDepositMonthly">Amount Expected to Deposit Monthly</label>
                    <input type="text" class="form-control" name="amountExpectedToDepositMonthly" value="<?php echo isset($amountExpectedToDepositMonthly) ? $amountExpectedToDepositMonthly : '' ?>">
                </div>
                <div class="col-md-4">
                    <label for="address">Address</label>
                    <textarea name="address" class="form-control" required><?php echo isset($address) ? $address : '' ?></textarea>
                </div>
            </div>
            <!-- ... Rest of the form inputs ... -->
        </form>
    </div>
</div>



<script>
    $('#manage-borrower').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'save_borrower',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp){
                if(resp == 1){
                    alert_toast("Borrower data successfully saved.", "success");
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    });
</script>

@endsection <!-- Closing the 'content' section -->



