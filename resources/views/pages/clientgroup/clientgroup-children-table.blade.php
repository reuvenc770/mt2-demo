<md-table-container>
    <table md-table class="mt2-table-cell-center">
        <thead md-head>
            <tr md-row>
                <td md-column>Feed</td>
                <td md-column>Status</td>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="feed in clientGroup.clientMap[record.id] track by $index">
                <td md-cell>@{{ feed.name }}</td>
                <td md-cell ng-class="{ 'bg-success' : feed.status == 'A' , 'bg-warn' : feed.status == 'P' , 'bg-danger' : feed.status == 'D' }"><strong>@{{ feed.status == 'A' ? 'Active' : feed.status == 'P' ? 'Paused' : 'Inactive'  }}</strong></td>
            </tr>
        </tbody>
    </table>
</md-table-container>
