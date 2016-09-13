@extends( 'layout.default' )

@section( 'title' , 'Deploy Packages' )

@section( 'angular-controller' , 'ng-controller="DeployController as deploy"' )

@section( 'page-menu' )
    <div ng-show="app.largePageWidth()">
        @if (Sentinel::hasAccess('api.deploy.store'))
        <md-button ng-click="deploy.displayForm()">
            <span>New Deploy</span>
        </md-button>
        @endif
        @if (Sentinel::hasAccess('api.attachment.upload'))
        <md-button flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'deploys' , '_token' : '{{ csrf_token() }}' } }"
                flow-files-submitted="$flow.upload()"
                flow-file-success="deploy.fileUploaded($file); $flow.cancel()" flow-btn>
                <span>Upload Deploy List</span>
                <input type="file" style="visibility: hidden; position: absolute;"/>
        </md-button>
        @endif
        @if (Sentinel::hasAccess('api.deploy.exportcsv'))
        <md-button ng-click="deploy.exportCsv()" ng-disabled="deploy.disableExport">
            <span>Export to CSV</span>
        </md-button>
        @endif

            @if (Sentinel::hasAccess('api.deploy.deploypackages'))
                <md-button ng-click="deploy.createPackages()" ng-disabled="deploy.disableExport" >
                    <span>Deploy Packages</span>
                </md-button>
            @endif

    </div>

    <md-menu ng-hide="app.largePageWidth()" md-position-mode="target-right target">
        <md-button aria-label="Options" class="md-icon-button" ng-click="$mdOpenMenu($event)">
            <md-icon md-svg-src="img/icons/ic_more_horiz_white_24px.svg"></md-icon>
        </md-button>
        <md-menu-content width="3">
            @if (Sentinel::hasAccess('api.deploy.store'))
            <md-menu-item>
                <md-button ng-click="deploy.displayForm()">
                    <span>New Deploy</span>
                </md-button>
            </md-menu-item>
            @endif
            @if (Sentinel::hasAccess('api.attachment.upload'))
            <md-menu-item>
                <md-button flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'deploys' , '_token' : '{{ csrf_token() }}' } }"
                        flow-files-submitted="$flow.upload()"
                        flow-file-success="deploy.fileUploaded($file); $flow.cancel()" flow-btn>
                        <span>Upload Deploy List</span>
                        <input type="file" style="visibility: hidden; position: absolute;"/>
                </md-button>
            </md-menu-item>
            @endif
            @if (Sentinel::hasAccess('api.deploy.exportcsv'))
            <md-menu-item ng-show="deploy.exportable">
                <md-button ng-click="deploy.exportCsv()">
                    <span>Export to CSV</span>
                </md-button>
            </md-menu-item>
            @endif
        </md-menu-content>
    </md-menu>
@stop

@section( 'content' )
    <md-card-content ng-init="deploy.loadAccounts()">
        <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
            <div flex-gt-md="60" flex="100">
                <md-card>
                    <md-toolbar class="md-hue-2">
                        <div class="md-toolbar-tools">
                            <span>Search Deploys</span>
                        </div>
                    </md-toolbar>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="input-group">
                                        <select name="esp_account_search" id="esp_account_search"
                                                ng-model="deploy.search.esp_id" class="form-control"
                                                ng-disabled="deploy.currentlyLoading">
                                            <option value="">- Please Choose an ESP -</option>
                                            @foreach ( $esps as $esp )
                                                <option value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                    ng-click="deploy.searchDeploys('esp',deploy.search.esp_id)">Search By Esp
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select name="esp_account_search" id="esp_account_search"
                                                ng-model="deploy.search.esp_account_id" class="form-control"
                                                ng-disabled="deploy.currentlyLoading">
                                            <option value="">- Please Choose an ESP Account -</option>
                                            <option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                                    ng-selected="option.id == deploy.search.esp_account_id">@{{ option.account_name }}
                                            </option>
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                ng-click="deploy.searchDeploys('espAccount',deploy.search.esp_account_id)">Search By Esp Account
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search_offer" value=""
                                               placeholder="Offer Name* wildcard" ng-model="deploy.search.offer"/>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                ng-click="deploy.searchDeploys('offer',deploy.search.offer)">Search By Offer
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="deploy_id" value=""
                                               placeholder="Deploy ID" ng-model="deploy.search.deployId"/>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                    ng-click="deploy.searchDeploys('deploy',deploy.search.deployId)">Search By Deploy ID
                                            </button>
                                        </span>
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
                                        <select name="deploy_status" id="deploy_status"
                                                ng-model="deploy.search.status" class="form-control">
                                            <option ng-selected="'' == deploy.search.status" value="">- Please Choose a Status -</option>
                                            <option ng-selected=" 0 == deploy.search.status" value="0">Not Deployed</option>
                                            <option ng-selected=" 1 == deploy.search.status" value="1">Deployed</option>
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="button"
                                                    ng-click="deploy.searchDeploys('status',deploy.search.status)">Search By Status
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </md-card>
            </div>
        </md-content>

        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-toolbar class="md-hue-2">
                    <div class="md-toolbar-tools">
                        <span>Deploys </span>
                        <span ng-if="deploy.searchType.length > 0">&nbsp;<md-icon md-svg-src="img/icons/ic_chevron_right_black_36px.svg"></md-icon> Search by @{{ deploy.searchType }}</span>
                    </div>
                </md-toolbar>
                <md-card-content class="no-padding">
                    <div id="mtTableContainer" class="table-responsive">
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
                                    @if (Sentinel::hasAccess('api.deploy.update'))
                                    <button ng-click="deploy.actionLink()"
                                            class="btn btn-small btn-primary">@{{ deploy.actionText() }}</button>
                                    @endif
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
                        </div>
                    </td>
                            </tr>

                    <tr ng-repeat="record in deploy.deploys track by $index" ng-class="{ info : record.deployment_status == 0,
                                                                                     success : record.deployment_status ==1,
                                                                                     warning : record.deployment_status == 2
                                                                                     }">
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
                </md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="deploy.paginationCount"
                                          currentpage="deploy.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="deploy.currentPage" maxpage="deploy.pageCount"
                                    disableceiling="deploy.reachedMaxPage"
                                    disablefloor="deploy.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </div>
    </md-content>
    <deploy-validate-modal upload-errors="deploy.uploadErrors" mass-upload="deploy.massUploadList()"
                           records="deploy.uploadedDeploys"></deploy-validate-modal>


@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
