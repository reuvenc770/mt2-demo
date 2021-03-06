mt2App.service( 'ListProfileApiService' , function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/ListProfile';
    self.baseApiUrl = '/api/listprofile';
    self.offerSearch = '/api/offer/lpsearch?searchTerm=';
    self.getCombineUrl = '/api/listprofile/listcombine/combineonly';
    self.createCombineUrl = '/api/listprofile/listcombine/create';
    self.exportCombineUrl = '/api/listprofile/listcombine/export';

    self.getListProfile = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + id
        } ).then( successCallback , failureCallback );
    };

    self.searchOffers = function ( string , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.offerSearch + string
        } ).then( successCallback , failureCallback );
    };

    self.getListProfiles = function ( page , count , partyType , successCallback , failureCallback ) {
        return $http({
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , "partyType" : partyType }
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
        angular.forEach( [ 'deliverable' , 'opener' , 'clicker' , 'converter' ] , function ( value , key ) {
            if ( typeof( formData.actionRanges[ value ] ) !== 'undefined' &&  formData.actionRanges[ value ].min == false && formData.actionRanges[ value ].max == false ) {
                delete( formData.actionRanges[ value ] );
            }
        } );

        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : formData
        } ).then( successCallback , failureCallback );
    };

    self.updateListProfile = function ( formData , successCallback , failureCallback ) {
        angular.forEach( [ 'deliverable' , 'opener' , 'clicker' , 'converter' ] , function ( value , key ) {
            if ( typeof( formData.actionRanges[ value ] ) !== 'undefined' &&  formData.actionRanges[ value ].min == false && formData.actionRanges[ value ].max == false ) {
                delete( formData.actionRanges[ value ] );
            }
        } );

        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/' + formData[ 'profile_id' ],
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
    };

    self.deleteListProfile = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : self.baseApiUrl + '/' + id
        } ).then( successCallback , failureCallback );
    };

    self.createCombine = function ( name, ftpFolder, selectedListProfiles , party, successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.createCombineUrl ,
            "data" : {"combineName":name, "ftpFolder":ftpFolder, "combineParty":party,"selectedProfiles": selectedListProfiles}
        } ).then( successCallback , failureCallback );
    };

    self.exportCombine = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.exportCombineUrl ,
            "data" : {"id":id}
        } ).then( successCallback , failureCallback );
    };

    self.getCombines = function (successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.getCombineUrl
        } ).then( successCallback , failureCallback );
    };

    self.getListCombine = function (id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/listcombine/' + id
        } ).then( successCallback );
    };

    self.getAllListProfiles = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.updateCombine = function ( combineData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/listcombine/' ,
            "params" : { "_method" : "PUT" } ,
            "data" : combineData
        } ).then( successCallback , failureCallback );
    };

    self.copyListProfile = function ( id , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl + "/copy" ,
            "data" : {"id":id}
        } ).then( successCallback , failureCallback );
    };
} );
