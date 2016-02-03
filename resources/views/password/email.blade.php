@extends( 'layout.default' )
@section('title', 'Password Reset Email')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Password Reset Link</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{route('forgetpassword.postemail')}}" accept-charset="UTF-8">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <fieldset>
                                <p>Enter your email and we will send you a link to reset your password.</p>
                                <!-- Email field -->
                                <div class="form-group @if ($errors->has('email')) has-error @endif">
                                    <input placeholder="Email" class="form-control" required="required" name="email" type="text">
                                    @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                                </div>

                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn btn-lg btn-primary btn-block" type="submit" value="Send Password Reset Link">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection