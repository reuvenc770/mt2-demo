mt2App.controller( 'ListProfileController' , [ 'ListProfileApiService' , 'ClientGroupApiService' , 'IspApiService', '$mdToast' , '$log' , function ( ListProfileApiService , ClientGroupApiService , IspApiService , $mdToast , $log ) {
    var self = this;

    var keycodeEnter = 13 ;
    var keycodeComma = 188 ;
    var keycodeTab = 9 ;
    self.mdChipSeparatorKeys = [ keycodeEnter , keycodeComma , keycodeTab ];

    self.current = {
        'name' : '' ,
        'countries' : [] ,
        'feeds' : {} ,
        'isps' : {} ,
        'categories' : {} ,
        'offers' : {} ,
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
        },
        'selectedColumns' : [],
        'includeCsvHeader' : false
    };

    self.highlightedFeeds = [];
    self.highlightedFeedsForRemoval = [];
    self.feedClientFilters = [];
    self.clientFeedMap = {};
    self.feedNameMap = {};
    self.feedVisibility = {};

    self.highlightedIsps = [];
    self.highlightedIspsForRemoval = [];
    self.ispVisibility = [];
    self.ispNameMap = {};

    self.highlightedCategories = [];
    self.highlightedCategoriesForRemoval = [];
    self.categoryVisibility = [];
    self.categoryNameMap = {};

    self.highlightedOffers = [];
    self.highlightedOffersForRemoval = [];
    self.offerVisibility = [];
    self.offerNameMap = {};

    self.columnList = [
        { 'header' : 'email_id' , 'label' : 'Email ID' },
        { 'header' : 'first_name' , 'label' : 'First Name' },
        { 'header' : 'last_name' , 'label' : 'Last Name' },
        { 'header' : 'address' , 'label' : 'Address' },
        { 'header' : 'address2' , 'label' : 'Address 2'},
        { 'header' : 'city' , 'label' : 'City' },
        { 'header' : 'state' , 'label' : 'State' },
        { 'header' : 'zip' , 'label' : 'Zip' },
        { 'header' : 'country' , 'label' : 'Country' },
        { 'header' : 'gender' , 'label' : 'Gender' },
        { 'header' : 'ip' , 'label' : 'IP Address' },
        { 'header' : 'phone' , 'label' : 'Phone Number' },
        { 'header' : 'source_url' , 'label' : 'Source URL' },
        { 'header' : 'age' , 'label' : 'Age' },
        { 'header' : 'device_type' , 'label' : 'Device Type' },
        { 'header' : 'device_name' , 'label' : 'Device Name' },
        { 'header' : 'carrier' , 'label' : 'Carrier' },
        { 'header' : 'capture_date' , 'label' : 'Capture Date' },
        { 'header' : 'esp_account' , 'label' : 'ESP Account' },
        { 'header' : 'email_address' , 'label' : 'Email Address' }
    ];
    self.selectedColumns = [];
    self.availableWidgetTitle = "Available Columns";
    self.chosenWidgetTitle = "Selected Columns";
    self.columnLabelField = 'label';
    self.columnHeaderField = 'header';

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

    self.addIsps = function () {
        angular.forEach( self.highlightedIsps , function ( ispId ) {
            self.ispVisibility[ ispId ] = false;

            self.current.isps[ ispId ] = self.ispNameMap[ ispId ];
        } );

        self.highlightedIsps = [];
    };

    self.removeIsps = function () {
        angular.forEach( self.highlightedIspsForRemoval , function ( ispId ) {
            self.ispVisibility[ ispId ] = true;

            delete( self.current.isps[ ispId ] );
        } );

        self.highlightedIspsForRemoval = [];
    };


    self.addCategories = function () {
        angular.forEach( self.highlightedCategories , function ( categoryId ) {
            self.categoryVisibility[ categoryId ] = false;

            self.current.categories[ categoryId ] = self.categoryNameMap[ categoryId ];
        } );

        self.highlightedCategories = [];
    };

    self.removeCategories = function () {
        angular.forEach( self.highlightedCategoriesForRemoval , function ( categoryId ) {
            self.categoryVisibility[ categoryId ] = true;

            delete( self.current.categories[ categoryId ] );
        } );

        self.highlightedCategoriesForRemoval = [];
    };

    self.addOffers = function () {
        angular.forEach( self.highlightedOffers , function ( offerId ) {
            self.offerVisibility[ offerId ] = false;

            self.current.offers[ offerId ] = self.offerNameMap[ offerId ];
        } );

        self.highlightedOffers = [];
    };

    self.removeOffers = function () {
        angular.forEach( self.highlightedOffersForRemoval , function ( offerId ) {
            self.offerVisibility[ offerId ] = true;

            delete( self.current.offers[ offerId ] );
        } );

        self.highlightedOffersForRemoval = [];
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

    self.columnMembershipCallback = function (){
        var columnList = [];
        angular.forEach( self.selectedColumns , function ( column , columnIndex ) {
            columnList.push( column[ self.columnHeaderField ] );
        } );
        self.current.selectedColumns = columnList;
    };


} ] );
