@extends('layouts.app')

@section('content')

<div class="container">

    @include('otp::flash')

    <div class="jumbotron">
        <h1>{{ $username }}</h1>
    </div>

    <div class="card">
        <h4 class="card-header">{{ __('otp::setup.remove.title') }}</h4>
        <div class="card-body">
            <p>{{ __('otp::setup.remove.text') }}</p>
        </div>
    </div>

    <div class="card">
        <h4 class="card-header">{{ __('otp::setup.scan.title') }}</h4>
        <div class="card-body row">
            <div class="col col-3">
                <p>{{ __('otp::setup.scan.text') }}</p>
                <p><img src="{{ $qrcodeSrc }}"></p>
            </div>
            <div class="col col-9 pt-4">
                <p>{!! __('otp::setup.scan.text-alt') !!}</p>
                <p style="overflow:auto;"><a href="{{ $dataUri }}" class="text-nowrap">{{ $dataUri }}</a></p>
            </div>
        </div>
    </div>

    <div class="card">
        <h4 class="card-header">{{ __('otp::setup.validate.title') }}</h4>
        <div class="card-body">
            <p>{{ __('otp::setup.validate.text') }}</p>
            <form method="post" action="{{ route('otp.verify', $user->id) }}">
                @csrf
                <label for="verification">{{ __('otp::setup.confirm.verification-code') }}</label>
                <input type="text" name="verification">
                <button type="submit">{{ __('otp::setup.confirm.submit') }}</button>
            </form>
        </div>
    </div>

</div>

@endsection
