mt2App.controller('WorkflowController', ['$rootScope', '$log', '$window', '$location', '$timeout', 'WorkflowApiService', 'formValidationService', 'modalService' , 'paginationService', function ($rootScope, $log, $window, $location, $timeout, WorkflowApiService, formValidationService, modalService, paginationService) {
    var self = this;
    self.$location = $location;

    self.workflows = [];
    self.sort = 'id';
    self.queryPromise = null;
    self.formSubmitted = false;
    
    // Pagination
    self.pageCount = null;
    self.workflowTotal = 0;
    self.pageCount = 20;
    self.paginationOptions = {};
    self.currentPage = 1;

    // Edit variables
    self.current = {
        'id': 0,
        'name': '',
        'feeds': [],
        'steps': []
    };

    // Feeds
    self.availableFeeds = [];

    self.highlightedFeeds = [];
    self.highlightedFeedsForRemoval = [];

    /**
     *  Main page methods
     */

    self.loadWorkflows = function() {
        self.queryPromise = WorkflowApiService.loadWorkflows(
            self.currentPage,
            self.paginationCount,
            self.sort,
            self.loadWorkflowsSuccess,
            self.loadWorkflowsFailure);
    }

    self.activate = function(id) {
        WorkflowApiService.activate(id, self.activateSuccess, self.activateFailure);
    };

    self.pause = function(id) {
        WorkflowApiService.pause(id, self.pauseSuccess, self.pauseFailure);
    };


    /**
     *  Add and Edit methods
     */

    self.loadWorkflow = function(id) {
        WorkflowApiService.get(id, self.loadWorkflowSuccess, self.loadWorkflowFailure);
    }

    self.saveWorkflow = function() {
        self.formSubmitted = true;
    };

    // Feed management

    self.addFeeds = function() {
        var len = self.availableFeeds.length;
        var newAvailableFeeds = [];

        for (var i = 0; i < len; i++) {
            var id = self.availableFeeds[i].id;

            if (self.highlightedFeeds.indexOf(id) >= 0) {
                self.current.feeds.push(self.availableFeeds[i]);
            }
            else {
                newAvailableFeeds.push(self.availableFeeds[i]);
            }
        }

        self.availableFeeds = newAvailableFeeds;
        self.highlightedFeeds = [];
    };

    self.removeFeeds = function() {
        var selectedFeedsLen = self.current.feeds.length;
        var newSelectedFeeds = [];

        for (var i = 0; i < selectedFeedsLen; i++) {
            var id = self.current.feeds[i].id;

            if (self.highlightedFeedsForRemoval.indexOf(id) >= 0) {
                self.availableFeeds.push(self.current.feeds[i]);
            }
            else {
                newSelectedFeeds.push(self.current.feeds[i]);
            }
        }

        self.current.feeds = newSelectedFeeds;
        self.highlightedFeedsForRemoval = [];
    };

    self.saveWorkflow = function () {};

    // Steps

    self.editStep = function(step) {

    };

    self.addStep = function () {};


    /**
     *  Callbacks
     */

    self.loadWorkflowsSuccess = function(response) {
        self.workflows = response.data.data;
        self.pageCount = response.data.last_page;
        self.workflowTotal = response.data.total;
    };

    self.loadWorkflowsFailure = function(response) {
        modalService.simpleToast('Failed to load workflows.');
    };

    self.activateSuccess = function(response) {
        var id = parseInt(response.data);
        setWorkflowFieldToValue(id, 'status', 1);
    };

    self.activateFailure = function(response) {
        modalService.simpleToast('Failed to activate workflow.');
    };

    self.pauseSuccess = function(response) {
        var id = parseInt(response.data);
        setWorkflowFieldToValue(id, 'status', 0);
    };

    self.pauseFailure = function(response) {
        modalService.simpleToast('Failed to pause workflow.');
    };

    self.loadWorkflowSuccess = function(response) {
        /**
         *  What needs to be done:
         *  1. Populate selected feeds
         *  2. Remove all selected feeds from highlighted
         *  3. Set up steps
         */

         data = response.data;

         self.current.id = data.id;
         self.current.name = data.name;
         self.current.feeds = data.selectedFeeds;
         self.current.steps = data.steps;
         self.availableFeeds = data.availableFeeds;
    }

    self.loadWorkflowFailure = function(response) {
        modalService.simpleToast('Failed to load workflow.');
    }

    /**
     *  Helper methods
     */

     function setWorkflowFieldToValue(id, field, value) {
        var index = 0;
        var len = self.workflows.length;

        for (var i = 0; i <= len; i++) {
            if (self.workflows[i]['id'] === id) {
                index = i;
                break;
            }
        }

        self.workflows[index][field] = value;
     }

}]);