<?php

return [

    'remove' => [
        'title' => '1. Remove old token',
        'text'  => 'Delete your current token before scanning: Your app may refuse to replace an existing token.',
    ],
    'scan' => [
        'title'    => '2. Scan QR code',
        'text'     => '',
        'text-alt' => '... or enter your token by this <code>oauth://</code> URL.',
    ],
    'validate' => [
        'title' => '3. Validate new token',
        'text'  => 'Submit the current OTP from your app to verify that it works.',
    ],
    'confirm' => [
        'title'             => '',
        'text'              => '',
        'verification-code' => 'Verification code:',
        'submit'            => 'Submit',
    ],
    'verified' => [
        'error'   => 'Verification failed. Please try again.',
        'success' => 'Verification was successfull. OTP secret has been stored.',
    ],
];
