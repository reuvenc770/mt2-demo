mt2App.controller( 'AttributionProjectionController' , [ 'AttributionApiService' , 'FeedApiService' , '$mdToast' , '$location' , function ( AttributionApiService , FeedApiService , $mdToast , $location ) {
    var self = this;

    self.modelId = 0;
    self.records = [];
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
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load chart data. Please contact support.' )
                        .hideDelay( 1500 )
                );
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
            legend: { position: 'top' }
        };

        var barChartDiff = new google.visualization.BarChart( document.getElementById( 'projectionChart' ) );

        var diffData = barChartDiff.computeDiff(
            google.visualization.arrayToDataTable( self.chartData.live ) ,
            google.visualization.arrayToDataTable( self.chartData.model ) 
        );

        barChartDiff.draw( diffData , options );
    };

    self.loadRecords = function () {
        AttributionApiService.getProjectionRecords(
            self.modelId ,
            function ( response ) {
                self.records = response.data;
            } ,
            function ( response ) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent( 'Failed to load table data. Please contact support.' )
                        .hideDelay( 1500 )
                );
            }
        );
    };
} ] );
