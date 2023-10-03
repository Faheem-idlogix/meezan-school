<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function generateFeeVoucher()
    {
        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y')
        ];
    
        $pdf = PDF::loadView('myPDF', $data);

        $pdf->setPaper('A4', 'portrait');

        // Enable DOMPDF's internal font rendering engine
        $pdf->getDomPDF()->set_option("enable_font_subsetting", true);
    
        // Set the font used for the PDF
        $pdf->getDomPDF()->set_option("default_font", "Arial");
    
        // (Optional) Set additional configuration options
    
        // Render the PDF
        $pdf->render();
    
        // Output the generated PDF to the browser or save it to a file
        return $pdf->stream('dailyActivity.pdf');
    
       //return $pdf;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fee $fee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee $fee)
    {
        //
    }
}
