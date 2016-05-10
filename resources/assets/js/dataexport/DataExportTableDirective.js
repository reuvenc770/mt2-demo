mt2App.directive( 'dataexportTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "records" : "=" ,
            "loadingflag" : "=",
            "toggleinclusion": "&",
            "changestatus": "&",
            "statuschangebuttontext": "=",
            "deleteexport": "&",
            "copyexport": "&"
        } ,
        "templateUrl" : "js/templates/dataexport-table.html"
    };
} );