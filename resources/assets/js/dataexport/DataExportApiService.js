mt2App.service( 'DataExportApiService' , function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/dataexport';
    self.mt1ApiUrl = '/api/mt1/dataexport';
    self.baseApiUrl = '/api/dataexport';
    self.mt1ClientGroupApiUrl = '/api/mt1/clientgroup';
    self.mt1ProfileApiUrl = '/api/mt1/uniqueprofiles';

    self.getDataExport = function ( exportId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.mt1ApiUrl + '/' + exportId
        }).then( successCallback , failureCallback );
    };

    self.getActiveDataExports = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl,
            "params" : { "page" : page , "count" : count, "action" : "listActive" }
        }).then( successCallback , failureCallback );
    };

    self.getPausedDataExports = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl,
            "params" : { "page" : page , "count" : count, "action" : "listPaused" }
        }).then( successCallback , failureCallback );
    };

    self.createDataExport = function ( exportData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl,
            "data" : exportData
        }).then( successCallback , failureCallback );
    };

    self.updateDataExport = function ( exportData , successCallback ,failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/' + exportData.eid ,
            "params" : { "_method" : "PUT" } ,
            "data" : exportData
        }).then( successCallback , failureCallback );
    };

    self.copyDataExport = function ( exportId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/copy/' + exportId ,
        }).then( successCallback , failureCallback );
    };

    self.deleteDataExport = function ( exportId , successCallback , failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : self.baseApiUrl + '/' + exportId
        }).then( successCallback , failureCallback );
    };

    self.pauseDataExport = function (exportId, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl + "/pause/" + exportId,
        }).then(successCallback, failureCallback);
    };

    self.massActivateDataExports = function (ids, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl,
            "params": {"action": "massActivate"},
            "data": ids
        }).then(sucessCallback, failureCallback);
    };

    self.massPauseDataExports = function (ids, successCallback, failureCallback) {
        $http({            
            "method": "PUT",
            "url": self.baseApiUrl,
            "params": {"action": "massPause"},
            "data": ids 
        }).then(sucessCallback, failureCallback);
    };

    self.massRePullDataExports = function (ids, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl,
            "params": {"action": "massRePull"},
            "data": ids
        }).then(sucessCallback, failureCallback);
    };

    /**
     * Data pulls
     */

     self.getProfiles = function (success, failure) {
        $http({
            "method": "GET",
            "url": self.mt1ProfileApiUrl
        }).then(success, failure);
     };

     self.getClientGroups = function(success, failure) {
        $http({
            "method": "GET",
            "url": self.mt1ClientGroupApiUrl
        }).then(success, failure);
     };

} );