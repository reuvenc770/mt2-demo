mt2App.controller( 'CpmPricingController' , [ 'CpmPricingApiService' , '$mdDialog' , '$timeout' , '$log' , 'formValidationService' , 'modalService' , function ( CpmPricingApiService , $mdDialog , $timeout , $log , formValidationService , modalService ) {
    var self = this;

    self.queryPromise = undefined;
    self.pricings = [];

    self.currentPricing = {};
    self.currentOverride = {};

    self.startDateState = undefined;
    self.endDateState = undefined;

    self.isNewRecord = false;
    self.formSubmitting = false;
    self.formType = 'schedule';
    self.prepopOfferName = '';
    self.currentId = 0;

    self.modalInfo = {
        'title' : { "schedule" : 'Add CPM Pricing' , "override" : 'Add Deploy ID Override' } ,
        'panel_title' : { "schedule" : 'Pricing Details' , "override" : 'Override Details' } ,
        'submit_text' : { "schedule" : 'Pricing' , "override" : 'Override' }
    };

    self.loadPricings = function () {
        self.queryPromise = CpmPricingApiService.getPricings( {} , self.loadPricingsSuccessCallback , self.loadPricingsFailureCallback );

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadPricingsSuccessCallback = function ( response ) {
        self.pricings = response.data;
    };

    self.loadPricingsFailureCallback = function ( response ) {
        $log.error( response );
    };

    self.addSchedule = function () {
        self.isNewRecord = true;

        self.switchToScheduleForm();

        self.showModal();
    };

    self.addOverride = function () {
        self.isNewRecord = true;

        self.switchToOverrideForm();

        self.showModal();
    };

    self.edit = function ( record ) {
        if ( typeof( record.deploy_id ) != undefined && record.deploy_id != '' ) {
            self.editOverride( record );
        } else {
            self.editSchedule( record );
        }
    };

    self.editSchedule = function ( record ) {
        self.switchToScheduleForm();

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

    self.editOverride = function ( record ) {
        self.switchToOverrideForm();

        self.isNewRecord = false;

        self.currentOverride = {
            "deploy_id" : record.deploy_id ,
            "amount" : record.amount
        };

        self.currentId = record.id;
        
        self.prepopDate( record.start_date , record.end_date );

        self.showModal();
    };

    self.switchToScheduleForm = function () {
        self.formType = 'schedule';
    };

    self.switchToOverrideForm = function () {
        self.formType = 'override';
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
        self.currentOverride = {};
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
        var formObjectName = ( self.formType === 'schedule' ? 'currentPricing' : 'currentOverride' );

        if ( self.startDateState ) {
            self[ formObjectName ].startDate = moment( self.startDateState ).format( 'YYYY-MM-DD' );
        }

        if ( self.endDateState ) {
            self[ formObjectName ].endDate = moment( self.endDateState ).format( 'YYYY-MM-DD' );
        }
    };

    self.saveForm = function () {
        self.formSubmitting = true;

        var data = {};

        if ( self.formType == 'schedule' ) {
            data = self.currentPricing;
        } else {
            data = self.currentOverride;
        }

        if ( self.isNewRecord ) {
            CpmPricingApiService.create(
                data ,
                function ( response ) {
                    self.formSubmitting = false;

                    self.loadPricings();
                    self.closeModal();
                } ,
                function ( response ) {
                    self.formSubmitting = false;
                } );
        } else {
            CpmPricingApiService.update(
                self.currentId ,
                data ,
                function ( response ) {
                    self.formSubmitting = false;

                    self.loadPricings();
                    self.closeModal();
                } ,
                function ( response ) {
                    self.formSubmitting = false;
                } );
        }
    };
} ] );
