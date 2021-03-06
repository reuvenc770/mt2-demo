<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/29/16
 * Time: 11:24 AM
 */

Breadcrumbs::register('home', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});
//User
Breadcrumbs::register('user.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Users', route('user.list'));
});

Breadcrumbs::register('user.add', function($breadcrumbs) {
    $breadcrumbs->parent('user.list');
    $breadcrumbs->push('Add User');
});

Breadcrumbs::register('user.edit', function($breadcrumbs) {
    $breadcrumbs->parent('user.list');
    $breadcrumbs->push('Edit User');
});

//ESP API
Breadcrumbs::register('espapi.list', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
    $breadcrumbs->push('ESP API Accounts', route('espapi.list'));
});

Breadcrumbs::register('espapi.add', function($breadcrumbs) {
    $breadcrumbs->parent('espapi.list');
    $breadcrumbs->push('Add ESP API Account');
});

Breadcrumbs::register('espapi.edit', function($breadcrumbs) {
    $breadcrumbs->parent('espapi.list');
    $breadcrumbs->push('Edit ESP API Account');
});

//ESP
Breadcrumbs::register('esp.list', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
    $breadcrumbs->push('ESP Accounts', route('esp.list'));
});

Breadcrumbs::register('esp.add', function($breadcrumbs) {
    $breadcrumbs->parent('esp.list');
    $breadcrumbs->push('Add ESP Account');
});

Breadcrumbs::register('esp.edit', function($breadcrumbs) {
    $breadcrumbs->parent('esp.list');
    $breadcrumbs->push('Edit ESP Account');
});

Breadcrumbs::register('esp.mapping', function($breadcrumbs) {
    $breadcrumbs->parent('esp.list');
    $breadcrumbs->push('Configure ESP Fields Mapping');
});

//Roles
Breadcrumbs::register('role.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push("Roles", route('role.list'));
});
Breadcrumbs::register('role.add', function($breadcrumbs) {
    $breadcrumbs->parent('role.list');
    $breadcrumbs->push('Add Role');
});

Breadcrumbs::register('role.edit', function($breadcrumbs) {
    $breadcrumbs->parent('role.list');
    $breadcrumbs->push('Edit Role');
});

//Client Feeds
Breadcrumbs::register('feed.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push("Feeds", route('feed.list'));
});
Breadcrumbs::register('feed.add', function($breadcrumbs) {
    $breadcrumbs->parent('feed.list');
    $breadcrumbs->push('Add Feed');
});

Breadcrumbs::register('feed.edit', function($breadcrumbs) {
    $breadcrumbs->parent('feed.list');
    $breadcrumbs->push('Edit Feed');
});

Breadcrumbs::register('feed.file.fieldorder', function($breadcrumbs) {
    $breadcrumbs->parent('feed.list');
    $breadcrumbs->push('Edit File Drop Field Order');
});

//Client Feeds
Breadcrumbs::register('feedgroup.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push("Feed Groups", route('feedgroup.list'));
});
Breadcrumbs::register('feedgroup.add', function($breadcrumbs) {
    $breadcrumbs->parent('feedgroup.list');
    $breadcrumbs->push('Add Feed Group');
});

Breadcrumbs::register('feedgroup.edit', function($breadcrumbs) {
    $breadcrumbs->parent('feedgroup.list');
    $breadcrumbs->push('Edit Feed Group');
});

//Clients
Breadcrumbs::register('client.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push("Clients", route('client.list'));
});
Breadcrumbs::register('client.add', function($breadcrumbs) {
    $breadcrumbs->parent('client.list');
    $breadcrumbs->push('Add Client');
});

Breadcrumbs::register('client.edit', function($breadcrumbs) {
    $breadcrumbs->parent('client.list');
    $breadcrumbs->push('Edit Client');
});

//List Profiles
Breadcrumbs::register( 'listprofile.list' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'home' );
    $breadcrumbs->push( 'List Profiles' , route( 'listprofile.list' ) );
} );

Breadcrumbs::register( 'listprofile.add' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'listprofile.list' );
    $breadcrumbs->push( 'Add List Profile' , route( 'listprofile.add' ) );
} );

Breadcrumbs::register( 'listprofile.edit' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'listprofile.list' );
    $breadcrumbs->push( 'Edit List Profile' );
} );

Breadcrumbs::register( 'listprofile.combine.edit' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'listprofile.list' );
    $breadcrumbs->push( 'Edit List Combine' );
} );

//YMLP Mapping

Breadcrumbs::register( 'ymlpcampaign.list' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'home' );
    $breadcrumbs->push( 'YMLP Campaigns' , route( 'ymlpcampaign.list' ) );
} );

Breadcrumbs::register( 'ymlpcampaign.add' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'ymlpcampaign.list' );
    $breadcrumbs->push( 'Add YMLP Campaign' , route( 'ymlpcampaign.add' ) );
} );

Breadcrumbs::register( 'ymlpcampaign.edit' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'ymlpcampaign.list' );
    $breadcrumbs->push( 'Edit YMLP Campaign' );
} );

// Single Pages
Breadcrumbs::register('devtools.jobs', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Job Status Tracker');
});

Breadcrumbs::register('tools.recordlookup', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Record Lookup' , route( 'tools.recordlookup' ) );
});

Breadcrumbs::register( 'client.attribution' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'feed.list' );
    $breadcrumbs->push( 'Attribution' );
} );

Breadcrumbs::register( 'tools.sourceurlsearch' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'home' );
    $breadcrumbs->push( 'Source URL Search' , route( 'tools.sourceurlsearch' ) );
} );

Breadcrumbs::register( 'tools.seed' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'home' );
    $breadcrumbs->push( 'Seed List' , route( 'tools.seed' ) );
} );

// Data Export Pages
Breadcrumbs::register('dataexport.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Data Exports' , route( 'dataexport.list' ) );
});

Breadcrumbs::register('dataexport.add', function($breadcrumbs) {
    $breadcrumbs->parent('dataexport.list');
    $breadcrumbs->push('Add Data Export');
});

Breadcrumbs::register('dataexport.edit', function($breadcrumbs) {
    $breadcrumbs->parent('dataexport.list');
    $breadcrumbs->push('Edit Data Export');
});

Breadcrumbs::register( 'tools.bulksuppression' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'home' );
    $breadcrumbs->push( 'Bulk Suppression' , route( 'tools.bulksuppression' ) );
} );

Breadcrumbs::register( 'tools.aweberlists' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'home' );
    $breadcrumbs->push( 'AWeber List Status' , route( 'tools.aweberlists' ) );
} );

Breadcrumbs::register('datacleanse.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Data Cleanse' , route( 'datacleanse.list' ));
});

Breadcrumbs::register('datacleanse.add', function($breadcrumbs) {
    $breadcrumbs->parent('datacleanse.list');
    $breadcrumbs->push('Add Data Cleanse');
});

Breadcrumbs::register('attribution.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Attribution' , route( 'attribution.list' ));
});

Breadcrumbs::register('attributionModel.add', function($breadcrumbs) {
    $breadcrumbs->parent('attribution.list');
    $breadcrumbs->push('Add Attribution Model');
});

Breadcrumbs::register('attributionModel.edit', function($breadcrumbs) {
    $breadcrumbs->parent('attribution.list');
    $breadcrumbs->push('Edit Attribution Model');
});

Breadcrumbs::register('attributionProjection.show', function($breadcrumbs) {
    $breadcrumbs->parent('attribution.list');
    $breadcrumbs->push('Projection');
});

Breadcrumbs::register('proxy.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Proxies', route('proxy.list'));
});

Breadcrumbs::register('proxy.add', function($breadcrumbs) {
    $breadcrumbs->parent('proxy.list');
    $breadcrumbs->push('Add Proxy');
});


Breadcrumbs::register('proxy.edit', function($breadcrumbs) {
    $breadcrumbs->parent('proxy.list');
    $breadcrumbs->push('Edit Proxy');
});

Breadcrumbs::register('registrar.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Registrars', route('registrar.list'));
});

Breadcrumbs::register('registrar.add', function($breadcrumbs) {
    $breadcrumbs->parent('registrar.list');
    $breadcrumbs->push('Add Registrar');
});

Breadcrumbs::register('registrar.edit', function($breadcrumbs) {
    $breadcrumbs->parent('registrar.list');
    $breadcrumbs->push('Edit Registrar');
});

Breadcrumbs::register('dba.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('DBAs', route('dba.list'));
});

Breadcrumbs::register('dba.add', function($breadcrumbs) {
    $breadcrumbs->parent('dba.list');
    $breadcrumbs->push('Add DBA');
});
Breadcrumbs::register('dba.edit', function($breadcrumbs) {
    $breadcrumbs->parent('dba.list');
    $breadcrumbs->push('Edit DBA');
});


Breadcrumbs::register('domain.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Domains', route('domain.list'));
});

Breadcrumbs::register('domain.add', function($breadcrumbs) {
    $breadcrumbs->parent('domain.list');
    $breadcrumbs->push('Add Domain');
});

Breadcrumbs::register('domain.listview', function($breadcrumbs) {
    $breadcrumbs->parent('domain.list');
    $breadcrumbs->push('View Domains');
});

Breadcrumbs::register('domain.search', function($breadcrumbs) {
    $breadcrumbs->parent('domain.list');
    $breadcrumbs->push('Domain Search Results');
});

Breadcrumbs::register('mailingtemplate.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Mailing Templates', route('mailingtemplate.list'));
});

Breadcrumbs::register('mailingtemplate.add', function($breadcrumbs) {
    $breadcrumbs->parent('mailingtemplate.list');
    $breadcrumbs->push('Add Mailing Template');
});

Breadcrumbs::register( 'mailingtemplate.edit' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'mailingtemplate.list' );
    $breadcrumbs->push( 'Edit Mailing Template' );
} );

Breadcrumbs::register('deploy.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Deploy Package', route('deploy.list'));
});

Breadcrumbs::register('ispgroup.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('ISP Groups', route('ispgroup.list'));
});
Breadcrumbs::register('ispgroup.add', function($breadcrumbs) {
    $breadcrumbs->parent('ispgroup.list');
    $breadcrumbs->push('Add ISP Group');
});
Breadcrumbs::register('ispgroup.edit', function($breadcrumbs) {
    $breadcrumbs->parent('ispgroup.list');
    $breadcrumbs->push('Edit ISP Group');
});

Breadcrumbs::register('isp.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('ISP Domains', route('isp.list'));
});
Breadcrumbs::register('isp.add', function($breadcrumbs) {
    $breadcrumbs->parent('isp.list');
    $breadcrumbs->push('Add ISP Domain');
});
Breadcrumbs::register('isp.edit', function($breadcrumbs) {
    $breadcrumbs->parent('isp.list');
    $breadcrumbs->push('Edit ISP Domain');
});


Breadcrumbs::register('report.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Reports', route('report.list'));
});

Breadcrumbs::register('tools.notifications', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Scheduled Notifications', route('tools.notifications'));
});

Breadcrumbs::register('tools.affiliates', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Cake Affiliates', route('tools.affiliates'));
});

Breadcrumbs::register('cpm.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('CPM Pricing', route('cpm.list'));
});
