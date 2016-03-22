@extends( 'layout.default' )

@section( 'title' , 'Client Group' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">List Profile</h1></div>
</div>

<div ng-controller="ListProfileController as listProfile" ng-init="listProfile.loadListProfiles()">
    @if (Sentinel::hasAccess('listprofile.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="listProfile.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add List Profile</button>
    </div>
    @endif
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
