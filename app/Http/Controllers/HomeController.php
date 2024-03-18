<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;

use Inertia\Inertia;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function lang(Request $request)
    {
        $lang = $request->input('lang', App::getLocale());
        $request->session()->put('locale', $lang);
        App::setLocale($lang);
        return back();
    }

    public function privacy()
    {
        return Inertia::render('Privacy');
    }

    public function terms()
    {
        return Inertia::render('Terms');
    }
}
