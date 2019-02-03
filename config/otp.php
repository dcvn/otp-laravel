<?php

return [
    // Values to configure the OneTimePassword.
    'issuer'    => env('OTP_ISSUER', env('APP_NAME', null)),
    'algorithm' => env('OTP_ALGORITHM', 'sha1'),
    'digits'    => env('OTP_DIGITS', 6),
    'period'    => env('OTP_PERIOD', 30),
    // Pixels per Point value for the phpqrcode generator.
    'pixelspp'  => env('OTP_PIXELSPP', 3),
    // OneTimePassword providers.
    'providers' => [
        'qrcode' => \Dcvn\Otp\QrcodeProvider::class,
        'rng'    => null,
        'time'   => null,
    ],
    // Name of the views that will be used by the OTP SetupController.
    'views' => [
        'scan'    => env('OTP_VIEW_SCAN', 'otp::scan'),
        'confirm' => env('OTP_VIEW_CONFIRM', 'otp::confirm'),
    ],
    // Paths of the routes that are connected to the OTP SetupController.
    'routes' => [
        'scan'    => '/otp/scan/user/{user}',
        'confirm' => '/otp/confirm/user/{user}',
        'verify'  => '/otp/verify/user/{user}',
    ],
    // Other configurable Setup settings.
    'setup' => [
        // Attribute of the User model that will be used for the QR Label.
        'username' => env('OTP_SETUP_USERNAME', 'email'),
        // Redirect or URL to redirect to, after the Setup has completed.
        'redirect' => function ($user) {
            return redirect('home');
        },
        // Example of the above distinguishing on user.
        // 'redirect' => function ($user) {
        //     $route = \Illuminate\Support\Facades\Auth::user()->id == $user->id
        //         ? route('authuser')
        //         : route('users.show', ['user' => $user->id]);
        //
        //     return redirect($route);
        // },
    ],
];
