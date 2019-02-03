<?php

namespace Dcvn\Otp\Http\Controllers;

use Carbon\Carbon;
use Dcvn\Otp\OneTimePassword;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class OtpSetupController extends BaseController
{
    use AuthorizesRequests;

    /**
     * Value of the attribute of the User model that will be used for the QR Label.
     *
     * @param Authenticatable $user
     *
     * @return string
     */
    protected function username(Authenticatable $user)
    {
        $username = config('otp.setup.username');

        return $user->$username;
    }

    /**
     * Show the page where the user can scan the QR code.
     *
     * @param Authenticatable $user
     *
     * @return Response
     */
    public function scan(Authenticatable $user)
    {
        $this->authorize('otp.setup', $user);

        $otp = OneTimePassword::configured();

        $secret = $otp->createSecret();
        $qrcodeSrc = $otp->getQRCodeImageAsDataUri($this->username($user), $secret);
        $dataUri = $otp->getQRText($this->username($user), $secret);

        session()->flash('otp_secret', $secret);

        return view(config('otp.views.scan'), [
            'user'      => $user,
            'username'  => $this->username($user),
            'dataUri'   => $dataUri,
            'qrcodeSrc' => $qrcodeSrc,
        ]);
    }

    /**
     * Show the page where the user can confirm the OTP code.
     *
     * @param Authenticatable $user
     *
     * @return Response
     */
    public function confirm(Authenticatable $user)
    {
        $this->authorize('otp.setup', $user);

        session()->reflash();

        return view(config('otp.views.confirm'), [
            'user'      => $user,
            'username'  => $this->username($user),
        ]);
    }

    /**
     * Handle the confirmation submit.
     *
     * @param Authenticatable $user
     * @param Request         $request
     *
     * @return Response
     */
    public function verify(Authenticatable $user, Request $request)
    {
        $this->authorize('otp.setup', $user);

        $otp = OneTimePassword::configured();

        if (empty($request->verification)
            || ! $otp->verifyCode(session('otp_secret'), $request->verification)
        ) {
            session()->flash('otp_verified', false);

            return redirect(route('otp.confirm', ['user' => $user->id]));
        }

        $user->otp_secret = session('otp_secret');
        $user->otp_secret_set_at = Carbon::now();
        $user->save();

        session()->forget('otp_secret');
        session()->flash('otp_verified', true);

        return $this->getRedirect($user);
    }

    /**
     * Get the redirect after the setup has completed.
     *
     * @param Authenticatable $user
     *
     * @return Response
     */
    private function getRedirect(Authenticatable $user)
    {
        $redirect = config('otp.setup.redirect');
        if (is_callable($redirect)) {
            return $redirect($user);
        }
        if (substr($redirect, 0, 4) == 'http') {
            return redirect($redirect);
        }

        return redirect(route('home'));
    }
}
