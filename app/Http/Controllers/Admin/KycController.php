<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessRegistration;
use App\Notifications\BusinessApprovedNotification;
use App\Notifications\BusinessDeletedNotification;
use App\Notifications\BusinessRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->has('status') ? $request->query('status') : 'all';
        $user_name = $request->has('user_name') ? $request->query('user_name') : NULL;

        return Inertia::render('Admin/Kyc/Index', [
            'user' => $user,
            'status' => $status,
            'user_name' => $user_name,
        ]);
    }

    public function viewPendingRegistrations(Request $request)
    {
        $user = Auth::user();

        $page = $request->query('page', 1);
        $length = $request->query("length", 10);

        $businesses = BusinessRegistration::with('user', 'state', 'city')
            ->addSelect('business_registrations.*')
            ->whereNotNull('status')
            ->filterStatus($request->query('status'))
            ->filterUserName($request->query('user_name'))
            ->filterFullName($request->query('full_name'))
            ->filterBusinessName($request->query('business_name'))
            ->filterPostalCode($request->query('postal_code'))
            ->filterEmail($request->query('email'))
            ->filterPhone($request->query('phone'))
            ->filterState($request->query('state'))
            ->filterCity($request->query('city'))
            ->filterDate($request->query('date'))
            ->filterBetweenDates($request->query('start_date'), $request->query('end_date'))
            ->orderBy("id", "DESC")
            ->paginate($length)
            ->withQueryString();

        return $businesses;
    }

    // Approve a pending business
    public function approve(Request $request, $id)
    {
        $business = BusinessRegistration::with('user')->findOrFail($id);

        // Only pending businesses can be approved
        if ($business->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This business is not pending approval.'
            ], 400);
        }

        // Update status
        $business->status = 'approved';
        $business->approved_at = now();
        $business->save();

        // Optionally mark user's business_registered flag
        $business->user->business_registered = 1;
        $business->user->business_registered_at = now();
        $business->user->save();

        // Send notification to the user
        Notification::send($business->user, new BusinessApprovedNotification($business));

        return response()->json([
            'success' => true,
            'message' => 'Business approved successfully.'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $business = BusinessRegistration::with('user')->findOrFail($id);

        if ($business->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This business cannot be rejected.'
            ], 400);
        }

        // Ensure reason is provided
        $reason = $request->input('reason');
        if (!$reason) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection reason is required.'
            ], 400);
        }

        $business->status = 'rejected';
        $business->rejection_reason = $reason;
        $business->rejected_at = now();
        $business->save();

        // Notify the user
        Notification::send($business->user, new BusinessRejectedNotification($business));

        return response()->json([
            'success' => true,
            'message' => 'Business rejected successfully.'
        ]);
    }


    public function delete(Request $request, $id)
    {
        $business = BusinessRegistration::with('user')->findOrFail($id);
        $user = $business->user;
        $businessName = $business->business_name;

        DB::transaction(function () use ($business, $user, $businessName) {

            if ($user) {
                // Delete the user's business documents folder
                $folderPath = "public/business_documents/{$user->id}";
                if (Storage::exists($folderPath)) {
                    Storage::deleteDirectory($folderPath);
                }

                // Optionally notify the user before deletion (if user not yet deleted)
                Notification::send($user, new BusinessDeletedNotification($business));

                // Delete the associated user account
                $user->delete();
            }

            // Delete the business record
            $business->delete();


        });

        return response()->json([
            'success' => true,
            'message' => 'Business, user account, and all documents deleted successfully.'
        ], 200);
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
