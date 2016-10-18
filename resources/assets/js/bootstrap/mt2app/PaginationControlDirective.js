mt2App.directive( 'paginationControl' ,  function ( $log ) {
    return {
        "replace" : true ,
        "scope" : {} ,
        "controller" : function () {
            var self = this;
        } ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            'mode' : '@'
        } ,
        "templateUrl" : "js/templates/pagination-control.html"
    };
} );
