mt2App.service( 'FeedApiService' , function ( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Client';
    self.baseApiUrl = '/api/feed';
    self.baseMt1ApiUrl = '/api/mt1';
    self.attributionApiUrl = '/api/attribution';
    self.attributionListApiUrl = '/api/feed/attribution/list';
    self.resetPasswordUrl = '/api/feed/updatepassword';

    self.getFeed = function ( id , successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback , failureCallback );
    };

    self.getFeeds = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl , 
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getAllFeeds = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.saveFeed = function ( feedData , successCallback , failureCallback ) {
        $http( { "method" : "POST" , "url" : this.baseApiUrl , "data" : feedData } )
            .then( successCallback , failureCallback );
    };

    self.updateFeed = function ( feedData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + feedData.client_id ,
            "params" : { "_method" : "PUT" } ,
            "data" : feedData
        } ).then( successCallback , failureCallback );
    };

    self.updatePassword = function ( feedData , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : this.resetPasswordUrl + '/' + feedData.ftp_user
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

    self.getFeedAttributionList = function ( currentPage , paginationCount , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" , 
            "url" : self.attributionListApiUrl ,
            "params" : {
                'page' : currentPage ,
                'count' : paginationCount
            }
        } ).then( successCallback , failureCallback );
    };

    self.setAttribution = function ( id , level , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.attributionApiUrl ,
            "params" : { "cid" : id , "level" : level }
        } ).then( successCallback , failureCallback );
    }

    self.deleteAttribution = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.attributionApiUrl ,
            "params" : { "cid" : id , "level" : 255 }
        } ).then( successCallback , failureCallback );
    }
} );
