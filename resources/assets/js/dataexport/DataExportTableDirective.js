mt2App.directive( 'dataexportTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggleinclusion": "&"
        } ,
        "templateUrl" : "js/templates/dataexport-table.html"
    };
} );