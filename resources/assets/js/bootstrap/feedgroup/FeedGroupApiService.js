mt2App.service( 'FeedGroupApiService' , [ 'paginationService' , '$http' , function ( paginationService , $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/FeedGroup';
    self.baseApiUrl = '/api/feedgroup';

    self.getFeedGroups = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , "sort" : sort }
        } ).then( successCallback , failureCallback );
    };

    self.createFeedGroup = function ( groupData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : groupData
        } ).then( successCallback , failureCallback );
    };

    self.updateFeedGroup = function ( groupData , successCallback ,failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/' + groupData.id ,
            "params" : { "_method" : "PUT" } ,
            "data" : groupData
        } ).then( successCallback , failureCallback );
    };
} ] );
