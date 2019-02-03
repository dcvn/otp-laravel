# OneTimePassword (OTP) for Laravel

*A Laravel layer for local OTP authentication.*

This is an attempt to make OTP implementation in Laravel easy.

Being a **layer**, this could not exist without these core dependencies:

 * the package
[robthree/twofactorauth](https://github.com/RobThree/TwoFactorAuth)
does the real OTP logic, and
 * the included merged version of
[phpqrcode](https://github.com/t0k4rt/phpqrcode)
does the real QRCode generation.

Being **local**, otp-laravel does, by default,  *not* depend on
network services to generate QR codes
*(with thanks to the offline example of RobThree using phpqrcode)*.

### Quickstart setup

Follow the [Quickstart](QUICKSTART.md)
to setup a new Laravel project using this OTP package.

The most important part is, in the LoginController,
to replace the existing *use AuthenticatesUsers*
to `use Dcvn\Otp\Http\Controllers\AuthenticatesUsers;`.

If you feel real lazy,
copy this [quickstart.sh script](scripts/quickstart.sh)
to the base dir of your projects, and run it (at your own risk).

### Features

*Assuming using the quickstart way, but you can implement how you like it.*

  * Adds an `otp_secret` column to the `users` table (migration).
  * Adds an OTP input to the login page, and validates for it when it has been set up.
  * Adds routes, controller methods and views for setting up OTP for a user.
  * By default, you are only allowed to do the setup for your own user.
  * Many settings are configurable by config and `.env`.
  * Translations available in english and dutch.
  * `OneTimePassword` as alias in the app.

#### OneTimePassword example

```php
<?php
$otp = OneTimePassword::configured();
if ($otp->verifyCode($user->otp_secret), $request->verification) { /* ... */ }
```

### Publish files

You don't necessarily need this,
but you can copy config, language or view files to your project this way:

```
artisan vendor:publish --provider "Dcvn\Otp\Providers\OtpServiceProvider" --tag config
artisan vendor:publish --provider "Dcvn\Otp\Providers\OtpServiceProvider" --tag lang
artisan vendor:publish --provider "Dcvn\Otp\Providers\OtpServiceProvider" --tag views
```

To override the access policy
(for example to allow (only) admin users to do the setup),
copy this to your `AuthServiceProvider`'s `boot()` method,
then modify it:

```php
<?php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Gate;

// in boot():
Gate::define('otp.setup', function (Authenticatable $user, Authenticatable $model) {
    return $user->id == $model->id;
});
```
----
