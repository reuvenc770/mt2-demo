mt2App.service('BulkSuppressionApiService', function ($http) {
    var self = this;

    self.baseApiUrl = '/api/bulksuppression';

    self.uploadEmails = function(data, successCallback, failureCallback) {
        data.emails = data.emails.join(',');
        $http({
            "method": "POST",
            "url": self.baseApiUrl + '/send',
            "params": data
        }).then(successCallback, failureCallback);
    }
});