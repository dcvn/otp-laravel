<?php

Route::group([
    'namespace'  => 'Dcvn\Otp\Http\Controllers',
    'middleware' => ['web', 'auth'],
], function () {
    Route::get(config('otp.routes.scan'), 'OtpSetupController@scan')->name('otp.scan');
    Route::get(config('otp.routes.confirm'), 'OtpSetupController@confirm')->name('otp.confirm');
    Route::post(config('otp.routes.verify'), 'OtpSetupController@verify')->name('otp.verify');
});
