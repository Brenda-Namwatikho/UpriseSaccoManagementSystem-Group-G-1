<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Illuminate\Http\Request;


class DepositController extends Controller
{
    public function showForm()
    {
        return view('deposit');
    }
    

    public function upload(Request $request)
    {
        // Get file
        $upload = $request->file('upload-file');
        $filePath = $upload->getRealPath();

        // Open and read the file
        $file = fopen($filePath, 'r');

        $header = fgetcsv($file);

        $escapedHeader = [];

        // Validate and sanitize headers
        foreach ($header as $key => $value) {
            $lheader = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }

        // Loop through the columns
        while ($columns = fgetcsv($file)) {
            if ($columns[0] == "") {
                continue;
            }

            // Trim data
            // foreach ($columns as $key => &$value) {
            //     $value = preg_replace('/\D/', '', $value);
            // }

            $data = array_combine($escapedHeader, $columns);

            // Set data types
            foreach ($data as $key => &$value) {
                $value = ($key == "receiptNo" || $key == "amount") ? (float)$value : $value;
            }

            // Create deposit record
            $deposit = new Deposit();
            $deposit->receiptNo = $data['receiptno'];
            $deposit->amount = $data['amount'];
            $deposit->date = $data['date'];
            $deposit->memberId = $data['memberid'];
            //$deposit->status = $data['status'];
            $deposit->save();
        }

        fclose($file);

        // Redirect or display a success message
        return redirect()->back()->with('success', 'Deposits uploaded successfully.');
    }
    public function searchDeposit()
    {
        $search_text = $_GET['query'];

        // Check if the search text is a valid integer (for receipt number) or a string (for member ID)
        if (is_numeric($search_text) ) {
            $searchdeposits = Deposit::where('receiptNo', 'LIKE', $search_text . '%')->get();
        } else  {
            $searchdeposits = Deposit::where(function ($query) use ($search_text) {
                $query->where('memberId', 'LIKE', "%{$search_text}%")
                    ->orWhereDate('date', $search_text); // Assuming date is a column in your 'Deposit' table.
            })->get();

        }
        $deposits = Deposit::all();

        return view('pages.deposits.searchdeposit', ['deposits' => $deposits, 'searchdeposits' => $searchdeposits]);

       // return view('pages/searchdeposit', compact('searchdeposits'));
    
        // Now you can use $deposits to access the search results
    }
}
