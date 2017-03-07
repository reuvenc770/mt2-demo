@extends( 'layout.default' )

@section( 'title' , 'Deploy Packages' )

@section( 'container' , 'container-fluid' )

@section( 'angular-controller' , 'ng-controller="DeployController as deploy"' )
@section( 'cacheTag' , 'Deploy' )
@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.deploy.store'))
        <li ng-click="deploy.displayNewDeployForm()">
            <a href="#">New Deploy</a>
        </li>
    @endif

    @if (Sentinel::hasAccess('api.attachment.upload'))
        <li flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'deploys' , '_token' : '{{ csrf_token() }}' } }"
            flow-files-submitted="$flow.upload()"
            flow-file-success="deploy.fileUploaded($file); $flow.cancel()" flow-btn>
            <a href="#">Upload Deploy List
                <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="File must include headers and be in CSV format.">help</md-icon></a>
            <input type="file" style="visibility: hidden; position: absolute;"/>
        </li>
    @endif

    @if (Sentinel::hasAccess('api.deploy.exportcsv'))
        <li ng-click="deploy.exportCsv()" ng-disabled="deploy.disableExport">
            <a href="#">Export to CSV</a>
        </li>
    @endif

    @if (Sentinel::hasAccess('api.deploy.copytofuture'))
        <li ng-click="deploy.copyToFuture( $event )" ng-disabled="deploy.disableExport">
            <a href="#">Copy to Future
                <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Copy selected deploy(s), including original deploy information, and schedule for a different send date. Different from an individual copy of a deploy row where all fields can be modified.">help</md-icon>
            </a>
        </li>
    @endif

    @if (Sentinel::hasAccess('api.deploy.deploypackages'))
        <li ng-click="deploy.createPackages()" ng-disabled="deploy.disableExport" >
            <a href="#">Send Zips to FTP
                <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Each selected deploy will be saved as an individual zip file and saved to FTP.">help</md-icon>
            </a>
        </li>
    @endif

    @if (Sentinel::hasAccess('deploy.preview'))
        <li ng-click="deploy.previewDeploys()" ng-disabled="deploy.disableExport">
            <a href="#">Preview Deploy(s)
                <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Preview creatives of selected deploys in new browser window.">help</md-icon>
            </a>
        </li>
    @endif

    @if (Sentinel::hasAccess('deploy.downloadhtml'))
        <li ng-click="deploy.downloadHtml()" ng-disabled="deploy.disableExport">
            <a href="#">Get Html
                <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="View raw HTML of selected deploys in new browser window.">help</md-icon>
            </a>
        </li>
    @endif
@stop

@section( 'content' )
    <div ng-init="deploy.loadAccounts()">
        <div class="col-xs-12 col-md-9">
            <div class="panel mt2-theme-panel center-block">
                <div class="panel-heading">
                    <h3 class="panel-title">Search Deploys</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">ESP</span>
                                <select name="esp_account_search" id="esp_account_search" class="form-control" ng-model="deploy.search.esp" ng-disabled="deploy.currentlyLoading">
                                    <option value="">---</option>
                                    @foreach ( $esps as $esp )
                                        <option value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">ESP Account</span>
                                <select name="esp_account_search" id="esp_account_search" class="form-control" ng-model="deploy.search.esp_account_id" ng-disabled="deploy.currentlyLoading">
                                    <option value=""></option>
                                    <option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                            ng-selected="option.id == deploy.search.esp_account_id">@{{ option.account_name }}
                                    </option>
                                </select>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Offer Name* Wildcard</span>
                                <input type="text" id="search_offer" class="form-control" value="" ng-model="deploy.search.offer"/>
                            </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Deploy ID</span>
                                <input id="deploy_id" value="" class="form-control" ng-model="deploy.search.deployId"/>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Status</span>
                                <select name="deploy_status" id="deploy_status" class="form-control" ng-model="deploy.search.status">
                                    <option ng-selected="'' == deploy.search.status" value="">Clear Search</option>
                                    <option ng-selected=" 0 == deploy.search.status" value="0">Not Deployed</option>
                                    <option ng-selected=" 1 == deploy.search.status" value="1">Deployed</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">List Profile Party</span>
                                    <select name="party_search" id="party_search" class="form-control" ng-model="deploy.search.list_profile_party">
                                        <option ng-selected="'' == deploy.search.list_profile_party" value="">Select Party</option>
                                        <option ng-selected=" 1 == deploy.search.list_profile_party" value="1">1st Party</option>
                                        <option ng-selected=" 3 == deploy.search.list_profile_party" value="3">3rd Party</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.startDate"
                                           md-placeholder="Start Date"></md-datepicker>
                            <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.endDate"
                                           md-placeholder="End date"></md-datepicker>
                        </div>
                    </div>

                    <br />
                    <div class="pull-right">
                        <button class="btn mt2-theme-btn-secondary btn-sm" ng-click="deploy.resetSearch()">Reset</button>
                        <button class="btn mt2-theme-btn-primary btn-sm" ng-click="deploy.searchDeploys()">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title">Table Display Options</h3></div>
                <div class="panel-body">
                    <div class="col-xs-12 col-sm-4 col-md-12 no-padding">
                        <md-switch class="no-margin" ng-model="deploy.columnToggleMapping['cfs']['showColumns']" aria-label="Show/Hide CFS Columns" title="@{{ deploy.columnToggleMapping['cfs']['switchText'] }} CFS" ng-change="deploy.toggleColumns( 'cfs' )"> @{{ deploy.columnToggleMapping['cfs']['switchText'] }} CFS Columns</md-switch>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-12 no-padding">
                        <md-switch class="no-margin" ng-model="deploy.columnToggleMapping['domains']['showColumns']" aria-label="Show/Hide CFS Columns" title="@{{ deploy.columnToggleMapping['domains']['switchText'] }} }} CFS" ng-change="deploy.toggleColumns( 'domains' )"> @{{ deploy.columnToggleMapping['domains']['switchText'] }} Domain Columns</md-switch>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form name="deployForm" novalidate ng-init="deploy.loadLastColumnView()">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <md-table-container>
            <table md-table md-row-select="true" multiple md-progress="deploy.queryPromise" ng-model="deploy.selectedRows">
                <thead md-head md-order="deploy.sort" md-on-reorder="deploy.sortCurrentRecords" class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="send_date" class="md-table-header-override-whitetext">Send Date</th>
                    <th md-column md-order-by="deploy_id" class="md-table-header-override-whitetext">Deploy ID</th>
                    <th md-column class="md-table-header-override-whitetext">ESP Account</th>
                    <th md-column class="md-table-header-override-whitetext">List Profile</th>
                    <th md-column class="md-table-header-override-whitetext">Offer</th>
                    <th md-column class="md-table-header-override-whitetext" ng-show="deploy.columnToggleMapping['cfs']['showColumns']">Creative</th>
                    <th md-column class="md-table-header-override-whitetext" ng-show="deploy.columnToggleMapping['cfs']['showColumns']">From</th>
                    <th md-column class="md-table-header-override-whitetext" ng-show="deploy.columnToggleMapping['cfs']['showColumns']">Subject</th>
                    <th md-column class="md-table-header-override-whitetext">Template</th>
                    <th md-column class="md-table-header-override-whitetext" ng-show="deploy.columnToggleMapping['domains']['showColumns']">Mailing Domain</th>
                    <th md-column class="md-table-header-override-whitetext" ng-show="deploy.columnToggleMapping['domains']['showColumns']">Content Domain</th>
                    <th md-column class="md-table-header-override-whitetext">Cake ID</th>
                    <th md-column class="md-table-header-override-whitetext">Notes</th>
                </tr>
                </thead>

                <tbody md-body>
                <tr md-row ng-repeat="record in deploy.deploys track by $index" class="table-row-condensed"
                    ng-class="{ 'bg-success' : record.deployment_status == 1 ,
                        'bg-warning' : record.deployment_status == 0 || record.deployment_status == 2 ,
                        'bg-info' : record.deployment_status == 3 }"
                    ng-show="@{{deploy.checkStatus(record.creative_approval,record.creative_status)
                            && deploy.checkStatus(record.from_approval,record.from_status)
                            && deploy.checkStatus(record.subject_approval,record.subject_status)}}"
                    md-select="record.deploy_id" md-on-select="deploy.checkExportStatus()">
                    <td md-cell class="mt2-table-btn-column" nowrap>
                            @if (Sentinel::hasAccess('api.deploy.update'))
                            <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs" ng-hide="record.deployment_status ==1" ng-click="deploy.editDeploy( record.deploy_id)" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">edit</md-icon>
                            @endif

                            <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs" ng-click="deploy.copyRow( record.deploy_id)" aria-label="Copy" data-toggle="tooltip" data-placement="bottom" title="Copy">content_copy</md-icon>

                    </td>
                    <td md-cell nowrap>@{{ record.send_date }}</td>
                    <td md-cell>@{{ record.deploy_id }}</td>
                    <td md-cell>@{{ record.account_name }}</td>
                    <td md-cell>@{{ record.list_profile }}</td>
                    <td md-cell nowrap>
                            <span data-toggle="popover" data-content="@{{ record.offer_name }}">
                            @{{ record.offer_name.substring(0,20) }}...
                            </span>
                    </td>
                    <td md-cell nowrap ng-show="deploy.columnToggleMapping['cfs']['showColumns']">
                            <span data-toggle="popover" data-content="@{{ record.creative }}">
                                @{{ record.creative.substring(0,20) }}...
                            </span>
                            <span ng-hide="deploy.checkStatus(record.creative_approval,record.creative_status)"
                                  class="deploy-error bg-danger">!! Creative has been unapproved or deactivated !!</span>
                    </td>
                    <td md-cell nowrap ng-show="deploy.columnToggleMapping['cfs']['showColumns']">
                        <div layout="row" layout-align="space-between center">
                            <span data-toggle="popover" data-content="@{{ record.from }}">
                                @{{ record.from.substring(0,20) }}...
                            </span>
                            <span ng-hide="deploy.checkStatus(record.from_approval,record.from_status)"
                                  class="deploy-error bg-danger">!! From has been unapproved or deactivated !!</span>
                            <md-button class="md-icon-button icon-button-xs" ngclipboard data-clipboard-text="@{{record.from}}" data-toggle="tooltip" data-placement="bottom" title="Copy From">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs">content_copy</md-icon>
                            </md-button>
                        </div>
                    </td>
                    <td md-cell nowrap ng-show="deploy.columnToggleMapping['cfs']['showColumns']">
                        <div layout="row" layout-align="space-between center">
                            <span data-toggle="popover" data-content="@{{ record.subject }}">
                                @{{ record.subject.substring(0,10) }}...
                            </span>
                            <md-button class="md-icon-button icon-button-xs" ngclipboard data-clipboard-text="@{{record.subject}}" data-toggle="tooltip" data-placement="bottom" title="Copy Subject">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs">content_copy</md-icon>
                            </md-button>
                        </div>
                            <span ng-hide="deploy.checkStatus(record.subject_approval,record.subject_status)"
                                  class="deploy-error bg-danger">!! Subject has been unapproved or deactivated !!</span>
                    </td>
                    <td md-cell>@{{ record.template_name }}</td>
                    <td md-cell ng-show="deploy.columnToggleMapping['domains']['showColumns']">@{{ record.mailing_domain }}</td>
                    <td md-cell ng-show="deploy.columnToggleMapping['domains']['showColumns']">@{{ record.content_domain }}</td>
                    <td md-cell>@{{ record.cake_affiliate_id }}</td>
                    <td md-cell>@{{ record.notes }}</td>
                </tr>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="15">
                        <md-content class="md-mt2-zeta-theme md-hue-2">
                            <md-table-pagination md-limit="deploy.paginationCount" md-limit-options="deploy.paginationOptions" md-page="deploy.currentPage" md-total="@{{deploy.deployTotal}}" md-on-paginate="deploy.loadAccounts" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
                </tfoot>
            </table>
        </md-table-container>
    </form>
    </div>
    <deploy-validate-modal upload-errors="deploy.uploadErrors" mass-upload="deploy.massUploadList()"
                           records="deploy.uploadedDeploys" close-modal="deploy.closeModal()"></deploy-validate-modal>

<div style="visibility: hidden">
    <div class="md-dialog-container" id="deployFormModal">
        <md-dialog>
            <md-toolbar class="mt2-theme-toolbar">
                <div class="md-toolbar-tools mt2-theme-toolbar-tools">
                    <h2>@{{ deploy.formHeader }}</h2>
                    <span flex></span>
                    <md-button class="md-icon-button" ng-click="deploy.closeModal()"><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                </div>
            </md-toolbar>
            <md-dialog-content>
            <div class="md-dialog-content">
                <div class="form-horizontal">

        <div class="panel panel-info">
            <div class="panel-heading">General</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.send_date }">
                                <label class="col-sm-2 col-md-3 control-label">Send Date</label>
                                <div class="col-sm-10 col-md-9">
                                    <md-datepicker name="dateField" ng-model="deploy.currentDeploy.send_date"
                                                   required md-placeholder="Enter date"
                                                   ng-change="deploy.updateDate()"
                                                   md-min-date="deploy.minDate">
                                    </md-datepicker>

                                    <div class="help-block" ng-show="deploy.formErrors.send_date">
                                        <div ng-repeat="error in deploy.formErrors.send_date">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.esp_account_id }">
                                <label class="col-sm-2 col-md-3 control-label">ESP Account</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="esp_account" id="esp_account" class="form-control"
                                            ng-change="deploy.updateSelects()"
                                            ng-model="deploy.currentDeploy.esp_account_id"
                                            ng-disabled="deploy.currentlyLoading">
                                        <option value="">ESP Account</option>
                                        <option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.esp_account_id">@{{ option.account_name }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.esp_account_id">
                                        <div ng-repeat="error in deploy.formErrors.esp_account_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.list_profile_combine_id }">
                                <label class="col-sm-2 col-md-3 control-label">List Profile</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="list_profile" id="list_profile" class="form-control" ng-class="{ 'has-error' : deploy.formErrors.list_profile_combine_id }"
                                            ng-model="deploy.currentDeploy.list_profile_combine_id"
                                            ng-disabled="deploy.currentlyLoading">
                                        <option value="">List Profile</option>
                                        <option ng-repeat="option in deploy.listProfiles" ng-value="option.id"
                                                ng-selected="option.id == deploy.currentDeploy.list_profile_combine_id">@{{ option.name }}
                                        </option>
                                    </select>
                                    <label style="font-weight: normal;">
                                        <input ng-model="deploy.currentDeploy.party" ng-change="deploy.toggleListProfile()" ng-true-value="1" ng-false-value="3" type="checkbox"> First Party Workflow Deploy
                                    </label>
                                    <div class="help-block" ng-show="deploy.formErrors.list_profile_combine_id">
                                        <div ng-repeat="error in deploy.formErrors.list_profile_combine_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.cake_affiliate_id }">
                                <label class="col-sm-2 col-md-3 control-label">Cake ID</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="cake_affiliate_id" id="cake_affiliate_id" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.cake_affiliate_id">
                                        <option value="">Cake ID</option>
                                        <option ng-repeat="option in deploy.cakeAffiliates" value="@{{ option.affiliateID }}">
                                            @{{ option.affiliateID }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.cake_affiliate_id">
                                        <div ng-repeat="error in deploy.formErrors.cake_affiliate_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.offer_id }">
                                <label class="col-sm-2 col-md-3 control-label">Offer <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="A send date must be selected before picking an offer. To search for offers type, start by typing the first 3 letters of offer name.">help</md-icon></label>
                                <div class="col-sm-10 col-md-9">
                                    <div ng-hide="deploy.allOffers">
                                    <div angucomplete-alt ng-required="true"
                                         id="offer"
                                         name="offer_id"
                                         disable-input="deploy.dateNotPicked"
                                         placeholder="Search Offers"
                                         selected-object="deploy.offerWasSelected"
                                         selected-object-data="deploy.currentDeploy.offer_id"
                                         remote-url="/api/offer/search?day=@{{deploy.selectedDay}}&searchTerm="
                                         title-field="name,id"
                                         text-searching="Looking for Offers..."
                                         selected-object-data="offer"
                                         minlength="3"
                                         input-class="form-control">
                                        </div>
                                        </div>
                                    <div ng-show="deploy.allOffers">
                                        <select name="offer_id"  id="offer_id" ng-disabled="deploy.dateNotPicked" class="form-control" ng-required="true"
                                                ng-model="deploy.currentDeploy.offer_id" ng-change="deploy.offerWasSelected()">
                                            <option ng-repeat="option in deploy.allOffersData" value="@{{ option.id }}">
                                                @{{ option.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div ng-click="deploy.hideAllOffers()" ng-class="{ 'text-muted' : deploy.allOffers }" style="font-size:11px; text-align: center; cursor: pointer; padding-top: 5px">Search Offers</div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div ng-click="deploy.showAllOffers()" ng-class="{ 'text-muted' : !deploy.allOffers }" style="font-size:11px; text-align: center; cursor: pointer; padding-top: 5px">View All Offers</div>
                                        </div>
                                    </div>
                                    <div class="help-block" ng-show="deploy.formErrors.offer_id">
                                        <div ng-repeat="error in deploy.formErrors.offer_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">Select an offer to view options for Creative, From, and Subject.</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.creative_id }">
                                <label class="col-sm-2 col-md-3 control-label">Creative</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="creative_id" id="creative_id" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.creative_id"
                                            ng-disabled="deploy.offerLoading">
                                        <option value="">Creative</option>
                                        <option ng-repeat="option in deploy.creatives" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                            @{{ option.name }} - @{{ option.id }} - @{{ option.click_rate ? parseFloat(option.click_rate).toFixed(2) + '%' : '' }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.creative_id">
                                        <div ng-repeat="error in deploy.formErrors.creative_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>

                                    <a ng-show="deploy.creatives.length > 0" target="_blank" href="creatives/preview/@{{ deploy.currentDeploy.offer_id }}">Preview All Creatives</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.from_id }">
                                <label class="col-sm-2 col-md-3 control-label">From</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="from_id" id="from_id" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.from_id"
                                            ng-disabled="deploy.offerLoading">
                                        <option value="">From</option>
                                        <option ng-repeat="option in deploy.froms" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                            @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate ? parseFloat(option.open_rate).toFixed(2) + '%' : '' }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.from_id">
                                        <div ng-repeat="error in deploy.formErrors.from_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.subject_id }">
                                <label class="col-sm-2 col-md-3 control-label">Subject</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="subject_id" id="subject_id" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.subject_id"
                                            ng-disabled="deploy.offerLoading">
                                        <option value="">Subject</option>
                                        <option ng-repeat="option in deploy.subjects" value="@{{ option.id }}" class="@{{option.days_ago <= 1 ? 'mt2-bg-super-danger' : ''}}">
                                            @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate ? parseFloat(option.open_rate).toFixed(2) + '%' : '' }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.subject_id">
                                        <div ng-repeat="error in deploy.formErrors.subject_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <div class="panel panel-info">
                 <div class="panel-heading">Select an ESP account to view options for Template, Mailing Domain, and Content Domain.</div>
                 <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.template_id }">
                                <label class="col-sm-2 col-md-3 control-label">Template</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="template" id="template" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.template_id"
                                            ng-disabled="deploy.espLoaded">
                                        <option value="">Template</option>
                                        <option ng-repeat="option in deploy.templates" value="@{{ option.id }}">
                                            @{{ option.template_name }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.template_id">
                                        <div ng-repeat="error in deploy.formErrors.template_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.mailing_domain_id }">
                                <label class="col-sm-2 col-md-3 control-label">Mailing Domain</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="mailing_domain" id="mailing_domain" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.mailing_domain_id"
                                            ng-disabled="deploy.espLoaded">
                                        <option value="">Mailing Domain</option>
                                        <option ng-repeat="option in deploy.mailingDomains track by $index" value="@{{ option.id }}">
                                            @{{ option.domain_name }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.mailing_domain_id">
                                        <div ng-repeat="error in deploy.formErrors.mailing_domain_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.content_domain_id }">
                                <label class="col-sm-2 col-md-3 control-label">Content Domain</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="content_domain" id="content_domain" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.content_domain_id"
                                            ng-disabled="deploy.espLoaded">
                                        <option value="">Content Domain</option>
                                        <option ng-repeat="option in deploy.contentDomains" value="@{{ option.id }}">
                                            @{{ option.domain_name }}
                                        </option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.content_domain_id">
                                        <div ng-repeat="error in deploy.formErrors.content_domain_id">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-heading">Link Format</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.encrypt_cake }">
                                <label class="col-sm-2 col-md-3 control-label">Encrypt Cake?</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="encrypt_cake" id="encrypt_cake" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.encrypt_cake">
                                        <option value="">Encrypt Cake?</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.encrypt_cake">
                                        <div ng-repeat="error in deploy.formErrors.encrypt_cake">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.fully_encrypt }">
                                <label class="col-sm-2 col-md-3 control-label">Fully Encrypt Links?</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="fully_encrypt" id="fully_encrypt" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.fully_encrypt">
                                        <option value="">Fully Encrypt Links?</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.fully_encrypt">
                                        <div ng-repeat="error in deploy.formErrors.fully_encrypt">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.url_format }">
                                <label class="col-sm-2 col-md-3 control-label">URL Format</label>
                                <div class="col-sm-10 col-md-9">
                                    <select name="url_format" id="url_format" class="form-control" ng-required="true"
                                            ng-model="deploy.currentDeploy.url_format">
                                        <option value="">URL Format</option>
                                        <option value="long">Long</option>
                                        <option value="short">Short</option>
                                        <option value="encrypt">Encrypt</option>
                                    </select>

                                    <div class="help-block" ng-show="deploy.formErrors.url_format">
                                        <div ng-repeat="error in deploy.formErrors.url_format">
                                            <span ng-bind="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="col-md-1 control-label">Notes</label>
                            <div class="col-md-11">
                                <textarea ng-model="deploy.currentDeploy.notes" class="form-control" rows="2" id="html"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </md-dialog-content>
            <md-dialog-actions layout="row" layout-align="center center" class="mt2-theme-dialog-footer">
                <div class="col-md-4">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-click="deploy.actionLink( $event , deployForm )" ng-disabled="deploy.formSubmitting" type="submit" value="@{{ deploy.formButtonText}}">
                </div>
            </md-dialog-actions>
        </md-dialog>
    </div>
</div>
@stop

<?php
Assets::add( [
        'resources/assets/js/deploy/DeployController.js' ,
        'resources/assets/js/deploy/DeployApiService.js' ,
        'resources/assets/js/deploy/DeployValidateModalDirective.js'
] , 'js' , 'pageLevel' );
?>