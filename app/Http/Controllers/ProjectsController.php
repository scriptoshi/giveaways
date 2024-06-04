<?php

namespace App\Http\Controllers;

use App\Actions\Social;
use App\Actions\Uploads;
use App\Http\Controllers\Controller;
use App\Http\Resources\Giveaway as ResourcesGiveaway;
use App\Http\Resources\Project as ProjectResource;
use App\Models\Giveaway;
use App\Models\Project;
use Inertia\Inertia;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $order = $request->get('order', 'created');
        $by = $request->get('by', 'latest');
        $perPage = 25;
        $query = Project::with(['logo'])
            ->withExists('launchpad as sale')
            ->withCount([
                'questers as followers',
                'giveaways as totalGiveaways',
                'giveaways as activeGiveaways' => fn (Builder $q) => $q->where('ends_at', '>=', now())->where('live', true),
            ])
            ->withSum('giveaways as sleep', 'sleep')
            ->withSum(['giveaways as totalPrize' => fn (Builder $q) => $q->where('ends_at', '>=', now())->where('live', true)], 'fee');
        if (!empty($keyword)) {
            $query
                ->where('name', 'LIKE', "%$keyword%")
                ->where('description', 'LIKE', "%$keyword%");
        }
        $orderColumn = match ($order) {
            'prize' => 'totalPrize',
            'joined' => 'followers',
            default => 'created_at',
        };
        if ($by == 'oldest') {
            $query->oldest($orderColumn);
        } else {
            $query->latest($orderColumn);
        }
        $giveawaysItems = $query->paginate($perPage);
        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection($giveawaysItems),
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
                return ProjectResource::collection($list);
            }
        ]);
    }




    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Project $project)
    {
        $project->load([
            'logo',
            'socials',
            'launchpad'
        ]);
        $project->loadCount([
            'questers as followers',
            'giveaways as totalGiveaways',
            'giveaways as activeGiveaways' => fn (Builder $q) => $q->where('ends_at', '>=', now())->where('live', true),
        ]);
        $project->loadSum('giveaways as totalPrize', 'fee');
        $project->loadSum('giveaways as sleep', 'sleep');
        $keyword = $request->get('search');
        $order = $request->get('order', 'created');
        $by = $request->get('by', 'latest');
        $perPage = 25;
        $query = $project->giveaways()
            ->withCount([
                'questers as totalParticipants',
                'quests as totalTasks',
            ]);
        if (!empty($keyword)) {
            $query->where('brief', 'LIKE', "%$keyword%");
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

        return Inertia::render('Projects/Show', [
            'project' => new ProjectResource($project),
            'giveaways' => ResourcesGiveaway::collection($giveawaysItems),
            'popular' => function () use ($request) {
                $list = Giveaway::with(['project.logo'])
                    ->withCount([
                        'questers as totalParticipants',
                        'quests as totalTasks',
                    ])
                    ->latest()
                    ->take(10)
                    ->get();
                return ResourcesGiveaway::collection($list);
            }
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $project->load(['logo', 'socials']);
        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $upload = app(Uploads::class);
        $request->validate([
            'description' => 'required|string',
            ...$upload->validation('logo'),
            //links
            'discord' => 'nullable|string',
            'facebook' => 'nullable|string',
            'github' => 'nullable|string',
            'instagram' => 'nullable|string',
            'medium' => 'nullable|string',
            'reddit' => 'nullable|string',
            'twitter' => 'nullable|string',
            'tiktok' => 'nullable|string',
            'telegram' => 'nullable|string',
            'youtube' => 'nullable|string',
            'youtube_trailer' => 'nullable|string',
            'website' => 'nullable|string',
            'docs' => 'nullable|string',
            'whitepaper' => 'nullable|string',
        ]);
        $project->description = $request->description;
        $project->save();
        app(Social::class)->update($request, $project);
        app(Uploads::class)->upload($request, $project, 'logo');
        return back()->with('success', 'Project updated!');
    }


    public function checkUsername(Request $request)
    {
        if (!$request->username)
            return ['error' => null, 'verified' => false];
        if (strlen($request->username) < 4)
            return ['error' => __('Must be more than 3 letters'), 'verified' => false];
        $exists = Project::where('slug', $request->username)->exists();
        if ($exists)
            return ['error' => __(':username is taken', ['username' => $request->username]), 'verified' => false];
        return ['error' => null, 'verified' => true];
    }
}
