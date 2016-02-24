mt2App.service( 'WizardApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/wizard/pager/';

    self.getStep = function (page, type , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + type +  "/" + page } )
            .then( successCallback );
    };

    self.getFirstStep = function (type, successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + type + '/0' } )
            .then( successCallback );
    };

} );
