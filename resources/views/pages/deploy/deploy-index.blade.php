@extends( 'layout.default' )

@section( 'title' , 'MT2 Deploy Packages' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Deploys</h1></div>
    </div>

    <div ng-controller="DeployController as deploy" ng-init="deploy.loadAccounts()">
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <deploy-table showrow="deploy.showRow"  currentdeploy="deploy.currentDeploy" espaccounts= "deploy.espAccounts" formerrors="deploy.formErrors" loadingflag="deploy.currentlyLoading" records="deploy.deploys"></deploy-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
