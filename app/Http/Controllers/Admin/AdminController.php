<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FactoryType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Factory as ResourcesFactory;
use App\Models\Factory;
use File;
use Inertia\Inertia;
use Request;

class AdminController extends Controller
{
    /**
     * Show a List of tokens
     * @return Inertia\Response;
     */
    public function defi()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/Defi', compact('factories'));
    }
    /**
     * Show a List of tokens
     * @return Inertia\Response;
     */
    public function tokens()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/Tokens', compact('factories'));
    }

    /**
     * Show a List of Staking contracts
     * @return Inertia\Response;
     */
    public function staking()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/Staking', compact('factories'));
    }

    /**
     * Show a List of Staking contracts
     * @return Inertia\Response;
     */
    public function launchpads()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/Launchpads', compact('factories'));
    }

    /**
     * Display a listing of  fairlaunchpads.
     * @return Inertia\Response;
     */
    public function fairlaunch()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/Fairlaunch', compact('factories'));
    }

    /**
     * Display a listing of  airdrops.
     * @return Inertia\Response;
     */
    public function airdrops()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/Airdrops', compact('factories'));
    }

    /**
     * Display a listing of  airdrops.
     * @return Inertia\Response;
     */
    public function tokenlock()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/TokenLock', compact('factories'));
    }


    /**
     * Display a listing of liquiditylock.
     * @return Inertia\Response;
     */
    public function liquiditylock()
    {
        $factories = ResourcesFactory::collection(Factory::all());
        return Inertia::render('Admin/LiquidityLock', compact('factories'));
    }


    /**
     * Manage Token factory settings..
     * @return Inertia\Response;
     */
    public function users()
    {
        return Inertia::render('Admin/Users');
    }
}
