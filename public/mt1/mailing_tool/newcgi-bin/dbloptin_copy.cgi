#!/usr/bin/perl
# ******************************************************************************
# dbloptin_copy.cgi
#
# this page is for copying Double Option campaigns 
#
# History
# Jim Sobeck, 04/01/08, Creation
# ******************************************************************************

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
my $cname;
my $client_id;
my $cday;

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
	$sql="select campaign_id,campaign_name,client_id,cday from double_optin where id=?"; 
	$sth=$dbhu->prepare($sql);
	$sth->execute($id);
	($camp_id,$cname,$client_id,$cday)=$sth->fetchrow_array();
	$sth->finish();
	
	$sql="insert into double_optin(campaign_name,client_id,cday,subject,fromline,header_image,content_str,template_id) select campaign_name,client_id,cday,subject,fromline,header_image,content_str,template_id from double_optin where id=$id";
	$rows=$dbhu->do($sql);
	#
	# Get the id for the record just added
	#
	$sql="select max(id) from double_optin where client_id=$client_id and cday=$cday and campaign_name='$cname'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($id)=$sth->fetchrow_array();
	$sth->finish();
	#
	# Create campaign record
	#
	$sql="insert into campaign(campaign_name,status,created_datetime,campaign_type) values('$cname','C',now(),'DAILY')";
	$rows=$dbhu->do($sql);
	#
	$sql="select max(campaign_id) from campaign where campaign_name='$cname' and status='C'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($camp_id)=$sth->fetchrow_array();
	$sth->finish();
	$sql="update double_optin set campaign_id=$camp_id where id=$id";
	$rows=$dbhu->do($sql);
}
#
print "Location: /cgi-bin/dbloptin.cgi?id=$id&c=1\n\n";
