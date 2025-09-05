<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\KycController as AdminKycController;
use App\Http\Controllers\Admin\RateController as AdminRateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArtisanTestController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Client\BankAccountController;
use App\Http\Controllers\Client\BusinessController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\KycController;
use App\Http\Controllers\Client\LocationController;
use App\Http\Controllers\Client\RateController;
use App\Http\Controllers\Client\ReceiptController;
use App\Http\Controllers\Client\TransactionController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\MainPageController;

use App\Http\Controllers\NotificationsController;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Route::get('/dashboard', function () {
//     // return Inertia::render('Dashboard');
//     return Inertia::render('HomeView');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/artisan_test', [ArtisanTestController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/tables', function () {
    return Inertia::render('TablesView');
})->name('tables');

Route::get('/forms', function () {
    return Inertia::render('FormsView');
})->name('forms');

Route::get('/ui', function () {
    return Inertia::render('UiView');
})->name('ui');

Route::get('/responsive', function () {
    return Inertia::render('ResponsiveView');
})->name('responsive');

Route::get('/profile', function () {
    return Inertia::render('ProfileView');
})->name('profile');

Route::get('/sign_in', function () {
    return Inertia::render('Auth/Login');
})->name('sign_in');

Route::get('/error', function () {
    return Inertia::render('ErrorView');
})->name('error');



require __DIR__.'/auth.php';

Route::post('/submit_front_page_message', [MainPageController::class, 'submitFrontPageMessage'])->name('submit_front_page_message');

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/notifications/', [NotificationsController::class, 'dashboardNotiIndex'])->name('dashboard.notifications');

    Route::scopeBindings()->name('notification.')->group(function () {
        Route::get('/notifications/all', [NotificationsController::class, 'index'])->name('index');
        Route::get('/notification/{notification}', [NotificationsController::class, 'show'])->name('show');
    });
});


//Clien routes
Route::prefix('client')->name('client.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/business/limbo/{status}', function ($status) {

        $user = Auth::user();
        $user = User::find($user->id);
        $registration = $user->businessRegistrations()->first();


        if ($registration->status === 'rejected') {
            return redirect()->route('client.business.create');
        }

        if ($registration->status === 'approved' && $user->business_registered) {
            // Fully registered → redirect to dashboard
            return redirect()->route('client.dashboard')->with('success', 'Your business is fully registered.');
        }

        return inertia('Client/Business/Limbo', [
            'status' => $status,
        ]);
    })->name('business.limbo');

    Route::get('/business/register', [BusinessController::class, 'create'])->name('business.create');
    Route::post('/business/register', [BusinessController::class, 'store'])->name('business.store');

    Route::name('location.')->group(function () {
        Route::post('/location/get_new_lgas_and_wards/{state}', [LocationController::class, 'getNewLgasAndWards'])->name('get_new_lgas_and_wards');


        Route::post('/location/get_new_wards/{lga}', [LocationController::class, 'getNewWards'])->name('get_new_wards');


    });
});




// Route::middleware(['auth', 'verified', 'admin'])->group(function () {


//     Route::get('/view_members_list', [AdminController::class, 'loadMembersListPage'])->name('view_members_list');

//     Route::get('/admin_edit_user_profile/{user}', [AdminController::class, 'loadAdminEditUserProfilePage'])->name('admin_edit_user_profile');



//     Route::post('/process_edit_users_profile/{user}', [AdminController::class, 'processEditUsersProfile'])->name('process_edit_users_profile');
//     Route::post('/get_user_data/{user}', [AdminController::class, 'getUserData'])->name('get_user_data');

// });

// Admin Routes (prefix: admin, middleware: auth:admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.updateStatus');

    // KYC Verification
    Route::get('/kyc', [AdminKycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc/load_all', [AdminKycController::class, 'viewPendingRegistrations'])->name('load_all');


    Route::post('/kyc/{id}/approve', [AdminKycController::class, 'approve'])
    ->name('kyc.approve');
    Route::post('/kyc/{id}/reject', [AdminKycController::class, 'reject'])
    ->name('kyc.reject');
    Route::post('/kyc/{id}/delete', [AdminKycController::class, 'delete'])
    ->name('kyc.delete');


    // Transactions
    Route::get('/transactions', [AdminTransactionController::class, 'pending'])->name('transactions.index');

    Route::put('/transactions/{transaction}/approve', [AdminTransactionController::class, 'approve'])->name('transactions.approve');
    Route::put('/transactions/{transaction}/reject', [AdminTransactionController::class, 'reject'])->name('transactions.reject');

    // Invoices & Proofs
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');

    // Exchange Rates


    Route::get('rates', [AdminRateController::class, 'index'])->name('rates.index');
    Route::post('get_current_rates', [AdminRateController::class, 'getCurrentRates'])->name('rates.get_current');
    Route::post('rates', [AdminRateController::class, 'store'])->name('rates.store');
    Route::post('rates/update/{id}', [AdminRateController::class, 'update'])->name('rates.update');
    Route::post('rates/delete/{id}', [AdminRateController::class, 'destroy'])->name('rates.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'daily'])->name('reports.index');

    Route::get('/reports/audit-logs', [ReportController::class, 'auditLogs'])->name('reports.auditLogs');

    // Support
    // Route::get('/support/messages', [SupportController::class, 'messages'])->name('support.messages');
    // Route::post('/support/messages/reply/{message}', [SupportController::class, 'reply'])->name('support.messages.reply');

    // System Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/admins', [SettingController::class, 'storeAdmin'])->name('settings.admins.store');
    Route::put('/settings/admins/{admin}', [SettingController::class, 'updateAdmin'])->name('settings.admins.update');
    Route::delete('/settings/admins/{admin}', [SettingController::class, 'destroyAdmin'])->name('settings.admins.destroy');

    Route::get('/settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::put('/settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general.update');
});


// Route::middleware(['auth', 'verified', 'check_registration'])->group(function () {

//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
//     Route::get('/edit_profile', [ProfileController::class, 'edit'])->name('edit_profile');


//     Route::get('change_password', [ChangePasswordController::class, 'edit'])->name('change_password');



//     Route::get('referral_link', [DashboardController::class, 'showReferralLink'])->name('referral_link');
//     Route::get('about_us', [DashboardController::class, 'aboutUs'])->name('about_us');






//     Route::scopeBindings()->name('user.')->group(function () {
//         Route::get('/load_details/{user}', [UserController::class, 'loadDetails'])->name('load_details');

//     });


//     Route::get('/dashboard/notifications/', [NotificationsController::class, 'dashboardNotiIndex'])->name('dashboard.notifications');



//     Route::scopeBindings()->name('notification.')->group(function () {
//         Route::get('/notifications/all', [NotificationsController::class, 'index'])->name('index');
//         Route::get('/notification/{notification}', [NotificationsController::class, 'show'])->name('show');
//     });



//     Route::post('/load_more_posts_home', [HomeController::class, 'loadMorePosts'])->name('load_more_posts_home');

//     Route::post('/edit_profile', [ProfileController::class, 'update'])->name('process_edit_profile');
//     Route::post('change_password', [ChangePasswordController::class, 'update'])->name('process_change_password');

// });

// Client Routes (prefix: client, middleware: auth:user)
Route::prefix('client')->name('client.')->middleware(['auth', 'verified', 'ensure.business'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/bank-accounts', [BankAccountController::class, 'store'])->name('profile.bank.store');
    Route::delete('/profile/bank-accounts/{id}', [BankAccountController::class, 'destroy'])->name('profile.bank.destroy');
    Route::get('/profile/kyc', [KycController::class, 'index'])->name('profile.kyc.index');
    Route::post('/profile/kyc', [KycController::class, 'store'])->name('profile.kyc.store');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/new', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Invoices & Receipts
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');

    // Rates
    Route::get('/rates', [RateController::class, 'index'])->name('rates.index');




    Route::get('change_password', [ChangePasswordController::class, 'edit'])->name('change_password');


    // Route::scopeBindings()->name('notification.')->group(function () {
    //     Route::get('/notifications/all', [NotificationsController::class, 'index'])->name('index');
    //     Route::get('/notification/{notification}', [NotificationsController::class, 'show'])->name('show');
    // });


    // Support
    // Route::get('/support/messages', [SupportController::class, 'messages'])->name('support.messages');
    // Route::post('/support/messages', [SupportController::class, 'storeMessage'])->name('support.messages.store');
    // Route::get('/support/faqs', [SupportController::class, 'faqs'])->name('support.faqs');

    // Settings
    Route::get('/settings/security', [SettingController::class, 'security'])->name('settings.security');
    // Route::put('/settings/security', [SettingController::class, 'updateSecurity'])->name('settings.security.update');
    // Route::get('/settings/notifications', [SettingController::class, 'notifications'])->name('settings.notifications');
    // Route::put('/settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.notifications.update');
});

