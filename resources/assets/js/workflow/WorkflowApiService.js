mt2App.service('WorkflowApiService', ['paginationService', '$http', '$log', function (paginationService, $http, $log) {
    var self = this;

    self.baseUrl = '/workflow/';
    self.pageApiUrl = '/api/pager/EspWorkflow';
    self.baseApiUrl = '/api/workflow';

    self.loadWorkflows = function (page, count, sortField, successCallback, failureCallback) {
        var sort = paginationService.sortPage( sortField );

        return $http({
            "method": "GET",
            "url": self.pageApiUrl,
            "params": {"page": page, "count": count, "sort": sort}
        }).then(successCallback, failureCallback);
    };


    self.pause = function(id, success, failure) {
        return $http({
            "method": "POST",
            "url": self.baseApiUrl + '/pause/' + id
        }).then(success, failure);
    };

    self.activate = function(id, success, failure) {
        return $http({
            "method": "POST",
            "url": self.baseApiUrl + '/activate/' + id
        }).then(success, failure);
    };

    self.get = function(id, success, failure) {
        return $http({
            "method": "GET",
            "url": self.baseUrl + 'get/' + id
        }).then(success, failure);
    }

}]);