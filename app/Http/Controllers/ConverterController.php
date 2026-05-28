<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConverterController extends Controller
{
    public function index()
    {
        return view('converter.index');
    }
    
    public function pdfToWord(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240'
        ]);
        
        // Placeholder - actual conversion will be implemented later
        return redirect()->back()->with('info', 'PDF to Word conversion coming soon!');
    }
    
    public function wordToPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:doc,docx|max:10240'
        ]);
        
        // Placeholder - actual conversion will be implemented later
        return redirect()->back()->with('info', 'Word to PDF conversion coming soon!');
    }
}