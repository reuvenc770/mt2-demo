#!/usr/bin/perl
# *****************************************************************************************
# preprocess_send_mail.pl
#
# Batch program that runs from cron to send the emails
# schedule the email.  This is a special version of preprocess_send_email.pl which sends to 2 campaigns
#
# History
# Jim Sobeck,   04/18/02,   Created
# *****************************************************************************************

# send_email.pl 

use strict;
use lib "/var/www/pms/src";
use pms;
use pms_mail;

my $pms = pms->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "send_email.pl";
my $errmsg;
my $email_mgr_addr;
my $bin_dir_http;
my $records_per_file = 10000;
my $max_files = 50000;
my $records_per_campaign = 1250000;
my $cnt;
my $total_cnt;
my $other_campaign_id;

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

# lookup the system mail address

$sql = "select parmval from sysparm where parmkey = 'SYSTEM_MGR_ADDR'";
$sth = $dbh->prepare($sql);
$sth->execute();
($email_mgr_addr) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'"; 
$sth = $dbh->prepare($sql); 
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
$sth->finish();

# Send any mail that needs to be sent

send_powermail();

$pms->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $campaign_id;

	# Check to see if any campaigns to process

	$sql = "select campaign_id from campaign where status='S' and 
		scheduled_date <= current_date() and campaign_id = 25 order by campaign_id"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	if (($campaign_id) = $sth->fetchrow_array())
	{
		$sth->finish();
		
		# Mark the campaign as pending
		
		$sql = "update campaign set status='P' where campaign_id=$campaign_id";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
    		$errmsg = $dbh->errstr();
       		print "Error updating campaign: $sql : $errmsg";
    		$pms->errmail($dbh,$program,$errmsg,$sql);
		}
		$other_campaign_id = $campaign_id + 1;
		$sql = "update campaign set status='P' where campaign_id=$other_campaign_id";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
    		$errmsg = $dbh->errstr();
       		print "Error updating campaign: $sql : $errmsg";
    		$pms->errmail($dbh,$program,$errmsg,$sql);
		}
	
		# Send e-mail
		
		$cdate = localtime();
		print "Sending email for Campaign $campaign_id at $cdate\n";
		mail_send($campaign_id);
		
		$sql = "update campaign set status='C',sent_datetime=now(),emails_sent=$total_cnt where campaign_id=$other_campaign_id";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
    		$errmsg = $dbh->errstr();
       		print "Error updating campaign: $sql : $errmsg";
    		$pms->errmail($dbh,$program,$errmsg,$sql);
		}
	}
	else
	{
		$sth->finish();
	}

}
# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{
	my ($camp_id) = @_;
	my $subject;
	my $from_addr;
	my $read_receipt;
	my $list_id;
	my $email_user_id;
	my $list_str;
	my $cemail;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	$sql = "select subject, from_addr, read_receipt from campaign where campaign_id=$camp_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	if (($subject,$from_addr,$read_receipt) = $sth->fetchrow_array())
	{
		$sth->finish();
		
		# Get all of the lists for the campaign which are active
		$sql = "select list.list_id from list,campaign_list 
			where campaign_id=$camp_id and status='A' and 
			list.list_id=campaign_list.list_id";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$list_str = "";
		while (($list_id) = $sth->fetchrow_array())
		{
			if ($list_str eq "")
			{
				$list_str = $list_id;
			}
			else
			{
				$list_str = $list_str . ',' . $list_id;
			}
		}
		$sth->finish();
	
		print "Lists for campaign $camp_id are: $list_str\n";
	
		# Now get a list of all the members and start processing
		
#		$sql = "select distinct email_user_id from list_member 
		$sql = "select email_addr, email_type,email_user.email_user_id from email_user,list_member 
			where list_id in ($list_str) and list_member.status='A' and email_user.email_user_id=list_member.email_user_id and email_user.status='A'";
		$sth = $dbh->prepare($sql);
		$sth->execute();
		open (OUTFILE, "> /var/www/pms/tmpmailfiles/list_${camp_id}_$filecnt.txt");
		printf OUTFILE "%d|%s|%s|%s\n",$camp_id,$from_addr,$subject,$read_receipt;
		$cnt = 0;
		$total_cnt = 0;
		while (($cemail,$email_type,$email_user_id) = $sth->fetchrow_array())
		{
			# if email_type is blank - then default it to H just in case

			if ($email_type eq "")
			{
				$email_type = "H";
			}
			$cnt++;
			$total_cnt++;
			if ($cnt > $records_per_file)
			{
				close OUTFILE;
				if ($total_cnt > $records_per_campaign)
				{
					$filecnt = 0;
					$total_cnt = $total_cnt - 1;
					$sql = "update campaign set status='C',sent_datetime=now(),emails_sent=$total_cnt where campaign_id=$camp_id";
					$rows = $dbh->do($sql);
					if ($dbh->err() != 0)
					{
    					$errmsg = $dbh->errstr();
       					print "Error updating campaign: $sql : $errmsg";
    					$pms->errmail($dbh,$program,$errmsg,$sql);
					}
					if ($camp_id == $other_campaign_id)
					{
						$sth->finish();
						exit();
					}
					$camp_id = $other_campaign_id;
					$total_cnt = 1;
				}
		
				$filecnt++;
				if ($filecnt > $max_files)
				{
					$sth->finish();
					exit();
				}
				open (OUTFILE, "> /var/www/pms/tmpmailfiles/list_${camp_id}_$filecnt.txt");
				printf OUTFILE "%d|%s|%s|%s\n",$camp_id,$from_addr,$subject,$read_receipt;
				$cnt = 1;
			}
			printf OUTFILE "%s|%s|%d\n",$cemail,$email_type,$email_user_id;
		}
		$sth->finish();
	}
	else
	{
		$sth->finish();
	}
	print "Finished sending mail for $camp_id\n";
	close OUTFILE;

}
