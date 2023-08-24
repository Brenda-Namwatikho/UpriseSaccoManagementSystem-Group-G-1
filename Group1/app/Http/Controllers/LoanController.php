<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    //
    public function getCompletedLoans()
    {
        $completedLoans = Loan::where('status', 'complete')->get();

        return view('pages/loans/completedloan', ['completedLoans' => $completedLoans]);
    }

    public function getUndisbursedLoans()
{
    $currentDate = now()->toDateString(); // Get the current date in 'YYYY-MM-DD' format
    $undisbursedLoans = Loan::where('paymentStart', '>', $currentDate)->get();

    return view('pages/loans/undisbursedloan', ['undisbursedLoans' => $undisbursedLoans]);

}

public function getDelayedLoans()
{
    $delayedLoans = Loan::where('delayedInstallments', '>', 0)->get();

    return view('pages/loans/delayedloan', ['delayedLoans' => $delayedLoans]);
}


public function getActiveLoans()
{
    $currentDate = now()->toDateString(); // Get the current date in 'YYYY-MM-DD' format
    $activeLoans = Loan::where('paymentStart', '<=', $currentDate)
                        ->where('status', 'in progress')
                        ->get();

    return view('pages.loans.activeloan', ['activeLoans' => $activeLoans]);
}

public function  getPdf(){
    $pdf = Pdf::loadView('pdf.loans');
    return $pdf->download('loans.pdf');

}
   
}
