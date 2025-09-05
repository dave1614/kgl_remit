<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Functions\UsefulFunctions;
use App\Models\EarningToWalletHistory;
use App\Models\EasySavings;
use App\Models\SavingsDuration;
use App\Models\SavingsFrequency;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $props = [];
        $user = Auth::user();
        $user = User::find($user->id);
        $user_id = $user->id;


        return Inertia::render('Client/Dashboard', $props);



    }

    public function showReferralLink(Request $request){
        $user = Auth::user();
        $props['user'] = $user;
        return Inertia::render('ReferralLink',$props);
    }

    public function aboutUs(Request $request){
        $user = Auth::user();
        $props['user'] = $user;
        return Inertia::render('AboutUs',$props);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
