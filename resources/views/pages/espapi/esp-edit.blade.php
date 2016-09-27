@extends( 'layout.default' )

@section( 'title' , 'Edit ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="espController as esp" ng-init="esp.loadAccount()">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Edit ESP API Account :: @{{esp.currentAccount.accountName}}</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="editEspForm" layout="column" novalidate>
                        <input type="hidden" ng-model="esp.currentAccount.id" />
                        <md-input-container>
                            <label>Account Name</label>
                            <input type="text" id="accountName" name="accountName" ng-model="esp.currentAccount.accountName" ng-required="true" />
                            <div ng-messages="editEspForm.accountName.$error">
                                <div ng-message="required">ESP Account Name is required.</div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Key 1</label>
                            <input type="text" id="key1" name="key1" ng-model="esp.currentAccount.key1" ng-required="true" />
                            <div ng-messages="editEspForm.key1.$error">
                                <div ng-message="required">ESP Key 1 is required.</div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Key 2</label>
                            <input type="text" id="key2" name="key2" ng-model="esp.currentAccount.key2" />
                            <div ng-messages="editEspForm.key2.$error">
                                <div ng-message="required"></div>
                            </div>
                        </md-input-container>

                        <md-button class="md-raised md-accent" ng-click="esp.editAccount()">Save</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
<script src="js/espapi.js"></script>
@stop
