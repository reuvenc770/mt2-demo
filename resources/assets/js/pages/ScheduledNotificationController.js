mt2App.controller( 'ScheduledNotificationController' , [ 'ScheduledNotificationApiService' , 'paginationService' , '$timeout' , '$mdDialog' , function ( ScheduledNotificationApiService , paginationService , $timeout , $mdDialog ) {
    var self = this;

    self.currentSchedule = {
        "title" : "Sample Title"
    };
    self.schedules = [];
    self.schedulePromise = null;

    self.unscheduled = [];
    self.logPromise = null;

    self.currentPage = 1;
    self.paginationCount = 20;
    self.sort = "-id";
    self.scheduleTotal = 0;
    self.pageCount = 0;
    self.scheduleTotal = 0;
    self.paginationOptions = paginationService.getDefaultPaginationOptions();

    self.loadSchedules = function () {
        self.schedulePromise = ScheduledNotificationApiService.getSchedules(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            function ( res ) {
                self.schedules = res.data.data ;
                self.pageCount = res.data.last_page;
                self.scheduleTotal = res.data.total;
            } ,
            function ( res ) {
            
            }
        );
    };

    self.loadUnscheduledKeys = function () {
        self.logPromise = ScheduledNotificationApiService.getUnscheduledKeys(
            function ( res ) {
                angular.forEach( res.data , function(value, key) {
                    value.content = angular.fromJson( value.content );
                    self.unscheduled.push( value ); 
                } , self );

                $timeout( function () { 
                    $(function () { $('[data-toggle="tooltip"]').tooltip() } );
                } , 1500 );
            } ,
            function ( res ) {
            
            }
        );
    };

    self.describeCron = function ( cronExpression ) {
        return cronstrue.toString( cronExpression ); 
    }

    self.showAddDialog = function () {
        $mdDialog.show( { 
            contentElement: '#addScheduleModal' ,
            parent: angular.element(document.body)
        } );
    };

} ] );
