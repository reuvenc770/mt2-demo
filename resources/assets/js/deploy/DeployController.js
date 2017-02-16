mt2App.controller('DeployController', ['$log', '$window', '$location', '$timeout', 'DeployApiService', '$rootScope', '$q', '$interval' , '$mdDialog' , 'modalService' , 'formValidationService' , 'paginationService' , '$cookies' , function ($log, $window, $location, $timeout, DeployApiService, $rootScope, $q, $interval , $mdDialog , modalService , formValidationService , paginationService , $cookies ) {
    var self = this;
    self.$location = $location;
    self.currentDeploy = {
        send_date: '',
        deploy_id: '',
        esp_account_id: '',
        offer_id: "",
        list_profile_combine_id: "0",
        mailing_domain_id: "",
        content_domain_id: "",
        template_id: "",
        cake_affiliate_id: "",
        notes: "",
        user_id: "",
        encrypt_cake: "",
        fully_encrypt:"",
        url_format:""

    };
    self.search = {
        esp_account_id: ''
    };
    self.selectedRows = [];
    self.esps = [];
    self.formButtonText = "Save Deploy";
    self.formHeader = "New Deploy";
    self.editView = false;
    self.uploadedDeploys = [];
    self.offerData = [];
    self.searchType = "";
    self.searchData = "";
    self.uploadErrors = false;
    self.espAccounts = [];
    self.firstParty = false;
    self.currentlyLoading = 0;
    self.templates = [];
    self.deployLinkText = "Download Package";
    self.tempDeploy = false;
    self.cakeAffiliates = [];
    self.espLoaded = true;
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
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = '1';
    self.deployTotal = 0;
    self.sort = "-deploy_id";
    self.queryPromise = null;
    self.copyToFutureDate = '';
    self.formSubmitting = false;
    self.recordListStatus = 'index';

    self.columnToggleMapping = {
        'cfs' : { 'showColumns' : true, 'switchText' : 'Hide' },
        'domains' : { 'showColumns' : true, 'switchText' : 'Hide' }
    };

    modalService.setPopover();

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
        self.queryPromise = DeployApiService.getDeploys(self.currentPage, self.paginationCount, self.sort, self.searchType, self.searchData, self.loadDeploysSuccess, self.loadDeploysFail);
    };

    self.sortCurrentRecords = function () {
        if (self.recordListStatus === 'index' ) {
            self.loadDeploys();
        }

        if ( self.recordListStatus === 'search' ) {
            self.searchDeploys();
        }
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

    self.displayNewDeployForm = function () {
        self.currentDeploy = self.resetAccount();
        self.editView = false;

        self.launchFormModal();
    };

    self.launchFormModal = function() {
        if (self.editView) {
            self.formButtonText = "Update Deploy";
        } else {
            self.formHeader = "New Deploy";
            self.formButtonText = "Save Deploy";
        }

        $mdDialog.show({
            contentElement: '#deployFormModal',
            parent: angular.element(document.body)
        });
    };

    self.closeModal = function(){
        $mdDialog.cancel();
    };

    self.saveNewDeploy = function ( event ) {
        self.formSubmitting = true;

        self.currentDeploy.user_id = _config.userId;
        self.currentDeploy.deploy_id = undefined; //faster then delete
        DeployApiService.insertDeploy(self.currentDeploy, self.loadNewDeploySuccess, self.formFail);
    };

    self.updateDeploy = function () {
        self.formSubmitting = true;

        DeployApiService.updateDeploy(self.currentDeploy, self.updateDeploySuccess, self.formFail);
    };

    self.createPackages = function () {
       var packageIds = self.selectedRows;
        DeployApiService.deployPackages(packageIds, _config.userName, self.createPackageSuccess, self.createPackagesFailed)
    };

    self.searchDeploys = function() {
        self.recordListStatus = 'search';

        var searchObj = {
            "dates": self.search.dates || undefined,
            "deployId": self.search.deployId || undefined,
            "espAccountId": self.search.esp_account_id || undefined,
            "status": self.search.status || undefined,
            "esp": self.search.esp || undefined,
            "offerNameWildcard": self.search.offer || undefined
        };

        self.queryPromise = DeployApiService.searchDeploys(self.paginationCount, self.sort, searchObj, self.loadDeploysSuccess, self.loadDeploysFail);
        self.currentlyLoading = 0;
    };

    self.resetSearch = function() {
        self.search = {
            esp_account_id: ''
        };

        self.loadAccounts();
        self.recordListStatus = 'index';
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

    self.editDeploy = function (deployId) {
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

    self.toggleListProfile = function (){
        self.firstParty =  self.firstParty ?  false: true;
    };

    self.checkExportStatus = function () {
        if (self.selectedRows.length > 0) {
            self.disableExport = false;
        } else {
            self.disableExport = true;
        }
    };

    self.toggleColumns = function ( columnSection ) {
        var isColumnShowing = self.columnToggleMapping[ columnSection ][ 'showColumns' ];

        if ( isColumnShowing ){
            self.columnToggleMapping[ columnSection ][ 'switchText' ] = "Hide";
        } else {
            self.columnToggleMapping[ columnSection ][ 'switchText' ] = "Show";
        }

        self.setColumnViewCookie();
    };

    self.setColumnViewCookie = function () {
        var columnCookieValues = angular.toJson( {"cfs" : self.columnToggleMapping.cfs.showColumns , "domains" : self.columnToggleMapping.domains.showColumns} );

        $cookies.put( 'deployColumnView' , columnCookieValues );
    };

    self.loadLastColumnView = function () {
        var columnCookieValues = angular.fromJson( $cookies.get( 'deployColumnView' ) );

        if ( typeof(columnCookieValues) != 'undefined') {
            self.columnToggleMapping.cfs.showColumns = columnCookieValues.cfs;
            self.columnToggleMapping.domains.showColumns =  columnCookieValues.domains;
        }
    };

    self.exportCsv = function () {
        returnUrl = DeployApiService.exportCsv(self.selectedRows);
        $window.open(returnUrl);
        self.selectedRows = [];
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
        var dayIndex = ( day == 0 ? 6 : day - 1 );
        var dateChar = self.offerData.exclude_days.charAt( dayIndex );

        return dateChar === 'N';
    };

    self.previewDeploys = function (){
        var packageIds = self.selectedRows;
        var url ="/deploy/preview/";
        for (index = 0; index < packageIds.length; ++index) {
            $window.open(url + packageIds[index]);
        }

        self.selectedRows = [];
    };
    self.downloadHtml = function (){
        var packageIds = self.selectedRows;
        var url ="/deploy/downloadhtml/";
        for (index = 0; index < packageIds.length; ++index) {
            $window.open(url + packageIds[index]);
        }
        self.selectedRows = [];
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
                    '<md-toolbar class="mt2-theme-toolbar">' +
                        '<div class="md-toolbar-tools mt2-theme-toolbar-tools">' +
                            '<h2>Schedule Future Deploy</h2>' +
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

        self.closeModal();

        modalService.setModalLabel('Success');
        modalService.setModalBody('Deploys Uploaded!');
        modalService.launchModal();

        self.loadDeploys();
        self.editView = false;
    };

    self.validateSuccess = function (response) {
        self.uploadedDeploys = response.data.rows;
        self.uploadErrors = response.data.errors;

        $mdDialog.show({
            contentElement: '#validateModal' ,
            parent: angular.element(document.body),
            clickOutsideToClose: true
        });
    };
    self.loadDeploysSuccess = function (response) {
        self.deploys = response.data.data;
        self.pageCount = response.data.last_page;
        self.deployTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
        $timeout( function () { $(function () { $('[data-toggle="popover"]').popover({trigger:'hover', placement: 'bottom'}) } ); } , 1500 );
    };

    self.loadDeploySuccess = function (response) {
        self.currentDeploy.esp_account_id = response.data.esp_account_id;
        self.updateSelects(function () {
            var deployData = response.data;
            self.reloadCFS(deployData.offer_id.id, function () {
                var pieces = deployData.send_date.split('-');
                self.currentDeploy = deployData;
                self.currentDeploy.creative_id = deployData.creative_id.toString();
                self.currentDeploy.list_profile_combine_id = deployData.list_profile_combine_id.toString();
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

        self.launchFormModal();
        self.formHeader = "Edit Deploy # " + response.data.id;
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
                self.currentDeploy.list_profile_combine_id = deployData.list_profile_combine_id;
                self.currentDeploy.template_id = deployData.template_id.toString();
                self.currentDeploy.mailing_domain_id = deployData.mailing_domain_id.toString();
                self.currentDeploy.content_domain_id = deployData.content_domain_id.toString();
                self.currentDeploy.cake_affiliate_id = deployData.cake_affiliate_id.toString();
                self.offerLoading = false;
            });
            $rootScope.$broadcast('angucomplete-alt:changeInput', 'offer', deployData.offer_id);

        });

        self.launchFormModal();
    };


    self.updateDeploySuccess = function (response) {
        self.currentDeploy = self.resetAccount();
        $rootScope.$broadcast('angucomplete-alt:clearInput');

        self.closeModal();

        modalService.setModalLabel('Success');
        modalService.setModalBody('Deploy Edited!');
        modalService.launchModal();

        self.loadDeploys();
        self.editView = false;

        self.formSubmitting = false;
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

        modalService.setModalLabel('Success');
        modalService.setModalBody('New Deploy Created!');
        modalService.launchModal();

        self.loadDeploys();

        self.formSubmitting = false;
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
        formValidationService.resetFieldErrors( self );

        formValidationService.loadFieldErrors( self , response );

        self.formSubmitting = false;
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

        modalService.setModalLabel('Success');
        modalService.setModalBody('Packages are being generated');
        modalService.launchModal();

        self.selectedRows = [];
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
            modalService.setModalLabel('Error');
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
            modalService.setModalBody(errorText);
            modalService.launchModal();
        } else {
            modalService.setModalLabel('Success');
            modalService.setModalBody('Deploys have been created!');
            modalService.launchModal();
        }
        self.currentDeploy = self.resetAccount();
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        self.selectedRows = [];
        self.disableExport = true;
        self.loadDeploys();
        self.editView = false;
    };

    self.copyToFutureFailure = function (response){
        modalService.setModalLabel('Error');
        modalService.setModalBody(response.data.errors.join("<br>"));
        modalService.launchModal();
    };




    self.loadEspFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading ESPs');
        modalService.launchModal();
    };
    self.updateDomainsFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Offers');
        modalService.launchModal();
    };

    self.updateTemplateFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Templates');
        modalService.launchModal();
    };

    self.loadDeploysFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Deploys');
        modalService.launchModal();
    };

    self.loadCakeFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Cake Affiliates');
        modalService.launchModal();
    };

    self.loadProfileFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading List Profiles');
        modalService.launchModal();
    };

    self.updateCreativesFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Creatives');
        modalService.launchModal();
    };

    self.updateFromsFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Froms');
        modalService.launchModal();
    };

    self.updateSubjectsFail = function () {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Something went wrong loading Subjects');
        modalService.launchModal();
    };

    self.resetAccount = function () {
        $rootScope.$broadcast('angucomplete-alt:clearInput');
        formValidationService.resetFieldErrors( self );
        return {
            send_date: '',
            deploy_id: '',
            esp_account_id: '',
            offer_id: "",
            list_profile_combine_id: "",
            mailing_domain_id: "",
            content_domain_id: "",
            template_id: "",
            cake_affiliate_id: "",
            notes: ""
        }
    };
}]);
