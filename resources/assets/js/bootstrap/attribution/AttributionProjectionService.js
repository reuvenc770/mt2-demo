mt2App.service( 'AttributionProjectionService' , [ 'AttributionApiService' , 'modalService' , '$location' , function ( AttributionApiService , modalService , $location ) {
    var self = this;

    self.modelId = 0;
    self.chartData = [];

    self.initPage = function () {
        self.setModelIdFromPath();
    };

    self.refreshPage = function () {
        self.getChartData();

        self.loadRecords();
    };

    self.setModelIdFromPath = function () {
        var path = $location.path();
        var idMatches = path.match( /\d+$/ );

        if ( null != idMatches ) {
            self.modelId = parseInt( idMatches[ 0 ] );
        }
    };

    self.initChart = function () {
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback( self.getChartData );
    };

    self.getChartData = function () {
        AttributionApiService.getProjectionChartData(
            self.modelId ,
            function ( response ) {
                self.chartData = response.data;
                self.drawChart();
            } ,
            function ( response ) {
                modalService.simpleToast( 'Failed to load chart data. Please contact support.' );
            }
        );
    };

    self.drawChart = function () {
        var options = {
            diff: {
                newData: {
                    widthFactor: 0.3 ,
                    opacity: 1 ,
                    color: '#4285F4'

                }
            } ,
            legend: { position: 'top' } ,
            height: 3000 ,
            width: 1200,
            chartArea : { left : "10%" , top : "5%" , width : "90%" , height : "90%" }
        };

        var barChartDiff = new google.visualization.BarChart( document.getElementById( 'projectionChart' ) );

        var diffData = barChartDiff.computeDiff(
            google.visualization.arrayToDataTable( self.chartData.live ) ,
            google.visualization.arrayToDataTable( self.chartData.model )
        );

        barChartDiff.draw( diffData , options );
    };

    self.loadRecords = function ( successCallback , failureCallback ) {
        return AttributionApiService.getProjectionRecords(
            self.modelId ,
            successCallback ,
            failureCallback
        );
    };
} ] );
