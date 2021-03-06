mt2App.service( 'FeedApiService' , [ 'paginationService' , '$http' , '$log' , function ( paginationService , $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/Feed';
    self.baseApiUrl = '/api/feed';
    self.baseMt1ApiUrl = '/api/mt1';
    self.resetPasswordUrl = '/api/feed/updatepassword';

    self.getFeed = function ( id , successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback , failureCallback );
    };

    self.getFeeds = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , 'sort' : sort }
        } ).then( successCallback , failureCallback );
    };

    self.getAllFeeds = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.getFeedCsv = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/exportList'
        } ).then( successCallback , failureCallback );
    };

    self.getListOwners = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseMt1ApiUrl + '/clientstatsgrouping'
        } ).then( successCallback , failureCallback );
    };

    self.saveFeed = function ( feedData , successCallback , failureCallback ) {
        $http( { "method" : "POST" , "url" : this.baseApiUrl , "data" : feedData } )
            .then( successCallback , failureCallback );
    };

    self.updateFeed = function ( feedData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + feedData.id ,
            "params" : { "_method" : "PUT" } ,
            "data" : feedData
        } ).then( successCallback , failureCallback );
    };

    self.updatePassword = function ( feedData , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : this.resetPasswordUrl + '/' + feedData.short_name
        } ).then( successCallback , failureCallback );
    };

    self.updateFeedFields = function ( id , fieldData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "params" : { "_method" : "PUT" } ,
            "url" : this.baseApiUrl + '/file/' + id ,
            "data" : fieldData
        } ).then( successCallback , failureCallback );
    };

    self.runReattribution = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "params" : { "_method" : "PUT" } ,
            "url" : this.baseApiUrl  + '/runreattribution/' + id,
            "data" : { "id" : id }
        } ).then( successCallback , failureCallback );
    };

    self.createSuppression = function ( id, successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl  + '/createsuppression/' + id,
            "data" : { "id" : id }
        } ).then( successCallback , failureCallback );
    };

    self.searchSourceUrl = function ( queryData , successCallback , failureCallback ) {
        return $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + '/' + 'searchsource' ,
            "data" : queryData
        } ).then( successCallback , failureCallback );
    };

    self.searchFeeds = function ( count , data , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : 1 , "count" : count , "sort" : sort , "data" : data }
        } ).then( successCallback , failureCallback );
    };

} ] );
