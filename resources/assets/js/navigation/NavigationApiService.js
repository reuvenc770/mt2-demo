mt2App.service( 'NavigationApiService' , function ( $http ) {
    var self = this;

    self.getTreeUrl = '/api/navigation/gettree';
    self.getOrphanUrl = '/api/navigation/orphans';
    self.getBasePost = '/api/navigation/';

    self.getPermissionsTree = function (successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.getTreeUrl
        } ).then( successCallback , failureCallback );
    };

    self.getValidOrphans = function (successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.getOrphanUrl
        } ).then( successCallback , failureCallback );
    };


    self.updateNavigation = function ( formData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.getBasePost,
            "data" : formData
        } ).then( successCallback , failureCallback );
    };

} );
