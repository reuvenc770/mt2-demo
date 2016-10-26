mt2App.service( 'AppendEidApiService' , function ( $http , $log ) {
    var self = this;

    self.uploadListUrl = '/api/appendeid/upload';

    self.uploadList = function ( data , successCallback , failureCallback  ) {
        $http( {
            "method" : "POST" ,
            "url" : this.uploadListUrl,
            "data" : data
        } ).then( successCallback , failureCallback );
    };


} );
