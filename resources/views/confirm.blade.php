@extends('layouts.app')

@section('content')

@include('otp::flash')

<div class="container">

    <div class="jumbotron">
        <h1>{{ $username }}</h1>
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
