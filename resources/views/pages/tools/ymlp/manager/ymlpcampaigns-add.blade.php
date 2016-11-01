@extends( 'layout.default' )

@section( 'title' , 'Add YMLP Campaign' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="ymlpCampaignController as ymlp">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Add YMLP Campaign</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="ymlpForm" layout="column" novalidate>
                        <md-input-container>
                            <label>ESP Account</label>
                            <md-select id="esp_account_id" name="esp_account_id"  ng-required="true" ng-model="ymlp.currentCampaign.esp_account_id">
                                @foreach ($espAccounts as $esp)
                                    <md-option value="{{$esp->id}}">{{$esp->account_name}}</md-option>
                                @endforeach
                            </md-select>
                            <div ng-messages="ymlpForm.esp_account_id.$error">
                                <div ng-message="required">ESP Account is required.</div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Name</label>
                            <input type="text" id="name" name="sub_id" ng-required="true" ng-model="ymlp.currentCampaign.sub_id"/>
                            <div ng-messages="ymlpForm.sub_id.$error">
                                <div ng-message="required">Name is required.</div>
                            </div>
                        </md-input-container>

                        <md-input-container>
                            <label>Date</label>
                            <input type="text" id="date" name="date" ng-required="true" ng-model="ymlp.currentCampaign.date" ng-change="ymlp.change( ymlpForm, 'date' )" />
                            <div ng-messages="ymlpForm.date.$error">
                                <div ng-message="required">Date is required.</div>
                                <div ng-repeat="error in ymlp.formErrors.date">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>

                        <md-button class="md-raised md-accent" ng-click="ymlp.saveNewCampaign( $event , ymlpForm )">Save</md-button>

                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
