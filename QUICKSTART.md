# Quickstart: otp-laravel

***including a basic Laravel setup***

*TL;DR: still look at: 1) Require this package, 2) File customations, 3) Test it!*

For this example, assume your project's name is "Frontdoor",
and we are going to implement the One Time Password (OTP) system
as the very first thing at your Frontdoor.

*This was tested with Laravel 5.7.*

#### Laravel setup

* `mkdir frontdoor`
* `cd frontdoor`
* `laravel new`
* `cp .env.example .env`
  * Edit .env: `APP_NAME=Frontdoor`
* `chmod +x ./artisan`
* `artisan key:generate`

#### Require this package

* `composer require "dcvn/otp-laravel"`

**Optional**, set some values in your .env, for example:
```ini
OTP_ISSUER=Frontdoor
OTP_ALGORITHM=sha512
```
See [dotenv-otp in scripts](scripts/dotenv-otp) for the possible values.

#### Setup database

For this demo, an sqlite database is used. For a multi-user application, use a database server.

* Edit .env: `DB_CONNECTION=sqlite`
* Edit .env: `DB_DATABASE=/full/path/to/frontdoor/database/app.sqlite` *Use absolute path to be sure*
* `touch database/app.sqlite` *Ensure the sqlite file exists*
* `artisan migrate`

#### Publish auth
Now publish the Laravel auth controllers and views into your project.
We will at least need the LoginController and login view.

* `artisan make:auth`

#### File customations

At this point, some customations have to be done.

* File: <small>`App\Http\Controllers\Auth\LoginController`</small>, at the top, replace the existing  <small>`use ...\AuthenticatesUsers`</small> for the package one:
```
use Dcvn\Otp\Http\Controllers\AuthenticatesUsers;
```
* File: <small>`App\User`</small> (or whatever your User model is), add `otp_secret_set_at` to the `$dates` array.
* File: <small>`resources/views/home.blade.php`</small> (or any other redirect page), add a block to show success or failure messages, inside the container div, somewhere at the top:
```
@include('otp::flash')
```

#### Data: seed users

The code has been prepared! Now we need some data, in our case: users.

*You may skip this, and use the `Register` function at the login page to create a user.*

* `artisan make:seeder UsersTableSeeder`
* Let this seeder create an admin user and some random users (see the [example in scripts](scripts/UsersTableSeeder.php)).
* `artisan db:seed --class UsersTableSeeder`

### Test it!

Let's see if everything went well. Start a dev-server and try it out.

* `artisan serve --host localhost --port 8081`
* Browse to http://localhost:8081/login and login using the created admin user; no OTP.
* Browse to http://localhost:8081/otp/scan/user/1 and setup the OTP by following instructions on the screen.
* Logout.
* Login again, using email, password and OTP.

*In your project, you may want to setup a UserController to manage your users more complete.*

----
