
@extends( 'layout.default' )

@section( 'title' , 'Edit Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Edit Client</h1></div>
</div>

<div ng-controller="ClientController as client" ng-init="client.loadClient()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-2 col-lg-3">

        </div>

        <div class="col-xs-12 col-md-8 col-lg-6">
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="" aria-controls="" role="tab" data-toggle="tab">Tab 1</a></li>
                    <li role="presentation"><a href="" aria-controls="" role="tab" data-toggle="tab">Tab 2</a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="">
                        <form class="form-horizontal">
Stuff
                        </form>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="">
                        <form class="form-horizontal">
More Stuff
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
