<?php

namespace Dcvn\Otp\Http\Controllers;

use Dcvn\Otp\OneTimePassword;
use Illuminate\Foundation\Auth\AuthenticatesUsers as FoundationAuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Extension for FoundationAuthenticatesUsers in the LoginController.
 */
trait AuthenticatesUsers
{
    use FoundationAuthenticatesUsers;

    /**
     * The request name for the OTP input.
     *
     * @return string
     */
    public function otp()
    {
        return 'otpcode';
    }

    /**
     * Show the application's login form.
     *
     * @override FoundationAuthenticatesUsers::showLoginForm()
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('otp::login');
    }

    /**
     * Validate the user login request.
     *
     * @override FoundationAuthenticatesUsers::validateLogin()
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password'        => 'required|string',
            $this->otp()      => 'nullable|digits:' . config('otp.digits'),
        ]);
    }

    /**
     * Attempt to verify the user authenticity by OTP.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function attemptOtp(Request $request) : bool
    {
        $user = $this->guard()->user();
        if (empty($user->otp_secret) && empty($request->input($this->otp()))) {
            return true;
        }

        $otp = OneTimePassword::configured();

        return $otp->verifyCode((string) $user->otp_secret, (string) $request->input($this->otp()));
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     *
     * @throws ValidationException]
     */
    protected function sendFailedOtpResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->otp() => [trans('otp::login.validation-failed')],
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @override FoundationAuthenticatesUsers::login()
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($this->attemptOtp($request)) {
                return $this->sendLoginResponse($request);
            }
            $this->incrementLoginAttempts($request);
            $this->guard()->logout();

            return $this->sendFailedOtpResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
