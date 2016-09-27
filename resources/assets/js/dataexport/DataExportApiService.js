mt2App.service( 'DataExportApiService' , function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/DataExport';
    self.mt1ApiUrl = '/api/mt1/dataexport';
    self.baseApiUrl = '/api/dataexport';
    self.mt1ClientGroupApiUrl = '/api/mt1/clientgroup';
    self.mt1ProfileApiUrl = '/api/mt1/uniqueprofiles';
    self.mt1EspApiUrl = '/api/mt1/esps';

    self.getDataExport = function ( exportId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/',
            "params": {"action": "view", "eid": exportId}
        }).then( successCallback , failureCallback );
    };

    self.getActiveDataExports = function ( page , count , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl,
            "params" : { "page" : page , "count" : count, "action" : "listActive" }
        }).then( successCallback , failureCallback );
    };

    self.getPausedDataExports = function ( page , count , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl,
            "params" : { "page" : page , "count" : count, "action" : "listPaused" }
        }).then( successCallback , failureCallback );
    };

    self.saveDataExport = function ( exportData , successCallback ,failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/update',
            "params" : {"action": "save"} ,
            "data" : exportData
        }).then( successCallback , failureCallback );
    };

    self.copyDataExport = function ( exportId , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/update',
            "params": {"action": "copy", "eid": exportId}
        }).then( successCallback , failureCallback );
    };

    self.deleteDataExport = function ( exportId , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/update',
            "params": {"action": "delete", "eid": exportId}
        }).then( successCallback , failureCallback );
    };

    self.pauseDataExport = function (exportId, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl + '/update',
            "params": {"action": "pause", "eid": exportId}
        }).then(successCallback, failureCallback);
    };

    self.activateDataExport = function (exportId, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl + '/update',
            "params": {"action": "activate", "eid": exportId}
        }).then(successCallback, failureCallback);
    };

    self.massActivateDataExports = function (ids, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl + '/update',
            "params": {"action": "massActivate", "eidArr": ids.join(',')},
        }).then(sucessCallback, failureCallback);
    };

    self.massPauseDataExports = function (ids, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl + '/update',
            "params": {"action": "massPause", "eidArr": ids.join(',')}
        }).then(successCallback, failureCallback);
    };

    self.massRePullDataExports = function (ids, successCallback, failureCallback) {
        $http({
            "method": "PUT",
            "url": self.baseApiUrl + '/update',
            "params": {"action": "massRePull", "eidArr": ids.join(',')}
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

     self.getEsps = function(success, failure) {
        $http({
            "method": "GET",
            "url": self.mt1EspApiUrl
        }).then(success, failure);
     };

} );