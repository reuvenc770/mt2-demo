@extends( 'layout.default' )

@section( 'title' , 'MT2 Deploy Packages' )


@section( 'content' )
    <div ng-controller="DeployController as deploy" ng-init="deploy.loadAccounts()">
        <div class="row">
            <div class="page-header col-xs-12">
                <h1 class="text-center">Deploys
                    <small ng-if="deploy.searchType.length > 0">Search by @{{ deploy.searchType }}</small>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">Search Deploys</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                    ng-click="deploy.searchDeploys('esp',deploy.search.esp_id)">Search By Esp
                                            </button>
                                        </span>
                                        <select name="esp_account_search" id="esp_account_search"
                                                ng-model="deploy.search.esp_id" class="form-control"
                                                ng-disabled="deploy.currentlyLoading">
                                            <option value="">- Please Choose an ESP -</option>
                                            @foreach ( $esps as $esp )
                                                <option value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                ng-click="deploy.searchDeploys('espAccount',deploy.search.esp_account_id)">Search By Esp Account
                                            </button>
                                        </span>
                                        <select name="esp_account_search" id="esp_account_search"
                                                ng-model="deploy.search.esp_account_id" class="form-control"
                                                ng-disabled="deploy.currentlyLoading">
                                            <option value="">- Please Choose an ESP Account -</option>
                                            <option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                                    ng-selected="option.id == deploy.search.esp_account_id">@{{ option.account_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                ng-click="deploy.searchDeploys('offer',deploy.search.offer)">Search By Offer
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" id="search_offer" value=""
                                               placeholder="Offer Name* wildcard" ng-model="deploy.search.offer"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                    ng-click="deploy.searchDeploys('deploy',deploy.search.deployId)">Search By Deploy ID
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" id="deploy_id" value=""
                                               placeholder="Deploy ID" ng-model="deploy.search.deployId"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <md-datepicker name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.startDate"
                                                   md-placeholder="Start Date"></md-datepicker>
                                    <md-datepicker name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.endDate"
                                                   md-placeholder="End date"></md-datepicker>
                                    <button class="btn btn-primary btn-block" type="button"
                                            ng-click="deploy.searchDeploys('date',deploy.search.dates)">Search By Date Range
                                    </button>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                    ng-click="deploy.searchDeploys('status',deploy.search.status)">Search By Status
                                            </button>
                                        </span>
                                        <select name="deploy_status" id="deploy_status"
                                                ng-model="deploy.search.status" class="form-control">
                                            <option ng-selected="'' == deploy.search.status" value="">- Please Choose a Status -</option>
                                            <option ng-selected=" 0 == deploy.search.status" value="0">Not Deployed</option>
                                            <option ng-selected=" 1 == deploy.search.status" value="1">Deployed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <button ng-click="deploy.displayForm()" class="btn btn-primary btn-sm">New Deploy</button>
                <button ng-click="deploy.exportCsv()" ng-show="deploy.exportable" class="btn btn-primary btn-sm">Export
                    to CSV
                </button>

                <button flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'deploys' , '_token' : '{{ csrf_token() }}' } }"
                        flow-files-submitted="$flow.upload()"
                        flow-file-success="deploy.fileUploaded($file); $flow.cancel()" class="btn btn-primary btn-sm"
                        flow-btn>
                    Upload Deploy List
                    <input type="file" style="visibility: hidden; position: absolute;"/>
                </button>
            </div>
        </div>
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
                        <tr ng-show="deploy.showRow">
                            <td>
                                <button ng-click="deploy.actionLink()"
                                        class="btn btn-small btn-primary">@{{ deploy.actionText() }}</button>

                                <button ng-click="deploy.showRow = false"
                                        class="btn btn-small btn-danger">Cancel</button>

                            </td>
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
                                        <option ng-repeat="option in deploy.listProfiles" value="@{{ option.id }}">
                                            @{{ option.profile_name }}
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
                            <option ng-repeat="option in deploy.creatives" value="@{{ option.id }}">
                                @{{ option.name }} - @{{ option.id }} - @{{ option.click_rate }}
                            </option>
                        </select>
                    <span class="help-block" ng-bind="deploy.formErrors.creative_id"
                          ng-show="deploy.formErrors.creative_id"></span>
                        <a ng-show="deploy.creatives.length > 0" target="_blank" href="creatives/preview/@{{ deploy.currentDeploy.offer_id }}">Preview All Creatives</a>
                    </div>
                </td>
                <td>
                    <div class="form-group"
                         ng-class="{ 'has-error' : deploy.formErrors.from_id }">
                        <select name="from_id" id="from_id"
                                ng-model="deploy.currentDeploy.from_id" class="form-control"
                                ng-disabled="deploy.offerLoading">
                            <option value="">- Please Choose a From -</option>
                            <option ng-repeat="option in deploy.froms" value="@{{ option.id }}">
                                @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate }}
                            </option>
                        </select>
                    <span class="help-block" ng-bind="deploy.formErrors.from_id"
                          ng-show="deploy.formErrors.from_id"></span>
                    </div>
                </td>
                <td>
                    <div class="form-group"
                         ng-class="{ 'has-error' : deploy.formErrors.subject_id }">
                        <select name="subject_id" id="subject_id"
                                ng-model="deploy.currentDeploy.subject_id" class="form-control"
                                ng-disabled="deploy.offerLoading">
                            <option value="">- Please Choose a Subject -</option>
                            <option ng-repeat="option in deploy.subjects" value="@{{ option.id }}">
                                @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate }}
                            </option>
                        </select>
                    <span class="help-block" ng-bind="deploy.formErrors.subject_id"
                          ng-show="deploy.formErrors.subject_id"></span>
                    </div>
                </td>
                <td>
                    <div class="form-group"
                         ng-class="{ 'has-error' : deploy.formErrors.template_id }">
                        <select name="template" id="template"
                                ng-model="deploy.currentDeploy.template_id" class="form-control"
                                ng-disabled="deploy.espLoaded">
                            <option value="">- Please Choose a Template -</option>
                            <option ng-repeat="option in deploy.templates" value="@{{ option.id }}">
                                @{{ option.template_name }}
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
                            <option ng-repeat="option in deploy.mailingDomains track by $index" value="@{{ option.id }}">
                                @{{ option.domain_name }}
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
                            <option ng-repeat="option in deploy.contentDomains" value="@{{ option.id }}">
                                @{{ option.domain_name }}
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
                            <option ng-repeat="option in deploy.cakeAffiliates" value="@{{ option.affiliateID }}">
                                @{{ option.affiliateID }}
                            </option>
                        </select>
                    <span class="help-block" ng-bind="deploy.formErrors.cake_affiliate_id"
                          ng-show="deploy.formErrors.cake_affiliate_id"></span>
                    </div>
                </td>
                <td>
                    <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.notes }">
                        <div class="form-group">
                            <textarea ng-model="deploy.currentDeploy.notes" class="form-control" rows="1"
                                      id="html"></textarea>
                        </div>
                        <span class="help-block" ng-bind="deploy.formErrors.notes"
                              ng-show="mailing.formErrors.notes"></span>
                    </div>
                </td>
                </tr>

                <tr ng-repeat="record in deploy.deploys track by $index">
                    <td>
                        <div class="checkbox">
                            <span ng-click="deploy.editRow( record.deploy_id)" class="glyphicon glyphicon-edit"></span>
                            <span ng-click="deploy.copyRow( record.deploy_id)" class="glyphicon glyphicon-copy"></span>
                            <label>
                                <input type="checkbox" name="selectedRows"
                                       ng-click="deploy.toggleRow(record.deploy_id)">
                            </label>
                        </div>
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
                        <pagination-count recordcount="deploy.paginationCount"
                                          currentpage="deploy.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="deploy.currentPage" maxpage="deploy.pageCount"
                                    disableceiling="deploy.reachedMaxPage"
                                    disablefloor="deploy.reachedFirstPage"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <deploy-validate-modal upload-errors="deploy.uploadErrors" mass-upload="deploy.massUploadList()"
                           records="deploy.uploadedDeploys"></deploy-validate-modal>


@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
