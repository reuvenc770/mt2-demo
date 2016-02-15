mt2App.controller( 'ShowinfoController' , [ 'ShowinfoApiService' , '$log' , '$window' , function ( ShowinfoApiService , $log , $window ) {
    var self = this;
    self.api = ShowinfoApiService;

    self.isLoaded = false;

    self.recordId = null;
    self.records = {};

    self.suppressionReasons = [];
    self.selectedReason = '';

    /**
     * Event Handlers
     */

    self.loadData = function ( $event ) {
        $event.preventDefault();

        self.api.getRecords( self.getType() , self.recordId , self.loadDataSuccessCallback , self.loadDataFailureCallback );
        
        self.isLoaded = true;
    };

    self.suppressRecord = function ( $event ) {
        $event.preventDefault();

        self.api.suppressRecord( self.recordId , self.selectedReason , self.suppressRecordSuccessCallback , self.suppressRecordFailureCallback );
    }

    /**
     * Helpers
     */

    self.getType = function () {
        var re = /\d{1,}/;

        return ( re.exec( self.recordId ) ? 'eid' : 'email' );
    }

    self.loadReasons = function () {
        self.api.getSuppressionReasons( self.loadReasonsSuccessCallback , self.loadReasonsFailureCallback );
    };

    /**
     * Callbacks
     */

    self.loadDataSuccessCallback = function ( response ) {
        $log.log( 'Response:' );
        $log.log( response );
        self.records = response.data;
    };

    self.loadDataFailureCallback = function ( response ) {
        $log.log( 'Load data failed...' );
        $log.log( response );
    };

    self.loadReasonsSuccessCallback = function ( response ) {
        self.suppressionReasons = response.data;
    };

    self.loadReasonsFailureCallback = function ( response ) {
        $log.log( "Failed to load reasons." );
        $log.log( response );
    };

    self.suppressRecordSuccessCallback = function ( response ) {};
    self.suppressRecordFailureCallback = function ( response ) {};
} ] );

mt2App.service( 'ShowinfoApiService' , function ( $http , $log ) {
    var self = this;

    self.apiUrl = '/api/showinfo';
    self.suppressionApiUrl = '/api/mt1/suppressionReason';

    self.getRecords = function ( type , id , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.apiUrl + '/' + id ,
            "data" : { "id" : id , "type" : type }
        } ).then( successCallback , failureCallback );
    };

    self.getSuppressionReasons = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.suppressionApiUrl
        } ).then( successCallback , failureCallback );
    };

    self.suppressRecord = function ( id , reason , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.apiUrl ,
            "data" : { "id" : id , "reason" : reason }
        } ).then( successCallback , failureCallback );
    };
} );

//# sourceMappingURL=show-info.js.map
