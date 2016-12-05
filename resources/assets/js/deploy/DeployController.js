mt2App.controller('DeployController', ['$log', '$window', '$location', '$timeout', 'DeployApiService', '$mdToast', '$rootScope', '$q', '$interval' , '$mdDialog' , function ($log, $window, $location, $timeout, DeployApiService, $mdToast, $rootScope, $q, $interval , $mdDialog ) {
    var self = this;
    self.$location = $location;
    self.currentDeploy = {
        send_date: '',
        deploy_id: '',
        esp_account_id: '',
        offer_id: "",
        list_profile_id: "0",
        mailing_domain_id: "",
        content_domain_id: "",
        template_id: "",
        cake_affiliate_id: "",
        notes: "",
        user_id: "",
        encrypt_cake: "",
        fully_encrypt:"",
        url_format:"",
        party:"3"

    };
    self.search = {
        esp_account_id: ''
    };
    self.selectedRows = [];
    self.esps = [];
    var text = "ID to Be Generated";
    self.deployIdDisplay = text;
    self.editView = false;
    self.uploadedDeploys = [];
    self.offerData = [];
    self.searchType = "";
    self.searchData = "";
    self.uploadErrors = false;
    self.espAccounts = [];
    self.currentlyLoading = 0;
    self.templates = [];
    self.firstParty = false;
    self.deployLinkText = "Download Package";
    self.tempDeploy = false;
    self.cakeAffiliates = [];
    self.espLoaded = true;
    self.showRow = false;
    self.offerLoading = true;
    self.mailingDomains = []; //id is 1
    self.contentDomains = []; //id is 2
    self.offers = [];
    self.formErrors = [];
    self.minDate = new Date();
    self.deploys = [];
    self.searchText = "";
    self.listProfiles = [];
    self.disableExport = true;
    self.file = "";
    self.polling = "";
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = '1';
    self.deployTotal = 0;
    self.sort = "-deployment_status";
    self.queryPromise = null;
    self.copyToFutureDate = '';

    self.loadAccounts = function () {
        self.loadEspAccounts();
        self.loadAffiliates();
        //self.loadListProfiles();
        self.loadDeploys();
        self.currentlyLoading = 0;
    };


    self.loadEspAccounts = function () {
        self.currentlyLoading = 1;
        DeployApiService.getEspAccounts(self.loadEspSuccess, self.loadEspFail);
    };

    self.loadDeploys = function () {
        self.queryPromise = DeployApiService.getDeploys(self.currentPage, self.paginationCount, self.searchType, self.searchData, self.loadDeploysSuccess, self.loadDeploysFail);
    };

    self.loadListProfiles = function () {
        self.currentlyLoading = 1;
        DeployApiService.getListProfiles(self.loadProfileSuccess, self.loadProfileFail)
    };

    self.updateSelects = function (callBack) {
        $q.all([
            DeployApiService.getMailingDomains(self.currentDeploy.esp_account_id, 1, self.updateMailingSuccess, self.updateDomainsFail),
            DeployApiService.getMailingDomains(self.currentDeploy.esp_account_id, 2, self.updateContentSuccess, self.updateDomainsFail),
            DeployApiService.getTemplates(self.currentDeploy.esp_account_id, self.updateTemplateSuccessNoCopy, self.updateTemplateFail)
        ]).then(callBack);
    };

    self.loadAffiliates = function () {
        DeployApiService.getCakeAffiliates(self.loadCakeSuccess, self.loadCakeFail);
    };

    self.copyRow = function (id) {
        self.showRow = false;
        DeployApiService.getDeploy(id, self.loadDeployCopySuccess, self.loadDeployFail);
        self.espLoaded = false;
        self.editView = false;
    };


    self.startPolling = function () {
        //Set the Timer start message.
        self.polling = $interval(function () {
           DeployApiService.checkForPackages(self.successCheckPackageStatus,self.failCheckPacakgeStatus);
        }, 3000);
    };

    //Timer stop function.
    self.stopPolling = function () {
             $interval.cancel(self.polling);
    };

    self.updateSearchDate = function () {
        var startString = '';
        var endString = '';
        if(self.search.startDate){
            startString =  moment( self.search.startDate ).format( 'YYYY-MM-DD' );
        }

        if(self.search.endDate){
            endString =  moment( self.search.endDate ).format( 'YYYY-MM-DD' );
        }
        if(self.search.startDate && self.search.endDate) {
            self.search.dates = startString + ',' + endString;
        }
    };

    self.displayForm = function () {
        self.deployIdDisplay = text;
        self.currentDeploy = self.resetAccount();
        self.showRow = true;
        self.editView = false;
    };



    self.saveNewDeploy = function ( event , form ) {
        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {

            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        self.currentDeploy.user_id = _config.userId;
        self.currentDeploy.deploy_id = undefined; //faster then delete
        DeployApiService.insertDeploy(self.currentDeploy, self.loadNewDeploySuccess, self.formFail);
    };
    self.updateDeploy = function () {
        DeployApiService.updateDeploy(self.currentDeploy, self.updateDeploySuccess, self.formFail);
    };

    self.createPackages = function () {
       var packageIds = self.selectedRows;
        DeployApiService.deployPackages(packageIds, _config.userName, self.createPackageSuccess, self.createPackagesFailed)
    };

    self.searchDeploys = function() {
        var searchObj = {
            "dates": self.search.dates || undefined,
            "deployId": self.search.deployId || undefined,
            "espAccountId": self.search.esp_account_id || undefined,
            "status": self.search.status || undefined,
            "esp": self.search.esp || undefined,
            "offerNameWildcard": self.search.offer || undefined
        };

        self.queryPromise = DeployApiService.searchDeploys(self.paginationCount, searchObj, self.loadDeploysSuccess, self.loadDeploysFail);
        self.currentlyLoading = 0;
    }

    self.offerWasSelected = function (item) {
        if (typeof item != 'undefined') {
            if (item.title === undefined) {
                self.offerData = item.originalObject;
                self.currentDeploy.offer_id = item.originalObject.id;
                self.offerLoading = false;
            } else {
                self.reloadCFS(item.originalObject.id, function () {
                    self.currentDeploy.offer_id = item.originalObject.id;
                    self.offerData = item.originalObject;
                    self.offerLoading = false;
                });
            }
        }
    };

    self.reloadCFS = function (offerId, callBack) {
        $q.all([
            DeployApiService.getCreatives(offerId, self.updateCreativesSuccess, self.updateCreativesFail),
            DeployApiService.getSubjects(offerId, self.updateSubjectsSuccess, self.updateSubjectsFail),
            DeployApiService.getFroms(offerId, self.updateFromsSuccess, self.updateFromsFail)
        ]).then(callBack);
    };

    self.editRow = function (deployId) {
        self.showRow = false;
        self.currentDeploy = self.resetAccount();
        DeployApiService.getDeploy(deployId, self.loadDeploySuccess, self.loadDeployFail);
        self.espLoaded = false;
        self.editView = true;
    };

    self.actionLink = function ( event , form ) {
        if (self.editView) {
            self.updateDeploy();
        } else {
            self.saveNewDeploy( event , form );
        }
    };

    self.actionText = function () {
        if (self.editView) {
            return "Edit Row"
        } else {
            return "Save Row"
        }
    };

    self.toggleRow = function (selectedValue) {
        var index = self.selectedRows.indexOf(selectedValue);

        if (index >= 0) {
            self.selectedRows.splice(index, 1);
        } else {
            self.selectedRows.push(selectedValue);
        }

        if (self.selectedRows.length > 0) {
            self.disableExport = false;
        } else {
            self.disableExport = true;
        }

        if (self.selectedRows.length > 1) {
            self.deployLinkText = "Send Packages to FTP"
        } else {
            self.deployLinkText = "Download Package";
        }
    };

    self.checkChecked = function(selectedValue){
        var index = self.selectedRows.indexOf(selectedValue);
        return index >= 0;
    };

    self.exportCsv = function () {
        returnUrl = DeployApiService.exportCsv(self.selectedRows);
        $window.open(returnUrl);
    };


    self.fileUploaded = function ($file) {
        self.file = $file.relativePath;
        DeployApiService.validateDeploy(self.file, self.validateSuccess, self.validateFail);
    };

    self.massUploadList = function (){
        if(!self.uploadErrors && self.uploadedDeploys.length > 0)  {
            DeployApiService.massUpload(self.uploadedDeploys,self.massUploadSuccess, self.massUploadFail)
        }
    };

    self.canOfferBeMailed = function (date){
        var day = date.getDay();
        var dateChar = self.offerData.exclude_days.charAt(day);
        return dateChar === 'N';
    };

    self.previewDeploys = function (){
        var packageIds = self.selectedRows;
        var url ="/deploy/preview/";
        for (index = 0; index < packageIds.length; ++index) {
            $window.open(url + packageIds[index]);
        }
    };
    self.downloadHtml = function (){
        var packageIds = self.selectedRows;
        var url ="/deploy/downloadhtml/";
        for (index = 0; index < packageIds.length; ++index) {
            $window.open(url + packageIds[index]);
        }
    };

    self.checkStatus = function(approval,status){
        if(approval == 1 && status == 'A'){
            return true;
        }
        return false;
    };


    self.copyToFuture = function( ev ) {
        $mdDialog.show( {
            targetEvent : ev ,
            template :
                '<md-dialog>' + 
                    '<md-toolbar>' +
                        '<div class="md-toolbar-tools">' +
                            '<h2>Scedule Future Deploy</h2>' +
                        '</div>' +
                    '</md-toolbar>' +
                    '<md-dialog-content>' + 
                        '<div class="md-dialog-content">' +
                            '<h4>Please choose a future date for selected deploys</h4>' +
                        '</div>' +
                        '<md-datepicker ng-model="deployDate" md-min-date="minDate" md-placeholder="Pick a Date"></md-datepicker>'  +
                    '</md-dialog-content>' +
                    '<md-dialog-actions>' +
                        '<md-button ng-click="answer( false )">Cancel</md-button>' +
                        '<md-button ng-click="answer( true )">Submit Date</md-button>' +
                    '</md-dialog-actions>' +
                '</md-dialog>' ,
            controller : function DeployFutureDateController ( $scope , $mdDialog ) {
                $scope.deployDate = ( self.copyToFutureDate != '' ? new Date( self.copyToFutureDate ) : new Date() ); 
                $scope.minDate = new Date();

                $scope.answer = function ( submit ) {
                    if ( submit === true ) {
                        self.copyToFutureDate = $scope.deployDate;

                        DeployApiService.copyToFuture( self.selectedRows, self.copyToFutureDate, self.copyToFutureSuccess, self.copyToFutureFailure );
                    }

                    $mdDialog.hide();
                }
            }
        } );
    };

    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadDeploys();
        self.currentlyLoading = 0;
    } );



    /**
     * Callbacks
     */

    self.massUploadSuccess = function (response){
        self.currentDeploy = self.resetAccount();
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        $mdToast.showSimple('Deploys Uploaded!');
        self.loadDeploys();
        self.editView = false;
        self.showRow = false;
    };

    self.validateSuccess = function (response) {
        self.uploadedDeploys = response.data.rows;
        self.uploadErrors = response.data.errors;
        $('#validateModal').modal('show');
    };
    self.loadDeploysSuccess = function (response) {
        self.deploys = response.data.data;
        self.pageCount = response.data.last_page;
        self.deployTotal = response.data.total;
    };

    self.loadDeploySuccess = function (response) {
        self.currentDeploy.esp_account_id = response.data.esp_account_id;
        self.deployIdDisplay = response.data.id;
        self.updateSelects(function () {
            var deployData = response.data;
            self.reloadCFS(deployData.offer_id.id, function () {
                var pieces = deployData.send_date.split('-');
                self.currentDeploy = deployData;
                self.currentDeploy.creative_id = deployData.creative_id.toString();
               // self.currentDeploy.list_profile_id = deployData.list_profile_id.toString();
                self.currentDeploy.from_id = deployData.from_id.toString();
                self.currentDeploy.subject_id = deployData.subject_id.toString();
                self.currentDeploy.template_id = deployData.template_id.toString();
                self.currentDeploy.mailing_domain_id = deployData.mailing_domain_id == 0 ? null : deployData.mailing_domain_id.toString();
                self.currentDeploy.content_domain_id = deployData.content_domain_id == 0 ? null : deployData.content_domain_id.toString();
                self.currentDeploy.cake_affiliate_id = deployData.cake_affiliate_id.toString();
                self.currentDeploy.fully_encrypt = deployData.fully_encrypt.toString();
                self.currentDeploy.encrypt_cake = deployData.encrypt_cake.toString();
                self.currentDeploy.offer_id = deployData.offer_id.id;
                self.currentDeploy.send_date = new Date(pieces[0], pieces[1] - 1, pieces[2]);
                self.offerLoading = false;
            });
            $rootScope.$broadcast('angucomplete-alt:changeInput', 'offer', deployData.offer_id);
        });
        self.showRow = true;
    };

    self.loadDeployCopySuccess = function (response) {
        self.currentDeploy = self.resetAccount();
        self.currentDeploy.esp_account_id = response.data.esp_account_id;
        self.updateSelects(function () {
            var deployData = response.data;
            self.reloadCFS(response.data.offer_id.id, function () {
                var pieces = deployData.send_date.split('-');
                self.currentDeploy.send_date = new Date(pieces[0], pieces[1] - 1, pieces[2]);
                self.currentDeploy.notes = deployData.notes;
               // self.currentDeploy.list_profile_id = deployData.list_profile_id;
                self.currentDeploy.template_id = deployData.template_id.toString();
                self.currentDeploy.mailing_domain_id = deployData.mailing_domain_id.toString();
                self.currentDeploy.content_domain_id = deployData.content_domain_id.toString();
                self.currentDeploy.cake_affiliate_id = deployData.cake_affiliate_id.toString();
                self.offerLoading = false;
            });
            $rootScope.$broadcast('angucomplete-alt:changeInput', 'offer', deployData.offer_id);

        });
        self.showRow = true;
    };


    self.updateDeploySuccess = function (response) {
        self.currentDeploy = self.resetAccount();
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        $mdToast.showSimple('Deploy Edited!');
        self.loadDeploys();
        self.editView = false;
        self.showRow = false;
    };

    self.loadEspSuccess = function (response) {
        self.espAccounts = response.data;
    };

    self.updateContentSuccess = function (response) {
        self.contentDomains = response.data;
    };

    self.updateMailingSuccess = function (response) {
        self.mailingDomains = response.data;
    };

    self.updateTemplateSuccessNoCopy = function (response) {
        self.templates = response.data;
        self.espLoaded = false;
    };

    self.updateTemplateSuccess = function (response) {
        self.templates = response.data;
        self.espLoaded = false;
    };

    self.loadCakeSuccess = function (response) {
        self.cakeAffiliates = response.data;
    };

    self.loadProfileSuccess = function (response) {
        self.listProfiles = response.data;
    };

    self.loadNewDeploySuccess = function (response) {
        self.currentDeploy = self.resetAccount();
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        $mdToast.showSimple('New Deploy Created!');
        self.loadDeploys();
        self.showRow = false;
    };

    self.updateCreativesSuccess = function (response) {
        self.creatives = response.data;
    };

    self.updateFromsSuccess = function (response) {
        self.froms = response.data;
    };

    self.updateSubjectsSuccess = function (response) {
        self.subjects = response.data;
    };

    self.formFail = function (response) {
        self.loadFieldErrors(response);
    };

    self.exportCsvSuccess = function (response) {
        $window.open(response);
    };

    self.createPackageSuccess = function (response){
        if(!response.data.success) {
            var headers = response.headers();
            var blob = new Blob([response.data],{'type':"application/octet-stream"});
            var windowUrl = (window.URL || window.webkitURL);
            var downloadUrl = windowUrl.createObjectURL(blob);
            var anchor = document.createElement("a");
            anchor.href = downloadUrl;
            var fileNamePattern = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            anchor.download = fileNamePattern.exec(headers['content-disposition'])[1].replace(/['"]+/g, '');
            document.body.appendChild(anchor);
            anchor.click();
            windowUrl.revokeObjectURL(blob);
        }
        $mdToast.showSimple('Packages are being generated');
        self.loadDeploys();
        self.startPolling();
    };

    self.successCheckPackageStatus = function (response){
        var count = response.data.length;
        if(count == 0){
            self.stopPolling();
            self.loadDeploys()
        }
    };

    self.copyToFutureSuccess = function (response){
        var errors = response.data.errors;
        var errorText = "";
        if(response.data.errors.length > 1){
            self.setModalLabel('Error');
            for (i = 0; i < errors.length; i++) {
                deploy_id = errors[i].deploy_id;
                delete errors[i].deploy_id;
                errorText += "<b>Deploy ID " + deploy_id + " has errors:</b><br/>";
                textErrors = Object.keys(errors[i]).map(function(k) { return errors[i][k] });
                for (y = 0; y < textErrors.length; y++) {
                    errorText += textErrors[y];
                }
                errorText += "<br/>";
            }
            self.setModalBody(errorText);
            self.launchModal();
        } else {
            $mdToast.showSimple('Deploys have been created!');
        }
        self.currentDeploy = self.resetAccount();
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        self.selectedRows = [];
        self.disableExport = true;
        self.loadDeploys();
        self.editView = false;
        self.showRow = false;
    };

    self.copyToFutureFailure = function (response){
        $mdToast.showSimple('FAIL');
    };




    self.loadEspFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading ESPs');
        self.launchModal();
    };
    self.updateDomainsFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Offers');
        self.launchModal();
    };

    self.updateTemplateFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Templates');
        self.launchModal();
    };

    self.loadDeploysFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Deploys');
        self.launchModal();
    };

    self.loadCakeFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Cake Affiliates');
        self.launchModal();
    };

    self.loadProfileFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading List Profiles');
        self.launchModal();
    };

    self.updateCreativesFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Creatives');
        self.launchModal();
    };

    self.updateFromsFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Froms');
        self.launchModal();
    };

    self.updateSubjectsFail = function () {
        self.setModalLabel('Error');
        self.setModalBody('Something went wrong loading Subjects');
        self.launchModal();
    };


    /**
     * Errors
     */
    self.loadFieldErrors = function (response) {
        angular.forEach(response.data, function (value, key) {
            self.setFieldError(key, value);
        });
    };

    self.setFieldError = function (field, errorMessage) {
        self.formErrors[field] = errorMessage;
    };

    self.resetFieldErrors = function () {
        self.formErrors = {};
    };

    self.resetAccount = function () {
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        self.resetFieldErrors();
        return {
            send_date: '',
            deploy_id: '',
            esp_account_id: '',
            offer_id: "",
            list_profile_id: "",
            mailing_domain_id: "",
            content_domain_id: "",
            template_id: "",
            cake_affiliate_id: "",
            notes: ""
        }
    };



    /**
     * Page Modal
     */

    self.setModalLabel = function (labelText) {
        var modalLabel = angular.element(document.querySelector('#pageModalLabel'));

        modalLabel.text(labelText);
    };

    self.setModalBody = function (bodyText) {
        var modalBody = angular.element(document.querySelector('#pageModalBody'));

        modalBody.html(bodyText);
    };

    self.launchModal = function () {
        $('#pageModal').modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel('');
        self.setModalBody('');

        $('#pageModal').modal('hide');
    };
}]);
