@extends( 'layout.default' )

@section( 'title' , 'Edit ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Edit ESP Account</h1></div>
</div>

<div ng-controller="espController as esp" ng-init="esp.loadAccount()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-2 col-lg-3"></div>

        <div class="col-xs-12 col-md-8 col-lg-6">
            <form class="form-horizontal">
                <input type="hidden" ng-model="esp.currentAccount.id" />

                <div class="form-group">
                    <h2 class="text-center">{{ $espName }}</h2>
                </div>

                <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.accountName }">
                    <label for="accountName" class="col-sm-2 control-label">Account Name</label>

                    <div class="col-sm-10">
                        <input type="text" id="accountName" class="form-control" required="required"  ng-model="esp.currentAccount.accountName" />

                        <span class="help-block" ng-bind="esp.formErrors.accountName" ng-show="esp.formErrors.accountName"></span>
                    </div>
                </div>

                <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key1 }">
                    <label for="key1" class="col-sm-2 control-label">Key 1</label>

                    <div class="col-sm-10">
                        <input type="text" id="key1" class="form-control" required="required" ng-model="esp.currentAccount.key1" />

                        <span class="help-block" ng-bind="esp.formErrors.key1" ng-show="esp.formErrors.key1"></span>
                    </div>
                </div>

                <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key2 }">
                    <label for="key2" class="col-sm-2 control-label">Key 2</label>

                    <div class="col-sm-10">
                        <input type="text" id="key2" class="form-control"  ng-model="esp.currentAccount.key2" />

                        <span class="help-block" ng-bind="esp.formErrors.key2" ng-show="esp.formErrors.key2"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" class="btn btn-info btn-lg" ng-click="esp.editAccount()">Save</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="hidden-xs hidden-sm col-md-2 col-lg-3"></div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/esp.js"></script>
@stop
