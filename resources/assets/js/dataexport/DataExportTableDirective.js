mt2App.directive( 'dataexportTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "="
        } ,
        "templateUrl" : "js/templates/dataexport-table.html"
    };
} );