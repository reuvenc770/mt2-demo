@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Deploy Packages' )

@section( 'angular-controller' , 'ng-controller="DeployController as deploy"' )

@section( 'page-menu' )
@if (Sentinel::hasAccess('api.deploy.store'))
<li ng-click="deploy.displayForm()">
    <a href="#">New Deploy</a>
</li>
@endif

@if (Sentinel::hasAccess('api.attachment.upload'))
<li flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'deploys' , '_token' : '{{ csrf_token() }}' } }"
    flow-files-submitted="$flow.upload()"
    flow-file-success="deploy.fileUploaded($file); $flow.cancel()" flow-btn>
        <a href="#">Upload Deploy List</a>
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
    <a href="#">Copy to Future</a>
</li>
@endif

@if (Sentinel::hasAccess('api.deploy.deploypackages'))
<li ng-click="deploy.createPackages()" ng-disabled="deploy.disableExport" >
    <a href="#">Send zips to FTP</a>
</li>
@endif

@if (Sentinel::hasAccess('deploy.preview'))
<li ng-click="deploy.previewDeploys()" ng-disabled="deploy.disableExport">
    <a href="#">Preview Deploy(s)</a>
</li>
@endif

@if (Sentinel::hasAccess('deploy.downloadhtml'))
<li ng-click="deploy.downloadHtml()" ng-disabled="deploy.disableExport">
    <a href="#">Get Html</a>
</li>
@endif
@stop

@section( 'content' )
<div ng-init="deploy.loadAccounts()">
    <div layout="row" layout-align="left left">
        <div style="width:800px">
            <div class="panel panel-primary center-block"> 
                <div class="panel-heading">   
                    <h3 class="panel-title">Search Deploys</h3> 
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
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

                        <div class="col-lg-6">
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

                    <br />

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">Offer Name* Wildcard</span>
                                <input type="text" id="search_offer" class="form-control" value="" ng-model="deploy.search.offer"/>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">Deploy ID</span>
                                <input id="deploy_id" value="" class="form-control" ng-model="deploy.search.deployId"/>
                            </div>
                        </div>
                    </div>

                    <br />

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">Status</span>
                                <select name="deploy_status" id="deploy_status" class="form-control" ng-model="deploy.search.status">
                                    <option ng-selected="'' == deploy.search.status" value="">Clear Search</option>
                                    <option ng-selected=" 0 == deploy.search.status" value="0">Not Deployed</option>
                                    <option ng-selected=" 1 == deploy.search.status" value="1">Deployed</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.startDate"
                                           md-placeholder="Start Date"></md-datepicker>
                            <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.endDate"
                                           md-placeholder="End date"></md-datepicker>
                        </div>
                    </div>

                    <br />

                    <button class="btn btn-primary pull-right" ng-click="deploy.searchDeploys()">Search</button>
                </div>
            </div>
        </div>
    </div>

    <div layout="column" class="md-mt2-zeta-theme md-hue-1" flex="none">
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
                        <th md-column ng-show="deploy.showRow" class="md-table-header-override-whitetext">Cake Encryption</th>
                        <th md-column ng-show="deploy.showRow" class="md-table-header-override-whitetext">Full Encryption</th>
                        <th md-column ng-show="deploy.showRow" class="md-table-header-override-whitetext">URL Format</th>
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
                                 required md-placeholder="Enter date"
                                           ng-disabled="deploy.offerLoading"
                                           md-date-filter="deploy.canOfferBeMailed"
                                           md-min-date="deploy.minDate">
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
    </div>
</div>
    <deploy-validate-modal upload-errors="deploy.uploadErrors" mass-upload="deploy.massUploadList()"
                           records="deploy.uploadedDeploys"></deploy-validate-modal>
@stop

<?php
Assets::add( [
    'resources/assets/js/bootstrap/deploy/DeployController.js' ,
    'resources/assets/js/bootstrap/deploy/DeployApiService.js' ,
    'resources/assets/js/bootstrap/deploy/DeployValidateModalDirective.js'
] , 'js' , 'pageLevel' );
?>
