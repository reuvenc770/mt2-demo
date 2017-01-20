<div class="panel mt2-theme-panel">
    <div class="panel-heading">
        <div class="panel-title">Search Domains</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Choose an ESP</label>
                    <select name="esp_search" class="form-control" id="esp_search"
                            ng-model="domain.search.esp"
                            ng-change="domain.updateSearchEspAccounts()"
                            ng-disabled="domain.updatingAccounts">
                        <option value="">--</option>
                        @foreach ( $esps as $esp )
                            <option value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Choose an ESP Account</label>
                    <select name="esp_account_search" class="form-control" id="esp_account_search"
                               ng-model="domain.search.esp_account_id"
                               ng-disabled="domain.espNotChosen">
                        <option value="">--</option>
                        <option ng-repeat="option in domain.espAccounts" ng-value="option.id">@{{ option.account_name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Choose an DBA Account</label>
                    <select name="dba_search" class="form-control" id="dba_search"
                               ng-model="domain.search.doing_business_as_id"
                               ng-disabled="domain.updatingAccounts">
                        <option value="">--</option>
                        @foreach ( $dbas as $dba )
                            <option value="{{ $dba['id'] }}">{{ $dba['dba_name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Choose an Registrar</label>
                    <select name="registrar_search" class="form-control" id="registrar_search"
                               ng-model="domain.search.registrar_id"
                               ng-disabled="domain.updatingAccounts">
                        <option value="">--</option>
                        @foreach ( $regs as $reg )
                            <option value="{{ $reg['id'] }}">{{ $reg['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
            <div class="form-group">
                <label>Domain Name* wildcard</label>
                <input class="form-control" type="text" id="search_domain" value="" ng-model="domain.search.domain"/>
            </div>
        </div>
            <div class="col-sm-4">
                <label>Choose a Proxy</label>
                <select class="form-control" name="proxy_search" id="proxy_search"
                           ng-model="domain.search.proxy_id"
                           ng-disabled="domain.updatingAccounts">
                    <option value="">--</option>
                    <option ng-repeat="option in domain.proxies" ng-value="option.id">@{{ option.name  }}
                    </option>
                </select>
            </div>
            <div class="col-sm-4">
                <label>Choose a Domain Type</label>
                <select name="domain_type" class="form-control" id="domain_type"
                           ng-model="domain.search.domain_type"
                           ng-disabled="domain.updatingAccounts">
                    <option value="">--</option>
                    <option value="1">Mailing Domains</option>
                    <option value="2">Content Domains</option>
                </select>
            </div>
        </div>

        <div class="pull-right">
            <input class="btn mt2-theme-btn-secondary btn-sm" ng-click="domain.resetSearch()" type="submit" value="Reset">
            <input class="btn mt2-theme-btn-primary btn-sm" ng-click="domain.searchDomains()" type="submit" value="Search">
        </div>
    </div>
</div>