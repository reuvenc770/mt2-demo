#!/usr/bin/perl

# *****************************************************************************************
# upd_company.cgi
#
# this page updates information in the company_info table
#
# History
# Jim Sobeck, 05/17/06, Creation
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
my $company = $query->param('company');
my $addr = $query->param('addr');
my $manager_id= $query->param('manager_id');
my $affiliate_id = $query->param('affiliate_id');
my $backto = $query->param('backto');
my $passcard = $query->param('passcard');
if ($addr ne "")
{
}
else
{
	$addr='';
}
my $notes = $query->param('notes');
if ($notes ne "")
{
}
else
{
	$notes='';
}
$addr =~ s/'/''/g; 
$notes =~ s/'/''/g; 
$passcard=~ s/'/''/g; 
#
# Update old contact information
#
if ($company_id > 0)
{
	$sql = "update company_info set company_name='$company',contact_notes='$notes',physical_addr='$addr',manager_id=$manager_id,affiliate_id=$affiliate_id,passcard='$passcard' where company_id=$company_id";
}
else
{
	$sql="insert into company_info(company_name,contact_notes,physical_addr,manager_id,affiliate_id,passcard) values('$company','$notes','$addr',$manager_id,$affiliate_id,'$passcard')";
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    util::logerror("Updating company info record for $sql $user_id: $errmsg");
}
else
{
	if ($company_id == 0)
	{
		$sql="select max(company_id) from company_info where company_name='$company'";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		($company_id)=$sth1->fetchrow_array();
		$sth1->finish();

		$sql="insert into IOAdvertiser(AdvertiserName,company_id) values('$company',$company_id)";
		$sth = $dbhu->do($sql);

		my $IOAdvertiserID;
		$sql="select IOAdvertiserID from IOAdvertiser where AdvertiserName=? order by IOAdvertiserID desc limit 1";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($company);
		($IOAdvertiserID)=$sth1->fetchrow_array();
		$sth1->finish();

		$sql="insert into IOAdvertiserInfo(IOAdvertiserID,advertiser_id,AdvertiserName) values($IOAdvertiserID,0,'$company')"; 
		$sth = $dbhu->do($sql);
	}
}
#
# Display the confirmation page
#
if ($backto eq "")
{
	print "Location: /cgi-bin/company_list.cgi\n\n";
}
else
{
	print "Location: $backto\n\n";
}
$util->clean_up();
exit(0);
