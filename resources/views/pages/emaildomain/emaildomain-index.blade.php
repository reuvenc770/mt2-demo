@extends( 'layout.default' )

@section( 'title' , 'ISP Domain List' )

@section ( 'angular-controller' , 'ng-controller="EmailDomainController as emailDomain"' )
@section( 'cacheTag' , 'EmailDomain' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('isp.add'))
        <li><a ng-href="/isp/create" target="_self">Add ISP Domain</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="emailDomain.loadAccounts()">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel mt2-theme-panel center-block">
                <div class="panel-heading">
                    <h3 class="panel-title">Search Domains</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <div class="input-group">
                                <span class="input-group-addon">ISP Group</span>
                                <select ng-model="emailDomain.search.domain_group_id" placeholder="" name="search_domain_group_id"  class="form-control">
                                    <option  value="">Select ISP Group</option>
                                    @foreach ($domainGroups as $domainGroup)
                                    <option ng-selected="emailDomain.search.domain_group_id == {{ $domainGroup->id }}" value="{{$domainGroup->id}}">{{$domainGroup->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="pull-right">
                    <button class="btn btn-sm mt2-theme-btn-secondary" ng-click="emailDomain.resetSearch()">Reset</button>
                    <button class="btn btn-sm mt2-theme-btn-primary" ng-click="emailDomain.searchDomain()">Search</button>
                    </div>
                </div>
            </div>
        </div>
                <md-table-container>
                    <table md-table md-progress="emailDomain.queryPromise">
                        <thead md-head md-order="emailDomain.sort" md-on-reorder="emailDomain.sortCurrentRecords" class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column md-order-by="domain_name" class="md-table-header-override-whitetext">ISP Domain Name</th>
                            <th md-column md-order-by="domain_group" class="md-table-header-override-whitetext">ISP Group</th>
                        </tr>
                        </thead>
                        <tbody md-body>
                        <tr md-row ng-repeat="record in emailDomain.accounts track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <a ng-href="@{{ '/isp/edit/' + record.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    </a>
                                </div>
                            </td>
                            <td md-cell>
                                @{{ record.domain_name }}
                            </td>
                            <td md-cell>@{{ record.domain_group }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">
                                    <md-content class="md-mt2-zeta-theme md-hue-2">
                                        <md-table-pagination md-limit="emailDomain.paginationCount" md-limit-options="emailDomain.paginationOptions" md-page="emailDomain.currentPage" md-total="@{{emailDomain.accountTotal}}" md-on-paginate="emailDomain.loadAccounts" md-page-select></md-table-pagination>
                                    </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/emaildomain/EmailDomainController.js',
        'resources/assets/js/emaildomain/EmailDomainApiService.js'],'js','pageLevel') ?>