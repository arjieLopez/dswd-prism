<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function show()
    {
        // Replace with real data fetching logic as needed
        $labels = ['January', 'February', 'March', 'April', 'May'];
        $approvePR = [13, 15, 8, 30, 25];
        $pendingPR = [10, 20, 15, 12, 20];
        $rejectPR = [2, 3, 1, 4, 2];


        return view('user.requestingUnit_dashboard', compact('labels', 'approvePR', 'pendingPR', 'rejectPR'));
    }
}
