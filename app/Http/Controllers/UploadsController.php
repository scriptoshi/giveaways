<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Upload as UploadResource ;
use App\Models\Upload;
use Inertia\Inertia;

use App\Models\User;
use Illuminate\Http\Request;

class UploadsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        if (!empty($keyword)) {
            $uploadsItems  = Upload::with(['user','uploadable'])->where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('uploadable', 'LIKE', "%$keyword%")
                ->orWhere('key', 'LIKE', "%$keyword%")
                ->orWhere('url', 'LIKE', "%$keyword%")
                ->orWhere('path', 'LIKE', "%$keyword%")
                ->orWhere('drive', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $uploadsItems = Upload::with(['user','uploadable'])->latest()->paginate($perPage);
        }
        $uploads = UploadResource::collection($uploadsItems);
		$users = User::pluck('id','name')->all();

        return Inertia::render('Admin/Uploads/Index', compact('uploads','users'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$users = User::pluck('id','name')->all();

        return Inertia::render('Admin/Uploads/Create', compact('uploads','users'));
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'user_id' => 'required|integer|exists:users,id',
			'key' => 'required|true',
			'url' => 'required|true',
			'path' => 'required|true',
			'drive' => 'required|true'
		]);
        
        $upload = new Upload;
		$upload->user_id = $request->user_id;
		$upload->uploadable = $request->uploadable;
		$upload->key = $request->key;
		$upload->url = $request->url;
		$upload->path = $request->path;
		$upload->drive = $request->drive;
		$upload->save();
        return redirect()->route('bets.uploads.index')->with('success', 'Upload added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request , $id)
    {
        $upload = Upload::with(['user','uploadable'])->findOrFail($id);
        $uploadResource = new UploadResource($upload);
        return Inertia::render('Admin/Uploads/Show', compact('uploadResource'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request , $id)
    {
        $upload = Upload::findOrFail($id);
		$users = User::pluck('id','name')->all();

        $uploadResource = new UploadResource($upload);
        return Inertia::render('Admin/Uploads/Edit', compact('uploadResource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request , $id)
    {
        $request->validate([
			'user_id' => 'required|integer|exists:users,id',
			'key' => 'required|true',
			'url' => 'required|true',
			'path' => 'required|true',
			'drive' => 'required|true'
		]);
        
        $upload = Upload::findOrFail($id);
		$upload->user_id = $request->user_id;
		$upload->uploadable = $request->uploadable;
		$upload->key = $request->key;
		$upload->url = $request->url;
		$upload->path = $request->path;
		$upload->drive = $request->drive;
		$upload->save();
        return back()->with('success', 'Upload updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request , $id)
    {
        Upload::destroy($id);
        return redirect()->route('bets.uploads.index')->with('success', 'Upload deleted!');
    }
}
