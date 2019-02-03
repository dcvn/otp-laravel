
{{-- BEGIN OTP BLOCK --}}
<div class="form-group row">
    <label for="otpcode" class="col-md-4 col-form-label text-md-right">{{ __('OTP code') }}</label>

    <div class="col-md-6">
        <input id="otpcode" type="text" class="form-control{{  $errors->has('otpcode') ? ' is-invalid' : '' }}" name="otpcode">

        @if ($errors->has('otpcode'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('otpcode') }}</strong>
            </span>
        @endif
    </div>
</div>
{{-- END OTP BLOCK --}}
