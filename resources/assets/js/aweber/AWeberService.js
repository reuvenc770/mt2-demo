mt2App.service( 'AWeberService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/tools/getunmappedreports/';
    self.convertReportUrl ='/api/tools/convertreport';
    self.aweberListsUrl = '/api/tools/getaweberlists/';
    self.updatelistUrl = '/api/tools/aweberlists/update';
    self.getReports = function (successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback );
    };

    self.convertReport = function ( internalId, deployId , campaignName , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.convertReportUrl ,
            "data" : {internal_id: internalId, deploy_id: deployId , campaign_name: campaignName }
        } ).then( successCallback , failureCallback );
    };

    self.getLists = function (id, successCallback, failureCallback){
        return $http( {
            "method" : "Get" ,
            "url" : self.aweberListsUrl + id
        } ).then( successCallback , failureCallback );
    };

    self.updateLists = function (lists, successCallback, failureCallback){
        return $http( {
            "method" : "POST" ,
            "url" : self.updatelistUrl,
            "data" : {ids: lists}
        } ).then( successCallback , failureCallback );
    };

} );
