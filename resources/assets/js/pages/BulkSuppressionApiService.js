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
        }).then( successCallback , failureCallback );
    }

    self.getSuppressionReasons = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.suppressionApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.transferFiles = function( reason , successCallback, failureCallback) {
        $http({
            "method": "POST",
            "url": self.baseApiUrl + '/transfer' ,
            "data" : { 'reason' : reason }
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
