@extends( 'layout.default' )
@section('title', 'Edit Mass Adjustment')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="MassAdjustmentsController as ma" ng-init="ma.loadAdjustment()">
                <div class="panel-heading">
                    <h1 class="panel-title">Edit Mass Adjustment</h1>
                </div>
                <div class="panel-body">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset>
                        @include('pages.cake.massadjustments.massadjustments-form')
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="ma.editAdjustment()" type="submit" value="Update Mass Adjustment">
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/massadjustments.js"></script>
@stop
