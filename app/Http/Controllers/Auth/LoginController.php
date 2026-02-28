<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    {
        ActivityLog::log('login', 'User logged in: ' . $user->name . ' (' . $user->email . ')');

        return redirect()->intended($this->redirectPath())
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        ActivityLog::log('logout', 'User logged out: ' . auth()->user()->name . ' (' . auth()->user()->email . ')');

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse($request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->with('error', 'Invalid email or password. Please try again.');
    }
}
