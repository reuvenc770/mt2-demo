mt2App.service( 'YmlpCampaignApiService' , function ( $http , $log ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/YmlpCampaign';
    self.baseApiUrl = '/api/ymlp-campaign';

    self.getCampaign = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    }

    self.getCampaigns = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" , 
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    }

    self.saveNewCampaign = function ( newAccount , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } ).then( successCallback , failureCallback );
    }

    self.editCampaign = function ( account , successCallback , failureCallback  ) {
        var request = account;

        request[ '_method' ] = 'PUT';
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    }
} );
