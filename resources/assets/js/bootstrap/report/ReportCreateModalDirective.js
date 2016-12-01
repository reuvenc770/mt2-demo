mt2App.directive( 'ampReportCreate' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "reportName" : "=",
            "reportId" : "=",
            "createReport" : "&",
            "reportSaving" : "=",
            "formType" : "=",
            "reportError" : "="
        } ,
        "templateUrl" : "js/bootstrap/templates/ampreport-create-modal.html"
    };
} );
