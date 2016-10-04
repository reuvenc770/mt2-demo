@extends( 'layout.default' )

@section( 'title' , 'Deploy Packages' )

@section( 'angular-controller' , 'ng-controller="DeployController as deploy"' )

@section( 'page-menu' )
    <md-menu md-position-mode="target-right target">
        <md-button aria-label="Options" class="md-icon-button" ng-click="$mdOpenMenu($event)">
            <md-icon md-font-set="material-icons" class="mt2-icon-black">more_horiz</md-icon>
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
            @if (Sentinel::hasAccess('api.deploy.copytofuture'))
            <md-menu-item>
                <md-button ng-click="deploy.copyToFuture( $event )" ng-disabled="deploy.disableExport">
                    <span>Copy to Future</span>
                </md-button>
            </md-menu-item>
            @endif
            @if (Sentinel::hasAccess('api.deploy.deploypackages'))
            <md-menu-item>
                <md-button ng-click="deploy.createPackages()" ng-disabled="deploy.disableExport" >
                    <span>Send zips to FTP</span>
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
                    <div layout="row" layout-align="end end">
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
                    <span ng-if="deploy.searchType.length > 0">&nbsp;<md-icon md-font-set="material-icons" class="mt2-icon-black">chevron_right</md-icon> Search by @{{ deploy.searchType }}</span>
                </div>
            </md-toolbar>
        <form name="deployForm" novalidate>
            <md-table-container>
                <table md-table md-progress="deploy.queryPromise">
                    <thead md-head>
                    <tr md-row>
                        <th md-column></th>
                        <th md-column class="md-table-header-override-whitetext"></th>
                        <th md-column class="md-table-header-override-whitetext">Send Date</th>
                        <th md-column class="md-table-header-override-whitetext">Deploy ID</th>
                        <th md-column class="md-table-header-override-whitetext">ESP Account</th>
                        <th md-column class="md-table-header-override-whitetext">Offer</th>
                        <th md-column class="md-table-header-override-whitetext">Creative</th>
                        <th md-column class="md-table-header-override-whitetext">From</th>
                        <th md-column class="md-table-header-override-whitetext">Subject</th>
                        <th md-column class="md-table-header-override-whitetext">Template</th>
                        <th md-column class="md-table-header-override-whitetext">Mailing Domain</th>
                        <th md-column class="md-table-header-override-whitetext">Content Domain</th>
                        <th md-column class="md-table-header-override-whitetext">Cake ID</th>
                        <th ng-show="deploy.showRow" class="md-table-header-override-whitetext">Cake Encryption</th>
                        <th ng-show="deploy.showRow" class="md-table-header-override-whitetext">Full Encryption</th>
                        <th ng-show="deploy.showRow" class="md-table-header-override-whitetext">URL Format</th>
                        <th md-column class="md-table-header-override-whitetext">Notes</th>
                    </tr>
                    </thead>

                    <tbody md-body>
                    <tr md-row ng-show="deploy.showRow">
                        <td md-cell></td>
                        <td md-cell>
                            @if (Sentinel::hasAccess('api.deploy.update'))
                            <md-button ng-click="deploy.actionLink( $event , deployForm )" class="md-icon-button">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">save</md-icon>
                                <md-tooltip>@{{ deploy.actionText() }}</md-tooltip>
                            </md-button>
                            @endif
                            <md-button ng-click="deploy.showRow = false" class="md-icon-button">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">clear</md-icon>
                                <md-tooltip>Cancel</md-tooltip>
                            </md-button>
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
                            <md-input-container>
                                <label>ESP Account</label>
                                <md-select name="esp_account" id="esp_account" ng-required="true"
                                        ng-change="deploy.updateSelects()"
                                        ng-model="deploy.currentDeploy.esp_account_id"
                                        ng-disabled="deploy.currentlyLoading">
                                    <md-option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                            ng-selected="option.id == deploy.currentDeploy.esp_account_id">@{{ option.account_name }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.esp_account.$error">
                                    <div ng-message="required">ESP account name is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.offer_id }">
                                <div angucomplete-alt ng-required="true"
                                     id="offer"
                                     name="offer_id"
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
                            <span class="mt2-error-message" ng-bind="deploy.formErrors.offer_id"
                                  ng-show="deploy.formErrors.offer_id"></span>
                        </td>
                        <td md-cell>
                            <div layout="column">
                            <md-input-container>
                                <label>Creative</label>
                                <md-select name="creative_id" id="creative_id" ng-required="true"
                                        ng-model="deploy.currentDeploy.creative_id"
                                        ng-disabled="deploy.offerLoading">
                                    <md-option ng-repeat="option in deploy.creatives" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                        @{{ option.name }} - @{{ option.id }} - @{{ option.click_rate ? parseFloat(option.click_rate).toFixed(2) + '%' : '' }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.creative_id.$error">
                                    <div ng-message="required">A creative is required.</div>
                                </div>
                                <a ng-show="deploy.creatives.length > 0" target="_blank" href="creatives/preview/@{{ deploy.currentDeploy.offer_id }}">Preview All Creatives</a>
                            </md-input-container>
                            </div>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>From</label>
                                <md-select name="from_id" id="from_id" ng-required="true"
                                        ng-model="deploy.currentDeploy.from_id"
                                        ng-disabled="deploy.offerLoading">
                                    <md-option ng-repeat="option in deploy.froms" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                        @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate ? parseFloat(option.open_rate).toFixed(2) + '%' : '' }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.from_id.$error">
                                    <div ng-message="required">From field is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Subject</label>
                                <md-select name="subject_id" id="subject_id" ng-required="true"
                                        ng-model="deploy.currentDeploy.subject_id"
                                        ng-disabled="deploy.offerLoading">
                                    <md-option ng-repeat="option in deploy.subjects" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                        @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate ? parseFloat(option.open_rate).toFixed(2) + '%' : '' }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.subject_id.$error">
                                    <div ng-message="required">Subject is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Template</label>
                                <md-select name="template" id="template" ng-required="true"
                                        ng-model="deploy.currentDeploy.template_id"
                                        ng-disabled="deploy.espLoaded">
                                    <md-option ng-repeat="option in deploy.templates" value="@{{ option.id }}">
                                        @{{ option.template_name }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.template.$error">
                                    <div ng-message="required">Template is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Mailing Domain</label>
                                <md-select name="mailing_domain" id="mailing_domain" ng-required="true"
                                        ng-model="deploy.currentDeploy.mailing_domain_id"
                                        ng-disabled="deploy.espLoaded">
                                    <md-option ng-repeat="option in deploy.mailingDomains track by $index" value="@{{ option.id }}">
                                        @{{ option.domain_name }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.mailing_domain.$error">
                                    <div ng-message="required">Mailing domain is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Content Domain</label>
                                <md-select name="content_domain" id="content_domain" ng-required="true"
                                        ng-model="deploy.currentDeploy.content_domain_id"
                                        ng-disabled="deploy.espLoaded">
                                    <option ng-repeat="option in deploy.contentDomains" value="@{{ option.id }}">
                                        @{{ option.domain_name }}
                                </md-select>
                                <div ng-messages="deployForm.content_domain.$error">
                                    <div ng-message="required">Content domain is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Cake ID</label>
                                <md-select name="cake_affiliate_id" id="cake_affiliate_id" ng-required="true"
                                        ng-model="deploy.currentDeploy.cake_affiliate_id">
                                    <md-option ng-repeat="option in deploy.cakeAffiliates" value="@{{ option.affiliateID }}">
                                        @{{ option.affiliateID }}
                                    </md-option>
                                </md-select>
                                <div ng-messages="deployForm.cake_affiliate_id.$error">
                                    <div ng-message="required">Cake ID is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Encrypt Cake?</label>
                                <md-select name="encrypt_cake" id="encrypt_cake" ng-required="true"
                                        ng-model="deploy.currentDeploy.encrypt_cake">
                                    <md-option value="1">Yes</md-option>
                                    <md-option value="2">No</md-option>
                                </md-select>
                                <div ng-messages="deployForm.encrypt_cake.$error">
                                    <div ng-message="required">Required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Fully Encrypt Links?</label>
                                <md-select name="fully_encrypt" id="fully_encrypt" ng-required="true"
                                        ng-model="deploy.currentDeploy.fully_encrypt">
                                    <md-option value="1">Yes</md-option>
                                    <md-option value="2">No</md-option>
                                </md-select>
                                <div ng-messages="deployForm.fully_encrypt.$error">
                                    <div ng-message="required">Required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>URL Format</label>
                                <md-select name="url_format" id="url_format" ng-required="true"
                                        ng-model="deploy.currentDeploy.url_format">
                                    <md-option value="new">New</md-option>
                                    <md-option value="gmail">Gmail</md-option>
                                    <md-option value="old">Old</md-option>
                                </md-select>
                                <div ng-messages="deployForm.url_format.$error">
                                    <div ng-message="required">URL format is required.</div>
                                </div>
                            </md-input-container>
                        </td>
                        <td md-cell>
                            <md-input-container>
                                <label>Notes</label>
                                <textarea ng-model="deploy.currentDeploy.notes" rows="1" id="html"></textarea>
                            </md-input-container>
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
                                <md-button class="md-icon-button" ng-hide="record.deployment_status ==1" ng-click="deploy.editRow( record.deploy_id)" aria-label="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                </md-button>
                                <md-button class="md-icon-button" ng-click="deploy.copyRow( record.deploy_id)" aria-label="Copy">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">content_copy</md-icon>
                                    <md-tooltip md-direction="bottom">Copy Row</md-tooltip>
                                </md-button>
                            </td>
                            <td md-cell>@{{ record.send_date }}</td>
                            <td md-cell>@{{ record.deploy_id }}</td>
                            <td md-cell>@{{ record.account_name }}</td>
                            <td md-cell>@{{ record.offer_name }}</td>
                            <td md-cell>
                                @{{ record.creative }}
                                <span ng-hide="deploy.checkStatus(record.creative_approval,record.creative_status)"
                                      class="deploy-error mt2-bg-danger">!! Creative has been unapproved or deactivated !!</span>
                            </td>
                            <td md-cell>
                                @{{ record.from }}
                                <span ng-hide="deploy.checkStatus(record.from_approval,record.from_status)"
                                      class="deploy-error mt2-bg-danger">!! From has been unapproved or deactivated !!</span>
                            </td>
                            <td md-cell>
                                <span>
                                @{{ record.subject.substring(0,10) }}...
                                <md-tooltip md-direction="top">@{{ record.subject }}</md-tooltip>
                                </span>
                                <md-button class="md-icon-button" ngclipboard data-clipboard-text="@{{record.subject}}">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">content_copy</md-icon>
                                    <md-tooltip md-direction="bottom">Copy Subject</md-tooltip>
                                </md-button>
                                <span ng-hide="deploy.checkStatus(record.subject_approval,record.subject_status)"
                                      class="deploy-error mt2-bg-danger">!! Subject has been unapproved or deactivated !!</span>
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
        </form>
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
