<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\ProfileResource;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('logout', 'me');
        $this->middleware('guest')->only('loginPage', 'login');
    }

    /**
     * Return login page view.
     *
     * @return View
     */
    public function loginPage()
    {
        return view('login-page');
    }

    /**
     * Authenticate user.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! auth()->attempt($credentials)) {
            return redirect()->back();
        }

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Log out current user.
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        auth()->logout();

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Get profile info.
     *
     * @return ProfileResource
     */
    public function me(): ProfileResource
    {
        return new ProfileResource(auth()->user());
    }
}
