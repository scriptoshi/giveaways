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
            'projects' => function () use ($request) {
                $projects = Project::query()->withSum([
                    'giveaways as totalPrize' => fn (Builder $q) => $q->where('ends_at', '>=', now())->where('sleep_balance', '>=', 0)
                ], 'prize')
                    ->withCount(['questers as totalParticipants'])
                    ->take(10)
                    ->get();
                return  ResourcesProject::collection($projects);
            },
            // giveaways
            'giveaways' =>  function () use ($request) {
                $keyword = $request->get('search');
                $q = $request->get('q');
                $perPage = 25;
                $query =  Giveaway::query()
                    ->with([
                        'project.logo'
                    ])
                    ->withCount(['questers as totalParticipants']);
                if (auth()->check()) {
                    $user = $request->user();
                    $query->withExists([
                        'questers as quested' => fn (Builder $q) => $q->where('user_id', $user->id),
                    ]);
                    if ($q == 'mine') $query->whereHas('project', fn (Builder $query) => $query->where('user_id', $user->id));
                }
                if (!empty($keyword)) {
                    $query->whereHas('project', function (Builder $query) use ($keyword) {
                        $query->orWhere('name', 'LIKE', "%$keyword%")
                            ->orWhere('description', 'LIKE', "%$keyword%");
                    });
                }
                match ($q) {
                    'top' => $query->latest('prize'),
                    'sleep' => $query->latest('sleep'),
                    'popular' => $query->latest('totalParticipants'),
                    default => $query->latest()
                };
                $giveaways = $query->paginate($perPage);
                return ResourcesGiveaway::collection($giveaways);
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
