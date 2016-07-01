mt2App.service( 'AttributionApiService' , function ( $http , $log ) {
    var self = this; 

    self.pagerApiUrl = '/api/pager/AttributionModel';
    
    self.getModels = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };
} );
