<md-content layout="row" layout-align="left left" class="md-mt2-zeta-theme md-hue-1">
    <div flex>
        <md-card>
            <md-toolbar class="md-hue-2">
                <div class="md-toolbar-tools">
                    <span>Search Domains</span>
                </div>
            </md-toolbar>
            <md-card-content>
                <div layout="column" layout-gt-sm="row">
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Choose an ESP</label>
                            <md-select name="esp_search" id="esp_search"
                                       ng-model="domain.search.esp"
                                       ng-change="domain.updateSearchEspAccounts()"
                                       ng-disabled="domain.updatingAccounts">
                                <md-option value="">--</md-option>
                                @foreach ( $esps as $esp )
                                    <md-option value="{{ $esp['name'] }}">{{ $esp['name'] }}</md-option>
                                @endforeach
                            </md-select>
                        </md-input-container>
                    </div>
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Choose an ESP Account</label>
                            <md-select name="esp_account_search" id="esp_account_search"
                                       ng-model="domain.search.esp_account_id"
                                       ng-disabled="domain.espNotChosen">
                                <md-option value="">--</md-option>
                                <md-option ng-repeat="option in domain.espAccounts" ng-value="option.id">@{{ option.account_name }}
                                </md-option>
                            </md-select>
                        </md-input-container>

                    </div>
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Choose an DBA Account</label>
                            <md-select name="dba_search" id="dba_search"
                                       ng-model="domain.search.doing_business_as_id"
                                       ng-disabled="domain.updatingAccounts">
                                <md-option value="">--</md-option>
                                @foreach ( $dbas as $dba )
                                    <md-option value="{{ $dba['id'] }}">{{ $dba['dba_name'] }}</md-option>
                                @endforeach
                            </md-select>
                        </md-input-container>

                    </div>
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Choose an Registrar</label>
                            <md-select name="registrar_search" id="registrar_search"
                                       ng-model="domain.search.registrar_id"
                                       ng-disabled="domain.updatingAccounts">
                                <md-option value="">--</md-option>
                                @foreach ( $regs as $reg )
                                    <md-option value="{{ $reg['id'] }}">{{ $reg['name'] }}</md-option>
                                @endforeach
                            </md-select>
                        </md-input-container>

                    </div>
                </div>
                <div layout="column" layout-gt-sm="row">
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Domain Name* wildcard</label>
                            <input type="text" id="search_domain" value="" ng-model="domain.search.domain"/>
                        </md-input-container>
                    </div>
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Choose a Proxy</label>
                            <md-select name="proxy_search" id="proxy_search"
                                       ng-model="domain.search.proxy_id"
                                       ng-disabled="domain.updatingAccounts">
                                <md-option value="">--</md-option>
                                <md-option ng-repeat="option in domain.proxies" ng-value="option.id">@{{ option.name  }}
                                </md-option>
                            </md-select>
                        </md-input-container>
                    </div>
                    <div layout="row" layout-gt-sm="column" flex-gt-sm="25" flex="100">
                        <md-input-container flex>
                            <label>Choose a Domain Type</label>
                            <md-select name="domain_type" id="domain_type"
                                       ng-model="domain.search.domain_type"
                                       ng-disabled="domain.updatingAccounts">
                                <md-option value="">--</md-option>
                                <md-option value="1">Mailing Domains</md-option>
                                <md-option value="2">Content Domains</md-option>
                                </md-option>
                            </md-select>
                        </md-input-container>
                    </div>
                </div>
                <div layout="row">
                    <md-button class="md-raised md-accent" ng-click="domain.searchDomains()">Search</md-button>
                </div>
            </md-card-content>
        </md-card>
    </div>
</md-content>