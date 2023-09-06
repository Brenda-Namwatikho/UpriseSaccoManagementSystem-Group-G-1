<?php

namespace App\Http\Controllers;

use App\Models\Loanrequest;
use Illuminate\Http\Request;

class LoanrequestController extends Controller
{
    //
    public function getPendingLoanRequests() {
        $pendingloanreqs = Loanrequest::where('approval', '=', 'pending')->get();

        
        return view('pages/loanrequests/pendingloanreq', ['pendingloanreqs' => $pendingloanreqs]);
        
      }

      public function showPendingLoanRequests()
      {
          // Step 1: Retrieve pending requests from the database
          $pendingRequests = LoanRequest::where('approval', 'pending')->get();
  
          // Step 2: Rank the pending requests based on specified criteria
          $rankedRequests = $pendingRequests->sortByDesc(function ($request) {
              // Use any formula to calculate rank based on avPerformance, totalContribution, and monthlyContribution
              return $request->avPerformance + $request->totalContribution + $request->monthlyContribution;
          });
  
          // Step 3: Update approval status for the top 4 requests as "qualify" and the rest as "not qualify"
          $count = 0;
          foreach ($rankedRequests as $request) {
              $count++;
              if ($count <= 4) {
                  $request->update(['approval' => 'qualify']);
              } else {
                  $request->update(['approval' => 'not qualify']);
              }
          }
  
          // Step 4: Pass the ranked and updated requests to the view
          return view('pages.loanrequests.rank', ['rankedRequests' => $rankedRequests]);
      }
      
}
