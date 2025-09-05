<?php

namespace App\Functions;

use App\Models\BusinessRegistration;
use App\Models\User;
use Illuminate\Support\Str;

class UsefulFunctions
{

    public function getAdminUser(){
        return User::where('is_admin', 1)->first();
    }

    public function giveUsersInitBusinesses()
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->is_admin == 0) {
                BusinessRegistration::create([
                    'user_id' => $user->id,
                    'country_id' => 151,
                    'status' => null
                ]);
            }
        }
    }

    public function getAdminId()
    {

        $admin = User::where('is_admin', 1)->first();
        if ($admin) {
            return $admin->id;
        }
    }

    public function generateUniqueSlugForUser($user_name)
    {
        $slug = Str::slug($user_name);
        $user = User::where("slug", $slug)->first();
        if ($user) {
            $slug = $slug . "-" . rand(1000, 9999);
        }
        return $slug;
    }
}
