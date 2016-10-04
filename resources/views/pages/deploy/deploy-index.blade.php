@extends( 'layout.default' )

@section( 'title' , 'Deploy Packages' )

@section( 'angular-controller' , 'ng-controller="DeployController as deploy"' )

@section( 'page-menu' )
    <div ng-hide="app.isMobile()">
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

            @if (Sentinel::hasAccess('api.deploy.copytofuture'))
                <md-button ng-click="deploy.copyToFuture( $event )" ng-disabled="deploy.disableExport">
                    <span>Copy To Future</span>
                </md-button>
            @endif

            @if (Sentinel::hasAccess('api.deploy.deploypackages'))
                <md-button ng-click="deploy.createPackages()" ng-disabled="deploy.disableExport" >
                    <span>@{{ deploy.deployLinkText }}</span>
                </md-button>
            @endif
            @if (Sentinel::hasAccess('deploy.preview'))

                    <md-button ng-click="deploy.previewDeploys()" ng-disabled="deploy.disableExport">
                        <span>Preview Deploy(s)</span>
                    </md-button>
            @endif

            @if (Sentinel::hasAccess('deploy.downloadhtml'))
                    <md-button ng-click="deploy.downloadHtml()" ng-disabled="deploy.disableExport">
                        <span>Get Html</span>
                    </md-button>
            @endif

    </div>

    <md-menu ng-show="app.isMobile()" md-position-mode="target-right target">
        <md-button aria-label="Options" class="md-icon-button" ng-click="$mdOpenMenu($event)">
            <md-icon md-svg-src="img/icons/ic_more_horiz_black_24px.svg"></md-icon>
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
            <md-menu-item>
                <md-button ng-click="deploy.exportCsv()" ng-disabled="deploy.disableExport">
                    <span>Export to CSV</span>
                </md-button>
            </md-menu-item>
            @endif
            @if (Sentinel::hasAccess('api.deploy.deploypackages'))
            <md-menu-item>
                <md-button ng-click="deploy.createPackages()" ng-disabled="deploy.disableExport" >
                    <span>Deploy Packages</span>
                </md-button>
            </md-menu-item>
                    @if (Sentinel::hasAccess('deploy.preview'))
                        <md-menu-item>
                        <md-button ng-click="deploy.previewDeploys()" ng-disabled="deploy.disableExport">
                            <span>Preview Deploy(s)</span>
                        </md-button>
                            </md-menu-item>
                    @endif

            @endif
                @if (Sentinel::hasAccess('deploy.downloadhtml'))
                    <md-menu-item>
                        <md-button ng-click="deploy.downloadHtml()" ng-disabled="deploy.disableExport">
                            <span>Get Html</span>
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
                <md-card-content>
                    <div layout="column" layout-gt-sm="row">
                        <div layout="row" flex-gt-sm="45">
                            <md-input-container flex>
                                <label>Choose an ESP</label>
                                <md-select name="esp_account_search" id="esp_account_search"
                                    ng-model="deploy.search.esp"
                                    ng-disabled="deploy.currentlyLoading">
                                    <md-option value="">--</md-option>
                                    @foreach ( $esps as $esp )
                                        <md-option value="{{ $esp['name'] }}">{{ $esp['name'] }}</md-option>
                                    @endforeach
                                </md-select>
                            </md-input-container>
                        </div>
                        <div flex hide-sm hide-xs></div>
                        <div layout="row" flex-gt-sm="45">
                            <md-input-container flex>
                                <label>Choose an ESP Account</label>
                                <md-select name="esp_account_search" id="esp_account_search"
                                        ng-model="deploy.search.esp_account_id"
                                        ng-disabled="deploy.currentlyLoading">
                                    <md-option value="">--</md-option>
                                    <md-option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                            ng-selected="option.id == deploy.search.esp_account_id">@{{ option.account_name }}
                                    </md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>
                    <div layout="column" layout-gt-sm="row">
                        <div layout="row" flex-gt-sm="45">
                            <md-input-container flex>
                                <label>Offer Name* wildcard</label>
                                <input type="text" id="search_offer" value="" ng-model="deploy.search.offer"/>
                            </md-input-container>
                        </div>
                        <div flex hide-sm hide-xs></div>
                        <div layout="row" flex-gt-sm="45">
                            <md-input-container flex>
                                <label>Deploy ID</label>
                                <input id="deploy_id" value="" ng-model="deploy.search.deployId"/>
                            </md-input-container>
                        </div>
                    </div>
                    <div layout="column" layout-gt-sm="row">
                        <div layout="column" flex-gt-sm="45">
                            <div layout="row">
                                <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.startDate"
                                               md-placeholder="Start Date"></md-datepicker>
                                <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.endDate"
                                               md-placeholder="End date"></md-datepicker>
                           </div>
                        </div>
                        <div flex hide-sm hide-xs></div>
                        <div layout="row" flex-gt-sm="45">
                            <md-input-container flex>
                                <label>Choose a Status</label>
                                <md-select name="deploy_status" id="deploy_status"
                                        ng-model="deploy.search.status">
                                    <md-option ng-selected="'' == deploy.search.status" value="">Clear Search</md-option>
                                    <md-option ng-selected=" 0 == deploy.search.status" value="0">Not Deployed</md-option>
                                    <md-option ng-selected=" 1 == deploy.search.status" value="1">Deployed</md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>
                    <div layout="row">
                        <md-button class="md-raised md-accent" ng-click="deploy.searchDeploys()">Search</md-button>
                    </div>
                </md-card-content>
            </md-card>
        </div>
    </md-content>

    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-toolbar class="md-hue-2">
                <div class="md-toolbar-tools">
                    <span>Deploys</span>
                    <span ng-if="deploy.searchType.length > 0">&nbsp;<md-icon md-svg-src="img/icons/ic_chevron_right_black_36px.svg"></md-icon> Search by @{{ deploy.searchType }}</span>
                </div>
            </md-toolbar>
            <md-table-container>
                <table md-table md-progress="deploy.queryPromise">
                    <thead md-head>
                    <tr md-row>
                        <th md-column></th>
                        <th md-column class="md-table-header-override-whitetext">
                            <strong><span class="glyphicon glyphicon-refresh rotateMe"
                                          ng-if="deploy.loadingflag == 1"></span></strong>
                        </th>
                        <th md-column class="md-table-header-override-whitetext">Send Date</th>
                        <th md-column class="md-table-header-override-whitetext">Deploy ID</th>
                        <th md-column class="md-table-header-override-whitetext">EspAccount</th>
                        <th md-column class="md-table-header-override-whitetext">Offer</th>
                        <th md-column class="md-table-header-override-whitetext">Creative</th>
                        <th md-column class="md-table-header-override-whitetext">From</th>
                        <th md-column class="md-table-header-override-whitetext">Subject</th>
                        <th md-column class="md-table-header-override-whitetext">Template</th>
                        <th md-column class="md-table-header-override-whitetext">Mailing Domain</th>
                        <th md-column class="md-table-header-override-whitetext">Content Domain</th>
                        <th md-column class="md-table-header-override-whitetext">Cake ID</th>
                        <th ng-show="deploy.showRow" class="md-table-header-override-whitetext" >Cake Encryption</th>
                        <th ng-show="deploy.showRow" class="md-table-header-override-whitetext" >Full Encryption</th>
                        <th ng-show="deploy.showRow" class="md-table-header-override-whitetext">URL Format</th>
                        <th md-column class="md-table-header-override-whitetext">Notes</th>
                    </tr>
                    </thead>

                    <tbody md-body>
                    <tr md-row ng-show="deploy.showRow">
                        <td md-cell></td>
                        <td md-cell>
                            @if (Sentinel::hasAccess('api.deploy.update'))
                            <button ng-click="deploy.actionLink()"
                                    class="btn btn-small btn-primary">@{{ deploy.actionText() }}</button>
                            @endif
                            <button ng-click="deploy.showRow = false"
                                    class="btn btn-small btn-danger">Cancel</button>

                        </td>
                        <td md-cell>
                            <md-datepicker name="dateField" ng-model="deploy.currentDeploy.send_date"
                                 required md-placeholder="Enter date" ng-disabled="deploy.offerLoading" md-date-filter="deploy.canOfferBeMailed">
                            </md-datepicker>
                            <div class="validation-messages" ng-show="deploy.formErrors.send_date">
                                <div ng-bind="deploy.formErrors.send_date"></div>
                            </div>

                        </td>
                        <td md-cell>@{{ deploy.deployIdDisplay }}</td>
                        <td md-cell>
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
                        <td md-cell>
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
                        <td md-cell>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : deploy.formErrors.creative_id }">
                                <select name="creative_id" id="creative_id"
                                        ng-model="deploy.currentDeploy.creative_id" class="form-control"
                                        ng-disabled="deploy.offerLoading">
                                    <option value="">- Please Choose a Creative -</option>
                                    <option ng-repeat="option in deploy.creatives" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                        @{{ option.name }} - @{{ option.id }} - @{{ option.click_rate ? parseFloat(option.click_rate).toFixed(2) + '%' : '' }}
                                    </option>
                                </select>
                            <span class="help-block" ng-bind="deploy.formErrors.creative_id"
                                  ng-show="deploy.formErrors.creative_id"></span>
                                <a ng-show="deploy.creatives.length > 0" target="_blank" href="creatives/preview/@{{ deploy.currentDeploy.offer_id }}">Preview All Creatives</a>
                            </div>
                        </td>
                        <td md-cell>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : deploy.formErrors.from_id }">
                                <select name="from_id" id="from_id"
                                        ng-model="deploy.currentDeploy.from_id" class="form-control"
                                        ng-disabled="deploy.offerLoading">
                                    <option value="">- Please Choose a From -</option>
                                    <option ng-repeat="option in deploy.froms" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                        @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate ? parseFloat(option.open_rate).toFixed(2) + '%' : '' }}
                                    </option>
                                </select>
                            <span class="help-block" ng-bind="deploy.formErrors.from_id"
                                  ng-show="deploy.formErrors.from_id"></span>
                            </div>
                        </td>
                        <td md-cell>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : deploy.formErrors.subject_id }">
                                <select name="subject_id" id="subject_id"
                                        ng-model="deploy.currentDeploy.subject_id" class="form-control"
                                        ng-disabled="deploy.offerLoading">
                                    <option value="">- Please Choose a Subject -</option>
                                    <option ng-repeat="option in deploy.subjects" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                        @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate ? parseFloat(option.open_rate).toFixed(2) + '%' : '' }}
                                    </option>
                                </select>
                            <span class="help-block" ng-bind="deploy.formErrors.subject_id"
                                  ng-show="deploy.formErrors.subject_id"></span>
                            </div>
                        </td>
                        <td md-cell>
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
                        <td md-cell>
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
                        <td md-cell>
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
                        <td md-cell>
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
                        <td md-cell>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : deploy.formErrors.encrypt_cake }">
                                <select name="encrypt_cake" id="encrypt_cake"
                                        ng-model="deploy.currentDeploy.encrypt_cake" class="form-control">
                                    <option value="">- Encrypt Cake? -</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                    <span class="help-block" ng-bind="deploy.formErrors.encrypt_cake"
                          ng-show="deploy.formErrors.encrypt_cake"></span>
                            </div>
                        </td>
                        <td md-cell>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : deploy.formErrors.fully_encrypt }">
                                <select name="fully_encrypt" id="fully_encrypt"
                                        ng-model="deploy.currentDeploy.fully_encrypt" class="form-control">
                                    <option value="">- Fully Encrypt Links? -</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                    <span class="help-block" ng-bind="deploy.formErrors.fully_encrypt"
                          ng-show="deploy.formErrors.fully_encrypt"></span>
                            </div>
                        </td>
                        <td md-cell>
                            <div class="form-group"
                                 ng-class="{ 'has-error' : deploy.formErrors.url_format }">
                                <select name="url_format" id="url_format"
                                        ng-model="deploy.currentDeploy.url_format" class="form-control">
                                    <option value="">- Pick URL Format -</option>
                                    <option value="new">New</option>
                                    <option value="gmail">Gmail</option>
                                    <option value="old">Old</option>

                                </select>
                    <span class="help-block" ng-bind="deploy.formErrors.url_format"
                          ng-show="deploy.formErrors.url_format"></span>
                            </div>
                        </td>
                        <td md-cell>
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.notes }">
                            <div class="form-group">
                                <textarea ng-model="deploy.currentDeploy.notes" class="form-control" rows="1"
                                          id="html"></textarea>
                            </div>
                            </div>
                        </td>
                        </tr>

                        <tr md-row ng-repeat="record in deploy.deploys track by $index" ng-class="{ 'mt2-bg-info' : record.deployment_status == 0,
                                         'mt2-bg-success' : record.deployment_status ==1,
                                         'mt2-warning' : record.deployment_status == 2 }">
                            <td md-cell>
                                <md-checkbox ng-checked="deploy.checkChecked(record.deploy_id)" ng-show="@{{deploy.checkStatus(record.creative_approval,record.creative_status)
                                && deploy.checkStatus(record.from_approval,record.from_status)
                                && deploy.checkStatus(record.subject_approval,record.subject_status)}}" aria-label="Select" name="selectedRows"
                                             ng-click="deploy.toggleRow(record.deploy_id)"> </md-checkbox>
                            </td>
                            <td md-cell>
                                <md-button ng-hide="record.deployment_status ==1" class="md-raised" ng-click="deploy.editRow( record.deploy_id)">Edit</md-button>
                                <md-button class="md-raised md-accent" ng-click="deploy.copyRow( record.deploy_id)">Copy</md-button>
                            </td>
                            <td md-cell>@{{ record.send_date }}</td>
                            <td md-cell>@{{ record.deploy_id }}</td>
                            <td md-cell>@{{ record.account_name }}</td>
                            <td md-cell>@{{ record.offer_name }}</td>
                            <td md-cell>
                                @{{ record.creative }}
                                <span ng-hide="deploy.checkStatus(record.creative_approval,record.creative_status)"
                                      class="deploy-error mt2-bg-danger">!! Creative has been unapproved or deactivate !!</span>
                            </td>
                            <td md-cell>
                                @{{ record.from }}
                                <span ng-hide="deploy.checkStatus(record.from_approval,record.from_status)"
                                      class="deploy-error mt2-bg-danger">!! From has been unapproved or deactivate !!</span>
                            </td>
                            <td md-cell>
                                @{{ record.subject }}
                                <span ng-hide="deploy.checkStatus(record.subject_approval,record.subject_status)"
                                      class="deploy-error mt2-bg-danger">!! Subject has been unapproved or deactivate !!</span>
                            </td>
                            <td md-cell>@{{ record.template_name }}</td>
                            <td md-cell>@{{ record.mailing_domain }}</td>
                            <td md-cell>@{{ record.content_domain }}</td>
                            <td md-cell>@{{ record.cake_affiliate_id }}</td>
                            <td ng-show="deploy.showRow" md-cell></td>
                            <td ng-show="deploy.showRow" md-cell></td>
                            <td ng-show="deploy.showRow" md-cell></td>
                            <td md-cell>@{{ record.notes }}</td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

            <md-content class="md-mt2-zeta-theme md-hue-2">
                <md-table-pagination md-limit="deploy.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="deploy.currentPage" md-total="@{{deploy.deployTotal}}" md-on-paginate="deploy.loadAccounts" md-page-select></md-table-pagination>
            </md-content>

        </md-card>
    </md-content>
</md-card-content>
    <deploy-validate-modal upload-errors="deploy.uploadErrors" mass-upload="deploy.massUploadList()"
                           records="deploy.uploadedDeploys"></deploy-validate-modal>
@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
