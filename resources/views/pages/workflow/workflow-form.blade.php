<h3 class="bold-text">
    Select Feeds
</h3>
<div class="row form-group" id="feedWidget">
    <div class="col-sm-6">
        <label>Available Feeds</label>

        <div class="pull-right">
            <label ng-click="workflow.addFeeds()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="workflow.highlightedFeeds" multiple style="width: 100%; height: 150px;">
            @foreach ($feeds as $feed)
                <option value="{{$feed['id']}}">{{$feed['short_name']}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Feeds</label>

        <div class="pull-right">
            <label ng-click="workflow.removeFeeds()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="workflow.highlightedFeedsForRemoval" multiple="" style="width: 100%; height: 150px;">
            <option ng-repeat="(feedId, short_name) in workflow.current.feeds" ng-value="::feedId">@{{::short_name}}</option>
        </select>
    </div>
</div>
<br>
<h3 class="bold-text">
    Build Steps
</h3>
