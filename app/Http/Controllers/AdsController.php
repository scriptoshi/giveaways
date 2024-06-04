<?php

namespace App\Http\Controllers;

use App\Actions\Uploads;
use App\Enums\AdCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ad as AdResource;
use App\Models\Ad;
use Inertia\Inertia;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class AdsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $order = $request->get('order', 'created');
        $by = $request->get('by', 'latest');
        $cat = $request->get('cat', null);
        $tag = $request->get('tag', null);
        $is_mine = $request->get('show', null) == 'mine';
        $query = Ad::query()->with([
            'user.account',
            'image'
        ])
            ->withCount([
                'escrows as totalOrders',
                'messages as comments',
            ])
            ->withAvg('messages as rating', 'rating');
        if (!empty($keyword)) {
            $query->where(function (Builder $query) use ($keyword) {
                $query->where('category', 'LIKE', "%$keyword%")
                    ->orWhere('tags', 'LIKE', "%$keyword%")
                    ->orWhere('title', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
            });
        }
        if (!empty($cat)) {
            $query->where('category',  $cat);
            if (!empty($tag)) {
                $query->where('tags', 'LIKE', "%$tag%");
            }
        }

        $orderColumn = match ($order) {
            'price' => 'price',
            'ratings' => 'rating',
            default => 'created_at',
        };
        if ($by == 'oldest') {
            $query->oldest($orderColumn);
        } else {
            $query->latest($orderColumn);
        }
        if ($is_mine & auth()->check()) {
            $query->where('user_id', $request->user()->id);
        }
        $adsItems = $query->latest()->paginate($perPage);
        return Inertia::render('Services/Index', [
            'services' => AdResource::collection($adsItems),
            'popular' => function () use ($request) {
                $list = Ad::with(['user', 'image'])
                    ->withCount([
                        'escrows as totalOrders',
                        'messages as comments',
                    ])
                    ->withAvg('messages as rating', 'rating')
                    ->latest()
                    ->take(10)
                    ->get();
                return AdResource::collection($list);
            },
            'categories' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->descriptions()]),
            'tags' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->tags()]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('Services/Create', [
            'categories' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->descriptions()]),
            'tags' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->tags()]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', new Enum(AdCategory::class)],
            'tags' => 'required|array',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'period' => 'required|string',
            'telegram' => 'nullable|string',
            'url' => 'nullable|string'
        ]);
        $ad = new Ad;
        $ad->user_id = $request->user()->id;
        $ad->category = $request->category;
        $ad->tags = $request->tags;
        $ad->title = $request->title;
        $ad->slug = str($request->title)->slug()->value();
        $ad->description = $request->description;
        $ad->price = $request->price;
        $ad->duration = $request->duration;
        $ad->period = $request->period;
        $ad->telegram = $request->telegram;
        $ad->url = $request->get('url');
        $ad->rating = 1;
        $ad->save();
        $ad->slug = $ad->slug . '-' . $ad->id;
        $ad->save();
        if ($request->filled('image_uri')) {
            app(Uploads::class)->upload($request,  $ad, 'image');
        }
        return redirect()->route('services.index')->with('success', 'Ad added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Ad $ad)
    {
        $ad->load(['image', 'messages', 'user.account',]);
        $ad->loadCount([
            'escrows as totalOrders',
            'messages as comments',
        ]);
        $ad->loadAvg('messages as ratings', 'rating');
        return Inertia::render('Services/Show', [
            'service' => new AdResource($ad),
            'popular' => function () use ($request) {
                $list = Ad::with(['user', 'image'])
                    ->withCount([
                        'escrows as totalOrders',
                        'messages as comments',
                    ])
                    ->withAvg('messages as rating', 'rating')
                    ->latest()
                    ->take(5)
                    ->get();
                return AdResource::collection($list);
            },
            'categories' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->descriptions()]),
            'tags' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->tags()]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Ad $ad)
    {
        $this->authorize('update', $ad);
        $ad->load('image');
        return Inertia::render('Services/Edit', [
            'service' => new AdResource($ad),
            'categories' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->descriptions()]),
            'tags' => collect(AdCategory::cases())->mapWithKeys(fn (AdCategory $cat) => [$cat->value => $cat->tags()]),
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
    public function update(Request $request, Ad $ad)
    {
        $this->authorize('update', $ad);
        $request->validate([
            'category' => ['required', 'string', new Enum(AdCategory::class)],
            'tags' => 'required|array',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'period' => 'required|string',
            'telegram' => 'nullable|string',
            'url' => 'nullable|string'
        ]);
        $ad->category = $request->category;
        $ad->tags = $request->tags;
        $ad->title = $request->title;
        $ad->description = $request->description;
        $ad->price = $request->price;
        $ad->duration = $request->duration;
        $ad->period = $request->period;
        $ad->telegram = $request->telegram;
        $ad->url = $request->get('url');
        $ad->save();
        if ($request->filled('image_uri')) {
            app(Uploads::class)->upload($request,  $ad, 'image');
        }
        return back()->with('success', 'Ad updated!');
    }

    /**
     * Store messages and ratings.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function message(Request $request, Ad $ad)
    {
        $request->validate([
            'message' => 'required|string',
            'rating' => 'nullable|numeric'
        ]);
        $ad->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'rating' => $request->rating,
            'is_reply' => false
        ]);
        return back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Ad $ad)
    {
        $this->authorize('delete', $ad);
        $ad->delete();
        return back()->with('success', 'Ad deleted!');
    }
}
