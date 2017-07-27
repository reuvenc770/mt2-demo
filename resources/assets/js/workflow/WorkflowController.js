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
        WorkflowApiService.loadWorkflow(id, self.loadWorkflowSuccess, self.loadWorkflowFailure);
    }

    self.saveWorkflow = function() {
        self.formSubmitted = true;
    }

    // Feed management

    self.addFeeds = function() {
        console.dir(self.highlightedFeeds);
        self.current.feeds = self.current.feeds.concat(self.highlightedFeeds);

        // also, remove these from 
        self.highlightedFeeds = [];
    }

    self.removeFeeds = function() {
        var newList = self.current.feeds.filter(function (x) {
            return self.highlightedFeedsForRemoval.indexOf(x) < 0;
        });

        self.current.feeds = newList;
        self.highlightedFeedsForRemoval = [];
    }


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
        self.current.
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