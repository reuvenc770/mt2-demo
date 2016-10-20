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
