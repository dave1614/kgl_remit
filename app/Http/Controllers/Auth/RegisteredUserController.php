<?php

namespace App\Http\Controllers\Auth;

use App\Functions\UsefulFunctions;
use App\Http\Controllers\Controller;
use App\Models\BusinessRegistration;
use App\Models\City;
use App\Models\InecLga;
use App\Models\InecState;
use App\Models\InecWard;
use App\Models\State;
use App\Models\User;
use App\Notifications\AdminUsersRegisteredNotification;
use App\Rules\LgaVerificationRule;
use App\Rules\NoEmailPlusSign;
use App\Rules\RegiSponsorRule;
use App\Rules\StateVerificationRule;
use App\Rules\WardVerificationRule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{

    public $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }
    /**
     * Display the registration view.
     */
    public function create(Request $request)
    {
        $props['year'] = date('Y');


        $props['u'] = $request->u;
        // $props['u'] = $request->has('u') ? $request->u : 'admin';


        // return $props;

        return Inertia::render('Auth/NewerRegister', $props);
    }



    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        $date  = date('j M Y');
        $time = date('h:i:sa');
        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|alpha_dash|max:35|unique:' . User::class,
            'email' => ['required', 'string', 'lowercase', 'email', 'max:50', 'unique:' . User::class, new NoEmailPlusSign()],
            'phone' => 'required|max:16',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);



        $user = User::create([

            'name' => $request->name,
            'user_name' => strtolower($request->user_name),
            'slug' => $this->functions->generateUniqueSlugForUser($request->user_name),
            'email' => $request->email,
            'country_id' => 151,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(10),
            'created' => 0,
        ]);

        $business_registration = BusinessRegistration::create([
            'user_id'             => $user->id,
            'country_id'             => 151,
            'status' => null
        ]);

        event(new Registered($user));


        Auth::login($user);

        $admin_user = $this->functions->getAdminUser();

        $admin_user->notify(new AdminUsersRegisteredNotification($user, $date, $time));


        $redirect_route = $user->is_admin == 1 ? 'admin.dashboard' : 'client.dashboard';

        return redirect(route($redirect_route, absolute: false));
    }
}
