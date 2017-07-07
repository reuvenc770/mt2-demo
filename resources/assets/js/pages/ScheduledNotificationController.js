mt2App.controller( 'ScheduledNotificationController' , [ 'ScheduledNotificationApiService' , 'paginationService' , '$timeout' , '$mdDialog' , 'modalService' , function ( ScheduledNotificationApiService , paginationService , $timeout , $mdDialog , modalService ) {
    var self = this;

    self.emptySchedule = {
        "title" : '' ,
        "cron_expression" : '' ,
        "content_key" : '' ,
        "level" : '' ,
        "content_lookback" : '' ,
        "use_email" : false ,
        "email_recipients" : '' ,
        "email_template_path" : '' ,
        "use_slack" : false ,
        "slack_recipients" : '' ,
        "slack_template_path" : ''
    };
    self.currentSchedule = angular.copy( self.emptySchedule );
    self.cronSelectorOptions = { 
        allowMultiple : true ,
        options : { allowYear : false , allowMonth : false }
    };


    self.schedules = [];
    self.schedulePromise = null;

    self.unscheduled = [];
    self.logPromise = null;

    self.currentPage = 1;
    self.paginationCount = 10;
    self.sort = "-id";
    self.scheduleTotal = 0;
    self.pageCount = 0;
    self.scheduleTotal = 0;
    self.paginationOptions = paginationService.getDefaultPaginationOptions();

    self.formSubmitted = false;
    self.addDialogTitle = 'Add New Schedule Notification';
    self.editDialogTitle = 'Edit Schedule Notification';
    self.currentDialogTitle = self.addDialogTitle;

    self.addDialogButton = 'Save Schedule Notification';
    self.editDialogButton = 'Update Schedule Notification';
    self.currentDialogButton = self.addDialogButton;

    self.levelOptions = [
        { 
            "label" : "Normal" ,
            "value" : "normal"
        } ,
        {
            "label" : "Critical" ,
            "value" : "critical"
        }
    ];
    self.contentKeys = [];
    self.emailTemplates = [];
    self.slackTemplates = [];

    self.loadSchedules = function () {
        self.schedulePromise = ScheduledNotificationApiService.getSchedules(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            function ( response ) {
                self.schedules = response.data.data ;
                self.pageCount = response.data.last_page;
                self.scheduleTotal = response.data.total;
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to load Schedules' ); }
        );
    };

    self.loadUnscheduledKeys = function () {
        self.logPromise = ScheduledNotificationApiService.getUnscheduledKeys(
            function ( response ) {
                angular.forEach( response.data , function(value, key) {
                    value.content = angular.fromJson( value.content );
                    self.unscheduled.push( value ); 
                } , self );

                $timeout( function () { 
                    $(function () { $('[data-toggle="tooltip"]').tooltip() } );
                } , 1500 );
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to load Unscheduled Log Keys.' ); }
        );
    };

    self.loadContentKeys = function () {
        ScheduledNotificationApiService.getContentKeys(
            function ( response ) {
                self.contentKeys = response.data;
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to load Log Keys.' ); }
        );
    };

    self.loadEmailTemplates = function () {
        ScheduledNotificationApiService.getEmailTemplates(
            function ( response ) {
                self.emailTemplates = response.data;
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to load Email Templates' ); }
        );
    };

    self.loadSlackTemplates = function () {
        ScheduledNotificationApiService.getSlackTemplates(
            function ( response ) {
                self.slackTemplates = response.data;
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to load Slack Templates' ); }
        );
    };

    self.saveSchedule = function  () {
        self.formSubmitted = true;

        ScheduledNotificationApiService.saveSchedule(
            self.currentSchedule ,
            function ( response ) {
                self.formSubmitted = false;
                modalService.simpleToast( 'Successfully saved Schedule' );
            } ,
            function ( response ) {
                self.formSubmitted = false;
                modalService.simpleToast( 'Failed to save Schedule' );
            }
        );
    }

    self.toggleStatus = function ( id , currentStatus ) {
        ScheduledNotificationApiService.toggleStatus(
            id ,
            currentStatus ,
            function ( response ) {
                self.loadSchedules();
                modalService.simpleToast( 'Successfully toggled status for schedule.' );
            } ,
            function ( response ) { modalService.simpleToast( 'Failed to toggle status for schedule.' ); }
        );
    };

    self.describeCron = function ( cronExpression ) {
        return cronstrue.toString( cronExpression ); 
    }

    self.showAddDialog = function ( isEdit ) {
        if ( isEdit ) {
            self.currentDialogTitle = self.editDialogTitle;
            self.currentDialogButton = self.editDialogButton;
        } else {
            self.currentDialogTitle = self.addDialogTitle;
            self.currentDialogButton = self.addDialogButton;
        }

        $mdDialog.show( { 
            contentElement: '#addScheduleModal' ,
            parent: angular.element(document.body)
        } );
    };

    self.showEditDialog = function ( listIndex ) {
        self.currentSchedule = self.schedules[ listIndex ];
        self.showAddDialog( true );
    };

    self.showAddDialogForUnscheduled = function ( contentKey ) {
        self.currentSchedule = angular.copy( self.emptySchedule );
        self.currentSchedule.content_key = contentKey;

        self.showAddDialog();
    };

    self.closeDialog = function () {
        $mdDialog.hide();
    };
} ] );
