mt2App.controller( 'ScheduledNotificationController' , [ 'ScheduledNotificationApiService' , 'paginationService' , function ( ScheduledNotificationApiService , paginationService ) {
    var self = this;

    self.schedules = [];

    self.currentPage = 1;
    self.paginationCount = 20;
    self.sort = "-id";
    self.scheduleTotal = 0;
    self.pageCount = 0;
    self.scheduleTotal = 0;
    self.paginationOptions = paginationService.getDefaultPaginationOptions();

    self.loadSchedules = function () {
        ScheduledNotificationApiService.getSchedules(
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
} ] );
