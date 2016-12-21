mt2App.service( 'AWeberService' , function ( $http , $log ) {
    var self = this;
    
    self.baseApiUrl = '/api/tools/getunmappedreports/';
    self.convertReportUrl ='/api/tools/convertreport';
    self.getReports = function (successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback );
    };

    self.convertReport = function ( reportId, deployId , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.convertReportUrl ,
            "data" : {report_id: reportId, deploy_id: deployId}
        } ).then( successCallback , failureCallback );
    };

   
} );
