mt2App.directive( 'paginationCount' , [ '$rootScope' , '$timeout' , function ( $rootScope , $timeout ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {
            var self = this;

            self.updateRecordCount = function () {
                self.currentpage = 1;

                $timeout( function () {
                    $rootScope.$emit( 'updatePage' );
                    $rootScope.$emit( 'resetPaginationPager' );
                } , 200 );
            }
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'recordcount' : '=' ,
            'currentpage' : '='
        } ,
        "templateUrl" : "js/templates/pagination-count.html"
    };
} ] );
