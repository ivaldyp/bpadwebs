<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models76\App_faq;

class FaqController extends Controller
{
    public function index($app)
    {
        $faqs = App_faq::where('app_name', $app)->orderBy('questions')->get();

        $applications = App_faq::distinct('app_name')->orderBy('app_name')->get('app_name');

        return view('pages.bpaddtfaq.faq')
				->with('faqs', $faqs)
                ->with('applications', $applications)
                ->with('app', $app);
    }
}
