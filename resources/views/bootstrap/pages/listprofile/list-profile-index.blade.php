@extends( 'bootstrap.layout.default' )

@section( 'title' , 'List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('listprofile.add'))
        <li ng-click="app.redirect( '/listprofile/create' )" aria-label="Add List Profile">
            <a href="#">Add List Profile</a>
        </li>
    @endif
@stop

@section( 'content' )
    <md-table-container ng-init="listProfile.loadListProfiles()">
        <table md-table md-progress="listProfile.queryPromise">
            <thead md-head>
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column class="md-table-header-override-whitetext">Name</th>
                    <th md-column class="md-table-header-override-whitetext">Deliverable Range</th>
                    <th md-column class="md-table-header-override-whitetext">Opener Range</th>
                    <th md-column class="md-table-header-override-whitetext">Clicker Range</th>
                    <th md-column class="md-table-header-override-whitetext">Converter Range</th>
                    <th md-column class="md-table-header-override-whitetext">Pull Frequency</th>
                    <th md-column class="md-table-header-override-whitetext">Record Count</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="profile in listProfile.listProfiles track by $index">
                    <td md-cell class="mt2-table-btn-column">
                        <a ng-href="@{{ ::( '/listprofile/edit/' + profile.id ) }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                            <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                        </a>
                    </td>
                    <td md-cell ng-bind="::profile.name"></td>
                    <td md-cell>@{{ ::( profile.deliverable_start + ' to ' + profile.deliverable_end ) }}</td>
                    <td md-cell>@{{ ::( profile.clickers_start + ' to ' + profile.clickers_end ) }} (@{{ ::( profile.click_count + 'x' ) }})</td>
                    <td md-cell>@{{ ::( profile.openers_start + ' to ' + profile.openers_end ) }} (@{{ ::( profile.open_count + 'x' ) }})</td>
                    <td md-cell>@{{ ::( profile.converters_start + ' to ' + profile.converters_end ) }} (@{{ ::( profile.conversion_count + 'x' ) }})</td>
                    <td md-cell ng-bind="::profile.run_frequency" nowrap></td>
                    <td md-cell ng-bind="::profile.total_count"></td>
                </tr>
            </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <md-content class="md-mt2-zeta-theme md-hue-2">
                                <md-table-pagination md-limit="listProfile.paginationCount" md-limit-options="[ 10, 25, 50, 100]" md-page="listProfile.currentPage" md-total="@{{listProfile.profileTotal}}" md-on-paginate="listProfile.loadListProfiles" md-page-select></md-table-pagination>
                            </md-content>
                        </td>
                    </tr>
                </tfoot>
        </table>
    </md-table-container>
@stop

<?php
Assets::add( [
    'resources/assets/js/bootstrap/listprofile/ListProfileController.js' ,
    'resources/assets/js/bootstrap/listprofile/ListProfileApiService.js' ,
] , 'js' , 'pageLevel' );
?>
