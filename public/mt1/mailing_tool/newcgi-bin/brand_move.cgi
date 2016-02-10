#!/usr/bin/perl
# *****************************************************************************************
# brand_move.cgi
#
# this page moves a brand 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $newclient= $query->param('newclient');
my $bid= $query->param('bid');

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql = "update client_brand_info set client_id=$newclient where brand_id=$bid";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating client_brand_info $sql: $errmsg");
	exit(0);
}
#
print "Location: edit_client_brand.cgi?bid=$bid&cid=$newclient&mode=U\n\n";

# exit function

$util->clean_up();
exit(0);
