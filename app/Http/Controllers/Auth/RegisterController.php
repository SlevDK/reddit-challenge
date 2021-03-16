<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Only guests allowed here.
     *
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show registration page.
     *
     * @return View
     */
    public function registerPage(): View
    {
        return view('register-page');
    }

    /**
     * Store user to the database and return redirect to the login page.
     *
     * @param RegistrationRequest $request
     * @return RedirectResponse
     */
    public function register(RegistrationRequest $request): RedirectResponse
    {
        $user = $this->createUser($request->all());
        auth()->login($user);

        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Store user to the database.
     *
     * @param array $data
     * @return mixed
     */
    protected function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
