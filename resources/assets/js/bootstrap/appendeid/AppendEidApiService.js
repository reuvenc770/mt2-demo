mt2App.service( 'AppendEidApiService' , function ( $http , $log ) {
    var self = this;

    self.uploadListUrl = '/api/appendeid/upload';

    self.uploadList = function ( list , successCallback , failureCallback  ) {
        $http( {
            "method" : "POST" ,
            "url" : this.uploadListUrl,
            "data" : {fileName: list}
        } ).then( successCallback , failureCallback );
    };


} );
