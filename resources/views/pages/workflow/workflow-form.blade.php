<div class="form-group" id="name">
    <label for="name">Workflow Name</label>
    <div class="input-group">
        <input type="text" name="name" id="name" class="form-control" ng-model="workflow.current.name" />
    </div>
</div>
<br>
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
            <option ng-repeat="a in workflow.availableFeeds" ng-value="a.id">@{{a.short_name}}</option>
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Feeds</label>

        <div class="pull-right">
            <label ng-click="workflow.removeFeeds()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="workflow.highlightedFeedsForRemoval" multiple="" style="width: 100%; height: 150px;">
            <option ng-repeat="f in workflow.current.feeds" ng-value="f.id">@{{f.short_name}}</option>
        </select>
    </div>
</div>
<br>
<h3 class="bold-text">
    Progression
</h3>
<div class="row form-group" id="stepWidget">
    <table md-table md-row-select="true" multiple>
        <thead md-head class="mt2-theme-thead">
            <tr md-row>
                <th md-column class="mt2-table-btn-column"></th>
                <th md-column class="md-table-header-override-whitetext">Step</th>
                <th md-column class="md-table-header-override-whitetext">Deploy</th>
                <th md-column class="md-table-header-override-whitetext">Offer</th>
            </tr>
        </thead>
        <tbody md-body>
            <tr md-row ng-repeat="step in workflow.current.steps track by $index" class="table-row-condensed">
                <td>
                    <md-icon 
                        md-font-set="material-icons" 
                        class="mt2-icon-black"
                        ng-click="workflow.editStep(step.step)" 
                        aria-label="Edit" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Edit">
                    edit
                    </md-icon>
                </td>
                <td>@{{step.step}}</td>
                <td>@{{step.deploy_id}}</td>
                <td>@{{step.offer_name}}</td>
            </tr>
            <tr>
                <td>
                    <md-icon 
                        md-font-set="material-icons" 
                        class="mt2-icon-black icon-xxl"
                        ng-click="workflow.addStep()" 
                        aria-label="Edit" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Edit">
                    add
                    </md-icon>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
