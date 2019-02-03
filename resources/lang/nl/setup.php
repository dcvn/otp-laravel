<?php

return [

    'remove' => [
        'title' => '1. Verwijder het oude token',
        'text'  => 'Verwijder het huidige token alvorens te scannen: de app kan weigeren een bestaand token te vervangen.',
    ],
    'scan' => [
        'title'    => '2. Scan de QR-code',
        'text'     => '',
        'text-alt' => '... of importeer het token door de <code>oauth://</code>-URL te volgen.',
    ],
    'validate' => [
        'title' => '3. Valideer het nieuwe token',
        'text'  => 'Verstuur de huidige OTP van de app om vast te stellen dat het werkt.',
    ],
    'confirm' => [
        'title'             => '',
        'text'              => '',
        'verification-code' => 'Verificatiecode:',
        'submit'            => 'Verstuur',
    ],
    'verified' => [
        'error'   => 'Verificatie mislukt. Probeer het opnieuw.',
        'success' => 'Verificatie gelukt. De OTP sleutel is opgeslagen.',
    ],
];
