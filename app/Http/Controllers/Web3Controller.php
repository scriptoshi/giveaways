<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Actions\Web3\Signature;
use App\Http\Resources\Account as ResourcesAccount;
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class Web3Controller extends AuthenticatedSessionController
{
    // get login nonce
    public function status(Request $request)
    {
        if (Auth::check()) return  ['register' => null];
        $account = Account::with('user')->where('address', $request->address)->first();
        if ($account)
            return  ['register' => null];
        return ['register' => route('register')];
    }

    public function nonce(Request $request)
    {
        if (Auth::check()) return  ['nonce' => $request->user()->getNonce()];
        $account = Account::with('user')->where('address', $request->address)->first();
        if ($account) return ['address' => route('login'), "register" => false, 'nonce' => $account->user->getNonce()];
        if ($request->session()->has('nonce')) {
            $nonce = $request->session()->get('nonce');
        } else {
            $nonce = Str::random(20);
            $request->session()->put('nonce', $nonce);
        }
        return ['address' => route('register'), "register" => true, 'nonce' => $nonce];
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function accounts(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $accountList = $request->user()->accounts()->latest()->paginate($perPage);
        $accounts = ResourcesAccount::collection($accountList);
        return Inertia::render('Profile/Accounts', compact('accounts'));
    }


    public function addAccount(Request $request)
    {
        $request->validate([
            'address' => 'unique:accounts,address',
            'provider' => 'string'
        ]);
        $user = $request->user();
        $verified = (new Signature)->verify($user->nonce, $request->signature, $request->address);
        if ($verified) {
            $user->accounts()->save(new Account(['address' => $request->address, 'provider' => $request->provider]));
            return back()->with('success', __('Account Added Successfully'));
        }
        return back()->with('error', __('Invalid Signature'));
    }

    public function deleteAccount(Request $request, $id)
    {
        if ($request->user()->accounts()->count() == 1) {
            return back()->with('error', __('Last account cannot be disconnected'));
        }
        $user = $request->user();
        $accounts = $request->user()->accounts()->pluck('address')->all();
        $verified = (new Signature)->verify($user->nonce, $request->signature, $accounts);
        if (!$verified)
            return back()->with('error', __('Invalid Signature'));
        $account = $request->user()->accounts()->findOrFail($id);
        $account->forceDelete();
        return back()->with('success', __('Account disconnected successfully'));
    }
}
