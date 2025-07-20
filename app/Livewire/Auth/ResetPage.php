<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Livewire\Component;

#[Title('Reset Password')]

class ResetPage extends Component
{

    public $token;
    #[Url]
    public $email;
    public $password;
    public $password_confirmation;

    public function mount($token) {
        $this->token = $token;
    }

    public function save() {
        $this->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6|max:255'
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token
            ],
            function(User $user, string $password) {
                $password = $this->password;
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );    

        if($status === Password::PASSWORD_RESET) {
            return redirect('/login');
        } else {
            session()->flash('error', 'Something went wrong');
        }
    
    }

    public function render()
    {
        return view('livewire.auth.reset-page');
    }
}
