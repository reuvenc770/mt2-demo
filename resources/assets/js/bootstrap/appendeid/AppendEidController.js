mt2App.controller('AppendEidController', ['$log', '$window', '$location', '$timeout', 'AppendEidApiService', 'modalService', function ($log, $window, $location, $timeout, AppendEidApiService, modalService) {
    var self = this;
    self.$location = $location;
    self.text = "File not ready";
    self.formSubmitted = true;
    self.file ="";
    self.fields = false;
    self.email = _config.emailAddress;
    self.suppress = false;
    self.feed = false;

    self.createFile = function () {
        var data = {
            'fileName' : self.file,
            'feed' : self.feed,
            'suppress' : self.suppress,
            'fields': self.fields,
            'email': self.email
        };
        AppendEidApiService.uploadList(data, self.uploadSuccessCallback,self.uploadFailCallback);
    };

    self.uploadSuccessCallback = function(response){
        modalService.setModalLabel('Success');
        modalService.setModalBody('Job has been submitted to be run, please check your email later ');
        modalService.launchModal();
        self.text = "File not ready";
        self.file ="";
        self.formSubmitted = true;
    };

    self.unlockButtonLoadFile = function($file){
        self.file = $file.relativePath;
        self.text = "Click here to generate file";
        self.formSubmitted = false;
    }

}]);
