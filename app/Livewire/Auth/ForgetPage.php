<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Forget Password')]

class ForgetPage extends Component
{

    public $email;

    public function save() {

        $this->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Password reset link has been sent to your email address successfully!');
            $this->email = '';
        }
    }


    public function render()
    {
        return view('livewire.auth.forget-page');
    }
}
