<div style="visibility: hidden">
    <div ng-init="listProfile.loadFeedList()" class="md-dialog-container" id="feedGroupFormModal">
        <md-dialog>
            <md-toolbar class="mt2-theme-toolbar">
                <div class="md-toolbar-tools mt2-theme-toolbar-tools">
                    <h2>Add New Feed Group</h2>
                    <span flex></span>
                    <md-button class="md-icon-button" ng-click="listProfile.closeFeedGroupModal()"><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                </div>
            </md-toolbar>
            <md-dialog-content>
                <div class="md-dialog-content">
                    <div class="form-horizontal">
                        <div class="panel panel-info" style="width:500px;">
                            <div class="panel-heading">Please Select Feeds</div>
                            <div class="panel-body">
                                <div class="row" style="margin:1em;">
                                    <div class="col-md-12">
                                        <div class="form-group" ng-class="{ 'has-error' : listProfile.formErrors.name }">
                                            <label>Feed Group Name</label>
                                            <input placeholder="Feed Group Name" value="" class="form-control" name="name" ng-model="listProfile.feedGroupName" type="text">
                                            <div class="help-block" ng-show="listProfile.formErrors.feedGroupName">
                                                <div ng-repeat="error in listProfile.formErrors.feedGroupName">
                                                    <div ng-bind="error"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin:1em;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <lite-membership-widget height="200" recordlist="listProfile.feedList" namefield="'short_name'" chosenrecordlist="listProfile.feedForFeedGroup" availablerecordtitle="'Available'" chosenrecordtitle="'Selected'"></lite-membership-widget>
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
                    <input class="btn mt2-theme-btn-primary btn-block" ng-click="listProfile.createFeedGroup()" ng-disabled="listProfile.feedGroupFormSubmitting" type="submit" value="Save Deploy">
                </div>
            </md-dialog-actions>
        </md-dialog>
    </div>
</div>
