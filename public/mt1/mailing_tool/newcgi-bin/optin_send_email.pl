#!/usr/bin/perl
# ******************************************************************************
# optin_send_mail.pl
#
# Batch program that runs from cron to send the optin emails
#
# History
# ******************************************************************************

# send_email.pl 

use strict;
use lib "/var/www/util/src";
use util;
use util_mail;

my $util = util->new;
my $sth;
my $sth1;
my $sth2;
my $dbh;
my $sql;
my $rows;
my $cdate = localtime();
my $program = "optin_send_email.pl";
my $errmsg;
my $email_mgr_addr;
my $bin_dir_http;
my $records_per_file = 10000;
my $max_files = 40000;
my $cnt;
my $total_cnt;
my $aol_cnt;
my $list_aol_cnt;
my $list_cnt;
my $last_email_user_id;
my $max_emails;
my $clast60;
my $aolflag;
my $openflag;
my $first_email_user_id;
my $addrec;
#
#  Set up array for servers
#
my $sarr_cnt = 1;
my $cnt2;
my @sarry = (
	["jjdb","2"]
);

# connect to the util database

$| = 1;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

# lookup the system mail address

$sql = "select parmval from sysparm where parmkey = 'SYSTEM_MGR_ADDR'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($email_mgr_addr) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'"; 
$sth = $dbhq->prepare($sql); 
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
$sth->finish();

# Send any mail that needs to be sent

send_powermail();

$util->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $campaign_id;
	my $user_id;

	# Check to see if any campaigns to process

	$sql = "select campaign_id,max_emails,first_email_user_id,user_id,last60_flag,aol_flag,open_flag from campaign where campaign_id=1043"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	if (($campaign_id,$max_emails,$first_email_user_id,$user_id,$clast60,$aolflag,$openflag) = $sth->fetchrow_array())
	{
		$sth->finish();
		
		# Send e-mail
		$last_email_user_id=1;
		$cdate = localtime();
		print "Sending email for Campaign $campaign_id at $cdate\n";
		mail_send($campaign_id,$max_emails,$first_email_user_id,$user_id,$clast60,$aolflag,$openflag);
		
		print "Sent $total_cnt emails with $aol_cnt AOLs\n";
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
	my ($camp_id,$max_emails,$first_email_user_id,$user_id,$clast60,$aolflag,$open_flag) = @_;
	my $subject;
	my $from_addr;
	my $list_id;
	my $email_user_id;
	my $cemail;
	my $email_type;
	my $the_email;
	my $filename;
	my $filecnt;
	my $curcnt;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	if ($max_emails != -1)
	{
		$max_files = $max_emails / $records_per_file;
	}
	print "Max files - $max_files\n";

	$sql = "select subject, from_addr from campaign where campaign_id=$camp_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	if (($subject,$from_addr) = $sth->fetchrow_array())
	{

		$cnt2 = 0;
		$curcnt = 1;
		open (OUTFILE, "> /var/www/util/tmpmailfiles/list_$sarry[$cnt2][0]_${camp_id}_$filecnt.txt");
		printf OUTFILE "%d|%s|%s|%d\n",$camp_id,$from_addr,$subject,$user_id;
		$cnt = 0;
		$total_cnt = 0;
		$aol_cnt = 0;
		$sth->finish();
		
#		$sql = "select email_addr, email_user_id from new_member where create_datetime=curdate()";
		$sql = "select new_member.email_addr, new_member.email_user_id from new_member,member_list1 where new_member.email_user_id=member_list1.email_user_id and new_member.status='A' and member_list1.status='A'";
		$email_type="H";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		while (($cemail,$email_user_id) = $sth->fetchrow_array())
		{
			# if email_type is blank - then default it to H just in case
			$last_email_user_id = $email_user_id;

			if ($email_type eq "")
			{
				$email_type = "H";
			}
			$cemail =~ tr/[A-Z]/[a-z]/;
			$_ = $cemail;
			$addrec = 1;
            if ((/\@aol.com/) || (/\@netscape.net/) || (/\@cs.com/) || (/\@netscape.com/))
			{
				$aol_cnt++;
				$addrec = 0;
				$sql = "update new_member set status='S' where email_user_id=$email_user_id";
        		$rows = $dbhu->do($sql);
			}
			elsif (/\@hotmail.com/)
			{
				$addrec = 0;
				$sql = "update new_member set status='S' where email_user_id=$email_user_id";
        		$rows = $dbhu->do($sql);
			}
			elsif (/\@msn.com/)
			{
				$addrec = 0;
				$sql = "update new_member set status='S' where email_user_id=$email_user_id";
        		$rows = $dbhu->do($sql);
			}
			else
			{
				$sql = "update new_member set status='S' where email_user_id=$email_user_id";
        		$rows = $dbhu->do($sql);
			}
			$list_cnt++;
			if ($addrec == 1)
			{
					$cnt++;
					$total_cnt++;
					if ($cnt > $records_per_file)
					{
						close OUTFILE;
						$curcnt++;
						$filecnt++;
						if ($filecnt > $max_files)
						{
							$sth->finish();
							$sth2->finish();
							$total_cnt--;
							return;
						}
						if ($curcnt > $sarry[$cnt2][1])
						{
							$cnt2++;
							if ($cnt2 == $sarr_cnt)
							{
								$cnt2 = 0;
							}
							$curcnt = 1;
						}
						open (OUTFILE, "> /var/www/util/tmpmailfiles/list_$sarry[$cnt2][0]_${camp_id}_$filecnt.txt");
						printf OUTFILE "%d|%s|%s|%d\n",$camp_id,$from_addr,$subject,$user_id;
						$cnt = 1;
					}
					printf OUTFILE "%s|%s|%d\n",$cemail,$email_type,$email_user_id;
					$last_email_user_id = $email_user_id;
				}
			}
			$sth->finish();
	}
	else
	{
		$sth->finish();
	}
	$cdate = localtime();
	print "Finished sending mail for $camp_id at $cdate\n";
	close OUTFILE;
}
