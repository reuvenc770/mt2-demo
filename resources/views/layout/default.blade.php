<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App">
        @include( 'layout.header' )

        @include( 'layout.top-nav' )

        <div id="pageContent" class="container-fluid">
            @include('flash::message')

            <ol class="breadcrumb">
              <li><a href="#">Home</a></li>
              <li><a href="#">ESP</a></li>
              <li class="active">Accounts</li>
            </ol>

            @yield( 'content' )
        </div>

        <div class="modal fade" id="pageModal" tabindex="-1" role="dialog" aria-labelledby="pageModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="pageModalLabel"></h4>
                    </div>

                    <div class="modal-body" id="pageModalBody"></div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @include( 'layout.body-footer' )

        @yield( 'pageIncludes' )

    <script>
        $(function () {
            $('#adminNav').offcanvas( {
                "toggle" : "offcanvas" ,
                "target" : "#adminNav" ,
                "canvas" : "body" ,
                "autohide" : true 
            } ).offcanvas( 'show' );

            $( '#espDropdown' ).dropdown( 'toggle' );
        });
    </script>

    </body>
</html>
