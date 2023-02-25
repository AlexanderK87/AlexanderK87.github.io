<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Http\Request;

class CredoController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login() : string
    {
        return Auth::user()->name;
    }

    public function index()
    {
        if(Auth::check()) {return view('home', ['user' => $this->user->infoUser($this->login()), 'base' => $this->user->infoBase()]);}
        return redirect()->intended('auth.user');
    }

    public function get_present()
    {
        $this->user->getPresent();
        return $this->index();
    }

    public function refuse()
    {
        $this->user->refusePresent();
        return redirect()->route('home');
    }

    public function take_present(Request $request) {
        if (session('money')) {session(['obmen' => true]); return $this->index();}
        $this->user->updateBalanceBalli($this->login());
        if (session('gift')) {$this->user->updateBalanceGift($this->login());}
        $this->user->clearSession();
        return redirect()->route('home');
    }

    public function get_action_money()
    {
        session(['obmen' => false]);
        $this->user->updateBalanceMoney($this->login());
        return redirect()->route('home');
    }

    public function get_action_balli()
    {
        session(['balli' => 2*session('money')]);
        $this->user->updateBalanceBalli($this->login());
        $this->user->updateBalanceMoney($this->login());
        session(['obmen' => false]);
        return redirect()->route('home');
    }

}
