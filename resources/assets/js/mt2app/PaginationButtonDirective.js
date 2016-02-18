mt2App.directive( 'paginationButton' , function ( $log , $rootScope ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'pagenumber' : '='
        } ,
        "templateUrl" : "js/templates/pagination-button.html"
    };
} );
