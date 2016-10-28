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
    <md-table-container>
        <table md-table>
            <thead md-head>
                <tr md-row>
                    <th md-column></th>
                    <th md-column class="md-table-header-override-whitetext">Name</th>
                    <th md-column class="md-table-header-override-whitetext">Deliverable Range</th>
                    <th md-column class="md-table-header-override-whitetext">Opener Range</th>
                    <th md-column class="md-table-header-override-whitetext">Clicker Range</th>
                    <th md-column class="md-table-header-override-whitetext">Converter Range</th>
                    <th md-column class="md-table-header-override-whitetext">Last Pull</th>
                    <th md-column class="md-table-header-override-whitetext">Record Count</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="profile in listProfile.demoProfiles track by $index">
                    <td md-cell>
                        <md-button class="md-icon-button" ng-href="@{{ ::( '/listprofile/edit/' + ( $index + 1 ) ) }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                            <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                        </md-button>
                    </td>
                    <td md-cell ng-bind="::profile.name"></td>
                    <td md-cell>@{{ ::( profile.actionRanges.deliverable.min + ' to ' + profile.actionRanges.deliverable.max ) }}</td>
                    <td md-cell>@{{ ::( profile.actionRanges.opener.min + ' to ' + profile.actionRanges.opener.max ) }} (@{{ ::( profile.actionRanges.opener.multiaction + 'x' ) }})</td>
                    <td md-cell>@{{ ::( profile.actionRanges.clicker.min + ' to ' + profile.actionRanges.clicker.max ) }} (@{{ ::( profile.actionRanges.clicker.multiaction + 'x' ) }})</td>
                    <td md-cell>@{{ ::( profile.actionRanges.converter.min + ' to ' + profile.actionRanges.converter.max ) }} (@{{ ::( profile.actionRanges.converter.multiaction + 'x' ) }})</td>
                    <td md-cell ng-bind="::profile.lastPull" nowrap></td>
                    <td md-cell ng-bind="::profile.recordCount"></td>
                </tr>
            </tbody>
        </table>
    </md-table-container>
@stop

<?php
Assets::add( [
    'resources/assets/js/bootstrap/listprofile/ListProfileController.js' ,
    'resources/assets/js/bootstrap/listprofile/ListProfileApiService.js' ,
] , 'js' , 'pageLevel' );
?>
