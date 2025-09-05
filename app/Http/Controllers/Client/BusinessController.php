<?php

namespace App\Http\Controllers\Client;

use App\Functions\UsefulFunctions;
use App\Http\Controllers\Controller;
use App\Models\BusinessRegistration;
use App\Models\InecLga;
use App\Models\InecState;
use App\Models\User;
use App\Notifications\AdminBusinessRegisteredNotification;
use App\Notifications\BusinessRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BusinessController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $registration = $user->businessRegistrations()->first();

        // If no registration exists, create empty (should normally exist)
        if (!$registration) {
            $registration = $user->businessRegistrations()->create([]);
        }

        // Redirect logic based on registration status
        if ($registration->status === 'pending') {
            // Pending → waiting/limbo page
            return redirect()->route('client.business.limbo', ['status' => 'pending']);
        }

        if ($registration->status === 'approved' && $user->business_registered) {
            // Fully registered → redirect to dashboard
            return redirect()->route('client.dashboard')->with('success', 'Your business is fully registered.');
        }

        // Otherwise (rejected or new registration) → show create page
        // Prepare states and default state
        $statesQuery = InecState::where('id', '!=', 0)->orderBy('name', 'ASC');
        $states = $statesQuery->get();
        $state = $registration->state_id ? InecState::find($registration->state_id) : $states->first();

        // Prepare cities based on state
        $citiesQuery = InecLga::where('inec_state_id', $state->id)->orderBy('name', 'ASC');
        $cities = $citiesQuery->get();
        $city = $registration->city_id ? InecLga::find($registration->city_id) : $cities->first();

        return Inertia::render('Client/Business/Create', [
            'year' => date('Y'),
            'registration' => $registration,
            'props_states' => $states,
            'props_cities' => $cities,
            'state' => $state->id,
            'city' => $city->id,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $registration = $user->businessRegistrations()->first();

        // Validation rules: all fields required
        $rules = [
            'business_name' => 'required|string|max:255',
            'state' => 'required|exists:inec_states,id',
            'city' => 'required|exists:inec_lgas,id',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:20',
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'moa' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'cosc' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'valid_id_type' => 'required|string',
            'valid_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        $data = $request->validate($rules);

        // Process file uploads
        $fileFields = ['certificate', 'moa', 'cosc', 'valid_id'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($registration->$field) {
                    Storage::disk('public')->delete($registration->$field);
                }

                // Store new file in public disk
                $registration->$field = $request->file($field)
                    ->store("business_documents/{$user->id}", 'public');
            }
        }

        // Update other fields
        $registration->business_name = $data['business_name'];
        $registration->state_id = $data['state'];
        $registration->city_id = $data['city'];
        $registration->address = $data['address'];
        $registration->postal_code = $data['postal_code'];
        $registration->valid_id_type = $data['valid_id_type'];

        // Reset status to pending on submission
        $registration->status = 'pending';
        $registration->save();

        $admin_user = $this->functions->getAdminUser();

        Notification::send($user, new BusinessRegisteredNotification($user, $registration));

        Notification::send($admin_user, new AdminBusinessRegisteredNotification($registration, $user));

        return redirect()->route('client.business.limbo', ['status' => 'pending'])
            ->with('success', 'Business registration submitted successfully. Admin will review it.');
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
