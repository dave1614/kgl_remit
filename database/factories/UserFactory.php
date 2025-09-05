<?php

namespace Database\Factories;

use App\Functions\UsefulFunctions;
use App\Models\InecWard;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected ?string $password = null;

    /**
     * The ward to be used for all users in this batch.
     */
    protected ?InecWard $ward = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Initialize the password once per batch
        if (!$this->password) {
            $this->password = bcrypt('password');  // Use bcrypt for faster seeding
        }

        $user_name = fake()->userName();

        return [
            'name' => fake()->name(),
            'user_name' => $user_name,
            'slug' => Str::slug($user_name), // Simplified slug generation
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => null,
            'country_id' => 151,
            'phone' => fake()->numerify('08#########'),
            'password' => $this->password,  // Reuse pre-generated password
            'remember_token' => Str::random(10),
            // 'created' => 1,
        ];

    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
