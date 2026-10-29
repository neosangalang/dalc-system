<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Display the DALC Terms of Use.
     */
    public function terms()
    {
        return view('legal.terms', [
            'activeTab' => 'terms',
            'lastUpdated' => 'October 28, 2026',
        ]);
    }

    /**
     * Display the DALC Data Privacy Policy (compliant with RA 10173).
     */
    public function privacy()
    {
        return view('legal.privacy', [
            'activeTab' => 'privacy',
            'lastUpdated' => 'October 28, 2026',
        ]);
    }

    /**
     * Display the Parental / Guardian Consent & Photo/Media Release Policy.
     */
    public function parentalConsent()
    {
        return view('legal.parental-consent', [
            'activeTab' => 'parental-consent',
            'lastUpdated' => 'October 28, 2026',
        ]);
    }
}
