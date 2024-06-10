<?php

namespace App\Http\Controllers;

use App\Enums\GiveawayStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Giveaway as ResourcesGiveaway;
use App\Http\Resources\PublicUser;
use App\Http\Resources\User as ResourcesUser;
use App\Models\Giveaway;
use App\Models\Project;
use App\Models\User;
use App\Models\Account;
use App\Support\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
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
        $accounts = $request->user()
            ->accounts()
            ->whereNull('code')
            ->get();
        $accounts->each(function (Account $account) {
            $account->update([
                'code' => Utils::uniqidID(10)
            ]);
        });
        $codes = $request->user()->accounts()->pluck('code');
        $giveaways = Giveaway::query()
            ->with('project')
            /* ->whereHas('project', function (Builder $query) use ($codes) {
                $query->whereIn('code', $codes->all());
            })*/
            ->latest()
            ->paginate($perPage);
        $referrals = ResourcesGiveaway::collection($giveaways);
        $counts  = Project::query()
            ->whereIn('code', $codes->all())
            ->groupBy('code')
            ->selectRaw('count(*) as total, code')
            ->get();
        $total = Project::query()
            ->whereIn('code', $codes->all())
            ->count();
        $totalGiveways = Giveaway::query()
            ->where('status', GiveawayStatus::PAID)
            ->whereHas('project', function (Builder $query) use ($codes) {
                $query->whereIn('code', $codes->all());
            })->count();
        $sumGiveways = Giveaway::query()
            ->where('status', GiveawayStatus::PAID)
            ->whereHas('project', function (Builder $query) use ($codes) {
                $query->whereIn('code', $codes->all());
            })->sum('paid');
        $refLinks = $codes->map(function ($account) use ($counts) {
            $count = $counts->first(fn ($c) => $c->code == $account);
            return [
                'refLink' => route('home', ['bonus' => $account]),
                'code' => $account,
                'count' => $count?->total ?? 0,
            ];
        });
        return Inertia::render('Referrals/Index', compact(
            'referrals',
            'refLinks',
            'total',
            'totalGiveways',
            'sumGiveways',
        ));
    }

    public function setChain(Request $request)
    {
        if ($request->chain)
            $request->session()->put('chainId', $request->chain);
        if ($request->coin)
            $request->session()->put('coinId', $request->coin);
        return back();
    }

    public function activate(Request $request)
    {
        $request->validate(['code' => 'exists:users,otp'], ['code' => 'Invalid activation code']);
        if ($request->user()->otp !=  $request->code)
            throw ValidationException::withMessages(['code' => ['Invalid activation code']]);
        $user = $request->user();
        $user->otp = null;
        $user->email_verified_at = now();
        $user->save();
        return back();
    }
}
