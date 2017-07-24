@extends( 'layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Cake Affiliates' )

@section( 'angular-controller' , 'ng-controller="CakeAffiliateController as aff"' )

@section( 'cacheTag' , 'Builder' )

@section( 'page-menu' )
    <li ng-click="aff.showAddDialog()" role="button"><a href="#">Add Cake Affiliate/Redirect</a></li>
@stop

@section( 'content' )
<div ng-init="aff.loadAffiliateRedirectDomains()">
    <div class="navbar navbar-topper navbar-primary" role="navigation">
        <div class="container-fluid">
            <a class="navbar-brand pull-left md-table-header-override-whitetext">Cake Affiliates And Redirect Domains</a>
        </div>
    </div>

    <md-table-container>
        <table md-table class="mt2-table-large" md-progress="aff.affiliatePromise"> <!-- Add promise when ready -->
            <thead md-head class="mt2-theme-thead" md-order="aff.sort" md-on-reorder="aff.loadAffiliateRedirectDomains">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="cake_redirect_id" class="md-table-header-override-whitetext">ID</th>
                    <th md-column md-order-by="id" class="md-table-header-override-whitetext">Affiliate ID</th>
                    <th md-column md-order-by="name" class="md-table-header-override-whitetext">Affiliate Name</th>
                    <th md-column md-order-by="offer_type" class="md-table-header-override-whitetext">Offer Type</th>
                    <th md-column md-order-by="redirect_domain" class="md-table-header-override-whitetext">Redirect Domain</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in aff.affiliateRedirects track by $index">
                    <td md-cell class="mt2-table-btn-column mt2-cell-left-padding" nowrap>
                        <a aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit Affiliate" ng-click="aff.showEditDialog( $index )">
                            <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                        </a>
                    </td>
                    <td md-cell ng-bind="record.cake_redirect_id"></td>
                    <td md-cell ng-bind="record.id"></td>
                    <td md-cell ng-bind="record.name"></td>
                    <td md-cell ng-bind="record.offer_type"></td>
                    <td md-cell ng-bind="record.redirect_domain"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <md-content class="md-mt2-zeta-theme">
                            <md-table-pagination md-limit="aff.paginationCount" md-limit-options="aff.paginationOptions" md-page="aff.currentPage" md-total="@{{aff.affiliateTotal}}" md-on-paginate="aff.loadAffiliateRedirectDomains" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>


<div style="visibility: hidden;">
    <div class="md-dialog-container" id="addRedirectModal" flex="100" layout-align="center center">
        <md-dialog flex=50>
            <md-toolbar class="mt2-theme-toolbar">
                <div class="mt2-toolbar-tools mt2-theme-toolbar-tools">
                    <h3 class="pull-left" ng-bind="aff.currentDialogTitle"></h3>
                    <span flex></span>
                    <md-button class="md-icon-button pull-right" ng-click="aff.closeDialog()"><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                </div>
            </md-toolbar>

            <md-dialog-content>
                <div class="md-dialog-content">
                    <div class="panel panel-info">
                        <div class="panel-heading">Affiliate Redirect Domain Details</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-horizontal">
                                    <div class="col-sm-12" style="margin-bottom: 1em;">
                                        <input id="newAffiliateButton" class="btn btn-block" ng-click="aff.showNewAffiliateFields()" ng-class="{ 'mt2-theme-btn-primary' : aff.showNewAffiliateFieldsFlag , 'btn-success' : !aff.showNewAffiliateFieldsFlag }" ng-value="aff.showNewAffiliateButtonText">
                                    </div>

                                    <div class="form-group" ng-show="!aff.showNewAffiliateFieldsFlag">
                                        <label class="control-label col-sm-2" for="affiliateId">Affiliate</label>
                                        <div class="col-sm-9">
                                            <select id="affiliateId" class="form-control" ng-model="aff.currentRedirect.id" required> 
                                                <option value="">Select Affiliate</option>
                                                @foreach( $affiliateList as $affiliate )
                                                <option value="{{ $affiliate[ 'id' ] }}">{{ $affiliate[ 'name' ] }}</option>
                                                @endforeach
                                            </select>

                                            <div class="help-block" ng-show="aff.formErrors.id">
                                                <div ng-repeat="error in aff.formErrors.id">
                                                    <span class="text-danger" ng-bind="error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-show="aff.showNewAffiliateFieldsFlag">
                                        <label class="control-label col-sm-2" for="newAffiliateId">New Affiliate ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="newAffiliateId" class="form-control" ng-model="aff.currentRedirect.new_affiliate_id" placeholder="Affiliate ID" />

                                            <div class="help-block" ng-show="aff.formErrors.new_affiliate_id">
                                                <div ng-repeat="error in aff.formErrors.new_affiliate_id">
                                                    <spa class="text-danger"n ng-bind="error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-show="aff.showNewAffiliateFieldsFlag">
                                        <label class="control-label col-sm-2" for="newAffiliateName">New Affiliate Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="newAffiliateName" class="form-control" ng-model="aff.currentRedirect.new_affiliate_name" placeholder="Affiliate Name" />

                                            <div class="help-block" ng-show="aff.formErrors.new_affiliate_name">
                                                <div ng-repeat="error in aff.formErrors.new_affiliate_name">
                                                    <span class="text-danger" ng-bind="error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="offerType">Offer Types</label>
                                        <div class="col-sm-9">
                                            <select id="offerType" class="form-control" ng-model="aff.currentRedirect.offer_payout_type_id" required> 
                                                <option value="">Select Offer Type</option>
                                                @foreach( $offerTypeList as $offerType )    
                                                <option value="{{ $offerType[ 'id' ] }}">{{ $offerType[ 'name' ] }}</option>
                                                @endforeach
                                            </select>

                                            <div class="help-block" ng-show="aff.formErrors.offer_payout_type_id">
                                                <div ng-repeat="error in aff.formErrors.offer_payout_type_id">
                                                    <span class="text-danger" ng-bind="error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="redirectDomain">Redirect Domain</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="redirectDomain" class="form-control" ng-model="aff.currentRedirect.redirect_domain" placeholder="Redirect Domain" />

                                            <div class="help-block" ng-show="aff.formErrors.redirect_domain">
                                                <div ng-repeat="error in aff.formErrors.redirect_domain">
                                                    <span class="text-danger" ng-bind="error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </md-dialog-content>

            <md-dialog-actions layout="row" layout-align="center center" class="mt2-theme-dialog-footer layout-align-center-center layout-row">
                <div class="col-md-4">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-click="aff.saveRedirect()" ng-disabled="aff.formSubmitted" type="submit" ng-value="aff.currentDialogButton">
                </div>
            </md-dialog-actions>
        </md-dialog>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/pages/CakeAffiliateController.js',
        'resources/assets/js/pages/CakeAffiliateApiService.js' ,
        ],'js','pageLevel') ?>
