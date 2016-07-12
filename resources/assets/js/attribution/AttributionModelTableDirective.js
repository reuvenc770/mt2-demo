mt2App.directive( 'attributionModelTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=" ,
            "baseurl" : "&" ,
            "copymodel" : "&"
        } ,
        "templateUrl" : "js/templates/attribution-model-table.html"
    };
} );
