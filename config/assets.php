<?php
/**
 * laravel-assets: asset management for Laravel 5
 *
 * Copyright (C) 2016 Greg Roach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

return [
    // --------------------------------------------------------------------------
    // Enable the pipeline and filter functions.
    // --------------------------------------------------------------------------
    // You may want to disable this for development, to make debugging easier.
    'enabled' => true,

    // --------------------------------------------------------------------------
    // Where do we find the application's original assets?
    // --------------------------------------------------------------------------
    // This is a relative path from the root of the public folder.
    'css_source' => 'css',
    'js_source' => '/',

    // --------------------------------------------------------------------------
    // Where do we store our generated (e.g. filtered and pipelined) assets?
    // --------------------------------------------------------------------------
    // This is a relative path from the root of the public folder.
    // It is also used as the default URL when generating links to assets.
    // IMPORTANT - you must have write permission to this folder.
    'destination' => 'js/min',

    // --------------------------------------------------------------------------
    // Absolute URL to our generated (e.g. filtered and pipelined) assets?
    // --------------------------------------------------------------------------
    // If you want to fetch assets from a cookie-free domain or a CDN, specify
    // specify the URL here.  e.g. "http://some-other-domain.com/min".
    // If left blank, a URL will be generated using the "destination" setting.
    'destination_url' => '',

    // --------------------------------------------------------------------------
    // What should we do after we create an asset file?
    // --------------------------------------------------------------------------
    // You could use this opportunity to upload the asset file to your CDN.
    // Notifiers must be instances of Fisharebest\LaravelAssets\NotifierInterface
    'notifiers' => [],

    // --------------------------------------------------------------------------
    // Generate compressed versions of generated assets?
    // --------------------------------------------------------------------------
    // If set to an integer between 1 (fast) and 9 (best), a compressed ".gz"
    // version of each file will be generated.  6 is a good value to use.
    // See http://nginx.org/en/docs/http/ngx_http_gzip_static_module.html
    'gzip_static' => 0,

    // --------------------------------------------------------------------------
    // Should we inline small asset files?
    // --------------------------------------------------------------------------
    // Asset files smaller than this number of bytes will be rendered inline,
    // instead of being fetched in a separate HTTP request.
    // Typical values might be between 1024 and 2048.
    'inline_threshold' => 0,

    // --------------------------------------------------------------------------
    // A list of predefined resources.
    // --------------------------------------------------------------------------
    // Predefined groups of resources.  Note that the order of files determines
    // the order in which they are loaded.  Hence each collection should specify
    // its dependencies before its own files.
    'collections' => [
        'base' => [
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/angular/angular.min.js',
            'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
            'node_modules/jasny-bootstrap/dist/js/jasny-bootstrap.min.js',
            'node_modules/angular-material/angular-material.min.js',
            'node_modules/angular-aria/angular-aria.min.js',
            'node_modules/angular-animate/angular-animate.min.js',
            'node_modules/angular-messages/angular-messages.min.js',
            'node_modules/clipboard/dist/clipboard.min.js',
            'node_modules/ngclipboard/dist/ngclipboard.min.js',
            'node_modules/moment/min/moment-with-locales.min.js',
            'node_modules/ui-select/dist/select.min.js',
            'node_modules/ng-flow/dist/ng-flow-standalone.min.js',
            'node_modules/angular-ivh-treeview/dist/ivh-treeview.min.js',
            'node_modules/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js',
            'node_modules/angular-material-data-table/dist/md-data-table.min.js',
            'node_modules/angu-complete/angucomplete-alt.js',
            'node_modules/angular-cookies/angular-cookies.min.js',
            'node_modules/headroom.js/dist/headroom.js',
            'node_modules/headroom.js/dist/angular.headroom.js',

        ],
        'mt2AppBase' => [
            'resources/assets/js/app.js',
            'resources/assets/js/mt2app/AppController.js',
            'resources/assets/js/mt2app/GenericTableDirective.js',
            'resources/assets/js/mt2app/EditButtonDirective.js',
            'resources/assets/js/mt2app/PaginationDirective.js',
            'resources/assets/js/mt2app/PaginationControlDirective.js',
            'resources/assets/js/mt2app/PaginationButtonDirective.js',
            'resources/assets/js/mt2app/PaginationCountDirective.js',
            'resources/assets/js/mt2app/MembershipWidgetDirective.js',
            'resources/assets/js/mt2app/LiteMembershipWidgetDirective.js',
            'resources/assets/js/mt2app/CompileHtml.js',
            'resources/assets/js/mt2app/CustomValidationService.js'
        ],
        'mt2BootstrapBase' => [
            'resources/assets/js/bootstrap/app.js',
            'resources/assets/js/bootstrap/mt2app/AppController.js',
            'resources/assets/js/bootstrap/mt2app/GenericTableDirective.js',
            'resources/assets/js/bootstrap/mt2app/EditButtonDirective.js',
            'resources/assets/js/bootstrap/mt2app/PaginationDirective.js',
            'resources/assets/js/bootstrap/mt2app/PaginationControlDirective.js',
            'resources/assets/js/bootstrap/mt2app/PaginationButtonDirective.js',
            'resources/assets/js/bootstrap/mt2app/PaginationCountDirective.js',
            'resources/assets/js/bootstrap/mt2app/MembershipWidgetDirective.js',
            'resources/assets/js/bootstrap/mt2app/LiteMembershipWidgetDirective.js',
            'resources/assets/js/bootstrap/mt2app/ValidationService.js',
            'resources/assets/js/bootstrap/mt2app/ModalService.js',
            'resources/assets/js/bootstrap/mt2app/CompileHtml.js',
            'resources/assets/js/bootstrap/mt2app/CustomValidationService.js',
            'resources/assets/js/bootstrap/mt2app/FixedTableHeader.js',
        ]
    ]
];
