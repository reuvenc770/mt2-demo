mt2App.service( 'ListProfileApiService' , function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/UniqueProfile';
    self.baseApiUrl = '/api/listprofile';

    self.getListProfile = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + id
        } ).then( successCallback , failureCallback );
    };

    self.getListProfiles = function ( page , count , successCallback , failureCallback ) {
        $http({
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count }
        }).then( successCallback , failureCallback );
    };

    self.getIspsByProfileId = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/isps/' + groupId 
        } ).then( successCallback , failureCallback );
    };

    self.getSourcesByProfileId = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/sources/' + groupId 
        } ).then( successCallback , failureCallback );
    };

    self.getSeedsByProfileId = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/seeds/' + groupId 
        } ).then( successCallback , failureCallback );
    };

    self.getZipsByProfileId = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/zips/' + groupId 
        } ).then( successCallback , failureCallback );
    };

    self.calculateListProfile = function ( formData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : formData
        } ).then( successCallback , failureCallback );
    };

    self.saveListProfile = function ( formData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : formData
        } ).then( successCallback , failureCallback );
    };

    self.updateListProfile = function ( formData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/' + formData[ 'pid' ],
            "param" : { '_method' : "PUT" } ,
            "data" : formData
        } ).then( successCallback , failureCallback );
    };

    self.copyListProfile = function ( id , name , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + "/copy" ,
            "params" : { 'action' : 'copy' , 'pid' : id , 'pname' : name }
        } ).then( successCallback , failureCallback );
    }

    self.deleteListProfile = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : self.baseApiUrl + '/' + id
        } ).then( successCallback , failureCallback );
    }
} );