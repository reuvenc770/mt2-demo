@extends( 'layout.default' )
@section('title', 'Edit DBA')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="DBAController as dba" ng-init="dba.loadAccount()">
                <div class="panel-heading">
                    <h1 class="panel-title">Add DBA</h1>
                </div>
                <div class="panel-body">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset>
                        <!-- Email field -->
                        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.name }">
                            <input placeholder="name" value="{{old('name') }}" class="form-control" ng-model="dba.currentAccount.name" required="required" name="name" type="text">
                            <span class="help-block" ng-bind="dba.formErrors.name" ng-show="dba.formErrors.name"></span>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.state_id }">
                            <select ng-model="dba.currentAccount.state_id" placeholder="Select State" name="state_id"  class="form-control">
                                <option  value="">Select a State</option>
                                @foreach ( $states as $state )
                                    <option ng-selected="dba.currentAccount.state_id == {{ $state->id }}" value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="dba.editAccount()" type="submit" value="Update DBA Account">
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
