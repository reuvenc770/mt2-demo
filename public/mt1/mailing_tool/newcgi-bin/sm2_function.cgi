#!/usr/bin/perl

# *****************************************************************************************
# sm2_function.cgi
#
# this page calls the appropriate routine based on the based in data 
#
# History
# Jim Sobeck, 07/30/07, Creation
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
my $sid;
my $rows;
my $errmsg;
my $images = $util->get_images_url;

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

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $tid = $query->param('tid');
my $pmesg= $query->param('pmesg');
my $submit= $query->param('submit');
if (($submit eq "edit") or ($submit eq "edit it"))
{
    print "Location: sm2_edit.cgi?tid=$tid&pmesg=$pmesg\n\n";
}
elsif ($submit eq "deploy")
{
    print "Location: sm2_deploy_main.cgi?tid=$tid\n\n";
}
elsif ($submit eq "deploy it")
{
	my $profile_id = $query->param('profile_id');
	my $mta_id = $query->param('mta_id');
    print "Location: sm2_deploy_save.cgi?tid=$tid&profile_id=$profile_id&mta_id=$mta_id\n\n";
}
elsif ($submit eq "preview")
{
    print "Location: sm2_preview.cgi?tid=$tid\n\n";
}
elsif ($submit eq "delete")
{
	my $ctype;
	$sql="select campaign_type from test_campaign where $userDataRestrictionWhereClause test_id=$tid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($ctype)=$sth->fetchrow_array();
	$sth->finish();
	if ($ctype eq "HISTORY")
	{
		$ctype="H";
	}
	elsif ($ctype eq "DEPLOYED")
	{
		$ctype="D";
	}
	else
	{
		$ctype="T";
	}
	$sql="delete from test_campaign where $userDataRestrictionWhereClause test_id=$tid";
	my $rows=$dbhu->do($sql);
    print "Location: sm2_list.cgi?type=$ctype\n\n";
}
elsif ($submit eq "cancel")
{
	my $ctype;
	$sql="select campaign_type from test_campaign where $userDataRestrictionWhereClause test_id=$tid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($ctype)=$sth->fetchrow_array();
	$sth->finish();
	if ($ctype eq "HISTORY")
	{
		$ctype="H";
	}
	elsif ($ctype eq "DEPLOYED")
	{
		$ctype="D";
	}
	else
	{
		$ctype="T";
	}
	$sql="update test_campaign set status='CANCELLED' where $userDataRestrictionWhereClause test_id=$tid";
	my $rows=$dbhu->do($sql);
    print "Location: sm2_list.cgi?type=$ctype\n\n";
}
elsif ($submit eq "activate selected")
{
	my @f_box = $query->param('inactive_box');
	foreach my $f1 (@f_box)
	{
		$sql="update test_campaign set history_status='Active' where $userDataRestrictionWhereClause test_id=$f1";
		my $rows=$dbhu->do($sql); 
	}
    print "Location: sm2_list.cgi?type=H\n\n";
}
elsif ($submit eq "deactivate selected")
{
	my @f_box = $query->param('active_box');
	foreach my $f1 (@f_box)
	{
		$sql="update test_campaign set history_status='Inactive' where $userDataRestrictionWhereClause test_id=$f1";
		my $rows=$dbhu->do($sql); 
		$sql="delete from test_schedule where test_id=$f1 and status='START'";
		$rows=$dbhu->do($sql); 
	}
    print "Location: sm2_list.cgi?type=H\n\n";
}
$util->clean_up();
exit(0);
