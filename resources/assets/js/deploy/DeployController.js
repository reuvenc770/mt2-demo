mt2App.controller('DeployController', ['$log', '$window', '$location', '$timeout', 'DeployApiService', '$mdToast', '$rootScope', '$q', '$interval', function ($log, $window, $location, $timeout, DeployApiService, $mdToast, $rootScope, $q, $interval) {
    var self = this;
    self.$location = $location;
    self.currentDeploy = {
        send_date: '',
        deploy_id: '',
        esp_account_id: '',
        offer_id: "",
        list_profile_id: "",
        mailing_domain_id: "",
        content_domain_id: "",
        template_id: "",
        cake_affiliate_id: "",
        notes: "",
        user_id: ""
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
    self.deploys = [];
    self.searchText = "";
    self.listProfiles = [];
    self.disableExport = true;
    self.file = "";
    self.polling = "";
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = '1';


    self.loadAccounts = function () {
        self.loadEspAccounts();
        self.loadAffiliates();
        self.loadListProfiles();
        self.loadDeploys();
        self.currentlyLoading = 0;
    };


    self.loadEspAccounts = function () {
        self.currentlyLoading = 1;
        DeployApiService.getEspAccounts(self.loadEspSuccess, self.loadEspFail);
    };

    self.loadDeploys = function () {
        self.currentlyLoading = 1;
        DeployApiService.getDeploys(self.currentPage, self.paginationCount, self.searchType, self.searchData, self.loadDeploysSuccess, self.loadDeploysFail);
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
            endString =  moment( self.search.startDate ).format( 'YYYY-MM-DD' );
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

    self.saveNewDeploy = function () {
        self.currentDeploy.user_id = _config.userId;
        self.currentDeploy.deploy_id = undefined; //faster then delete
        DeployApiService.insertDeploy(self.currentDeploy, self.loadNewDeploySuccess, self.formFail);
    };
    self.updateDeploy = function () {
        DeployApiService.updateDeploy(self.currentDeploy, self.updateDeploySuccess, self.formFail);
    };

    self.createPackages = function () {
       var packageIds = self.selectedRows;
        DeployApiService.deployPackages(packageIds, self.createPackageSuccess, self.createPackagesFailed)
    };

    self.searchDeploys = function (type, searchData, text){
        self.searchType = type;
        self.searchData = searchData;
        self.loadDeploys();
        self.currentlyLoading = 0;
        self.search = {};
    };


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

    self.actionLink = function () {
        if (self.editView) {
            self.updateDeploy();
        } else {
            self.saveNewDeploy();
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
                self.currentDeploy.list_profile_id = deployData.list_profile_id.toString();
                self.currentDeploy.from_id = deployData.from_id.toString();
                self.currentDeploy.subject_id = deployData.subject_id.toString();
                self.currentDeploy.template_id = deployData.template_id.toString();
                self.currentDeploy.mailing_domain_id = deployData.mailing_domain_id.toString();
                self.currentDeploy.content_domain_id = deployData.content_domain_id.toString();
                self.currentDeploy.cake_affiliate_id = deployData.cake_affiliate_id.toString();
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
                self.currentDeploy.list_profile_id = deployData.list_profile_id;
                self.currentDeploy.template_id = deployData.template_id;
                self.currentDeploy.mailing_domain_id = deployData.mailing_domain_id;
                self.currentDeploy.content_domain_id = deployData.content_domain_id;
                self.currentDeploy.cake_affiliate_id = deployData.cake_affiliate_id;
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

        modalBody.text(bodyText);
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
