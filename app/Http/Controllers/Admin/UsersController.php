<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query = User::with(['accounts']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('email', 'LIKE', "%$keyword%");
            if (str($keyword)->startsWith('0x')) {
                $query->orWhereHas('accounts', fn ($q) => $q->where('address', 'LIKE', "%$keyword%"));
            }
        }
        $usersItems = $query->latest()->paginate($perPage);
        $users = UserResource::collection($usersItems);
        return Inertia::render('Admin/Users', ['users' => $users]);
    }
}
