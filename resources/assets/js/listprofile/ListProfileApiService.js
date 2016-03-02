mt2App.service( 'ListProfileApiService' , function ( $http ) {
    var self = this;

    self.baseApiUrl = '/api/listprofile';

    self.saveListProfile = function ( formData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : formData
        } ).then( successCallback , failureCallback );
    };
} );
