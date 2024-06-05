<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Http\Resources\Giveaway as ResourcesGiveaway;
use App\Http\Resources\Project as ResourcesProject;
use App\Models\Giveaway;
use App\Models\Project;
use Inertia\Inertia;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function home(Request $request)
    {
        return Inertia::render('Home', [
            // projects
            'popular' => function () use ($request) {
                $list = Project::with(['logo'])
                    ->withCount([
                        'questers as followers',
                        'giveaways as totalGiveaways',
                        'giveaways as activeGiveaways' => fn (Builder $q) => $q->where('ends_at', '>=', now())->where('live', true),
                    ])
                    ->withSum('giveaways as sleep', 'sleep')
                    ->withSum('giveaways as totalPrize', 'fee')
                    ->latest()
                    ->take(10)
                    ->get();
                return ResourcesProject::collection($list);
            },
            // giveaways
            'giveaways' =>  function () use ($request) {
                $keyword = $request->get('search');
                $order = $request->get('order', 'created');
                $by = $request->get('by', 'latest');
                $perPage = 25;
                $query = Giveaway::with(['project.logo'])
                    ->withCount('questers as totalParticipants');
                if (!empty($keyword)) {
                    $query->whereHas('project', fn (Builder $q) => $q->where('name', 'LIKE', "%$keyword%")->where('description', 'LIKE', "%$keyword%"));
                }
                $orderColumn = match ($order) {
                    'prize' => 'prize',
                    'winners' => 'num_winners',
                    'joined' => 'totalParticipants',
                    default => 'created_at',
                };
                if ($by == 'oldest') {
                    $query->oldest($orderColumn);
                } else {
                    $query->latest($orderColumn);
                }
                $giveawaysItems = $query->paginate($perPage);
                return ResourcesGiveaway::collection($giveawaysItems);
            }
        ]);
    }
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
