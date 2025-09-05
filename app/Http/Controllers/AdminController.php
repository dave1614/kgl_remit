<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\Country;
use App\Models\User;
use App\Rules\PhoneEditProfRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();


    }

    public function getUserData(Request $request, User $user)
    {

        $user->country_name = Country::find($user->country_id)->name;
        $response_arr = ['success' => true, 'user' => $user];
        return $response_arr;
        // return back()->with('data', $response_arr);
    }

    public function loadAdminEditUserProfilePage(Request $request, User $user)
    {

        $props = [];
        $real_countries = [];

        $countries = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();

        $i = -1;
        foreach ($countries as $country) {
            $i++;
            $id = $country->id;
            $name = $country->name;
            $phone_code = $country->phone_code;

            $real_countries[] = [
                'id' => $id,
                'label' => $name . ' (' . $phone_code . ')',
            ];

            if ($id == $user->country_id) {
                $props['selected_country'] = $i;
            }
        }

        $props['user'] = $user;

        $props['countries'] = $real_countries;

        return Inertia::render('Admin/EditUserProfile', $props);
    }


    public function processEditUsersProfile(Request $request, User $user)
    {

        $response_arr = ['success' => false, 'email_changed' => false];

        $rules = [
            'email' => 'required|string|email|max:255',
            // 'country' => ['required', new CountryRule],
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'min_digits:7', 'max_digits:15', new PhoneEditProfRule($user->id)],
            'address' => 'required|string|max:1000',

        ];

        $messages = [
            'sponsor_user_name.exists' => 'This user does not exist.',
        ];

        $request->validate($rules, $messages);

        // $user->country_id = $request->country['id'];
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;


        if ($user->email != $request->email) {
            $response_arr['email_changed'] = true;
            $user->newEmail($request->email);
        }

        $user->save();

        $response_arr['success'] = true;

        return back()->with('data', $response_arr);
    }

    public function loadMembersListPage(Request $request)
    {
        $user = Auth::user();

        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $users = User::where('is_admin', 0);

        $users = $users->join('countries', 'users.country_id', '=', 'countries.id')->addSelect('countries.name as country_name');
        $users = $users->addSelect('users.*');

        $users = $request->query('name') ? $users->where('users.name', 'like', '%' . $request->query('name') . '%') : $users;
        $users = $request->query('user_name') ? $users->where('users.user_name', 'like', '%' . $request->query('user_name') . '%') : $users;

        $users = $request->query('country') ? $users->where('users.country_id', $request->query('country')['id']) : $users;
        $users = $request->query('phone') ? $users->where('users.phone', 'like', '%' . $request->query('phone') . '%') : $users;
        $users = $request->query('email') ? $users->where('users.email', 'like', '%' . $request->query('email') . '%') : $users;
        $users = !empty($request->query('created_date')) && $request->query('created_date') != "" ? $users->where('users.created_date', 'like', date("j M Y", strtotime($request->query('created_date')))  . '%') : $users;

        $users = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $users->whereBetween('users.created_at', [$request->query('start_date'), $request->query('end_date')]) : $users;



        $users = $users->orderBy("name", "ASC")
            ->paginate($length)->withQueryString();




        // return $users;


        $real_countries = [];

        $countries = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();

        $i = -1;
        foreach ($countries as $country) {
            $i++;
            $id = $country->id;
            $name = $country->name;
            $phone_code = $country->phone_code;

            $real_countries[] = [
                'id' => $id,
                'label' => $name . ' (' . $phone_code . ')',
            ];

            // $props['selected_country'] = !isset($props['selected_country']) && $id == $user->country_id = $i;
            if ($id == $user->country_id) {
                $props['selected_country'] = $i;
            }
        }


        $props['countries'] = $real_countries;
        $props['users'] = $users;
        $props['length'] = $length;
        $props['name'] = $request->query('name') ? $request->query('name') : NULL;
        $props['user_name'] = $request->query('user_name') ? $request->query('user_name') : NULL;

        $props['country'] = $request->query('country') ? $request->query('country') : $real_countries[$props['selected_country']];
        $props['phone'] = $request->query('phone') ? $request->query('phone') : NULL;
        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['created_date'] = $request->query('created_date') ? $request->query('created_date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/MembersList", $props);
    }

}
