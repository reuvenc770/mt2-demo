mt2App.directive( 'espapiTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/espmapping-table.html"
    };
} );
