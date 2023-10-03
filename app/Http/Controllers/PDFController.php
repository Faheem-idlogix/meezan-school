<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use PDF;
  
class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF()
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
    public function generatePDFImage()
    {
        $data = ['title' => 'Welcome to ItSolutionStuff.com'];
        $pdf = PDF::loadView('pdfImage', $data);
        $pdf->render();
    
        // Output the generated PDF to the browser or save it to a file
        return $pdf->stream('dailyActivity.pdf');
        // return $pdf->download('itsolutionstuff.pdf');
    }
}