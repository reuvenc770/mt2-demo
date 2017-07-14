mt2App.controller( 'CpmPricingController' , [ 'CpmPricingApiService' , '$mdDialog' , '$timeout' , '$log' , 'formValidationService' , 'modalService' , 'paginationService' , function ( CpmPricingApiService , $mdDialog , $timeout , $log , formValidationService , modalService , paginationService ) {
    var self = this;

    self.queryPromise = undefined;
    self.pricings = [];

    self.currentPricing = {};

    self.startDateState = undefined;
    self.endDateState = undefined;

    self.isNewRecord = false;
    self.formSubmitting = false;
    self.prepopOfferName = '';
    self.currentId = 0;

    self.currentPage = 1;
    self.paginationCount = 10;
    self.sort = "-offer_id";
    self.pricingsTotal = 0;
    self.pageCount = 0;
    self.paginationOptions = paginationService.getDefaultPaginationOptions();

    self.loadPricings = function () {
        self.queryPromise = CpmPricingApiService.getPricings( 
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            self.loadPricingsSuccessCallback ,
            self.loadPricingsFailureCallback
        );

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadPricingsSuccessCallback = function ( response ) {
        self.pricings = response.data.data;
        self.pageCount = response.data.last_page;
        self.pricingsTotal = response.data.total;
    };

    self.loadPricingsFailureCallback = function ( response ) {
         modalService.simpleToast( 'Failed to load Pricings' ); 
    };

    self.addSchedule = function () {
        self.isNewRecord = true;

        self.showModal();
    };

    self.edit = function ( record ) {
        self.editSchedule( record );
    };

    self.editSchedule = function ( record ) {
        self.isNewRecord = false;

        self.prepopOfferName = record.name;

        self.currentPricing = {
            "offer_id" : record.offer_id ,
            "amount" : record.amount
        };

        self.currentId = record.id;
        
        self.prepopDate( record.start_date , record.end_date );

        self.showModal();
    };

    self.showModal = function () {
        if ( !self.startDateState && !self.endDateState ) {
            self.startDateState = moment().add( 1 , 'month' ).startOf( 'month' ).toDate();
            self.endDateState = moment().add( 1 , 'month' ).endOf( 'month' ).toDate();
            self.updateDateRange();
        }

        $mdDialog.show( {
            contentElement : '#formModal' ,
            parent: angular.element( document.body )
        } );
    };

    self.closeModal = function () {
        self.currentPricing = {};
        self.startDateState = undefined;
        self.endDateState = undefined;
        self.prepopOfferName = '';
        self.currentId = 0;

        $mdDialog.cancel();
    };

    self.storeSelectedOffer = function ( $item ) {
        if ( typeof( $item ) != 'undefined' ) {
            self.currentPricing[ 'offer_id' ] = $item.originalObject.id;
        }
    };

    self.prepopDate = function ( startDate , endDate ) {
        self.startDateState = moment( startDate ).toDate();
        self.endDateState = moment( endDate ).toDate();

        self.updateDateRange();
    };

    self.updateDateRange = function () {
        if ( self.startDateState ) {
            self.currentPricing.startDate = moment( self.startDateState ).format( 'YYYY-MM-DD' );
        }

        if ( self.endDateState ) {
            self.currentPricing.endDate = moment( self.endDateState ).format( 'YYYY-MM-DD' );
        }
    };

    self.saveForm = function () {
        self.formSubmitting = true;

        if ( self.isNewRecord ) {
            CpmPricingApiService.create(
                self.currentPricing ,
                function ( response ) {
                    self.formSubmitting = false;

                    self.loadPricings();
                    self.closeModal();
                } ,
                function ( response ) {
                    self.formSubmitting = false;

                    modalService.simpleToast( response.data.message ); 
                }
            );
        } else {
            CpmPricingApiService.update(
                self.currentId ,
                self.currentPricing ,
                function ( response ) {
                    self.formSubmitting = false;

                    self.loadPricings();
                    self.closeModal();
                } ,
                function ( response ) {
                    self.formSubmitting = false;

                    modalService.simpleToast( response.data.message ); 
                }
            );
        }
    };
} ] );
