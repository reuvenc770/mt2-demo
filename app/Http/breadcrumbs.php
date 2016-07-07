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
    $breadcrumbs->parent( 'client.list' );
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
    $breadcrumbs->parent( 'client.list' );
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
