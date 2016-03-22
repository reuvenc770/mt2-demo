var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('app.scss');

    mix.copy( 'node_modules/bootstrap-sass/assets/fonts/' , 'public/fonts' );

    mix.copy( 'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js' , 'public/js/bootstrap.min.js' );

    mix.copy( 'node_modules/jasny-bootstrap/dist/js/jasny-bootstrap.min.js' , 'public/js/jasny-bootstrap.min.js' );

    mix.copy( 'node_modules/angular/angular.min.js' , 'public/js/angular.min.js' );

    mix.copy( 'node_modules/jquery/dist/jquery.min.js' , 'public/js/jquery.min.js' );

    mix.copy( 'node_modules/angular-material/angular-material.min.js' , 'public/js/angular-material.min.js' );

    mix.copy( 'node_modules/angular-aria/angular-aria.min.js' , 'public/js/angular-aria.min.js' );

    mix.copy( 'node_modules/angular-animate/angular-animate.min.js' , 'public/js/angular-animate.min.js' );

    mix.copy( 'node_modules/angular-messages/angular-messages.min.js' , 'public/js/angular-messages.min.js' );

    mix.copy( 'node_modules/ngclipboard/dist/ngclipboard.min.js' , 'public/js/ngclipboard.min.js' );
    mix.copy( 'node_modules/clipboard/dist/clipboard.min.js' , 'public/js/clipboard.min.js' );

    mix.copy( 'node_modules/moment/min/moment-with-locales.min.js' , 'public/js/momment-with-locales.min.js' );

    mix.copy( 'node_modules/ui-select/dist/select.min.js' , 'public/js/select.min.js' );

    mix.copy( 'node_modules/ui-select/dist/select.min.css' , 'public/css/select.min.css' );

    mix.scripts( [
        'app.js' ,
        'mt2app/GenericTableDirective.js' ,
        'mt2app/EditButtonDirective.js' ,
        'mt2app/PaginationDirective.js' ,
        'mt2app/PaginationControlDirective.js' ,
        'mt2app/PaginationButtonDirective.js' ,
        'mt2app/PaginationCountDirective.js',
        'mt2app/CompileHtml.js'
    ] , 'public/js/angular_base.js' );

    mix.scripts( [ 'espapi/EspController.js' , 'espapi/EspApiService.js' , 'espapi/EspapiTableDirective.js' ] , 'public/js/espapi.js' );
    mix.scripts( [ 'user/UserController.js' , 'user/UserApiService.js' ] , 'public/js/user.js' );
    mix.scripts( [ 'role/RoleController.js' , 'role/RoleApiService.js' ] , 'public/js/role.js' );
    mix.scripts( [ 'job/JobController.js' , 'job/JobApiService.js' ] , 'public/js/job.js' );
    mix.scripts( [ 'pages/ShowinfoController.js' , 'pages/ShowinfoApiService.js' ] , 'public/js/show-info.js' );
    mix.scripts( [  'wizard/WizardController.js' , 'wizard/WizardApiService.js' ] , 'public/js/wizard.js' );
    mix.scripts( [  'ymlpmanager/YmlpCampaignController.js' , 'ymlpmanager/YmlpCampaignApiService.js', 'ymlpmanager/YmlpCampaignTableDirective.js' ] , 'public/js/ymlpcampaign.js' );

    mix.scripts( [
        'client/ClientController.js' ,
        'client/ClientApiService.js' ,
        'client/ClientTableDirective.js' ,
        'client/ClientUrlModalDirective.js'
    ] , 'public/js/client.js' );

    mix.scripts( [
        'clientgroup/ClientGroupController.js' ,
        'clientgroup/ClientGroupApiService.js' ,
        'client/ClientApiService.js' ,
        'clientgroup/ClientGroupTableDirective.js' ,
        'clientgroup/ClientGroupChildrenTableDirective.js'
    ] , 'public/js/clientgroup.js' );

    mix.scripts([
        'dataexport/DataExportController.js',
        'dataexport/DataExportApiService.js',
        'dataexport/DataExportTableDirective.js',
	'dataexport/StatusButtonDirective.js',
	'dataexport/DataExportDeleteDirective.js',
	'dataexport/DataExportCopyDirective.js'
    ], 'public/js/dataexport.js');
    mix.scripts( [
        'listprofile/ListProfileController.js' ,
        'listprofile/ListProfileApiService.js' ,
        'listprofile/ListProfileTableDirective.js' ,
        'clientgroup/ClientGroupApiService.js' ,
        'client/ClientApiService.js'
    ] , 'public/js/listprofile.js' );

    mix.copy( 'resources/assets/js/templates' , 'public/js/templates' );

    mix.phpUnit();
});
