@extends( 'layout.default' )

@section( 'title' , 'List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('listprofile.add'))
        <md-button ng-click="app.redirect( '/listprofile/create' )" aria-label="Add List Profile">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add List Profile</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
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
                                <md-button class="md-icon-button" ng-href="@{{ ::( '/listprofile/edit/' + ( $index + 1 ) ) }}" aria-label="Edit" target="_self">
                                    <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                                    <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                </md-button>
                            </td>
                            <td md-cell ng-bind="::profile.name"></td>
                            <td md-cell>@{{ ::( profile.actionRanges.deliverable.min + ' to ' + profile.actionRanges.deliverable.max ) }}</td>
                            <td md-cell>@{{ ::( profile.actionRanges.opener.min + ' to ' + profile.actionRanges.opener.max ) }} (@{{ ::( profile.actionRanges.opener.multiaction + 'x' ) }})</td>
                            <td md-cell>@{{ ::( profile.actionRanges.clicker.min + ' to ' + profile.actionRanges.clicker.max ) }} (@{{ ::( profile.actionRanges.clicker.multiaction + 'x' ) }})</td>
                            <td md-cell>@{{ ::( profile.actionRanges.converter.min + ' to ' + profile.actionRanges.converter.max ) }} (@{{ ::( profile.actionRanges.converter.multiaction + 'x' ) }})</td>
                            <td md-cell ng-bind="::profile.lastPull"></td>
                            <td md-cell ng-bind="::profile.recordCount"></td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>
        </md-card>
    </md-content>
@stop

@section( 'pageIncludes' )
<script src="js/listprofile.js"></script>
@stop
