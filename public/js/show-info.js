mt2App.controller( 'ShowinfoController' , [ '$log' , '$http' , function ( $log , $http ) {
    var self = this;

    self.isLoaded = false;
    self.recordId = null;
    self.record = {};
    self.suppressionReasons = [];
    self.selectedReason = '';

    self.loadMockData = function ( $event ) {
        $event.preventDefault();

        self.record = {
            email : "test@test.com" ,
            firstname : "ckmtest" ,
            lastname : "ckmtest" ,
            address : "1 Main St" ,
            source : "yahoo.com" ,
            ip : "1.1.1.1" ,
            date : "2015-01-10" ,
            birthday : "1990-04-15" ,
            gender : "Male" ,
            listname : "zxAdt" ,
            network : "" ,
            action : "" ,
            actiondate : "" ,
            subscribedata : "" ,
            status : "" ,
            archived : "No" ,
            removaldate : ""
        };

        self.isLoaded = true;
    };

    self.loadReasons = function () {
        self.suppressionReasons = [
            { "name" : "Value 1" , "value" : "1" } ,
            { "name" : "Value 2" , "value" : "2" } ,
            { "name" : "Value 3" , "value" : "3" }
        ];
    };

    self.addToSuppression = function ( $event ) {
        $event.preventDefault();

        window.alert( 'Suppressed!!' );
    }
} ] );

//# sourceMappingURL=show-info.js.map
