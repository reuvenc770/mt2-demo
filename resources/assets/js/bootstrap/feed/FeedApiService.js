mt2App.service( 'FeedApiService' , function ( $http , $log ) {
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
        var sort = { 'field' : sortField , 'desc' : false };

        if (/^\-/.test( sortField ) ) {
            sort.field = sort.field.substring( 1 );
            sort.desc = true;
        }

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
            "url" : this.resetPasswordUrl + '/' + feedData.ftp_user
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

} );
