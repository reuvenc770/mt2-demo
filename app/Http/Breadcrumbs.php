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

// Single Pages
Breadcrumbs::register('devtools.jobs', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Job Status Tracker');
});

Breadcrumbs::register('tools.recordlookup', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Record Lookup');
});