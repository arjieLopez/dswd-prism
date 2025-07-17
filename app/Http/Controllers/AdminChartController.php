<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminChartController extends Controller
{
    public function show()
    {
        $labels = ['January', 'February', 'March', 'April', 'May'];
        $prData = [79, 100, 57, 65, 73];
        $poData = [52, 80, 70, 25, 80];

        return view('admin_dashboard', [
            'labels' => $labels,
            'prData' => $prData,
            'poData' => $poData
        ]);
    }
}



