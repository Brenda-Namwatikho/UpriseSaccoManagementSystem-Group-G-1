@php
include 'db_connect.php';

// Fetch the number of pending requests from the loanstatus table
$query = "SELECT COUNT(*) as total_pending FROM loanstatus WHERE status = 'pending'";
$result = $conn->query($query);
$totalPending = $result->fetch_assoc()['total_pending'];

$query = "SELECT date(datedeposited) as deposit_date, sum(amount) as total_amount FROM deposit  GROUP BY date(datedeposited)";
$result = $conn->query($query);

$total_amount = array();
$deposit_date = array();

while ($row = mysqli_fetch_assoc($result)) {
    $total_amount[] = $row["total_amount"];
    $deposit_date[] = $row["deposit_date"];
}
@endphp

@extends('layouts.app') <!-- Assuming you have a layout -->

@section('content')
<style>
    /* Your styles here */
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid" style="background-color:lavender;">
    <!-- Your HTML content here -->
    <div class="row ml-2 mr-2">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mr-3">
                        <div class="text-white-75">Deposits Today</div>
                        <div class="text-lg font-weight-bold">
                            {{ number_format($payment->num_rows > 0 ? $payment->fetch_array()['total'] : 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="text-white stretched-link" href="{{ route('payments.index') }}">View Deposits</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mr-3">
                        <div class="text-white-75">Clients</div>
                        <div class="text-lg font-weight-bold">
                            {{ $user->num_rows }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="text-white stretched-link" href="{{ route('borrowers.index') }}">View Clients</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-warning text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mr-3">
                        <div class="text-white-75">Active Loans</div>
                        <div class="text-lg font-weight-bold">
                            {{ $loans->num_rows }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="text-white stretched-link" href="{{ route('accepted_loans.index') }}">View Loan List</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-info text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mr-3">
                        <div class="text-white-75">Total Deposits</div>
                        <div class="text-lg font-weight-bold">
                            {{ number_format($payment->num_rows > 0 ? $payment->fetch_array()['total'] : 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="text-white stretched-link" href="{{ route('payments.index') }}">View Deposit List</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-danger text-white mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mr-3">
                        <div class="text-white-75">Pending Requests</div>
                        <div class="text-lg font-weight-bold">
                            {{ $totalPending }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="text-white stretched-link" href="{{ route('pending_requests.index') }}">View Pending Requests</a>
            </div>
        </div>
    </div>

    <!-- Add similar code for other cards here -->
</div>


    <div class="row ml-2 mr-2">
        <!-- Your card columns here -->

        <!-- Deposit chart -->
        <div class="diagram_div" style="height:500px; width:500px;">
            <canvas id="myChart"></canvas>
        </div>
        <div class="card-body" style="height:500px; width:500px;">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>
</div>

<script>
    

    var chartEl = document.getElementById('myChart');
    chartEl.height = 250;

    const config = {
        type: 'bar',
        data: data,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Deposit Statistics',
                },
                legend: {
                    display: true,
                    position: 'bottom',
                },
            },
        },
    };

    const myChart = new Chart(document.getElementById('myChart'), config);
</script>

<script>


    var comparisonChartEl = document.getElementById('comparisonChart');
    comparisonChartEl.height = 250;

    const comparisonConfig = {
        type: 'pie',
        data: comparisonData,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Clients Statistics',
                },
                legend: {
                    display: true,
                    position: 'bottom',
                },
            },
        },
    };

    const comparisonChart = new Chart(document.getElementById('comparisonChart'), comparisonConfig);
</script>
@endsection
