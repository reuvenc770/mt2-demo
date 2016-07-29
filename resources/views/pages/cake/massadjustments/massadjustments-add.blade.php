@extends( 'layout.default' )
@section('title', 'Add Mass Adjustment')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="MassAdjustmentsController as ma">
                <div class="panel-heading">
                    <h1 class="panel-title">Add DBA</h1>
                </div>
                <div class="panel-body">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset>
                        @include( 'pages.cake.massadjustments.massadjustments-form' )
                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="ma.saveNewAdjustment()" type="submit" value="Submit Adjustment">
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
