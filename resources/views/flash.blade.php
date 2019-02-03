
@if (session()->has('otp_verified'))
    @if (session()->get('otp_verified') === false)
        <div class="alert alert-danger" role="alert">
            {{ __('otp::setup.verified.error') }}
        </div>
    @endif
    @if (session()->get('otp_verified') === true)
        <div class="alert alert-success" role="alert">
            {{ __('otp::setup.verified.success') }}
        </div>
    @endif
@endif
