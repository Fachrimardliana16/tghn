<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_admin' => true], $this->remember)) {
            session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        $this->addError('email', 'Email atau password salah, atau Anda bukan admin.');
    }

    public function render()
    {
        return view('livewire.admin.login')
            ->layout('components.layouts.guest');
    }
}
