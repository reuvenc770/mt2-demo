mt2App.controller( 'NavigationController' , [ 'NavigationApiService' , 'modalService' , function ( NavigationApiService , modalService ) {
    var self = this;
    self.navigation = [];
    self.orphans = [];
    self.formSubmitted = false;

    self.loadNavigation = function (){
       NavigationApiService.getPermissionsTree(self.successNavCallback,self.failNavCallback);
       NavigationApiService.getValidOrphans(self.successOrphanCallback,self.failOrphanCallback);
    };


    self.updateNavigation = function(){
        self.formSubmitted = true;
        NavigationApiService.updateNavigation(self.navigation,self.successUpdateCallback,self.failUpdateCallback)
    };

    self.successNavCallback = function (response) {
        self.navigation = response.data;
    };

    self.successOrphanCallback = function (response) {
        self.orphans = response.data;
    };

    self.successUpdateCallback = function (){
        self.formSubmitted = false;
        modalService.setModalLabel( 'Success' );
        modalService.setModalBody( 'Navigation has been updated. Please refresh page to see new navigation.' );
        modalService.launchModal();
    };

    self.failNavCallback = function() {
        modalService.simpleToast( 'Failed to get nav tree.' );
    };

    self.failOrphanCallback = function () {
        modalService.simpleToast( 'Failed to load unused routes.' );
    };

    self.failUpdateCallback = function (){
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to update navigation.' );
        modalService.launchModal();
    };

} ] );
