@extends( 'layout.default' )

@section( 'title' , 'CPM Pricing' )

@section( 'angular-controller' , 'ng-controller="CpmPricingController as cpm"' )

@section( 'cacheTag' , 'Builder' )

@section( 'page-menu' )
        <li><a href="#" ng-click="cpm.addSchedule()">Add CPM Pricing</a></li>
@stop

@section( 'content' )
<div ng-init="cpm.loadPricings()" >
    <md-table-container>
        <table md-table md-progress="cpm.queryPromise" class="mt2-table-large">
            <thead md-head class="mt2-theme-thead" md-order="cpm.sort" md-on-reorder="cpm.loadPricings">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="name" class="md-table-header-override-whitetext">Name</th>
                    <th md-column md-order-by="offer_id" class="md-table-header-override-whitetext">Offer ID</th>
                    <th md-column md-order-by="cake_offer_id" class="md-table-header-override-whitetext">Cake Offer ID</th>
                    <th md-column md-order-by="amount" class="md-table-header-override-whitetext">Pricing</th>
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
                    <td md-cell ng-bind="price.cake_offer_id || 'CAKE Offer ID Unknown'"></td>
                    <td md-cell ng-bind="price.amount | currency:'$':2"></td>
                    <td md-cell ng-bind="price.start_date"></td>
                    <td md-cell ng-bind="price.end_date"></td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="7">
                        <md-content class="md-mt2-zeta-theme">
                            <md-table-pagination md-limit="cpm.paginationCount" md-limit-options="cpm.paginationOptions" md-page="cpm.currentPage" md-total="@{{cpm.pricingsTotal}}" md-on-paginate="cpm.loadPricings" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>

    <div style="visibility:hidden;">
        <div class="md-dialog-container" id="formModal">
            <md-dialog>
                <md-toolbar class="mt2-theme-toolbar">
                    <div class="md-toolbar-tools mt2-theme-toolbard-tools">
                        <h2>@{{ cpm.isNewRecord ? 'Add' : 'Update' }} CPM Pricing</h2>

                        <span flex></span>

                        <md-button class="md-icon-button" ng-click="cpm.closeModal()"><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                    </div>
                </md-toolbar>

                <md-dialog-content>
                    <div class="md-dialog-content">
                        <div class="form-horizontal">
                            <div class="panel panel-info">
                                <div class="panel-heading">Pricing Details</div>

                                <div class="panel-body">
                                    <div class="col-md-6">
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

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-sm-2 col-md-3 control-label">Pricing</label>

                                            <div class="col-sm-10 col-md-9">
                                                <input type="text" id="form-amount" class="form-control" value="" ng-model="cpm.currentPricing.amount"/>
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
                        <input class="btn mt2-theme-btn-primary btn-block" ng-click="cpm.saveForm()" ng-disabled="cpm.formSubmitting" type="submit" ng-value="( cpm.isNewRecord ? 'Save' : 'Update' ) + ' Pricing'">
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
