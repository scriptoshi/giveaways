<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Social;
use Illuminate\Http\Request;

class SocialsController extends Controller
{
    // register clicks to external sites
    public function go(Request $request, $to)
    {
        $social = Social::where('code', $to)->firstOrFail();
        $social->increment('clicks');
        return Inertia::location($social->link);
    }
}
