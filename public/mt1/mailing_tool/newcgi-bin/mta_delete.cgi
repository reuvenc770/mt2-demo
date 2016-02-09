#!/usr/bin/perl

# *****************************************************************************************
# mta_delete.cgi
#
# this page deletes an mta setting 
#
# History
# Jim Sobeck, 03/28/08, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $dbh;
my $mta_id = $query->param('mta_id');
my $new_id;
my $sql;

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
        print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
#


my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}


#------ connect to the util database ------------------
$sql="delete from mta_detail where $userDataRestrictionWhereClause mta_id=$mta_id";
my $rows=$dbhu->do($sql);
$sql="delete from mta_ramp_up where $userDataRestrictionWhereClause mta_id=$mta_id";
$rows=$dbhu->do($sql);
$sql="delete from mta_templates where $userDataRestrictionWhereClause mta_id=$mta_id";
$rows=$dbhu->do($sql);
$sql="delete from mta_footers where $userDataRestrictionWhereClause mta_id=$mta_id";
$rows=$dbhu->do($sql);
$sql="delete from mta where $userDataRestrictionWhereClause mta_id=$mta_id";
$rows=$dbhu->do($sql);

print "Location: mta_list.cgi\n\n";
