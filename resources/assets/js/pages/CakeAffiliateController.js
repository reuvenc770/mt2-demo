mt2App.controller( 'CakeAffiliateController' , [ 'CakeAffiliateApiService' , 'paginationService' , 'formValidationService' , '$mdDialog' , 'modalService' , function ( CakeAffiliateApiService , paginationService , formValidationService , $mdDialog , modalService ) {
    var self = this;

    self.affiliatePromise = null;
    self.affiliateRedirects = [];
    self.currentRedirect = {
        'id' : null , 
        'offer_payout_type_id' : null ,
        'redirect_domain' : null 
    };

    self.currentPage = 1;
    self.paginationCount = 10;
    self.sort = "-cake_redirect_id";
    self.affiliateTotal = 0;
    self.pageCount = 0;
    self.paginationOptions = paginationService.getDefaultPaginationOptions();

    self.formSubmitted = false;
    self.formErrors = [];
    self.addDialogTitle = 'Add New Redirect Domain';
    self.editDialogTitle = 'Edit Redirect Domain';
    self.currentDialogTitle = self.addDialogTitle;

    self.addDialogButton = 'Save Redirect Domain';
    self.editDialogButton = 'Update Redirect Domain';
    self.currentDialogButton = self.addDialogButton;
    self.showNewAffiliateButtonText = "New Affiliate";

    self.loadAffiliateRedirectDomains = function () {
        self.affiliatePromise = CakeAffiliateApiService.loadAffiliateRedirectDomains(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            function ( response ) {
                self.affiliateRedirects = response.data.data ;
                self.pageCount = response.data.last_page;
                self.affiliateTotal = response.data.total;
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to load Schedules' ); }
        );
    };

    self.showAddDialog = function ( isEdit ) {
        if ( isEdit ) {
            self.currentDialogTitle = self.editDialogTitle;
            self.currentDialogButton = self.editDialogButton;
        } else {
            self.currentDialogTitle = self.addDialogTitle;
            self.currentDialogButton = self.addDialogButton;
        }

        $mdDialog.show( { 
            contentElement: '#addRedirectModal' ,
            parent: angular.element(document.body)
        } );
    };

    self.showEditDialog = function ( listIndex ) {
        self.currentRedirect = {
            redirect_domain_id : self.affiliateRedirects[ listIndex ][ 'redirect_domain_id' ] ,
            id : String( self.affiliateRedirects[ listIndex ][ 'id' ] ) ,
            name : self.affiliateRedirects[ listIndex ][ 'name' ] ,  
            offer_type : self.affiliateRedirects[ listIndex ][ 'offer_type' ] ,
            offer_payout_type_id : String( self.affiliateRedirects[ listIndex ][ 'offer_payout_type_id' ] ) ,
            redirect_domain : self.affiliateRedirects[ listIndex ][ 'redirect_domain' ]
        };

        self.showAddDialog( true );
    };

    self.clearForm = function () {
        self.currentRedirect = {
            id : null ,
            name : null ,
            offer_type : null ,
            offer_payout_type_id : null ,
            redirect_domain : null 
        };
    };

    self.closeDialog = function () {
        self.clearForm();
        formValidationService.resetFieldErrors( self );

        $mdDialog.hide();
    };

    self.showNewAffiliateFields = function () {
        formValidationService.resetFieldErrors( self );

        if ( self.showNewAffiliateFieldsFlag ) {
            self.showNewAffiliateFieldsFlag = false; 
            self.showNewAffiliateButtonText = "New Affiliate";

            delete self.currentRedirect.new_affiliate_id;
            delete self.currentRedirect.new_affiliate_name;
        } else {
            self.showNewAffiliateFieldsFlag = true; 
            self.showNewAffiliateButtonText = "Select Affiliate";

            self.currentRedirect.new_affiliate_id = null;
            self.currentRedirect.new_affiliate_name = null;
        }
    };

    self.saveRedirect = function () {
        formValidationService.resetFieldErrors( self );
        self.formSubmitted = true;

        CakeAffiliateApiService.saveRedirectAndAffiliate(
            self.currentRedirect ,
            self.showNewAffiliateFieldsFlag ,
            function ( response ) {
                formValidationService.loadFieldErrors( self , response );
                self.formSubmitted = false;
                self.closeDialog();
                self.loadAffiliateRedirectDomains();
                modalService.simpleToast( 'Successfully saved redirect.' );
            } ,
            function ( response ) {
                self.formSubmitted = false;
                formValidationService.loadFieldErrors( self , response );

                modalService.simpleToast( 'Failed to save redirect.' );
            }
        );
    };
} ] );
