mt2App.service('BulkSuppressionApiService', function ($http) {
    var self = this;

    self.baseApiUrl = '/api/bulksuppression';
    self.suppressionApiUrl = '/api/suppressionReason';
    self.uploadEmails = function(data, successCallback, failureCallback) {
        data.emails = data.emails.join(',');
        $http({
            "method": "POST",
            "url": self.baseApiUrl + '/send',
            "params": data
        }).then(function (result) {
            var errorRe = /Error/;
            if (errorRe.exec(result['data'])) {
                failureCallback(result['data']);
            }
            else {
                successCallback();
            }
        });
    }

    self.getSuppressionReasons = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.suppressionApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.transferFiles = function(successCallback, failureCallback) {
        $http({
            "method": "POST",
            "url": self.baseApiUrl + '/transfer'
        }).then(function (result) {
            if (result['data'].length > 0) {
                failureCallback(result['data']);
            }
            else {
                successCallback();
            }
        });
    }
});