mt2App.service( 'ScheduledNotificationApiService' , [ '$http' , 'paginationService' ,  function ( $http , paginationService ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/NotificationSchedule';
    self.baseApiUrl = 'api/notifications';

    self.getSchedules = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , 'sort' : sort }
        } ).then( successCallback , failureCallback );
    };

    self.saveSchedule = function ( schedule , successCallback , failureCallback ) {
        var requestObj = {
            "method" : ( schedule.id ? "PUT" : "POST" ) ,
            "url" : self.baseApiUrl + ( schedule.id ? '/' + schedule.id : '' )
        };

        if ( schedule.id ) {
            requestObj.params = { "_method" : "PUT" };
            requestObj.data = schedule;
        } else {
            requestObj.params = { "data" : schedule };
        }

        return $http( requestObj ).then( successCallback , failureCallback );
    };

    self.getUnscheduledKeys = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" , 
            "url" : self.baseApiUrl + '/unscheduled'
        } ).then( successCallback , failureCallback );
    };

    self.getContentKeys = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/contentkey'
        } ).then( successCallback , failureCallback );
    };

    self.getEmailTemplates = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/emailtemplates'
        } ).then( successCallback , failureCallback );
    };

    self.getSlackTemplates = function ( successCallback , failureCallback ) {
        return $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/slacktemplates'
        } ).then( successCallback , failureCallback );
    };

    self.toggleStatus = function ( id , currentStatus , successCallback , failureCallback ) {
        return $http( {
            "method" : "DELETE" ,
            "url" : self.baseApiUrl + '/' + id ,
            "params" : { "currentStatus" : currentStatus }
        } ).then( successCallback , failureCallback );
    };
} ] );
