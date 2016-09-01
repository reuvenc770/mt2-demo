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
    $breadcrumbs->push('Esp API Accounts', route('espapi.list'));
});

Breadcrumbs::register('espapi.add', function($breadcrumbs) {
    $breadcrumbs->parent('espapi.list');
    $breadcrumbs->push('Add API Account');
});

Breadcrumbs::register('espapi.edit', function($breadcrumbs) {
    $breadcrumbs->parent('espapi.list');
    $breadcrumbs->push('Edit API Account');
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

//Client Feeds
Breadcrumbs::register('clientgroup.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push("Client Groups", route('clientgroup.list'));
});
Breadcrumbs::register('clientgroup.add', function($breadcrumbs) {
    $breadcrumbs->parent('clientgroup.list');
    $breadcrumbs->push('Add Client Group');
});

Breadcrumbs::register('clientgroup.edit', function($breadcrumbs) {
    $breadcrumbs->parent('clientgroup.list');
    $breadcrumbs->push('Edit Client Group');
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

Breadcrumbs::register( 'client.attribution' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'feed.list' );
    $breadcrumbs->push( 'Attribution' );
} );

Breadcrumbs::register('datacleanse.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Data Cleanse' , route( 'datacleanse.list' ));
});

Breadcrumbs::register('datacleanse.add', function($breadcrumbs) {
    $breadcrumbs->parent('datacleanse.list');
    $breadcrumbs->push('Add Data Cleanse');
});

Breadcrumbs::register('attr.model.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Attribution Models' , route( 'attr.model.list' ));
});

Breadcrumbs::register('attr.model.add', function($breadcrumbs) {
    $breadcrumbs->parent('attr.model.list');
    $breadcrumbs->push('Add Attribution Model');
});

Breadcrumbs::register('attr.model.edit', function($breadcrumbs) {
    $breadcrumbs->parent('attr.model.list');
    $breadcrumbs->push('Edit Attribution Model');
});

Breadcrumbs::register('report.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Reporting');
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

Breadcrumbs::register('mailingtemplate.list', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Mailing Templates', route('mailingtemplate.list'));
});

Breadcrumbs::register('mailingtemplate.add', function($breadcrumbs) {
    $breadcrumbs->parent('mailingtemplate.list');
    $breadcrumbs->push('Add Mailing Template');
});

Breadcrumbs::register( 'mailingtemplate.edit' , function ( $breadcrumbs ) {
    $breadcrumbs->parent( 'mailingtemplates.list' );
    $breadcrumbs->push( 'Edit Mailing Template' );
} );
