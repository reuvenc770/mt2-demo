@extends( 'layout.default' )

@section( 'title' , 'Add ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="espController as esp">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Add ESP API Account</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="addEspForm" layout="column" novalidate>
                        <md-input-container>
                            <label>ESP Name</label>
                            <md-select id="espId" name="espId" ng-required="true" ng-model="esp.currentAccount.espId">
                                @foreach( $espList as $espId => $esp )
                                    <md-option value="{{ $espId }}">{{ $esp }}</md-option>
                                @endforeach
                            </md-select>
                            <div ng-messages="addEspForm.espId.$error">
                                <div ng-message="required">Please choose an ESP.</div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Account Name</label>
                            <input type="text" id="accountName" name="accountName" ng-required="true" ng-model="esp.currentAccount.accountName" ng-change="esp.onFormFieldChange( $event , addEspForm , 'accountName' )" />
                            <div ng-messages="addEspForm.accountName.$error">
                                <div ng-message="required">ESP account name is required.</div>
                                <div ng-repeat="error in esp.formErrors.accountName">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Key 1</label>
                            <input type="text" id="key1" name="key1" ng-required="true" ng-model="esp.currentAccount.key1" ng-change="esp.onFormFieldChange( $event , addEspForm , 'key1' )" />
                            <div ng-messages="addEspForm.key1.$error">
                                <div ng-message="required">ESP key 1 is required.</div>
                                <div ng-repeat="error in esp.formErrors.key1">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Key 2</label>
                            <input type="text" id="key2" name="key2" ng-model="esp.currentAccount.key2" />
                        </md-input-container>

                        <md-button class="md-raised md-accent" ng-click="esp.saveNewAccount( $event , addEspForm )">Save</md-button>

                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
    <script src="js/espapi.js"></script>
@stop
