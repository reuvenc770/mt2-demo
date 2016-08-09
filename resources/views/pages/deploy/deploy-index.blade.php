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
                    <button ng-click="deploy.displayForm()" class="btn btn-primary">New Deploy</button>
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
                        <tr ng-show="deploy.showRow">
                            <td><button ng-click="deploy.actionLink()" class="btn btn-small btn-primary">@{{ deploy.actionText() }}</button></td>
                            <td>
                                <md-datepicker name="dateField" ng-model="deploy.currentDeploy.send_date"
                                               required
                                               md-placeholder="Enter date"></md-datepicker>
                                <div class="validation-messages" ng-show="deploy.formErrors.send_date">
                                    <div ng-bind="deploy.formErrors.send_date"></div>
                                </div>

                            </td>
                            <td>@{{ deploy.deployIdDisplay }}</td>
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
                    <span class="help-block" ng-bind="deploy.formErrors.esp_account_id"
                          ng-show="deploy.formErrors.esp_account_id"></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.list_profile_id }">
                                    <select name="list_profile_id" id="list_profile_id"
                                            ng-model="deploy.currentDeploy.list_profile_id" class="form-control"
                                            ng-disabled="deploy.currentlyLoading">
                                        <option value="">- Please Choose a List Profile -</option>
                                        <option ng-repeat="option in deploy.listProfiles" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.list_profile_id">@{{ option.profile_name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.list_profile_id"
                          ng-show="deploy.formErrors.list_profile_id"></span>
                                </div>
                            </td>

                            </td>
                            <td>
                                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.offer_id }">
                                    <div angucomplete-alt
                                         id="offer"
                                         placeholder="Search Offers"
                                         selected-object="deploy.offerWasSelected"
                                         selected-object-data="deploy.currentDeploy.offer_id"
                                         remote-url="/api/offer/search?searchTerm="
                                         title-field="name,id"
                                         text-searching="Looking for Offers..."
                                         selected-object-data="offer"
                                         minlength="3"
                                         input-class="form-control">
                                    </div>
                                </div>
                                <span class="help-block" ng-bind="deploy.formErrors.offer_id"
                                      ng-show="deploy.formErrors.offer_id"></span>
                </div>
                            </td>
                            <td>
                                <div class="form-group"
                                     ng-class="{ 'has-error' : deploy.formErrors.creative_id }">
                                    <select name="creative_id" id="creative_id"
                                            ng-model="deploy.currentDeploy.creative_id" class="form-control"
                                            ng-disabled="deploy.offerLoading">
                                        <option value="">- Please Choose a Creative -</option>
                                        <option ng-repeat="option in deploy.creatives" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.creative_id">@{{ option.name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.creative_id"
                          ng-show="deploy.formErrors.creative_id"></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group"
                                     ng-class="{ 'has-error' : deploy.formErrors.from_id }">
                                    <select name="from_id" id="from_id"
                                            ng-model="deploy.currentDeploy.from_id" class="form-control"
                                            ng-disabled="deploy.offerLoading">
                                        <option value="">- Please Choose a From -</option>
                                        <option ng-repeat="option in deploy.froms" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.from_id">@{{ option.name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.from_id"
                          ng-show="deploy.formErrors.from_id"></span>
                                </div>
                            </td>
                            <td><div class="form-group"
                                     ng-class="{ 'has-error' : deploy.formErrors.subject_id }">
                                    <select name="subject_id" id="subject_id"
                                            ng-model="deploy.currentDeploy.subject_id" class="form-control"
                                            ng-disabled="deploy.offerLoading">
                                        <option value="">- Please Choose a Subject -</option>
                                        <option ng-repeat="option in deploy.subjects" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.subject_id">@{{ option.name }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.subject_id"
                          ng-show="deploy.formErrors.subject_id"></span>
                                </div></td>
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
                                        <option ng-repeat="option in deploy.mailingDomains track by $index" ng-value="option.id"
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
                            <td>
                                <div class="form-group"
                                     ng-class="{ 'has-error' : deploy.formErrors.cake_affiliate_id }">
                                    <select name="cake_affiliate_id" id="cake_affiliate_id"
                                            ng-model="deploy.currentDeploy.cake_affiliate_id" class="form-control">
                                        <option value="">- Please Choose an Cake ID -</option>
                                        <option ng-repeat="option in deploy.cakeAffiliates" ng-value="option.affiliateID"
                                                ng-selected="option.id == deploy.currentDeploy.cake_affiliate_id">@{{ option.affiliateID }}
                                        </option>
                                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.cake_affiliate_id"
                          ng-show="deploy.formErrors.cake_affiliate_id"></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.notes }">
                                    <div class="form-group" >
                                        <textarea ng-model="deploy.currentDeploy.notes" class="form-control" rows="1" id="html"></textarea>
                                    </div>
                                    <span class="help-block" ng-bind="deploy.formErrors.notes" ng-show="mailing.formErrors.notes"></span>
                                </div>
                            </td>
                        </tr>

                        <tr ng-repeat="record in deploy.deploys track by $index">
                            <td>
                                <span  ng-click="deploy.editRow( record.deploy_id)" class="glyphicon glyphicon-edit"></span>
                                <span ng-click="deploy.copyRow( record.deploy_id)" class="glyphicon glyphicon-copy"></span>
                            </td>
                            <td>@{{ record.send_date }}</td>
                            <td>@{{ record.deploy_id }}</td>
                            <td>@{{ record.account_name }}</td>
                            <td>@{{ record.list_profile }}</td>
                            <td>@{{ record.offer_name }}</td>
                            <td>@{{ record.creative }}</td>
                            <td>@{{ record.from }}</td>
                            <td>@{{ record.subject }}</td>
                            <td>@{{ record.template_name }}</td>
                            <td>@{{ record.mailing_domain }}</td>
                            <td>@{{ record.content_domain }}</td>
                            <td>@{{ record.cake_affiliate_id }}</td>
                            <td>@{{ record.notes }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                            <pagination-count recordcount="deploy.paginationCount" currentpage="deploy.currentPage"></pagination-count>
                        </div>

                        <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                            <pagination currentpage="deploy.currentPage" maxpage="deploy.pageCount" disableceiling="deploy.reachedMaxPage" disablefloor="deploy.reachedFirstPage"></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
