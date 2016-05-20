mt2App.directive( 'copyexportButton' , function () {
  return {
      "scope" : {} ,
      "controller" : function () {} ,
      "controllerAs" : "ctrl" ,
      "bindToController" : {
        "recordid": "=",
        "copyexport": "&"
    } ,
    "templateUrl" : "js/templates/copyexport-button.html"
  };
});