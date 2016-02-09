#!/usr/bin/perl

# *****************************************************************************************
# send_approval.cgi
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
my $internal= $query->param('i');
if ($internal eq "")
{
	$internal=0;
}
my $cemail = $query->param('cemail');
my @textads= $query->param('textads');
my $format = "H"; 
my $email_str;
my $aname;
my $astatus;
my $cid;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id != 0)
{
	$sql = "select advertiser_name,company_id,status from advertiser_info where advertiser_id=$aid"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($aname,$cid,$astatus) = $sth->fetchrow_array();
	$sth->finish();
	if ($cemail eq "ALL")
	{
		$email_str = "";
		if ($internal == 1)
		{
			$email_str="group.approvals\@zetainteractive.com,";
			$sql = "select cm.email_addr from CampaignManager cm,company_info ci where cm.manager_id=ci.manager_id and ci.manager_id != 0 and ci.company_id=$cid"; 
			$sth = $dbhq->prepare($sql);
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
#			$sql = "select cm.contact_email from company_info_contact cm where cm.company_id=$cid"; 
			$sql = "select email_addr from company_approval where company_id=$cid"; 
			$sth = $dbhq->prepare($sql);
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
	}
	else
	{
		$email_str = $cemail;
	}
	if ($internal != 1)
	{
		$sql = "update advertiser_info set approval_requested_date=curdate() where advertiser_id=$aid";
		my $rows = $dbhu->do($sql);
		$sql="update creative set date_approved=now(),approved_by='SpireVision',approved_flag='Y' where status='A' and date_approved is null and advertiser_id=$aid";
		$rows = $dbhu->do($sql);
		$sql="update advertiser_subject set date_approved=now(),approved_by='SpireVision',approved_flag='Y' where status='A' and date_approved is null and advertiser_id=$aid";
		$rows = $dbhu->do($sql);
		$sql="update advertiser_from set date_approved=now(),approved_by='SpireVision',approved_flag='Y' where status='A' and date_approved is null and advertiser_id=$aid";
		$rows = $dbhu->do($sql);
		$sql="update advertiser_tracking set date_approved=now(),approved_by='SpireVision' where date_approved is null and advertiser_id=$aid";
		$rows = $dbhu->do($sql);
	}
	&util_mail::mail_approvaltest($dbhu,$email_str,$user_id,$aid,$aname,$uid,$internal,$astatus,@textads);
}

print "Location:advertiser_disp2.cgi?puserid=$aid&mode=U\n\n";

$util->clean_up();
exit(0);

