<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CreditReportExport;
use Maatwebsite\Excel\Facades\Excel;

class CreditReportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        return Excel::download(
            new CreditReportExport($request->from, $request->to),
            'reporte.xlsx'
        );
    }
}
