<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Inspiring;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    /**
     * Handle the welcome page.
     */
    public function __invoke(): View
    {

        $quote = Inspiring::quote();
        Log::info('WelcomeController ha sido alcanzado en el dominio central.');
        
        // Limpiar etiquetas de formato de consola (como <options=bold>, <fg=gray>, etc.)
        $cleanQuote = preg_replace('/<[^>]*>/', '', $quote);
        
        return view('welcome', [
            'quote' => $cleanQuote,
        ]);
    }
}
