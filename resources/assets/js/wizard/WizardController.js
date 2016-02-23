mt2App.controller( 'wizardController' , [ '$log' , '$window' , '$location' , '$timeout' , 'WizardApiService', '$sce'  , function ( $log , $window , $location , $timeout , WizardApiService, $sce ) {
    var self = this;
    self.$location = $location;
    self.stepHtml = "";

    self.loadFirstStep = function () {
        WizardApiService.getFirstStep(  function ( response ) {
            self.stepHtml = $sce.trustAsHtml(response.data);
        } )
    }




} ] );
