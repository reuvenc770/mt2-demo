@extends( 'layout.default' )
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Login</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{route("sessions.store")}}" accept-charset="UTF-8">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <fieldset>


                                <!-- Email field -->
                                <div class="form-group @if ($errors->has('login')) has-error @endif">
                                    <input placeholder="Email or Username" class="form-control" required="required" name="login" type="text" value="{{ old('login') }}">
                                    @if ($errors->has('login')) <p class="help-block">{{ $errors->first('login') }}</p> @endif
                                </div>

                                <!-- Password field -->
                                <div class="form-group @if ($errors->has('password')) has-error @endif">
                                    <input placeholder="Password" class="form-control" required="required" name="password" type="password" value="">
                                    @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                                </div>

                                <div class="checkbox">
                                    <!-- Remember me field -->
                                    <div class="form-group">
                                        <label>
                                            <input name="remember" type="checkbox" value="remember"> Remember me
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn btn-lg btn-success btn-block" type="submit" value="Login">
                                </div>


                            </fieldset>
                        </form>
                    </div>
                </div>
                <div style="text-align:center">
                    <p><a href="{{ url('forgot_password') }}">Forgot Password?</a></p>
                </div>
            </div>
        </div>
    </div>

@endsection