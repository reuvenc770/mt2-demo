@extends( 'layout.default' )

@section( 'title' , 'MT2 Deploy Packages' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Deploys</h1></div>
    </div>

    <div ng-controller="DeployController as deploy" ng-init="deploy.loadAccounts()">
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer">
                    <table class="table table-striped table-bordered table-hover text-center">
                        <thead>
                        <tr>
                            <th class="text-center">
                                <strong><span class="glyphicon glyphicon-refresh rotateMe"
                                              ng-if="deploy.loadingflag == 1"></span></strong>
                            </th>
                            <th class="text-center">Send Date</th>
                            <th class="text-center">Deploy ID</th>
                            <th class="text-center">EspAccount</th>
                            <th class="text-center">List Profile</th>
                            <th class="text-center">Offer</th>
                            <th class="text-center">Creative</th>
                            <th class="text-center">From</th>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Template</th>
                            <th class="text-center">Mailing Domain</th>
                            <th class="text-center">Content Domain</th>
                            <th class="text-center">Cake ID</th>
                            <th class="text-center">Notes</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <md-datepicker name="dateField" ng-model="deploy.currentDeploy.send_date"
                                               md-placeholder="Enter date"></md-datepicker>
                                <div class="validation-messages" ng-messages="myForm.dateField.$error">
                                    <div ng-message="valid">The entered value is not a date!</div>
                                    <div ng-message="required">This date is required!</div>
                                    <div ng-message="mindate">Date is too early!</div>
                                    <div ng-message="maxdate">Date is too late!</div>
                                    <div ng-message="filtered">Only weekends are allowed!</div>
                                </div>
                            </td>
                            <td>To Be Generated</td>
                            <td>
                                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.esp_account_id }">
                                    <select name="esp_account" id="esp_account"
                                            ng-change="deploy.updateSelects()"
                                            ng-model="deploy.currentDeploy.esp_account_id" class="form-control"
                                            ng-disabled="deploy.currentlyLoading">
                                        <option value="">- Please Choose an ESP Account -</option>
                                        <option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.esp_account_id">@{{ option.account_name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.espAccountId"
                          ng-show="deploy.formErrors.espAccountId"></span>
                                </div>
                            </td>
                            <td></td>
                            <td>
                                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.offerId }">
                                    <div angucomplete-alt
                                         id="offer"
                                         placeholder="Search Offers"
                                         selected-object="deploy.currentDeploy.offer"
                                         remote-url="/api/offer/search?searchTerm="
                                         title-field="name"
                                         text-searching="Looking for Offers..."
                                         selected-object-data="offer"
                                         minlength="3"
                                         input-class="form-control">
                                    </div>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <div class="form-group"
                                      ng-class="{ 'has-error' : deploy.formErrors.template_id }">
                                    <select name="template" id="template"
                                            ng-model="deploy.currentDeploy.template_id" class="form-control"
                                            ng-disabled="deploy.espLoaded">
                                        <option value="">- Please Choose a Template -</option>
                                        <option ng-repeat="option in deploy.templates" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.template_id">@{{ option.template_name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.mailing_domain_id"
                          ng-show="deploy.formErrors.mailing_domain_id"></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group"
                                     ng-class="{ 'has-error' : deploy.formErrors.mailing_domain_id }">
                                    <select name="mailing_domain" id="mailing_domain"
                                            ng-model="deploy.currentDeploy.mailing_domain_id" class="form-control"
                                            ng-disabled="deploy.espLoaded">
                                        <option value="">- Please Choose a Mailing Domain -</option>
                                        <option ng-repeat="option in deploy.mailingDomains" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.mailing_domain_id">@{{ option.domain_name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.mailing_domain_id"
                          ng-show="deploy.formErrors.mailing_domain_id"></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group"
                                     ng-class="{ 'has-error' : deploy.formErrors.content_domain_id }">
                                    <select name="content_domain" id="content_domain"
                                            ng-model="deploy.currentDeploy.content_domain_id" class="form-control"
                                            ng-disabled="deploy.espLoaded">
                                        <option value="">- Please Choose an Content Domain -</option>
                                        <option ng-repeat="option in deploy.contentDomains" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.content_domain_id">@{{ option.domain_name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.content_domain_id"
                          ng-show="deploy.formErrors.content_domain_id"></span>
                                </div>
                            </td>
                        </tr>

                        <tr ng-repeat="record in deploy.deploys track by $index">
                            <td>
                                <edit-button editurl="'/dba/edit/'" recordid="record.id"></edit-button>
                            </td>
                            <td>@{{ record.id }}</td>
                            <td>@{{ record.dba_name }}</td>
                            <td>@{{ record.email }}</td>
                            <td>@{{ record.address }}</td>
                            <td>@{{ record.state }}</td>
                            <td>@{{ record.phone }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
