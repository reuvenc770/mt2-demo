mt2App.service( 'AttributionApiService' , function ( $http , $log ) {
    var self = this; 

    self.baseApiUrl = '/api/attribution/model';
    self.pagerApiUrl = '/api/pager/AttributionModel';
    
    self.getModels = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.saveNewModel = function ( name , levels , successCallback , failureCallback ) {
        $http({
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : { 'name' : name , 'levels' : levels }
        }).then( successCallback , failureCallback );
    };
} );
