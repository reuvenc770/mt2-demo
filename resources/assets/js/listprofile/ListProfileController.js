mt2App.controller( 'ListProfileController' , [ 'ListProfileApiService' , 'ClientGroupApiService' , 'IspApiService', '$mdToast' , '$log' , function ( ListProfileApiService , ClientGroupApiService , IspApiService , $mdToast , $log ) {
    var self = this;

    var keycodeEnter = 13 ;
    var keycodeComma = 188 ;
    var keycodeTab = 9 ;
    self.mdChipSeparatorKeys = [ keycodeEnter , keycodeComma , keycodeTab ];

    self.current = {
        'name' : '' ,
        'countries' : [] ,
        'feeds' : {},
        'suppression' : {
            'global' : '' ,
            'list' : '' ,
            'offer' : '' ,
            'attribute' : { 'cities': [] , 'zips' : [] , 'states' : [] }
        },
        'actionRanges' : {
            'deliverable' : { 'min' : 0 , 'max' : 0 },
            'opener' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 },
            'clicker' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 },
            'converter' : { 'min' : 0 , 'max' : 0 , 'multiaction' : 1 }
        },
        'attributeFilters' : {
            'age' : { 'min' : 0 , 'max' : 0 , 'unknown' : false },
            'genders' : [],
            'zips' : [],
            'cities' : [],
            'states' : [],
            'deviceTypes' : [],
            'mobileCarriers' : []
        }
    };

    self.highlightedFeeds = [];
    self.highlightedFeedsForRemoval = [];
    self.feedClientFilters = [];
    self.clientFeedMap = {};
    self.feedNameMap = {};
    self.feedVisibility = {};
    self.columnList = ['email_id', 'first_name', 'last_name', 'address'];
    self.selectedColumns = [];

    self.addFeeds = function () {
        angular.forEach( self.highlightedFeeds , function ( feedId ) {
            self.feedVisibility[ feedId ] = false;

            self.current.feeds[ feedId ] = self.feedNameMap[ feedId ];
        } );

        self.highlightedFeeds = [];
    };

    self.removeFeeds = function () {
        angular.forEach( self.highlightedFeedsForRemoval , function ( feedId ) {
            self.feedVisibility[ feedId ] = true;

            delete( self.current.feeds[ feedId ] );
        } );

        self.updateFeedVisibility();

        self.highlightedFeedsForRemoval = [];
    };

    self.updateFeedVisibility = function () {
        var showAll = false;
        var noClientFiltersSelected = self.feedClientFilters.length <= 0;

        if ( noClientFiltersSelected ) {
            showAll = true;
        }

        angular.forEach( self.feedVisibility , function ( visibility , feedId ) {
            var feedNotSelected = typeof( self.current.feeds[ parseInt( feedId ) ] ) == 'undefined';

            if ( showAll && feedNotSelected ) {
                self.feedVisibility[ feedId ] = true;
            } else {
                self.feedVisibility[ feedId ] = false;

                angular.forEach( self.feedClientFilters , function ( clientId ) {
                    var feedListExistsAndBelongsToClient = ( typeof( self.clientFeedMap[ parseInt( clientId ) ] ) != 'undefined' && self.clientFeedMap[ parseInt( clientId ) ].indexOf( parseInt( feedId ) ) !== -1 );

                    if( feedListExistsAndBelongsToClient && feedNotSelected ) {
                        self.feedVisibility[ feedId ] = true;
                    }
                } );
            }
        } );
    };

    self.clearClientFeedFilter = function () {
        self.feedClientFilters = [];

        self.updateFeedVisibility();
    };

    self.toggleSelection = function (gender) {
        var idx = self.current.attributeFilters.genders.indexOf(gender);

        // is currently selected
        if (idx > -1) {
            self.current.attributeFilters.genders.splice(idx, 1);
        }

        // is newly selected
        else {
            self.current.attributeFilters.genders.push(gender);
        }
    };

} ] );
