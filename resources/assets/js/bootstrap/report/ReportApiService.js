mt2App.service( 'ReportApiService' , function ( $http ) {
    var self = this;

    self.reportApiUrl = '/api/report';
    self.pagerApiUrl = '/api/pager/AmpReport';

    self.getReports = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = { 'field' : sortField , 'desc' : false };

        if (/^\-/.test( sortField ) ) {
            sort.field = sort.field.substring( 1 );
            sort.desc = true;
        }

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count , "sort" : sort }
        } ).then( successCallback , failureCallback );
    };

    self.saveReport = function ( name , reportId , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.reportApiUrl ,
            "data" : { "name" : name , "reportId" : reportId }
        } ).then( successCallback , failureCallback );
    
    };

    self.updateReport = function ( systemId , name , reportId , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.reportApiUrl ,
            "params" : { "_method" : "PUT" } ,
            "data" : { "systemId" : systemId , "name" : name , "reportId" : reportId }
        } ).then( successCallback , failureCallback );
    
    };
} );
