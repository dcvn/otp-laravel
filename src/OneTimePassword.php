<?php

namespace Dcvn\Otp;

use OutOfBoundsException;
use RobThree\Auth\TwoFactorAuth;
use UnderflowException;

/**
 * Configuring layer around TwoFactorAuth using a Local QrcodeProvider.
 * Configuration precedence:
 *  1) values provided through setters,
 *  2) values from config() (which is reading DotEnv values),
 *  3) default values.
 */
class OneTimePassword
{
    protected $type = 'totp';
    protected $issuer;
    protected $digits = 6;
    protected $period = 30;
    protected $algorithm = 'sha1';

    protected $qrcodeProvider;
    protected $rngProvider;
    protected $timeProvider;

    protected $secretBits = 160; // default 80;

    protected $instance;

    public function __construct()
    {
        // Default until overridden by config or setter.
        $this->qrcodeProvider = new QrcodeProvider();
    }

    public function make()
    {
        $this->instance = new TwoFactorAuth(
            $this->issuer,
            $this->digits,
            $this->period,
            $this->algorithm,
            $this->qrcodeProvider,
            $this->rngProvider,
            $this->timeProvider
        );

        return $this;
    }

    private function requireInitialized()
    {
        if (! $this->instance instanceof TwoFactorAuth) {
            throw new UnderflowException('Authenticator not initialized');
        }
    }

    public static function configured() : self
    {
        $otp = new static();
        $envvars = ['digits', 'period', 'algorithm', 'issuer'];
        foreach ($envvars as $key) {
            $value = config('otp.' . $key);
            if ($value != '') {
                $otp->$key($value);
            }
        }
        $providers = ['qrcode', 'rng', 'time'];
        foreach ($providers as $key) {
            $value = config('otp.providers.' . $key);
            if (class_exists($value)) {
                $otp->provider($key, new $value());
            }
        }

        return $otp->make();
    }

    // Getters/setters to configuration options.
    // Setters return the `self` object (so they can be chained),
    // getters return the value requested.

    public function digits(int $value = null)
    {
        if (! is_null($value)) {
            if (! in_array($value, [6, 8])) {
                throw new OutOfBoundsException('Digits can only be 6 or 8');
            }
            $this->digits = (int) $value;

            return $this;
        }

        return $this->digits;
    }

    public function algorithm(string $value = null)
    {
        if (! is_null($value)) {
            $value = strtolower($value);
            if (! in_array($value, ['md5', 'sha1', 'sha256', 'sha512'])) {
                throw new OutOfBoundsException('Invalid algorithm');
            }
            $this->algorithm = $value;

            return $this;
        }

        return $this->algorithm;
    }

    public function period(int $value = null)
    {
        if (! is_null($value)) {
            // TODO These numbers are arbitrary.
            if ($value < 10 || $value > 150) {
                throw new OutOfBoundsException('Period to low or too high. Recommended is 30.');
            }
            $this->period = $value;

            return $this;
        }

        return $this->period;
    }

    public function issuer(string $value = null)
    {
        if (! is_null($value)) {
            if (strlen($value) < 1) {
                throw new OutOfBoundsException('Issuer name too short');
            }
            $this->issuer = $value;

            return $this;
        }

        return $this->issuer;
    }

    public function provider($key = 'qrcode', object $provider = null)
    {
        if (! in_array($key, ['qrcode', 'rng', 'time'])) {
            throw new OutOfBoundsException('Invalid provider type');
        }
        $property = $key . 'Provider';

        if (! is_null($provider)) {
            $this->$property = $provider;

            return $this;
        }

        return $this->$property;
    }

    // Configured "transparent" calls to TwoFactorAuth instance.

    public function createSecret()
    {
        $this->requireInitialized();

        return $this->instance->createSecret($this->secretBits, true);
    }

    public function getQRCodeImageAsDataUri(string $account, string $secret, int $size = null)
    {
        $this->requireInitialized();
        $label = $this->issuer . ':' . $account;
        if (is_null($size)) {
            $size = (int) config('otp.pixelspp');
        }

        return $this->instance->getQRCodeImageAsDataUri($label, $secret, $size);
    }

    public function getQRText(string $account, string $secret)
    {
        $this->requireInitialized();
        $label = $this->issuer . ':' . $account;

        return $this->instance->getQRText($label, $secret);
    }

    public function verifyCode($secret, $verification)
    {
        $this->requireInitialized();

        return $this->instance->verifyCode($secret, $verification, 1);
    }
}
