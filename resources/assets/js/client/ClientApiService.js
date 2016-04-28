mt2App.service( 'ClientApiService' , function ( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Client';
    self.baseApiUrl = '/api/client';
    self.baseMt1ApiUrl = '/api/mt1';
    self.attributionApiUrl = '/api/attribution';

    self.getClient = function ( id , successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback , failureCallback );
    };

    self.getClients = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getAllClients = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.saveClient = function ( clientData , successCallback , failureCallback ) {
        $http( { "method" : "POST" , "url" : this.baseApiUrl , "data" : clientData } )
            .then( successCallback , failureCallback );
    };

    self.updateClient = function ( clientData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + clientData.client_id ,
            "params" : { "_method" : "PUT" } ,
            "data" : clientData
        } ).then( successCallback , failureCallback );
    };

    self.getTypes = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseMt1ApiUrl + '/client/types'
        } ).then( successCallback , failureCallback );
    };

    self.getListOwners = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseMt1ApiUrl + '/clientstatsgrouping'
        } ).then( successCallback , failureCallback );
    };

    self.generateLinks = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" , 
            "url" : self.baseMt1ApiUrl + '/client/generatelinks/' + id
        } ).then( successCallback , failureCallback );
    };

    self.getClientAttributionList = function ( currentPage , paginationCount , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" , 
            "url" : self.attributionApiUrl + '/list',
            "params" : {
                'page' : currentPage ,
                'count' : paginationCount
            }
        } ).then( successCallback , failureCallback );
    };

    self.setAttribution = function ( id , level , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.attributionApiUrl ,
            "params" : { "cid" : id , "level" : level }
        } ).then( successCallback , failureCallback );
    }

    self.deleteAttribution = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.attributionApiUrl ,
            "params" : { "cid" : id , "level" : level }
        } ).then( successCallback , failureCallback );
    }
} );
