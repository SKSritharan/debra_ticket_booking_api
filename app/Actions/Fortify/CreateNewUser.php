<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $roleValidationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'role_id' => ['required'],
        ];

        // Add additional validation rules for 'company_name' and 'contact_number' if role is 'partner' (ID 2)
        if ($input['role_id'] == 2) {
            $roleValidationRules['company_name'] = ['required'];
            $roleValidationRules['contact_number'] = ['required', 'regex:/^\+(?:[0-9] ?){6,14}[0-9]$/',];
        }

        Validator::make($input, $roleValidationRules)->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role_id' => $input['role_id'],
            'password' => Hash::make($input['password']),
        ]);

        // Create a partner record only if the role is 'partner'
        if ($input['role_id'] == 2) {
            $partner = $user->partner()->create([
                'user_id' => $user->id,
                'contact_number' => $input['contact_number'],
                'company_name' => $input['company_name'],
                'status' => true,
            ]);
        }

        return $user;
    }
}
