mt2App.service( 'AttributionApiService' , function ( $http , $log ) {
    var self = this; 

    self.baseApiUrl = '/api/attribution/model';
    self.pagerApiUrl = '/api/pager/AttributionModel';
    self.reportApiUrl = '/api/attribution/report';
    self.projectionApiUrl = '/api/attribution/projection';
    
    self.getRecords = function ( query , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.reportApiUrl ,
            "params" : query
        } ).then( successCallback , failureCallback );
    };

    self.getModels = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getModelClients = function ( modelId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + modelId + '/clients'
        } ).then( successCallback , failureCallback ); 
    };

    self.saveNewModel = function ( name , levels , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : { 'name' : name , 'levels' : levels }
        } ).then( successCallback , failureCallback );
    };

    self.updateModel = function ( modelId , modelName, levels , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + modelId ,
            "params" : { "_method" : "PUT" } ,
            "data" : { "name" : modelName , "levels" : levels }
        } ).then( successCallback , failureCallback );
    };

    self.getLevels = function ( modelId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + modelId + '/levels'
        } ).then( successCallback , failureCallback );
    };

    self.getModel = function ( modelId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + modelId
        } ).then( successCallback , failureCallback );
    };

    self.copyLevels = function ( currentModelId , templateModelId , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/copyLevels' ,
            "data" : { "currentModelId" : currentModelId , "templateModelId" : templateModelId  }
        } ).then( successCallback , failureCallback );
    };

    self.getProjectionChartData = function ( modelId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.projectionApiUrl + '/chart/' + modelId 
        } ).then( successCallback , failureCallback );
    }

    self.getProjectionRecords = function ( modelId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.projectionApiUrl + '/report/' + modelId 
        } ).then( successCallback , failureCallback );
    }
} );
