@extends( 'layout.default' )
@section('title', 'Edit Mailing Template')

@section('content')
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default" ng-controller="MailingTemplateController as mailing" ng-init="mailing.loadAccount()">
            <div class="panel-heading">
                <h1 class="panel-title">Edit Mailing Template</h1>
            </div>
            <div class="panel-body">
                <fieldset>

                    @include( 'pages.mailingtemplate.mailingtemplate-form' )
                    <!-- Submit field -->
                    <div class="form-group">
                        <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.editAccount()" type="submit" value="Edit Mailing Template">
                    </div>
                    <div class="form-group">
                        <input class="btn btn-lg btn-primary btn-block" ng-click="mailing.preview()" type="submit" value="Preview Template">
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
@endsection


@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
