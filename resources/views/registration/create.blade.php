@extends( 'layout.default' )
@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Register</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{route("registration.store")}}" accept-charset="UTF-8">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <fieldset>
                                <!-- Email field -->
                                <div class="form-group  @if ($errors->has('email')) has-error @endif">
                                    <input placeholder="Email" value="{{old('email') }}" class="form-control" required="required" name="email" type="text">
                                    @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                                </div>

                                <div class="form-group  @if ($errors->has('username')) has-error @endif">
                                    <input placeholder="Email" value="{{old('username') }}" class="form-control" required="required" name="username" type="text">
                                    @if ($errors->has('username')) <p class="help-block">{{ $errors->first('username') }}</p> @endif
                                </div>

                                <!-- Password field -->
                                <div class="form-group @if ($errors->has('password')) has-error @endif">
                                    <input placeholder="Password" class="form-control" required="required" name="password" type="password" value="">
                                    @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                                </div>
                                <!-- Password Confirmation field -->
                                <div class="form-group @if ($errors->has('password_confirmation')) has-error @endif">
                                    <input placeholder="Password Confirm" class="form-control" required="required" name="password_confirmation" type="password" value="">
                                    @if ($errors->has('password_confirmation')) <p class="help-block">{{ $errors->first('password_confirmation') }}</p> @endif
                                </div>
                                <!-- First name field -->
                                <div class="form-group @if ($errors->has('first_name')) has-error @endif">
                                    <input placeholder="First Name" value="{{old('first_name') }}" class="form-control" required="required" name="first_name" type="text">
                                    @if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                                </div>
                                <!-- Last name field -->
                                <div class="form-group @if ($errors->has('last_name')) has-error @endif">
                                    <input placeholder="Last Name" value="{{old('last_name') }}" class="form-control" required="required" name="last_name" type="text">
                                    @if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                                </div>
                                <div class="form-group @if ($errors->has('type')) has-error @endif">
                                    <select name="type" class="form-control">
                                        <option value="">Account Type</option>
                                        @foreach ($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('type')) <p class="help-block">{{ $errors->first('type') }}</p> @endif
                                </div>
                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" type="submit" value="Create Account">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
