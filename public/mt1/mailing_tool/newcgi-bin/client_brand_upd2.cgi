#!/usr/bin/perl

# *****************************************************************************************
# client_brand_upd.cgi
#
# this page updates information for a client brand 
#
# History
# JES	11/01/06	Added logic for Newsletter Brands
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $cid = $query->param('clientID');
my $bid = $query->param('brandID');
my $tag_to= $query->param('tag_to');

if ($tag_to>0) {
	my $quer=qq|UPDATE client_brand_info SET tag=$tag_to WHERE client_id=$cid AND brand_id=$bid|;
	$dbhu->do($quer);
	print "Location: client_list.cgi\n\n";
}
else {
	print "Location: client_list.cgi\n\n";
}
exit;
