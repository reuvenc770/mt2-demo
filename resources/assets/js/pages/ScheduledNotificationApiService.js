mt2App.service( 'ScheduledNotificationApiService' , [ '$http' ,  function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/NotificationSchedule';
    self.baseApiUrl = 'api/notifications';

    self.getSchedules = function ( page , count , sortField , successCallback , failureCallback ) {
        //var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count } //, 'sort' : sort }
        } ).then( successCallback , failureCallback );
    };
} ] );
