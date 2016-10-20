<div class="modal fade" id="loadModels" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Attribution Levels</h4>
            </div>
            <div class="modal-body">
                <div class="form-group ">
                    <label>Please Select Model to Copy</label>
                    <select ng-model="attr.levelCopyModelId" class="form-control">
                        <option ng-repeat="model in attr.models" ng-value="model.id">@{{ model.name }}</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <button ng-click="attr.loadLevelPreview()" class="btn btn-primary btn-block">Load Levels
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <button ng-disabled="attr.disableCopyButton" ng-click="attr.copyLevels()"
                                class="btn btn-block btn-primary">Copy Levels
                        </button>
                    </div>
                </div>
                <div ng-if="attr.levelCopyClients.length > 0">
                    <h3>Levels Preview</h3>
                    <ul class="list-group" ng-cloak>
                        <li ng-repeat="client in attr.levelCopyClients"
                            class="list-group-item clearfix"
                            ng-class="{ 'list-group-item-success' : attr.clientLevels[ client.id ] > ( $index + 1 ) , 'list-group-item-danger' : attr.clientLevels[ client.id ] < ( $index + 1 )}">
                            <div class="col-sm-8">
                                @{{ client.name }}
                            </div>
                            <div class="col-sm-4">
                                @{{ attr.clientLevels[ client.id ] }}
                                <span class="glyphicon glyphicon-chevron-right"></span>
                                @{{ $index + 1 }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


