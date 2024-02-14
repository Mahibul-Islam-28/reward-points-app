<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    function about()
    {
        return view('about');
    }

    function termsPrivacy()
    {
        return view('terms-privacy');
    }

    function eula()
    {
        return view('eula');
    }

    function notFound()
    {
        return view('errors.404');
    }

    function download()
    {
        return view('download');
    }

    function works()
    {
        return view('how-works');
    }

}
