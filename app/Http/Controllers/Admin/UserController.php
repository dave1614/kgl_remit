<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->has('status') ? $request->query('status') : 'all';
        $user_name = $request->has('user_name') ? $request->query('user_name') : NULL;

        return Inertia::render('Admin/Users/Index', [
            'user' => $user,
            'status' => $status,
            'user_name' => $user_name
        ]);
    }

    public function viewAll(Request $request)
    {
        $length = $request->query('length', 10);

        $users = User::with('country')
            ->addSelect('users.*')
            // search filters

            ->filterStatus($request->query('status'))
            ->filterUserName($request->query('user_name'))
            ->filterFullName($request->query('full_name'))
            ->filterPhone($request->query('phone'))
            ->filterEmail($request->query('email'))

            ->filterDate($request->query('date'))
            ->filterEmailVerifiedDate($request->query('email_verified_at'))
            ->filterBusinessRegisteredDate($request->query('business_registered_at'))
            ->filterBetweenDates($request->query('start_date'), $request->query('end_date'))

            ->orderBy('id','DESC')
            ->paginate($length)
            ->withQueryString();

        return $users;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
