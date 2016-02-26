mt2App.controller( 'wizardController' , [ '$log' , '$window' , '$location' , '$timeout' , 'WizardApiService', '$sce', '$rootScope'  , function ( $log , $window , $location , $timeout , WizardApiService, $sce, $rootScope ) {
    var self = this;
    $rootScope.wizard = self;
    self.$location = $location;
    self.stepHtml = "";
    self.nextStep = "";
    self.prevStep = "";
    self.type = "";

    self.loadFirstStep = function (type) {
        WizardApiService.getFirstStep( type, function ( response ) {
            self.stepHtml = $sce.trustAsHtml(response.data['section']);
            self.nextStep = response.data['nextPage'];
            self.prevStep = response.data['prevPage'];
            self.type = response.data['type'];
        } )
    };

    self.goToNextStep = function () {
        WizardApiService.getStep(self.nextStep,self.type, function ( response ) {
            self.stepHtml = $sce.trustAsHtml(response.data['section']);
            self.nextStep = response.data['nextPage'];
            self.prevStep = response.data['prevPage'];
        } )
    };

    self.goToPrevStep = function () {
        WizardApiService.getStep(self.prevStep,self.type, function ( response ) {
            self.stepHtml = $sce.trustAsHtml(response.data['section']);
            self.nextStep = response.data['nextPage'];
            self.prevStep = response.data['prevPage'];
        } )
    };




} ] );
