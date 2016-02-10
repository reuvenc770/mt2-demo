#!/usr/bin/perl

# *****************************************************************************************
# adv_company_info_upd.cgi
#
# this page updates information in the advertiser_info table
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
#
# Get the information about the user from the form 
#
my $company_id = $query->param('company_id');
my $aid= $query->param('aid');
my $contact_id= $query->param('contact_id');
if ($contact_id eq "")
{
	$contact_id=0;
}
my $website_id= $query->param('website_id');
if ($website_id eq "")
{
	$website_id=0;
}
my $tracking_id= $query->param('tracking_id');
if ($tracking_id eq "")
{
	$tracking_id=0;
}
# Update old contact information
#
$sql = "update advertiser_info set company_id=$company_id,contact_id=$contact_id,website_id=$website_id,tracking_id=$tracking_id where advertiser_id=$aid";
my $rows=$dbhu->do($sql);
my $taid;
$sql="select affiliate_id from company_info_tracking where tracking_id=$tracking_id";
my $sth=$dbhu->prepare($sql);
$sth->execute();
($taid)=$sth->fetchrow_array();
$sth->finish();
if ($taid ne "")
{
	$sql="update company_info set affiliate_id=$taid where company_id=$company_id";
	my $rows=$dbhu->do($sql);
}


print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid\n\n";
$util->clean_up();
exit(0);
