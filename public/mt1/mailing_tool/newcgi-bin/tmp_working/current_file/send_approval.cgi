#!/usr/bin/perl

# *****************************************************************************************
# camp_test_gen.cgi
#
# this page sends one test email
#
# History
# Grady Nash, 8/15/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sql;
my $sth;
my $aid = $query->param('aid');
my $uid = $query->param('uid');
my $cemail = $query->param('cemail');
my $format = "H"; 
my $email_str;
my $aname;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id != 0)
{
	$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($aname) = $sth->fetchrow_array();
	$sth->finish();
	if ($cemail eq "ALL")
	{
		$email_str = "";
		$sql = "select email_addr from advertiser_approval where advertiser_id=$aid"; 
		$sth = $dbh->prepare($sql);
		$sth->execute();
		while (($cemail) = $sth->fetchrow_array())
		{
			$email_str = $email_str . $cemail . ",";
		}
		$sth->finish();
		$_ = $email_str;
		chop;
		$email_str = $_;
	}
	else
	{
		$email_str = $cemail;
	}
	$sql = "update advertiser_info set approval_requested_date=curdate() where advertiser_id=$aid";
	my $rows = $dbh->do($sql);
	&util_mail::mail_approvaltest($dbh,$email_str,$user_id,$aid,$aname,$uid);
}

print "Location:advertiser_disp2.cgi?puserid=$aid&mode=U\n\n";

$util->clean_up();
exit(0);

