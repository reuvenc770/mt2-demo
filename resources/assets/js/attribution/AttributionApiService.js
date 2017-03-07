mt2App.service( 'AttributionApiService' , function ( $http , $log ) {
    var self = this; 

    self.baseApiUrl = '/api/attribution/model';
    self.pagerApiUrl = '/api/pager/AttributionModel';
    self.projectionApiUrl = '/api/attribution/projection';

    self.getModels = function ( page , count , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getModelFeeds = function ( modelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + modelId + '/feeds'
        } ).then( successCallback , failureCallback ); 
    };

    self.saveNewModel = function ( name , levels , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : { 'name' : name , 'levels' : levels }
        } ).then( successCallback , failureCallback );
    };

    self.updateModel = function ( modelId , modelName, levels , successCallback , failureCallback ) {
        return $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + modelId ,
            "params" : { "_method" : "PUT" } ,
            "data" : { "name" : modelName , "levels" : levels }
        } ).then( successCallback , failureCallback );
    };

    self.deleteFeed = function ( modelId , feedId , successCallback , failureCallback ) {
        return $http( {
            "method" : "DELETE" ,
            "url" : this.baseApiUrl + '/' + modelId  + '/' + feedId ,
            "params" : { "_method" : "DELETE" }
        } ).then( successCallback , failureCallback );
    };

    self.getLevels = function ( modelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + modelId + '/levels'
        } ).then( successCallback , failureCallback );
    };

    self.getModel = function ( modelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + modelId
        } ).then( successCallback , failureCallback );
    };

    self.setModelLive = function ( modelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/setlive/' + modelId
        } ).then( successCallback , failureCallback );
    };

    self.runAttribution = function ( modelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/run',
            "data" : { "modelId" : modelId }
        } ).then( successCallback , failureCallback );
    };

    self.copyLevels = function ( currentModelId , templateModelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/copyLevels' ,
            "data" : { "currentModelId" : currentModelId , "templateModelId" : templateModelId  }
        } ).then( successCallback , failureCallback );
    };

    self.syncMt1Levels = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : '/api/attribution/syncLevels'
        } ).then( successCallback , failureCallback );
    };

    self.getProjectionChartData = function ( modelId , successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.projectionApiUrl + '/chart/' + modelId 
        } ).then( successCallback , failureCallback );
    }

    self.getProjectionRecords = function ( data , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.projectionApiUrl + '/report/' ,
            "data" : data
        } ).then( successCallback , failureCallback );
    }
} );
