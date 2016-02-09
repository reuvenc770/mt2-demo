#!/usr/bin/perl
# *****************************************************************************************
# dbloptin_del.cgi
#
# this page is for deleting Double Option campaigns 
#
# History
# Jim Sobeck, 03/31/08, Creation
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
my $id=$query->param('id');
my $camp_id;
my $rows;

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
        print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
if ($id > 0)
{
	$sql="select campaign_id from double_optin where id=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($id);
	($camp_id)=$sth->fetchrow_array();
	$sth->finish();
	
	if ($camp_id > 0)
	{
		$sql="update campaign set deleted_date=now() where campaign_id = $camp_id";
		$rows=$dbhu->do($sql);
	}
	$sql="delete from double_optin where id=$id";
	$rows=$dbhu->do($sql);
}
#
print "Location: /cgi-bin/dbloptin_list.cgi\n\n";
