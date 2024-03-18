<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FactoryType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Badge as BadgeResource;
use App\Models\Badge;
use App\Models\Factory;

use Inertia\Inertia;

use Illuminate\Http\Request;

class BadgesController extends Controller
{
    /**
     * Display a listing of the badges.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query = Badge::query();
        if (!empty($keyword)) {
            $query->where('badge', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%");
        }
        $badgesItems = $query->latest()->paginate($perPage);
        $badges = BadgeResource::collection($badgesItems);
        return Inertia::render('Admin/Badges/Index', compact('badges'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return Inertia::render('Admin/Badges/Create');
    }


    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'badge' => 'required|string',
            'description' => 'required|string',
        ]);
        $badge = new Badge;
        $badge->badge = $request->badge;
        $badge->description = $request->description;
        $badge->active = true;
        $badge->save();
        return redirect()->route('admin.badges.index')->with('success', 'Badge added!');
    }




    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $badgeItem = Badge::findOrFail($id);
        $badge = new BadgeResource($badgeItem);
        return Inertia::render('Admin/Badges/Edit', compact('badge'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'badge' => 'required|string',
            'description' => 'required|string',
        ]);
        $badge = Badge::findOrFail($id);
        $badge->badge = $request->badge;
        $badge->description = $request->description;
        $badge->save();
        return redirect()->route('admin.badges.index', $badge->chainId)->with('success', __('Badge updated!'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, $id)
    {
        $badge = Badge::findOrFail($id);
        $badge->active = !$badge->active;
        $badge->save();
        return back()->with('success', $badge->active ? __(' :name Badge Enabled!', ['name' => $badge->badge]) : __(' :name Badge Disabled!', ['name' => $badge->badge]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $id)
    {
        $badge = Badge::find($id);
        $chainId = $badge->chainId;
        Badge::destroy($id);
        return redirect()->route('admin.badges.index', $chainId)->with('success', __('Badge deleted!'));
    }
}
