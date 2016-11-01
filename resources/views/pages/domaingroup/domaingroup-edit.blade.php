@extends( 'layout.default' )
@section('title', 'Edit Isp Group')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="DomainGroupController as dg" ng-init="dg.loadAccount()">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit ISP Group</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset>
                @include( 'pages.domaingroup.domaingroup-form' )
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="dg.editAccount()" type="submit" value="Update Isp Group">
                        </div>
                    </fieldset>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/domaingroup.js"></script>
@stop
