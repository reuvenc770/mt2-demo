@extends( 'layout.default' )

@section('title', 'Password Reset')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Reset Password</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{route("password.store", array('token' => $token ))}}" accept-charset="UTF-8">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                        <fieldset>
                            <!-- Email field -->
                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                                <input placeholder="Email" class="form-control" required="required" name="email" type="text" value="{{old('email')}}">
                                @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                            </div>

                            <!-- Password field -->
                            <div class="form-group @if ($errors->has('password')) has-error @endif">
                                <input placeholder="Password" class="form-control" required="required" name="password" type="password" value="">
                                @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                            </div>

                            <!-- Password confirmation field -->
                            <div class="form-group @if ($errors->has('password_confirm')) has-error @endif">
                                <input placeholder="Password confirmation" class="form-control" required="required" name="password_confirmation" type="password" value="">
                                @if ($errors->has('password_confirm')) <p class="help-block">{{ $errors->first('password_confirm') }}</p> @endif
                            </div>

                            <!-- Hidden Token field -->
                            <input name="token" type="hidden" value="{{ $token }}">


                            <!-- Submit field -->
                            <div class="form-group">
                                <input class="btn btn btn-lg btn-primary btn-block" type="submit" value="Reset Password">
                            </div>
                        </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection