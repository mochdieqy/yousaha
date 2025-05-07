<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdditionalPageController extends Controller
{
    public function About() {
        return view('pages.additional-page.about');
    }

    public function Terms() {
        return view('pages.additional-page.terms');
    }

    public function Privacy() {
        return view('pages.additional-page.privacy');
    }
}
