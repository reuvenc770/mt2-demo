mt2App.controller( 'SeedController' , [ '$log' , '$window' , '$location' , '$timeout' , 'SeedApiService', '$rootScope', '$mdConstant' , 'formValidationService', 'modalService' , 'paginationService' , function ( $log , $window , $location , $timeout , SeedApiService, $rootScope , $mdConstant , formValidationService, modalService , paginationService ) {
    var self = this;
   self.email_address = '';

    self.delete = function(recordId) {
        var r = confirm("Are you sure you want to delete this");
        if (r == true) {
            SeedApiService.deleteRow(recordId, self.deleteRowSuccess, self.deleteRowFailure)
        }
    };
    
    self.create = function () {
        console.log(self.email_address);
        SeedApiService.saveRow({email_address: self.email_address}, self.addRowSuccess, self.addRowFailure);
    };
    
    
    
    //callbacks
    self.addRowFailure = function ( response ) {
        modalService.setModalLabel('Failed To Added Row');
        modalService.setModalBodyRawHtml(response.data.delete);
        modalService.launchModal();
        self.loadAccounts();
    };

    self.addRowSuccess = function ( response ) {
        modalService.simpleToast("Successfully Added Row");
        self.loadAccounts();
    };

    self.deleteRowFailure = function ( response ) {
        modalService.setModalLabel('Failed To Delete Row');
        modalService.setModalBodyRawHtml(response.data.delete);
        modalService.launchModal();
        self.loadAccounts();
    };

    self.deleteRowSuccess = function ( response ) {
        modalService.simpleToast("Successfully Deleted Row");
        self.loadAccounts();
    };
} ] );
