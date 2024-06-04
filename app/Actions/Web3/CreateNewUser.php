<?php

namespace App\Actions\Web3;

use App\Models\Account;
use App\Models\Country;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Models\Team;
use App\Support\Galxe;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Jetstream;
use SWeb3\Utils;


class CreateNewUser implements CreatesNewUsers
{
    //use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $signature = new Signature;
        $input_address = Utils::toChecksumAddress($input['address']);
        $account = Account::with('user')->where('address', $input_address)->first();
        if (!is_null($account) &&  $signature->verify($account->user->nonce, $input['signature'], $input_address)) return $account->user;
        Validator::make($input, [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'address' => ['required', 'string', 'max:255', 'unique:accounts,address'],
            'signature' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($input_address, $signature) {
                    $nonce = session()->pull('nonce');
                    $signed = $signature->verify($nonce, $value, $input_address);
                    if (!$signed) {
                        $fail('The Signature is invalid sig: ' . $nonce . ' sig: ' . $value . ' address: ' . $input_address);
                    }
                },
            ],
        ])->validate();
        return DB::transaction(function () use ($input) {
            $dumm_pass = Str::random(20);
            $referral = '0x1c3cAB3A544c06306e6934902474dE0d88709f96';
            if (request()->hasCookie('referral')) {
                $referral = request()->cookie('referral');
            }
            return tap(User::create([
                'name' => $input['username'],
                'referral' => $referral,
                'email' => $input['email'],
                //'username' => $input['username'],
                'email_verified_at' => null,
                'nonce' => Str::random(20),
                //'is_admin' => false,
                'password' => Hash::make($dumm_pass),
            ]), function (User $user) use ($input) {
                $user->accounts()->save(new Account([
                    'address' => Utils::toChecksumAddress($input['address']),
                    'provider' => $input['provider'] ?? null
                ]));
                //$this->createTeam($user);
            });
        });
    }
    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $team =  new Team;
        $team->user_id = $user->id;
        $team->name = $user->name . " - Team";
        $team->personal_team = true;
        $team->save();
        $user->current_team_id = $team->id;
        $user->save();
        $user->ownedTeams()->save($team);
    }
}
