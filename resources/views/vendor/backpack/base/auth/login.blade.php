@extends(backpack_view('layouts.plain'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow bg-body rounded">
                <div class="card-body text-center">

                    <img src="{{ asset('css/images/logo-01.png') }}" alt="dhanshar logo" class="pt-2 pb-3" style="max-width: 220px">
                    <!-- <h3 class="text-center mb-4">{{ trans('backpack::base.login') }}</h3> -->
                    <h3 class="welcome-text text-center mb-3">Welcome to <span style="color: #192840">Dhanshar</span></h3>
                    <form class="col-md-12 p-t-10" role="form" method="POST" action="{{ route('backpack.auth.login') }}">
                        {!! csrf_field() !!}

                        <div class="form-group">
                            <!-- <label class="control-label" for="{{ $username }}">{{ config('backpack.base.authentication_column_name') }}</label> -->

                            <div>
                                <input type="text" placeholder="Username" class="form-control{{ $errors->has($username) ? ' is-invalid' : '' }}" name="{{ $username }}" value="{{ old($username) }}" id="{{ $username }}">

                                @if ($errors->has($username))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first($username) }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <!-- <label class="control-label" for="password">{{ trans('backpack::base.password') }}</label> -->

                            <div>
                                <input type="password" placeholder="Password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> {{ trans('backpack::base.remember_me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if (backpack_users_have_email() && config('backpack.base.setup_password_recovery_routes', true))
                            <div class="text-center mb-3"><a href="{{ route('backpack.auth.password.reset') }}">{{ trans('backpack::base.forgot_your_password') }}</a></div>
                        @endif

                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-block btn-primary">
                                    {{ trans('backpack::base.login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if (config('backpack.base.registration_open'))
                <!-- <div class="text-center"><a href="{{ route('backpack.auth.register') }}">{{ trans('backpack::base.register') }}</a></div> -->
            @endif
        </div>
    </div>
@endsection
