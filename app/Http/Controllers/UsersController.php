<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as ResourcesUser;
use App\Models\User;
use Inertia\Inertia;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('show');
    }

    public function dashboard(Request $request)
    {
    }

    public function readAllNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
    }


    public function notifications(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->get();
        return ['count' => $notifications->count(), "notifications" => $notifications];
    }

    public function readNotification(Request $request, $notification)
    {
        $notification = $request->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();
        return back();
    }


    public function multiple(Request $request)
    {
        $request->validate([
            'multiple' => 'boolean|required'
        ]);
        $user = $request->user();
        $user->use_multiple_accounts =  $request->multiple;
        $user->save();
        $action = $request->multiple ? __('Activated') : __('Deactivated');
        return back()->with('success', __('Mutitple accounts :action Successfully', ['action' => $action]));
    }

    public function deleteAccount(Request $request, $id)
    {
        $account = $request->user()->accounts()->findOrFail($id);
        $account->delete();
        return back()->with('success', 'Account Removed successfully');
    }


    function referrals(Request $request)
    {
        $perPage = 25;
        $accounts = $request->user()->accounts->pluck('address');
        $referralItems = User::with(['account'])
            ->whereIn('referral', $accounts->all())
            ->latest()
            ->paginate($perPage);
        $referrals = ResourcesUser::collection($referralItems);
        $counts  = User::whereIn('referral', $accounts->all())
            ->groupBy('referral')
            ->selectRaw('count(*) as total, referral')
            ->get();
        $refLinks = $accounts->map(function ($account) use ($counts) {
            $count = $counts->first(fn ($c) => $c->referral == $account);
            return [
                'refLink' => route('home', ['r' => $account]),
                'account' => $account,
                'count' => $count?->total ?? 0,
            ];
        });
        return Inertia::render('Referrals/Index', compact('referrals', 'refLinks'));
    }

    public function setChain(Request $request)
    {
        if ($request->chain)
            $request->session()->put('chainId', $request->chain);
        if ($request->coin)
            $request->session()->put('coinId', $request->coin);
        return back();
    }
}
