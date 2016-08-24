@extends( 'layout.default' )

@section( 'title' , 'List Profile' )

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
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="listProfile.paginationCount" currentpage="listProfile.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="listProfile.currentPage" maxpage="listProfile.pageCount"></pagination>
                    </md-input-container>
                </div>

                <listprofile-table records="listProfile.profileList" loadingflag="clientGroup.currentlyLoading" copy="listProfile.copyListProfile( event , id )" delete="listProfile.deleteListProfile( id )"></listprofile-table>

                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="listProfile.paginationCount" currentpage="listProfile.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="listProfile.currentPage" maxpage="listProfile.pageCount"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
