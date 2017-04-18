@extends( 'layout.default' )

@section( 'title' , 'CPM Pricing' )

@section( 'angular-controller' , 'ng-controller="CpmPricingController as cpm"' )

@section( 'cacheTag' , 'CpmPricing' )

@section( 'page-menu' )
        <li><a href="#" ng-click="cpm.addSchedule()">Add CPM Pricing</a></li>
        <li><a href="#" ng-click="cpm.addOverride()">Add CPM Deploy ID Override</a></li>
@stop

@section( 'content' )
<div ng-init="cpm.loadPricings()" >
    <div style="width:800px;">
        <div class="panel mt2-theme-panel center-block">
            <div class="panel-heading">
                <h3 class="panel-title">Search Cpm Pricings</h3>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Offer Name</span>

                                <input type="text" id="search-offer-name" class="form-control" value="" ng-model="cpm.search.offer_name"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Cake Offer ID</span>

                                <input type="text" id="search-offer-id" class="form-control" value="" ng-model="cpm.search.offer_id"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Deploy ID</span>

                                <input type="text" id="search-deploy-id" class="form-control" value="" ng-model="cpm.search.deploy_id"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.startDate"
                                               md-placeholder="Start Date"></md-datepicker>
                                <md-datepicker flex="50" name="dateField" ng-change="deploy.updateSearchDate()" ng-model="deploy.search.endDate"
                                               md-placeholder="End date"></md-datepicker>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pull-right">
                    <button class="btn btn-sm mt2-theme-btn-secondary" ng-click="feed.resetSearch()">Reset</button>
                    <button class="btn btn-sm mt2-theme-btn-primary" ng-click="feed.searchFeeds()">Search</button>
                </div>
            </div>
        </div>
    </div>

    <md-table-container>
        <table md-table md-progress="cpm.queryPromise" class="mt2-table-large">
            <thead md-head class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="name" class="md-table-header-override-whitetext">Name</th>
                    <th md-column md-order-by="cake_offer_id" class="md-table-header-override-whitetext">Cake Offer ID</th>
                    <th md-column md-order-by="is_deploy_override" class="md-table-header-override-whitetext">DID Override</th>
                    <th md-column md-order-by="deploy_id" class="md-table-header-override-whitetext">Deploy ID</th>
                    <th md-column md-order-by="pricing" class="md-table-header-override-whitetext">Pricing</th>
                    <th md-column md-order-by="start_date" class="md-table-header-override-whitetext">Start Date</th>
                    <th md-column md-order-by="end_date" class="md-table-header-override-whitetext">End Date</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="price in cpm.pricings track by $index">
                    <td md-cell class="mt2-table-btn-column">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black icon-xs" ng-click="cpm.edit( price )" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">edit</md-icon>
                    </td>
                    <td md-cell ng-bind="price.name || 'Offer Unknown'"></td>
                    <td md-cell ng-bind="price.offer_id || 'Offer ID Unknown'"></td>
                    <td md-cell>@{{ price.deploy_id > 0 ? 'Yes' : 'No' }}</td>
                    <td md-cell ng-bind="price.deploy_id"></td>
                    <td md-cell ng-bind="price.amount | currency:'$':2"></td>
                    <td md-cell ng-bind="price.start_date"></td>
                    <td md-cell ng-bind="price.end_date"></td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

    <div style="visibility:hidden;">
        <div class="md-dialog-container" id="formModal">
            <md-dialog>
                <md-toolbar class="mt2-theme-toolbar">
                    <div class="md-toolbar-tools mt2-theme-toolbard-tools">
                        <h2 ng-bind="cpm.modalInfo.title[ cpm.formType ]"></h2>

                        <span flex></span>

                        <md-button class="md-icon-button" ng-click="cpm.closeModal()"><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                    </div>
                </md-toolbar>

                <md-dialog-content>
                    <div class="md-dialog-content">
                        <div class="form-horizontal">
                            <div class="panel panel-info">
                                <div class="panel-heading" ng-bind="cpm.modalInfo.panel_title[ cpm.formType ]"></div>

                                <div class="panel-body">
                                    <div class="col-md-6" ng-show="cpm.formType == 'schedule'">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-md-3 control-label">Offer</label>

                                            <div class="col-sm-10 col-md-9">
                                                <div    angucomplete-alt
                                                        ng-required="true"
                                                        placeholder="Search CPM Offers"
                                                        selected-object="cpm.storeSelectedOffer"
                                                        remote-url="/api/offer/search?cpm=1&searchTerm="
                                                        title-field="name,id"
                                                        text-searching="Looking for Offers..."
                                                        min-length="3"
                                                        input-class="form-control"
                                                        initial-value="cpm.prepopOfferName"
                                                        disable-input="!cpm.isNewRecord"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6" ng-show="cpm.formType == 'override'">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-md-3 control-label">Deploy ID</label>

                                            <div class="col-sm-10 col-md-9">
                                                <input type="text" id="form-amount" class="form-control" value="" ng-disabled="!cpm.isNewRecord" ng-model="cpm.currentOverride.deploy_id"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-md-3 control-label">Pricing</label>

                                            <div class="col-sm-10 col-md-9">
                                                <input type="text" id="form-amount" class="form-control" value="" ng-model="cpm[ ( cpm.formType === 'schedule' ? 'currentPricing' : 'currentOverride' ) ].amount"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading">Run Date Range</div>

                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <md-datepicker flex="50" name="dateField" ng-change="cpm.updateDateRange()" ng-model="cpm.startDateState"
                                                                   md-placeholder="Start Date"></md-datepicker>
                                                    <md-datepicker flex="50" name="dateField" ng-change="cpm.updateDateRange()" ng-model="cpm.endDateState"
                                                                   md-placeholder="End date"></md-datepicker>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </md-dialog-content>

                <md-dialog-actions>
                    <div class="col-md-4">
                        <input class="btn mt2-theme-btn-primary btn-block" ng-click="cpm.saveForm()" ng-disabled="cpm.formSubmitting" type="submit" ng-value="'Save ' + cpm.modalInfo.submit_text[ cpm.formType ]">
                    </div>
                </md-dialog-actions>
            </md-dialog>
        </div>
    </div>
</div>
@stop

<?php 
Assets::add( [
    'resources/assets/js/cpm/CpmPricingController.js' ,
    'resources/assets/js/cpm/CpmPricingApiService.js'
] , 'js' , 'pageLevel' ) 
?>
