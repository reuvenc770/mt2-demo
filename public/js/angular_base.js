/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [] );

mt2App.config( function ( $locationProvider ) {
    $locationProvider.html5Mode( true );
} );

mt2App.directive( 'genericTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" , 
        "bindToController" : { 
            "headers" : "=" ,
            "records" : "=" ,
            "editurl" : "="
        } ,
        "templateUrl" : "js/templates/generic-table.html"
    };
} );

mt2App.directive( 'editButton' , [ '$window' , '$location' , function ( $window , $location ) {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "editurl" : "=" ,
            "recordid" : "="
        } ,
        "templateUrl" : "js/templates/edit-button.html" ,
        "link" : function ( scope , element , attrs )  {
            if ( typeof( scope.ctrl ) != 'undefined' ) {
                element.on( 'click' , function () {
                    var fullEditUrl = scope.ctrl.editurl + scope.ctrl.recordid;
                    $location.url( fullEditUrl );
                    $window.location.href = fullEditUrl;
                } );
            }
        }
    };
} ] );

//# sourceMappingURL=angular_base.js.map
