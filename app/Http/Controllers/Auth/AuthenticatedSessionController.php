<?php

namespace App\Http\Controllers\Auth;

use App\Functions\UsefulFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Country;
use App\Models\MlmDb;
use App\Models\RegistrationVoucher;
use App\Models\User;

use App\Rules\CountryRule;
use App\Rules\RegiPlacementRule;
use App\Rules\VerifyUnusedRegVoucherRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as Support_Request;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }




    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // return Inertia::render('Auth/Login', [
        //     'canResetPassword' => Route::has('password.request'),
        //     'status' => session('status'),
        // ]);

        $props['countries'] = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();
        return Inertia::render('Auth/NewerLogin', $props);
    }

    public function submitProofOfPaymentToAdmin(Request $req)
    {
        $user = Auth::user();
        if ($user->created == 0) {
            $date = date("j M Y");
            $time = date("h:i:sa");
            $post_data = (object) $req->input();
            // return json_encode($post_data);

            $user_id = $user->id;

            $response_arr = array('success' => false, 'url' => '', 'messages' => '', "errors" => "", "empty" => true, "only_one_image" => false);



            if (!empty($_FILES['image']['name'])) {
                $response_arr['empty'] = false;
                $image_files_count = count(array($_FILES['image']['name']));

                if ($image_files_count == 1) {
                    $response_arr['only_one_image'] = true;

                    // $req->validate([
                    // 'image' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048'
                    // ]);

                    $validationRules = [
                        'image' => 'required|mimes:png,jpeg,jpg,gif,webp|max:4000',
                        'amount' => 'required|numeric',
                        'depositors_name' => 'required|max:150',
                        'date_of_payment' => 'required',

                    ];

                    $messages = [];

                    $validation = Support_Request::validate($validationRules);


                    if ($validation) {

                        // $fileModel = new FileUpload;

                        // echo "string";
                        if ($req->file('image')) {
                            // echo "string";
                            $image = $req->file('image');

                            if ($_SERVER['SERVER_NAME'] == "127.0.0.1") {
                                $fileName = time() . '_' . $image->getClientOriginalName();
                                $filePath = $image->storePubliclyAs('/public/images', $fileName);
                                $extension = $image->getClientOriginalExtension();
                                $filePath = str_replace('public', 'storage', $filePath);

                                $file = public_path($filePath);
                            } else {
                                $fileName = time() . '_' . $image->getClientOriginalName();
                                $filePath = $image->storeAs('images', $fileName, 'public_uploads');
                                // $path = $request->photo->storeAs('images', 'filename.jpg', 'public_uploads');
                                // echo $filePath . "<br>";
                                $extension = $image->getClientOriginalExtension();
                                $filePath = str_replace('public', 'storage', $filePath);

                                $file = base_path('storage/' . $filePath);
                            }
                            if (file_exists($file)) {


                                // $response_arr['image_name'] = $fileName;

                                // $response_arr['success'] = true;

                                $amount = $post_data->amount;
                                $depositors_name = $post_data->depositors_name;
                                $date_of_payment = $post_data->date_of_payment;


                                $form_array = array(
                                    'user_id' => $user_id,
                                    'amount' => $amount,
                                    'depositors_name' => $depositors_name,
                                    'date_of_payment' => $date_of_payment,
                                    'image' => $fileName,
                                    'date' => $date,
                                    'time' => $time
                                );



                                if ($this->functions->addPaymentProofRequest($form_array)) {
                                    $response_arr['success'] = true;
                                } else {
                                    $file_path = public_path() . '/storage/images/' . $fileName;
                                    unlink($file_path);
                                }
                            } else {
                                $response_arr['image_errors'] = 'We ran into some errors when uploading your file';
                            }
                        }
                    }
                }
            }


            return back()->with('data', $response_arr);
        }
    }



    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function store(LoginRequest $request)
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }

    public function store(Request $request)
    {
        $rules = [
            'login' => ['required'],
            'password' => ['required'],
        ];

        $messages = [
            'login.required' => 'The username or email field is required.',
            'password.required' => 'The password field is required.',
        ];

        //Validate input

        $validator = (!$request->wantsJson()) ? $request->validate($rules, $messages) : Validator::make(
            $request->all(),
            $rules,
            $messages
        );

        if ($request->wantsJson() && $validator->fails()) {
            return response()->json($validator->errors());
        }

        //Check if login is email or username
        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'user_name';

        // Store user input in user_data variable
        $user_data = array(
            $login_type  => $request->input('login'),
            'password' => $request->input('password')
        );

        //get the remember_me param
        $remember_me  = (!empty($request->remember_me)) ? TRUE : FALSE;


        // Check if username exists
        $user = User::where($login_type, $user_data[$login_type])->first();

        if (!$user) {
            return (!$request->wantsJson()) ? back()->with('error', 'This user does not exist') : response()->json(['error' => 'This user does not exist']);
        }

        // Check if login details are valid. If valid, redirect to admin page
        if (Auth::attempt($user_data)) {
            // $user = User::where([$login_type => $user_data[$login_type]])->first();

            $now = DB::raw('NOW()');
            $user->last_activity = $now;
            $user->save();

            $redirect_route = $user->is_admin == 1 ? 'admin.dashboard' : 'client.dashboard';

            $token = (!$request->wantsJson()) ? Auth::login($user, $remember_me) : $user->createToken('my-app-token')->plainTextToken;

            return (!$request->wantsJson()) ? redirect()->route($redirect_route)->with('success', 'Login Successful') : response()->json(['success' => true, 'user' => $user, 'token' => $token], 201);
        }

        return (!$request->wantsJson()) ? back()->with('error', 'Invalid login details') : response()->json(['error' => 'Invalid login details']);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
