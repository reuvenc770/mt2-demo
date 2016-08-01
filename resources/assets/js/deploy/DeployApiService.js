mt2App.service( 'DeployApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/deploy';


    self.getDeploys = function (successCallback, failureCallback) {
        $http({"method": "GET", "url": this.baseApiUrl})
            .then(successCallback, failureCallback);
    };


});
