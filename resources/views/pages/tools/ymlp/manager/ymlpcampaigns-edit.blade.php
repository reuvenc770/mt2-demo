@extends( 'layout.default' )

@section( 'title' , 'Edit YMLP Campaign' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="ymlpCampaignController as ymlp"
                 ng-init="ymlp.loadCampaign()">
                <div class="panel-heading">
                    <h1 class="panel-title">Edit YMLP Campaign :: @{{ymlp.currentCampaign.sub_id}}</h1>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group" ng-class="{ 'has-error' : ymlp.formErrors.esp_account_id }">
                            <label for="espId" class="col-sm-2 control-label">ESP Account</label>

                            <div class="col-sm-10">
                                <select id="espId" class="form-control" required="required"
                                        ng-model="ymlp.currentCampaign.esp_account_id"
                                        ng-options="country.id as country.account_name for country in ymlp.currentCampaign.espAccounts">
                                    <option value="">Choose Esp Account</option>
                                </select>

                            <span class="help-block" ng-bind="ymlp.formErrors.esp_account_id"
                                  ng-show="ymlp.formErrors.esp_account_id"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : ymlp.formErrors.sub_id }">
                            <label for="accountName" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-10">
                                <input type="text" id="name" class="form-control" required="required"
                                       ng-model="ymlp.currentCampaign.sub_id" value=""/>

                            <span class="help-block" ng-bind="ymlp.formErrors.sub_id"
                                  ng-show="ymlp.formErrors.sub_id"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : ymlp.formErrors.date }">
                            <label for="" class="col-sm-2 control-label">Date</label>

                            <div class="col-sm-10">
                                <input type="text" id="date" class="form-control" required="required"
                                       ng-model="ymlp.currentCampaign.date" value=""/>

                                <span class="help-block" ng-bind="ymlp.formErrors.date"
                                      ng-show="ymlp.formErrors.date"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-lg btn-primary btn-block"
                                        ng-click="ymlp.editCampaign()">Edit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
