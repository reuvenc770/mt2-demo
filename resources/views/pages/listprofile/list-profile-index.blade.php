@extends( 'layout.default' )

@section( 'title' , 'Client Group' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('listprofile.add'))
        <md-button ng-click="listProfile.viewAdd()" aria-label="Add List Profile">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add List Profile</span>
        </md-button>
    @endif
@stop

@section( 'content' )

<div ng-init="listProfile.loadListProfiles()">
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="listProfile.paginationCount" currentpage="listProfile.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="listProfile.currentPage" maxpage="listProfile.pageCount"></pagination>
                </div>
            </div>

            <listprofile-table records="listProfile.profileList" loadingflag="clientGroup.currentlyLoading" copy="listProfile.copyListProfile( event , id )" delete="listProfile.deleteListProfile( id )"></clientgroup-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="listProfile.paginationCount" currentpage="listProfile.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="listProfile.currentPage" maxpage="listProfile.pageCount"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
