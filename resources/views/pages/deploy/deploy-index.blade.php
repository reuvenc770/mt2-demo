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
                    <span>@{{ deploy.deployLinkText }}</span>
                </md-button>
            @endif

    </div>

    <md-menu ng-hide="app.largePageWidth()" md-position-mode="target-right target">
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
            @endif
        </md-menu-content>
    </md-menu>
@stop

@section( 'content' )
    <md-card-content ng-init="deploy.loadAccounts()">
        <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
            <div flex-gt-md="60" flex="100">
                <md-card>
                    <md-toolbar>
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
                                        ng-model="deploy.search.esp_id"
                                        ng-disabled="deploy.currentlyLoading">
                                        @foreach ( $esps as $esp )
                                            <md-option value="{{ $esp['name'] }}">{{ $esp['name'] }}</md-option>
                                        @endforeach
                                    </md-select>
                                </md-input-container>
                                <div>
                                    <md-button class="md-raised md-accent" ng-click="deploy.searchDeploys('esp',deploy.search.esp_id)">
                                        Search By ESP
                                    </md-button>
                                </div>
                            </div>
                            <div flex hide-sm hide-xs></div>
                            <div layout="row" flex-gt-sm="45">
                                <md-input-container flex>
                                    <label>Choose an ESP Account</label>
                                    <md-select name="esp_account_search" id="esp_account_search"
                                            ng-model="deploy.search.esp_account_id"
                                            ng-disabled="deploy.currentlyLoading">
                                        <md-option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                                ng-selected="option.id == deploy.search.esp_account_id">@{{ option.account_name }}
                                        </md-option>
                                    </md-select>
                                </md-input-container>
                                    <div>
                                        <md-button class="md-raised md-accent" ng-click="deploy.searchDeploys('espAccount',deploy.search.esp_account_id)">
                                            Search By ESP Account
                                        </md-button>
                                    </div>
                            </div>
                        </div>
                        <div layout="column" layout-gt-sm="row">
                            <div layout="row" flex-gt-sm="45">
                                <md-input-container flex>
                                    <label>Offer Name* wildcard</label>
                                    <input type="text" id="search_offer" value="" ng-model="deploy.search.offer"/>
                                </md-input-container>
                                <div>
                                    <md-button class="md-raised md-accent"
                                        ng-click="deploy.searchDeploys('offer',deploy.search.offer)">Search By Offer
                                    </md-button>
                                </div>
                            </div>
                            <div flex hide-sm hide-xs></div>
                            <div layout="row" flex-gt-sm="45">
                                <md-input-container flex>
                                    <label>Deploy ID</label>
                                    <input id="deploy_id" value="" ng-model="deploy.search.deployId"/>
                                </md-input-container>
                                <div>
                                    <md-button class="md-raised md-accent"
                                            ng-click="deploy.searchDeploys('deploy',deploy.search.deployId)">Search By Deploy ID
                                    </md-button>
                                </div>
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
                                <div layout="column">
                                    <md-button flex="grow" class="md-raised md-accent"
                                            ng-click="deploy.searchDeploys('date',deploy.search.dates)">Search By Date Range
                                    </md-button>
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
                                <div>
                                    <md-button class="md-raised md-accent"
                                            ng-click="deploy.searchDeploys('status',deploy.search.status)">Search By Status
                                    </md-button>
                                </div>
                            </div>
                        </div>
                    </md-card-content>
                </md-card>
            </div>
        </md-content>

        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                @include( 'pages.deploy.deploy-table' )
            </md-card>
        </md-content>
        <deploy-validate-modal upload-errors="deploy.uploadErrors" mass-upload="deploy.massUploadList()"
                           records="deploy.uploadedDeploys"></deploy-validate-modal>
    </md-card-content>


@stop

@section( 'pageIncludes' )
    <script src="js/deploy.js"></script>
@stop
