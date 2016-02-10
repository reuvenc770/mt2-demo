#!/usr/bin/perl
# *****************************************************************************************
# preprocess_send_mail.pl
#
# Batch program that runs from cron to send the emails
# schedule the email
#
# History
# Jim Sobeck,   08/07/01,   Created
# Jim Sobeck,	04/30/02,	Added logic for max_emails 
# Jim Sobeck,	05/06/02,	Added logic for creating files per server
# Jim Sobeck,	02/04/03,	Added logic for last60_flag
# Jim Sobeck,	03/06/03,	Added logic for aol_flag
# Jim Sobeck,	03/31/03,	Added logic for open_flag
# Jim Sobeck,	01/27/04,	Added logic for vendor suppression list
# Jim Sobeck,	05/14/04,	Added logic for 60-120, 120-180, 180 - on
# Jim Sobeck,	05/19/04,	Added logic for Last 30 Days 
# Jim Sobeck,	10/15/04,	Added logic to handle multiple co-locations
# Jim Sobeck,	03/28/05,	Added logic to handle multiple clients
# Jim Sobeck,	11/10/05,	Added logic to log to server_log table
# Jim Sobeck,	01/30/06, 	Added logic to allow sending of campaign_id to program and also added seed seed@spirevision.com
# Jim Sobeck,	03/14/06,	Added logic for test AOL servers
#*****************************************************************************************

# send_email.pl 

use strict;
use Sys::Hostname;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $STATE_USER = 0;
my $IP_USER = 0;
my $MALE_CID = 0;
my $sth;
my $try_again;
my $sth1;
my $table;
my $temp_str;
my $loop_flag;
my $reccnt;
my $sth2;
my $sth2a;
my $dbh;
my $dbh1;
my $dbh2;
my $sql;
my $rows;
my $tmp_cnt;
my $cdate = localtime();
my $wait_days = 0;
my $program = "send_email.pl";
my $errmsg;
my $records_per_file;
my $max_files = 40000;
my $REDIR_ROTATION=250000;
my $IMG_ROTATION=500000;
my $cnt;
my $yahoo_cnt;
my $total_cnt;
my $list_str;
my $campaign_str;
my $temp_id;
my @list_cnt;
my $last_email_user_id;
my $max_emails;
my $filecnt;
my $yahoo_filecnt;
my $clast60;
my $aolflag;
my $profile_id;
my $hotmailflag;
my $yahooflag;
my $otherflag;
my $openflag;
my $open_catid;
my $suppid;
my $catid;
my $content_id;
my $subdomain_name;
my $redir_domain;
my $yredir_domain;
my $redir_cnt;
my $redir_ind;
my $yredir_ind;
my $added_seeds;
my $img_domain;
my $yimg_domain;
my $img_cnt;
my $img_ind;
my $yimg_ind;
my $max_ind;
my $redir_max_ind;
my $sname;
my $ip_addr;
my $trand;
my $domain_suppid;
my $addrec;
my $begin;
my $old_last_id;
my $end;
my $bend;
my $sth3;
my $sth4;
my $client_id;
my $sendto_client;
my $subject;
my $from_addr;
my $list_id;
my $email_user_id;
my $cemail;
my $state;
my $fname;
my $lname;
my $city;
my $zip;
my $daycnt;
my $global_link_id;
my $name_str;
my $subject_name_str;
my $fullname;
my $loc;
my $email_type;
my $the_email;
my $filename;
my $dname;
my $days;
my $bid;
my $brand_name;
my $sid;
my $url_id;
my $turl_id;
my $lrDomIDsIN=[];
my $lrDomIDsNOTIN=[];
#
#  Set up array for servers
#
my $sarr_cnt;
my $cnt2;
my $ycnt2;
my $last_aol;
my @sarry;
my @iparry;
my $yarry_cnt;
my @yahooarry;
my @redirarry;
my @yredirarry;
my @imgarry;
my @yimgarry;
my $redirarr_cnt;
my $yredirarr_cnt;
my $imgarr_cnt;
my $yimgarr_cnt;
my @ourlarry;
my @yourlarry;
my $open_list_id;
my @creative_arr = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
my @subject_arr = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
my @subject_arr_str = {"","","","","","","","","","","","","","",""};
my @from_arr = {0,0,0,0,0,0,0,0,0,0};
my @from_arr_str = {"","","","","","","","","",""};
my $subject_str;
my $from_str;
my $ysubject_str;
my $yfrom_str;
my @arr;
my $arr_cnt;
my $creative_ind = 0;
my $ycreative_ind = 0;
my $subject_ind = 0;
my $ysubject_ind = 0;
my $from_ind = 0;
my $yfrom_ind = 0;
my $aid;
my $MAX_CAMPS=5;
my $camp_cnt=0;
my $domain_id;
my @DOMAIN_CNT = (0,0,0,0,0);
my $sub_field;
my $list_to_add_from;
my $days_since_added;
my $amount_to_add;
my @AOLDOMAIN;
my @HOTMAILDOMAIN;
my @YAHOODOMAIN;
my $input_camp=$ARGV[0];
if ($input_camp eq "")
{
	$input_camp=0;
}

my $i=0;
while ($i <= $#DOMAIN_CNT)
{
    $DOMAIN_CNT[$i] = 0;
    $i++;
}
# connect to the util database

$| = 1;

$util->db_connect();
$dbh = $util->get_dbh;
$util->db_connect1();
$dbh1 = $util->get_dbh1;

#
#	Check to see if max threads exceeded
#
my $max_thread_cnt;
my $server_id;
my $min_free_inode;
my $thread_cnt;
my $host = hostname();
$sql = "select threadsFB,id,freeInode from server_config where name=?";
$sth = $dbh->prepare($sql);
$sth->execute($host);
($max_thread_cnt,$server_id,$min_free_inode) = $sth->fetchrow_array();
$sth->finish();
open(PSEF_PIPE,"ps -ef| grep new_preprocess_send_email.pl | grep -v grep | wc -l | ");
while (<PSEF_PIPE>) 
{
	$thread_cnt = $_;
}
close(PSEF_PIPE);
print "Thread cnt $thread_cnt\n";
if ($thread_cnt > $max_thread_cnt)
{
	exit(0);
}
#
# Send any mail that needs to be sent
#
$sql="select domain_id from email_domains where domain_class=1";
$sth=$dbh->prepare($sql);
$sth->execute();
while (($domain_id)=$sth->fetchrow_array())
{
	$AOLDOMAIN[$domain_id]=1;
}
$sth->finish();
$sql="select domain_id from email_domains where domain_class=2";
$sth=$dbh->prepare($sql);
$sth->execute();
while (($domain_id)=$sth->fetchrow_array())
{
	$HOTMAILDOMAIN[$domain_id]=1;
}
$sth->finish();
$sql="select domain_id from email_domains where domain_class=3";
$sth=$dbh->prepare($sql);
$sth->execute();
while (($domain_id)=$sth->fetchrow_array())
{
	$YAHOODOMAIN[$domain_id]=1;
}
$sth->finish();
send_powermail();

$util->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $campaign_id;
	my $got_deal;
	my $temp_id;
	my $chunk_profile_str;
	my $chunk_profile_id;

	#
	# check to see if server db-1 do special processing
	#
	my $host = hostname();
	my @hosts=split/\./, $host;
	my $pid=$hosts[0] . $$;
	#
	# Get list of all CHUNKING Profiles
	#
	$sql = "select profile_id from list_profile where profile_type='CHUNK'"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$chunk_profile_str="0,";
	while (($chunk_profile_id) = $sth->fetchrow_array())
	{
		$chunk_profile_str = $chunk_profile_str . $chunk_profile_id . ",";
	}
	$sth->finish();
	$_ = $chunk_profile_str;
	chop;
	$chunk_profile_str = $_;

	# Check to see if any campaigns to process
	$got_deal = 1;
	while ($got_deal == 1)
	{
		$loop_flag="N";
		$sql = "start transaction";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
		$rows=$dbh->do($sql);
		if ($input_camp == 0)
		{
        	$sql = "select campaign_id,max_emails,last60_flag,aol_flag,hotmail_flag,yahoo_flag,other_flag,open_flag,server_id,campaign.advertiser_id,profile_id,campaign.brand_id from campaign where campaign.status='S' and scheduled_datetime <= now() and deleted_date is null and campaign.brand_id != 0 and disable_flag='N' and profile_id not in ($chunk_profile_str) order by scheduled_datetime asc limit 1 for update";
		}
		else
		{
        	$sql = "select campaign_id,max_emails,last60_flag,aol_flag,hotmail_flag,yahoo_flag,other_flag,open_flag,server_id,campaign.advertiser_id,profile_id,campaign.brand_id from campaign where campaign.status='S' and scheduled_datetime <= now() and deleted_date is null and campaign.brand_id != 0 and disable_flag='N' and profile_id not in ($chunk_profile_str) and campaign_id=$input_camp order by scheduled_datetime asc for update";
		}
		$sth4 = $dbh->prepare($sql);
		print "Sql <$sql>\n";
		$sth4->execute();
		if (($campaign_id,$max_emails,$clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$openflag,$content_id,$aid,$profile_id,$bid) = $sth4->fetchrow_array())
		{
			$sth4->finish();

			# Mark the campaign as pending
			$sql = "update campaign set status='P', dbID='$pid' where campaign_id=$campaign_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
			$rows = $dbh->do($sql);
			if ($dbh->err() != 0)
			{
    			$errmsg = $dbh->errstr();
       			print "Error updating campaign: $sql : $errmsg";
    			$util->errmail($dbh,$program,$errmsg,$sql);
			}
			$sql = "commit";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
			$rows=$dbh->do($sql);
			$util->clean_up();
			$util->db_connect();
			$dbh = $util->get_dbh;
			$domain_suppid = 0; # May need to added back in
			$sql="select vendor_supp_list_id,category_id from advertiser_info where advertiser_id=?";
			$sth4 = $dbh->prepare($sql);
			$sth4->execute($aid);
			($suppid,$catid) = $sth4->fetchrow_array();
			$sth4->finish();
#
#			if profile_id != 0 then get information about profile from list_profile
#
			$sql = "select day_flag,aol_flag,hotmail_flag,yahoo_flag,other_flag,client_id,max_emails,last_email_user_id,loop_flag,list_to_add_from,amount_to_add,datediff(curdate(),date_added) from list_profile where profile_id=?";
			$sth4 = $dbh->prepare($sql);
			$sth4->execute($profile_id);
			($clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$client_id,$max_emails,$old_last_id,$loop_flag,$list_to_add_from,$amount_to_add,$days_since_added) = $sth4->fetchrow_array();
			$sth4->finish();
			if ($days_since_added eq "")
			{
				$days_since_added=1;
			}
			#
			# check to see if any lists for current client
			#
			$sql = "select count(*) from list_profile_list where profile_id=?"; 
			$sth2 = $dbh->prepare($sql);
			$sth2->execute($profile_id);
			($reccnt) = $sth2->fetchrow_array();
			$sth2->finish();
			if ($reccnt > 0)
			{
				#
				# Get the information for the client
				#
				$sql="select brand_name,brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info,client_brand_info where category_id=? and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=? and category_brand_info.brand_id=client_brand_info.brand_id";
				$sth = $dbh->prepare($sql);
				$sth->execute($catid, $bid);
				($brand_name,$sid,$subdomain_name) = $sth->fetchrow_array();
				$sth->finish();
				$records_per_file=5300;
				$subdomain_name =~ s/{{BRAND}}/$brand_name/g;
				#
				# Get the redirect and image domains
				#
				$redirarr_cnt = 0;
				$sql = "select url_id,url,rand() from brand_url_info where brand_id=? and url_type='O' order by 2";
				$sth = $dbh->prepare($sql);
				$sth->execute($bid);
				while (($turl_id,$sname,$trand) = $sth->fetchrow_array())
				{
					$sname =~ tr/[A-Z]/[a-z]/;
					$redirarry[$redirarr_cnt] = $sname;
					$ourlarry[$redirarr_cnt] = $turl_id;
					print "Cnt - $redirarr_cnt ==> $sname\n";
					$redirarr_cnt++;
				}
				$sth->finish();
				$yredirarr_cnt = 0;
				$sql = "select url_id,url,rand() from brand_url_info where brand_id=? and url_type='Y' order by 2";
				$sth = $dbh->prepare($sql);
				$sth->execute($bid);
				while (($turl_id,$sname,$trand) = $sth->fetchrow_array())
				{
					$sname =~ tr/[A-Z]/[a-z]/;
					$yredirarry[$yredirarr_cnt] = $sname;
					$yourlarry[$yredirarr_cnt] = $turl_id;
					print "Cnt - $yredirarr_cnt ==> $sname\n";
					$yredirarr_cnt++;
				}
				$sth->finish();
				$imgarr_cnt = 0;
				$sql = "select url_id,url,rand() from brand_url_info where brand_id=? and url_type='OI' order by 2";
				$sth = $dbh->prepare($sql);
				$sth->execute($bid);
				while (($turl_id,$sname,$trand) = $sth->fetchrow_array())
				{
					$sname =~ tr/[A-Z]/[a-z]/;
					$imgarry[$imgarr_cnt] = $sname;
					print "Cnt - $imgarr_cnt ==> $sname\n";
					$imgarr_cnt++;
				}
				$sth->finish();
				$yimgarr_cnt = 0;
				$sql = "select url_id,url,rand() from brand_url_info where brand_id=? and url_type='YI' order by 2";
				$sth = $dbh->prepare($sql);
				$sth->execute($bid);
				while (($turl_id,$sname,$trand) = $sth->fetchrow_array())
				{
					$sname =~ tr/[A-Z]/[a-z]/;
					$yimgarry[$yimgarr_cnt] = $sname;
					print "Cnt - $yimgarr_cnt ==> $sname\n";
					$yimgarr_cnt++;
				}
				$sth->finish();
				#
				# Get the servers to send email to
				#
				$sarr_cnt = 0;
				if ($aolflag eq "Y") 
				{
					#
					# Check to see if scheduled thru weekly tool
					#
					my $temp_slot_id;
					$sql="select slot_id from camp_schedule_info where campaign_id=? and slot_type='A'";
					$sth = $dbh->prepare($sql);
					$sth->execute($campaign_id);
					if (($temp_slot_id) = $sth->fetchrow_array())
					{
						$sql = "select brand_host.server_name,rand(),brand_host.ip_addr from brand_host,server_config where brand_host.brand_id=? and brand_host.server_type='T' and brand_host.server_name=server_config.server and server_config.inService=1 order by 2";
						$records_per_file=5;
					}
					else
					{
						$sql = "select brand_host.server_name,rand(),brand_host.ip_addr from brand_host,server_config where brand_host.brand_id=? and brand_host.server_type='A' and brand_host.server_name=server_config.server and server_config.inService=1 order by 2";
					}
					$sth->finish();
				}
				elsif ($hotmailflag eq "Y") 
				{
					$sql = "select brand_host.server_name,rand(),'' from brand_host,server_config where brand_host.brand_id=? and brand_host.server_type='H' and brand_host.server_name=server_config.server and server_config.inService=1 order by 2";
				}
				else
				{
					$sql = "select brand_host.server_name,rand(),'' from brand_host,server_config where brand_host.brand_id=? and brand_host.server_type='O' and brand_host.server_name=server_config.server and server_config.inService=1 order by 2";
				}
				$sth = $dbh->prepare($sql);
				$sth->execute($bid);
				while (($sname,$trand,$ip_addr) = $sth->fetchrow_array())
				{
					$sname =~ tr/[A-Z]/[a-z]/;
					$sarry[$sarr_cnt] = $sname;
					$iparry[$sarr_cnt] = $ip_addr;
					print "Cnt - $sarr_cnt ==> $sname - $ip_addr\n";
					$sarr_cnt++;
				}
				$sth->finish();
	
				$yarry_cnt = 0;
				$sql = "select brand_host.server_name,rand() from brand_host,server_config where brand_host.brand_id=? and brand_host.server_type='Y' and brand_host.server_name=server_config.server and server_config.inService=1 order by 2";
				$sth = $dbh->prepare($sql);
				$sth->execute($bid);
				while (($sname,$trand) = $sth->fetchrow_array())
				{
					$sname =~ tr/[A-Z]/[a-z]/;
					$yahooarry[$yarry_cnt] = $sname;
					print "Cnt - $yarry_cnt ==> $sname\n";
					$yarry_cnt++;
				}
				$sth->finish();
				## Change made by ST on 9/13 to eliminate false alerts and exits.
				if ($sarr_cnt == 0 && $yarry_cnt == 0)
				{
                	open (MAIL,"| /usr/lib/sendmail -t");
                    my $from_addr = "Campaign Error <info\@spirevision.com>";
                    print MAIL "From: $from_addr\n";
                    print MAIL "To: alerts\@spirevision.com\n";
                    print MAIL "Subject: No Servers for Brand $brand_name for Campaign $campaign_id\n";
                    print MAIL "X-Priority: 1\n";
                    print MAIL "X-MSMail-Priority: High\n";
                    print MAIL "No servers for Brand $brand_name for Campaign $campaign_id\n";
                    close MAIL;
					print "Disabling campaign because no server - $campaign_id\n";
					$sql = "update campaign set status='S',disable_flag='E' where campaign_id=$campaign_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
					$rows = $dbh->do($sql);
					exit;
				}
				if (($yarry_cnt == 0) && ($yahooflag ne 'N'))
				{
                	open (MAIL,"| /usr/lib/sendmail -t");
                    my $from_addr = "Campaign Error <info\@spirevision.com>";
                    print MAIL "From: $from_addr\n";
                    print MAIL "To: alerts\@spirevision.com\n";
                    print MAIL "Subject: No Yahoo Servers for Brand $brand_name for Campaign $campaign_id\n";
                    print MAIL "X-Priority: 1\n";
                    print MAIL "X-MSMail-Priority: High\n";
                    print MAIL "No Yahoo servers for Brand $brand_name for Campaign $campaign_id\n";
                    close MAIL;
					print "Disabling campaign because no server - $campaign_id\n";
					$sql = "update campaign set status='S',disable_flag='E' where campaign_id=$campaign_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
					$rows = $dbh->do($sql);
					exit;
				}

				# Send e-mail
				$last_email_user_id=1;
				$cdate = localtime();
				$sql = "select link_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'";
				$sth = $dbh->prepare($sql);
				$sth->execute($aid, $client_id);
				if (($global_link_id) = $sth->fetchrow_array())
				{
				}
				else
				{
					$global_link_id=0;
				}
				$sth->finish();

				if ($subdomain_name ne "")
				{
					if ($global_link_id > 0)
					{
						print "Sending email for Campaign $campaign_id at $cdate\n";
						mail_send($campaign_id,$max_emails,$clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$openflag,$open_catid,$catid,$loop_flag, $host);
						print "Last Email User Id = $last_email_user_id\n";
					}
					else
					{
						print "Disabling campaign because no link - $client_id - $aid\n";
						$sql = "update campaign set status='S',disable_flag='E' where campaign_id=$campaign_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
						$rows = $dbh->do($sql);
                        open (MAIL,"| /usr/lib/sendmail -t");
                        my $from_addr = "Campaign Error <info\@spirevision.com>";
                        print MAIL "From: $from_addr\n";
                        print MAIL "To: alerts\@spirevision.com\n";
                        print MAIL "Subject: No Link for Campaign $campaign_id\n";
                        print MAIL "X-Priority: 1\n";
                        print MAIL "X-MSMail-Priority: High\n";
                        print MAIL "No link for Advertiser $aid and Client $client_id\n";
                        close MAIL;
					}
				}
				else
				{
					print "Disabling campaign because no subdomain_name\n";
					$sql = "update campaign set status='S',disable_flag='E' where campaign_id=$campaign_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
					$rows = $dbh->do($sql);
				}
			}
			if (($creative_arr[0] == 0) || ($subject_arr[0] == 0) || ($from_arr[0] == 0))
			{
				print "Disabling campaign because no creative rotation - $creative_arr[0],$subject_arr[0],$from_arr[0]\n";
				$sql = "update campaign set status='S',disable_flag='E' where campaign_id=$campaign_id";
			}
			else
			{
				if ($max_emails != -1)
				{
					$sql = "update list_profile set last_email_user_id=$last_email_user_id where profile_id=$profile_id";
				}
				else
				{
					$sql = "update list_profile set last_email_user_id=0 where profile_id=$profile_id";
				}
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
				$rows = $dbh->do($sql);
				$sql = "update campaign set status='C',sent_datetime=now() where campaign_id=$campaign_id";
			}
			print "Last SQL = <$sql>\n";	
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
			$rows = $dbh->do($sql);
			my $retry_cnt;
			$retry_cnt = 0; 
			while (($dbh->err() != 0) && ($retry_cnt < 3))
			{
    			$errmsg = $dbh->errstr();
       			print "Error updating campaign: $sql : $errmsg";
				$retry_cnt++;
				$util->db_connect();
				$dbh = $util->get_dbh;
				$rows = $dbh->do($sql);
		    }
			$camp_cnt++;
			if (($camp_cnt >= $MAX_CAMPS) || ($input_camp > 0))
			{
				$got_deal = 0;
			}
 		}
		else
		{
			$sth4->finish();
			$got_deal = 0;
		}
		#
		# Get list of all CHUNKING Profiles - we re-fetch in case any new CHUNK profiles added since last check
		#
		$sql = "select profile_id from list_profile where profile_type='CHUNK'"; 
		$sth = $dbh->prepare($sql);
		$sth->execute();
		$chunk_profile_str="0,";
		while (($chunk_profile_id) = $sth->fetchrow_array())
		{
			$chunk_profile_str = $chunk_profile_str . $chunk_profile_id . ",";
		}
		$sth->finish();
		$_ = $chunk_profile_str;
		chop;
		$chunk_profile_str = $_;
	}
}
# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{
	my ($camp_id,$max_emails,$clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$openflag,$open_catid,$catid,$loop_flag, $host) = @_;

	# Get the mail information for the campaign being used
	$filecnt = 1;
	$yahoo_filecnt = 1;
	$added_seeds = 0;

	if ($max_emails != -1)
	{
		if ($max_emails < 10000)
		{
			$max_files = 1;
			$records_per_file = $max_emails;
		}
		else
		{
			$max_files = $max_emails / $records_per_file;
		}
	}
	print "Max files - $max_files\n";
	$dname="";

	$begin = 0;
	#
	# check for advertiser record first
	#
	my $class_id;
	$class_id=4;
	if ($aolflag eq "Y")
	{
		$class_id=1;
	}
	elsif ($hotmailflag eq "Y")
	{
		$class_id=2;
	}
	elsif (($yahooflag ne "N") && ($otherflag eq "N"))
	{
		$class_id=3;
	}
	$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10 from advertiser_setup where advertiser_id=? and class_id=?";
	$sth = $dbh->prepare($sql);
	$sth->execute($aid,$class_id);
	if (($creative_arr[0],$creative_arr[1],$creative_arr[2],$creative_arr[3],$creative_arr[4],$creative_arr[5],$creative_arr[6],$creative_arr[7],$creative_arr[8],$creative_arr[9],$creative_arr[10],$creative_arr[11],$creative_arr[12],$creative_arr[13],$creative_arr[14],$subject_arr[0],$subject_arr[1],$subject_arr[2],$subject_arr[3],$subject_arr[4],$subject_arr[5],$subject_arr[6],$subject_arr[7],$subject_arr[8],$subject_arr[9],$subject_arr[10],$subject_arr[11],$subject_arr[12],$subject_arr[13],$subject_arr[14],$from_arr[0],$from_arr[1],$from_arr[2],$from_arr[3],$from_arr[4],$from_arr[5],$from_arr[6],$from_arr[7],$from_arr[8],$from_arr[9]) = $sth->fetchrow_array())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10 from campaign where campaign_id=?";
		$sth = $dbh->prepare($sql);
		$sth->execute($camp_id);
		($creative_arr[0],$creative_arr[1],$creative_arr[2],$creative_arr[3],$creative_arr[4],$creative_arr[5],$creative_arr[6],$creative_arr[7],$creative_arr[8],$creative_arr[9],$creative_arr[10],$creative_arr[11],$creative_arr[12],$creative_arr[13],$creative_arr[14],$subject_arr[0],$subject_arr[1],$subject_arr[2],$subject_arr[3],$subject_arr[4],$subject_arr[5],$subject_arr[6],$subject_arr[7],$subject_arr[8],$subject_arr[9],$subject_arr[10],$subject_arr[11],$subject_arr[12],$subject_arr[13],$subject_arr[14],$from_arr[0],$from_arr[1],$from_arr[2],$from_arr[3],$from_arr[4],$from_arr[5],$from_arr[6],$from_arr[7],$from_arr[8],$from_arr[9]) = $sth->fetchrow_array();
		$sth->finish();
	}
	if (($creative_arr[0] == 0) || ($subject_arr[0] == 0) || ($from_arr[0] == 0))
	{
		return;
	}
	#
	# Read all the subjects and froms
	#
	my $i=0;
	while ($i < 15)
	{
		if ($subject_arr[$i] > 0)
		{
			$sql="select advertiser_subject from advertiser_subject where subject_id=?";
			$sth = $dbh->prepare($sql);
			$sth->execute($subject_arr[$i]);
			($subject_arr_str[$i]) = $sth->fetchrow_array();
			$sth->finish();
		}
		$i++;
	}
	$i=0;
	while ($i < 10)
	{
		if ($from_arr[$i] > 0)
		{
			$sql="select advertiser_from from advertiser_from where from_id=?";
			$sth = $dbh->prepare($sql);
			$sth->execute($from_arr[$i]);
			($from_arr_str[$i]) = $sth->fetchrow_array();
			$sth->finish();
		}
		$i++;
	}
	#
	# Now setup the logs for the deal
	#
	$sql="delete from campaign_log where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
    $rows = $dbh->do($sql);
	$sql="delete from profile_log where campaign_id=$camp_id and profile_id=$profile_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
    $rows = $dbh->do($sql);
	$sql = "insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
    $rows = $dbh->do($sql);
	$sql="delete from email_log where campaign_id=$camp_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
    $rows = $dbh->do($sql);
	my $i;
##	for ($i=0; $i < $redirarr_cnt; $i++)
##	{
##		$sql = "insert into url_log(campaign_id,url_id,date_sent) values($camp_id,$ourlarry[$i],curdate())"; 
##unless ($dbh && $dbh->ping) {
##print "connecting\n";
##$util->db_connect();
##$dbh = $util->get_dbh;
##  }
##      	$rows = $dbh->do($sql);
##	}
##	for ($i=0; $i < $yredirarr_cnt; $i++)
##	{
##		$sql = "insert into url_log(campaign_id,url_id,date_sent) values($camp_id,$yourlarry[$i],curdate())"; 
##unless ($dbh && $dbh->ping) {
##print "connecting\n";
##$util->db_connect();
##$dbh = $util->get_dbh;
##   }
##       	$rows = $dbh->do($sql);
##	}
		$cnt2 = 0;
		$ycnt2 = 0;
		$creative_ind = 0;
		$subject_ind = 0;
		$from_ind = 0;
		$ycreative_ind = 0;
		$ysubject_ind = 0;
		$yfrom_ind = 0;
		$redir_ind = 0;
		$img_ind = 0;
		$yredir_ind = 0;
		$yimg_ind = 0;
		$redir_domain = $redirarry[$redir_ind];
		$yredir_domain = $yredirarry[$yredir_ind];
		$img_domain = $imgarry[$img_ind];
		$yimg_domain = $yimgarry[$yimg_ind];
		my $retry_cnt;
		$retry_cnt = 0; 
		$from_str="";
		$from_str=$from_arr_str[$from_ind];
		print "From Id - $from_arr[$from_ind] - $from_str - <$errmsg> - <$sql>\n";
		$yfrom_str="";
		$retry_cnt = 0; 
		$yfrom_str=$from_arr_str[$yfrom_ind];
		print "From IdY - $from_arr[$yfrom_ind] - $yfrom_str - <$errmsg> - <$sql>\n";
		$subject_str = $subject_arr_str[$subject_ind];
		$ysubject_str = $subject_arr_str[$ysubject_ind];
		if (($yahooflag eq "Y") or ($yahooflag eq "M"))
		{
			print "Ycnt2 - $ycnt2 - $yahooarry[$ycnt2]\n";
my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );
			open (OUTFILE1, "> /var/www/util/tmpmailfiles/list_fa_$yahooarry[$ycnt2]_y_${camp_id}_${yahoo_filecnt}_$hour$min$sec.txt");
			printf OUTFILE1 "%d|%d|%d|%s|%d|%s|%s|%s|%s|%d|%d|%d|%d|\n",$camp_id,$creative_arr[$ycreative_ind],$from_arr[$yfrom_ind],$yfrom_str,$subject_arr[$ysubject_ind],$ysubject_str,$yredir_domain,$yimg_domain,$subdomain_name,$client_id,$bid,$sid,$yourlarry[$yredir_ind];
		}
my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );
		open (OUTFILE, "> /var/www/util/tmpmailfiles/list_fa_$sarry[$cnt2]_${camp_id}_${filecnt}_$hour$min$sec.txt");
		printf OUTFILE "%d|%d|%d|%s|%d|%s|%s|%s|%s|%d|%d|%d|%d|%s\n",$camp_id,$creative_arr[$creative_ind],$from_arr[$from_ind],$from_str,$subject_arr[$subject_ind],$subject_str,$redir_domain,$img_domain,$subdomain_name,$client_id,$bid,$sid,$ourlarry[$redir_ind],$iparry[$cnt2];
		$redir_cnt = 0;
		$img_cnt = 0;
		$cnt = 0;
		$yahoo_cnt = 0;
		$total_cnt = 0;
		#
		# Get the table to use to get the member information
		#
		$sub_field="subscribe_date";
		#
		# Get link_id for advertiser_id
		#
		$sql = "select link_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute($aid, $client_id);
		($global_link_id) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($aolflag eq "Y")
		{
			$cemail="generalseed\@aol.com";
        	printf OUTFILE "$cemail|H|0000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
        	$cemail="spirevision\@aol.com";
        	printf OUTFILE "$cemail|H|000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
		}
        if ($hotmailflag eq "Y")
        {
            $cemail="daveo672\@hotmail.com";
            printf OUTFILE "$cemail|H|0000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
        }
        if ($yahooflag ne "N")
        {
            $cemail="martyharris06\@yahoo.com";
            printf OUTFILE "$cemail|H|0000|XX|Eric|New York, NY|10016|Marty Harris|$global_link_id|Marty\n";
        }
        $cemail="eric\@transitdirect.com";
        printf OUTFILE "$cemail|H|0000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
        $cemail="jlee\@spirevision.com";
        printf OUTFILE "$cemail|H|$cemail|XX|Johnny|New York, NY|10016|Johnny Lee|$global_link_id|Johnny\n";
        $cemail="mike\@spirevision.com";
        printf OUTFILE "$cemail|H|00000|XX|Mike|New York, NY|10016|Mike Rhapsody|$global_link_id|Mike\n";
        $cemail="mike\@transitdirect.com";
        printf OUTFILE "$cemail|H|0000000|XX|Mike|New York, NY|10016|Mike Rhapsody|$global_link_id|Mike\n";
        $cemail="seedaccount\@gmail.com";
        printf OUTFILE "$cemail|H|00000000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
        $cemail="seed\@spirevision.com";
        printf OUTFILE "$cemail|H|000000000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
        $cemail="chrisrock1511\@hotmail.com";
        printf OUTFILE "$cemail|H|000000000|XX|Spire|New York, NY|10016|Spire Vision|$global_link_id|Spire\n";
		#
		# Add seeds if any
		#
		$sql = "select email_addr from advertiser_seedlist where advertiser_id=?";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute($aid);
		while (($cemail) = $sth2->fetchrow_array())
		{
            printf OUTFILE "$cemail|H|$cemail|XX|$cemail|Your Area|Your Area|$cemail|$global_link_id|\n";
		}
		$sth2->finish();
		
		my $j=0;
		while ($j <= $#list_cnt)
		{
			$list_cnt[$j]=0;
			$j++;
		}
        my $keep_processing = 1;
        my $second_time = 0;
$lrDomIDsIN=[];
$lrDomIDsNOTIN=[];
		for (my $i=1; $i <= 3; $i++) 
		{
			my $qSelDom=qq^SELECT domain_id FROM email_domains WHERE domain_class=?^;
			connect_db2();
         	my $sthDom=$dbh2->prepare($qSelDom);
			$sthDom->execute($i);
			while (my ($domID)=$sthDom->fetchrow) 
			{
				print "$i - $domID\n";
				if ($i == 1) 
				{
					push @$lrDomIDsIN, $domID if $aolflag eq 'Y' && $otherflag eq 'N';
					push @$lrDomIDsNOTIN, $domID if $aolflag eq 'N' && $otherflag eq 'Y';
				}
				elsif ($i == 2) 
				{
					push @$lrDomIDsIN, $domID if $hotmailflag eq 'Y' && $otherflag eq 'N';
					push @$lrDomIDsNOTIN, $domID if $hotmailflag eq 'N' && $otherflag eq 'Y';
				}
				else 
				{
					push @$lrDomIDsIN, $domID if $yahooflag ne 'N' && $otherflag eq 'N';
				push @$lrDomIDsNOTIN, $domID if $yahooflag eq 'N' && $otherflag eq 'Y';
				}
			}
			$sthDom->finish;
		}
        while ($keep_processing == 1)
        {
		$sql="SELECT lpl.list_id FROM list_profile_list lpl, list l WHERE lpl.list_id=l.list_id AND l.list_name='Openers' AND lpl.profile_id=? AND l.user_id=?";
		$sth2a = $dbh->prepare($sql);
		$sth2a->execute($profile_id, $client_id);
        if (($open_list_id) = $sth2a->fetchrow_array())
		{
			print "Open <$sql> - $open_list_id\n";
			print "Process Open List - $client_id\n";
			process_list($camp_id,$open_list_id,"Y","Y", $host,0);
		}
		else
		{
			$open_list_id=0;
		}
		$sth2a->finish();

		# Get all of the lists for the campaign which are active
		my $tname;
		$sql = "select list.list_id,list.list_name from list,list_profile_list where profile_id=? and status='A' and list.list_id=list_profile_list.list_id and list.list_id != ? order by list.list_id desc";
		$sth2a = $dbh->prepare($sql);
		$sth2a->execute($profile_id, $open_list_id);
		my $temp_list_str="";
        while (($list_id,$tname) = $sth2a->fetchrow_array())
        {
			if ($tname eq "Newest Records")
			{
				process_list($camp_id,$list_id,"Y","N", $host,1);
			}
			else
			{
				process_list($camp_id,$list_id,"Y","N", $host,0);
			}
		}
		$sth2a->finish();	
		my $j=0;
		while ($j <= $#list_cnt)
		{
			if ($list_cnt[$j] > 0)
			{
				$sql = "insert into profile_log(campaign_id,profile_id,list_id,sent_cnt) values($camp_id,$profile_id,$j,$list_cnt[$j])";
				unless ($dbh && $dbh->ping) {
				$util->db_connect();
				$dbh = $util->get_dbh;
				print "Updating profile log <$sql>\n";
   				}
   				$rows = $dbh->do($sql);
			}
			$j++;
		}
        if (($loop_flag eq "N") || ($total_cnt >= $max_emails) || ($max_emails == -1) || ($second_time == 1))
        {
            $keep_processing = 0;
        }
        else
        {
            $second_time = 1;
            if ($loop_flag eq "Y")
            {
                $sql = "delete from profile_last_info where profile_id=$profile_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
                $rows = $dbh->do($sql);
            }
        }
        }
	$sql = "update campaign_log set sent_cnt=sent_cnt+$cnt where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$rows = $dbh->do($sql);
	$sql="insert into email_log(campaign_id,from_id,subject_id,creative_id) values($camp_id,$from_arr[$from_ind],$subject_arr[$subject_ind],$creative_arr[$creative_ind])";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
}
   	$rows = $dbh->do($sql);
	$sql = "update email_log set sent_cnt=sent_cnt+$cnt where from_id=$from_arr[$from_ind] and campaign_id=$camp_id and subject_id=$subject_arr[$subject_ind] and creative_id=$creative_arr[$creative_ind]";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$rows = $dbh->do($sql);
	$sql = "update campaign_log set sent_cnt=sent_cnt+$yahoo_cnt where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
    $rows = $dbh->do($sql);
	$sql="insert into email_log(campaign_id,from_id,subject_id,creative_id) values($camp_id,$from_arr[$yfrom_ind],$subject_arr[$ysubject_ind],$creative_arr[$ycreative_ind])";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
}
   	$rows = $dbh->do($sql);
	$sql = "update email_log set sent_cnt=sent_cnt+$yahoo_cnt where from_id=$from_arr[$yfrom_ind] and campaign_id=$camp_id and subject_id=$subject_arr[$ysubject_ind] and creative_id=$creative_arr[$ycreative_ind]";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$rows = $dbh->do($sql);
		print "Finished sending mail for $camp_id at $cdate\n";
		if (($added_seeds == 0) && ($client_id == 3))
		{
            $added_seeds = 1;
		}
		close OUTFILE;
		close OUTFILE1;
		log_server($camp_id,$sarry[$cnt2],$yahooarry[$ycnt2]);
}

sub process_list()
{
	my ($camp_id,$list_str,$add_yahoo,$processing_openers, $host,$usemaster) = @_;
	my $lid;
	print "Processing List $client_id - $list_str - $add_yahoo($yahooflag) - $processing_openers\n";

	$begin = 0;
	$end = 0;
    print "List str = <$list_str> - $begin - $end\n";
		# Now get a list of all the members and start processing
		if ($clast60 eq "N")
		{
			$sql = "select email_addr,email_user_id,state,first_name,last_name,city,zip,datediff(curdate(),$sub_field),list_id,domain_id from email_list where email_list.list_id in ($list_str) and email_list.status='A' "; 
		}
		else
		{
			if ($clast60 eq "Y")
			{
				$days = 60 + $wait_days;
			}
			elsif ($clast60 eq "7")
			{
				$days = 7 + $wait_days;
			}
			elsif ($clast60 eq "M")
			{
				$days = 30 + $wait_days;
			}
			elsif ($clast60 eq "9")
			{
				$days = 90 + $wait_days;
			}
			elsif ($clast60 eq "O")
			{
				$days = 180 + $wait_days;
			}
			$sql = "select email_addr, email_user_id,state,first_name,last_name,city,zip,datediff(curdate(),$sub_field),list_id,domain_id from email_list where email_list.list_id in ($list_str) and email_list.status='A' and $sub_field >= date_sub(curdate(), interval $days day) "; 
		}
		# JES - If add from list then dont mail records from yesterday
		if ($list_str == $list_to_add_from)
		{
			$sql = $sql . " and subscribe_date != date_sub(curdate(),interval 1 day)";
		}

		my $in_domain_sql=join(', ', @$lrDomIDsIN);
		my $nin_domain_sql=join(', ', @$lrDomIDsNOTIN);
		$sql.=qq^ AND email_list.domain_id IN ($in_domain_sql)^ if $in_domain_sql;
		$sql.=qq^ AND email_list.domain_id NOT IN ($nin_domain_sql)^ if $nin_domain_sql;
		$email_type="H";
		if ($usemaster == 1)
		{
			$sth = $dbh->prepare($sql);
		}
		else
		{
			$sth = $dbh1->prepare($sql);
        	$sth->{mysql_use_result}=1 unless $host eq 'sv-db.routename.com';
		}
		$sth->execute();
		print "$camp_id - <$sql>\n";
		while (($cemail,$email_user_id,$state,$fname,$lname,$city,$zip,$daycnt,$lid,$domain_id) = $sth->fetchrow_array())
		{
                if (exists($AOLDOMAIN[$domain_id]))
                {
                     $domain_id=1;
                }
                elsif (exists($HOTMAILDOMAIN[$domain_id]))
                {
                     $domain_id=2;
                }
                elsif (exists($YAHOODOMAIN[$domain_id]))
                {
                     $domain_id=3;
                }
                else
                {
                     $domain_id=4;
                }
			if ($fname eq "") 
			{
				$name_str = $cemail;
				$subject_name_str = "";
			}
			else
			{
            	$name_str = ucfirst lc $fname;
                $subject_name_str = ucfirst lc $fname;
			}
			if (($fname eq "") or ($lname eq ""))
			{
				$fullname = $cemail;
			}
			else
			{
				$fname = ucfirst lc $fname;
				$lname = ucfirst lc $lname;
				$fullname = $fname . " " . $lname;
			}
			if ($city ne "")
			{
				$loc = $city . ", " . $state;
			}
			elsif ($state ne "")
			{
				$loc = $state;
			}
			else
			{
				$loc = "Your Area";
			}	
			# if email_type is blank - then default it to H just in case
			$last_email_user_id = $email_user_id;

			if ($state eq "")
			{
				$state = "XX";
			}
			$cemail =~ tr/[A-Z]/[a-z]/;
			$cemail =~ s/ //g;
			$addrec = 0;
#			print "Email - <$cemail>\n";
            if ($domain_id == 1)
			{
				if ($aolflag eq "Y") 
				{
					$addrec = 1;
				}
			}
			elsif ($domain_id == 2)
			{
				if ($hotmailflag eq "Y")
				{
					$addrec = 1;
				}
			}
			else
			{
				if ($otherflag eq "Y")
				{
    				$_ = $cemail;
    				if ((/\.com$/) || (/\.net$/) || (/\.ca$/))
    				{
						if ($domain_id == 3)
						{	
							my $tmpid=1;
						}
						else
						{
							$addrec = 1;
						}
					}
				}
				if (($yahooflag eq "Y") && ($add_yahoo eq "Y"))
				{
                	if ($domain_id == 3) 
                    {
						if ($processing_openers eq "N")
						{
							$addrec = 1;
						}
						else
						{
							$addrec = 1;
						}
                    }
				}
				if (($yahooflag eq "M") && ($add_yahoo eq "Y"))
				{
                	if ($domain_id == 3) 
                    {
						if ($processing_openers eq "N")
						{
                            if ($daycnt <= 30) 
                            {
                                $addrec = 1;
                            }
						}
						else
						{
							$addrec = 1;
						}
                    }
				}
			}
			$_ = $cemail;
            if ((/\@comcast.net/) && ($client_id == 3))
            {
            	$addrec = 0;
            }
            if ((/\@us-cs.com/) ||
                                    (/\@terabytecomputers.net/) ||
                                    (/\@terabytecomputers.us/) ||
                                    (/\@bergin.us/) ||
                                    (/\@bergin.boone.nc.us/) ||
                                    (/\@berginsrus.com/) ||
                                    (/\@berginsrus.net/) ||
                                    (/\@lawchek.new/) ||
                                    (/\@enlightentech.net/))
                {
                    $addrec = 0;
                }
                if (/\@ksbc.com/)
                {
                    $addrec = 0;
                }
				if (($addrec == 1) && ($suppid > 0))
				{
					$sql = "select email_addr from vendor_supp_list where list_id=? and email_addr=?";
					connect_db2();
					$sth1 = $dbh2->prepare($sql);
					$sth1->execute($suppid, $cemail);
					if (($temp_str) = $sth1->fetchrow_array())
					{
						$addrec = 0;
					}
					$sth1->finish();

				}
                if (($addrec == 1) && ($domain_suppid > 0))
                {
my $caddr;
my $cdomain;
                    ($caddr,$cdomain) = split("@",$cemail);
                    $sql = "select domain from vendor_domain_supp_list where list_id=? and domain=?";
					connect_db2();
                    $sth1 = $dbh2->prepare($sql);
                    $sth1->execute($domain_suppid, $cdomain);
                    if (($temp_str) = $sth1->fetchrow_array())
                    {
                        $addrec = 0;
                    }
                    $sth1->finish();
                }
				if ($addrec == 1)
				{
					$total_cnt++;
					$list_cnt[$lid]++;
                    if (($total_cnt%153000)==0)
                    {
#                            my $t_str = "rotation\@rapidtec.com";
#        					printf OUTFILE "$t_str|H|00000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
#                            my $t_str = "report\@rapidtec.com";
#        					printf OUTFILE "$t_str|H|00000|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
                    }
					$_ = $cemail;
					if ($domain_id == 3) 
					{
						$yahoo_cnt++;
					}
					else
					{	
						$cnt++;
					}
					if (($cnt > $records_per_file) || (($total_cnt > $max_emails) && ($max_emails != -1)))
					{
						close OUTFILE;
						log_server($camp_id,$sarry[$cnt2],$yahooarry[$ycnt2]);
						$filecnt++;
						if (($total_cnt > $max_emails) && ($max_emails != -1))
						{
							$sth2->finish();
							$total_cnt--;
							my $tmp_cnt=$cnt-1;
							$sql = "update campaign_log set sent_cnt=sent_cnt+$tmp_cnt where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
       		 				$rows = $dbh->do($sql);
	$sql="insert into email_log(campaign_id,from_id,subject_id,creative_id) values($camp_id,$from_arr[$from_ind],$subject_arr[$subject_ind],$creative_arr[$creative_ind])";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
}
   	$rows = $dbh->do($sql);
							$sql = "update email_log set sent_cnt=sent_cnt+$tmp_cnt where from_id=$from_arr[$from_ind] and campaign_id=$camp_id and subject_id=$subject_arr[$subject_ind] and creative_id=$creative_arr[$creative_ind]";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
	$rows = $dbh->do($sql);
							return;
						}
						$cnt2++;
						if ($cnt2 == $sarr_cnt)
						{
							$cnt2 = 0;
						}
						$redir_ind++;
						if ($redir_ind >= $redirarr_cnt)
						{
							$redir_ind = 0;
						}
						$redir_domain = $redirarry[$redir_ind];
						$img_ind++;
						if ($img_ind >= $imgarr_cnt)
						{
							$img_ind = 0;
						}
						$img_domain = $imgarry[$img_ind];
						#
						# Update counts in logs
						#
						$sql = "update campaign_log set sent_cnt=sent_cnt+$records_per_file where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
        				$rows = $dbh->do($sql);
	$sql="insert into email_log(campaign_id,from_id,subject_id,creative_id) values($camp_id,$from_arr[$from_ind],$subject_arr[$subject_ind],$creative_arr[$creative_ind])";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
}
   	$rows = $dbh->do($sql);
						$sql = "update email_log set sent_cnt=sent_cnt+$records_per_file where from_id=$from_arr[$from_ind] and campaign_id=$camp_id and subject_id=$subject_arr[$subject_ind] and creative_id=$creative_arr[$creative_ind]";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
        				$rows = $dbh->do($sql);
my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );
						open (OUTFILE, "> /var/www/util/tmpmailfiles/list_fa_$sarry[$cnt2]_${camp_id}_${filecnt}_$hour$min$sec.txt");
						$creative_ind++;
						if ($creative_ind > 14)
						{
							$creative_ind = 0;
						}
						if ($creative_arr[$creative_ind] == 0)
						{
							$creative_ind = 0;
						}
						$subject_ind++;
						if ($subject_ind > 14)
						{
							$subject_ind = 0;
						}
						if ($subject_arr[$subject_ind] == 0)
						{
							$subject_ind = 0;
						}
						$from_ind++;
						if ($from_ind > 9)
						{
							$from_ind = 0;
						}
						if ($from_arr[$from_ind] == 0)
						{
							$from_ind = 0;
						}
						$from_str=$from_arr_str[$from_ind];
		print "From Id - $from_arr[$from_ind] - $from_str - <$errmsg> - <$sql>\n";
						$subject_str = $subject_arr_str[$subject_ind];
						printf OUTFILE "%d|%d|%d|%s|%d|%s|%s|%s|%s|%d|%d|%d|%d|%s\n",$camp_id,$creative_arr[$creative_ind],$from_arr[$from_ind],$from_str,$subject_arr[$subject_ind],$subject_str,$redir_domain,$img_domain,$subdomain_name,$client_id,$bid,$sid,$ourlarry[$redir_ind],$iparry[$cnt2];
						$cnt = 1;
					}
					$_ = $cemail;
					if ($domain_id == 3) 
					{
						if (($yahoo_cnt > $records_per_file) || (($total_cnt > $max_emails) && ($max_emails != -1)))
						{
							close OUTFILE1;
							log_server($camp_id,$sarry[$cnt2],$yahooarry[$ycnt2]);
							if (($total_cnt > $max_emails) && ($max_emails != -1))
							{
								$sth2->finish();
								$total_cnt--;
								my $tmp_cnt=$cnt - 1;
								$sql = "update campaign_log set sent_cnt=sent_cnt+$tmp_cnt where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
       		 					$rows = $dbh->do($sql);
	$sql="insert into email_log(campaign_id,from_id,subject_id,creative_id) values($camp_id,$from_arr[$from_ind],$subject_arr[$subject_ind],$creative_arr[$creative_ind])";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
}
   	$rows = $dbh->do($sql);
								$sql = "update email_log set sent_cnt=sent_cnt+$tmp_cnt where from_id=$from_arr[$from_ind] and campaign_id=$camp_id and subject_id=$subject_arr[$subject_ind] and creative_id=$creative_arr[$creative_ind]";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
        						$rows = $dbh->do($sql);
								return;
							}
							$sql = "update campaign_log set sent_cnt=sent_cnt+$records_per_file where campaign_id=$camp_id and user_id=$client_id";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
        					$rows = $dbh->do($sql);
	$sql="insert into email_log(campaign_id,from_id,subject_id,creative_id) values($camp_id,$from_arr[$yfrom_ind],$subject_arr[$ysubject_ind],$creative_arr[$ycreative_ind])";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
}
   	$rows = $dbh->do($sql);
							$sql = "update email_log set sent_cnt=sent_cnt+$records_per_file where from_id=$from_arr[$yfrom_ind] and campaign_id=$camp_id and subject_id=$subject_arr[$ysubject_ind] and creative_id=$creative_arr[$ycreative_ind]";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
        				$rows = $dbh->do($sql);
							$yahoo_filecnt++;
							$yredir_ind++;
							if ($yredir_ind >= $yredirarr_cnt)
							{
								$yredir_ind = 0;
							}
							$yredir_domain = $yredirarry[$yredir_ind];
							$yimg_ind++;
							if ($yimg_ind >= $yimgarr_cnt)
							{
								$yimg_ind = 0;
							}
							$yimg_domain = $yimgarry[$yimg_ind];
                        	$ycreative_ind++;
                        	if ($ycreative_ind > 14)
                        	{
                            	$ycreative_ind = 0;
                        	}
                        	if ($creative_arr[$ycreative_ind] == 0)
                        	{
                            	$ycreative_ind = 0;
                        	}
                        	$ysubject_ind++;
                        	if ($ysubject_ind > 14)
                        	{
                            	$ysubject_ind = 0;
                        	}
                        	if ($subject_arr[$ysubject_ind] == 0)
                        	{
                            	$ysubject_ind = 0;
                        	}
                        	$yfrom_ind++;
                        	if ($yfrom_ind > 9)
                        	{
                            	$yfrom_ind = 0;
                        	}
                        	if ($from_arr[$yfrom_ind] == 0)
                        	{
                            	$yfrom_ind = 0;
                        	}
						my $retry_cnt = 0; 
						$yfrom_str=$from_arr_str[$yfrom_ind];
		print "From IdY - $from_arr[$yfrom_ind] - $yfrom_str - <$errmsg> - <$sql>\n";
						$ysubject_str = $subject_arr_str[$ysubject_ind];
							$ycnt2++;
							if ($ycnt2 == $yarry_cnt)
							{
								$ycnt2 = 0;
							}
							print "Ycnt2 - $ycnt2 - $yahooarry[$ycnt2]\n";
my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );
							open (OUTFILE1, "> /var/www/util/tmpmailfiles/list_fa_$yahooarry[$ycnt2]_y_${camp_id}_${yahoo_filecnt}_$hour$min$sec.txt");
							printf OUTFILE1 "%d|%d|%d|%s|%d|%s|%s|%s|%s|%d|%d|%d|%d|\n",$camp_id,$creative_arr[$ycreative_ind],$from_arr[$yfrom_ind],$yfrom_str,$subject_arr[$ysubject_ind],$ysubject_str,$yredir_domain,$yimg_domain,$subdomain_name,$client_id,$bid,$sid,$yourlarry[$yredir_ind];
							$yahoo_cnt = 1;
						}
						printf OUTFILE1 "%s|%s|%d|%s|%s|%s|%s|%s|%d|%s\n",$cemail,$email_type,$email_user_id,$state,$name_str,$loc,$zip,$fullname,$global_link_id,$subject_name_str;
						$DOMAIN_CNT[$domain_id]++;
					}
					else
					{
						printf OUTFILE "%s|%s|%d|%s|%s|%s|%s|%s|%d|%s\n",$cemail,$email_type,$email_user_id,$state,$name_str,$loc,$zip,$fullname,$global_link_id,$subject_name_str;
						$DOMAIN_CNT[$domain_id]++;
					}
					$last_email_user_id = $email_user_id;
				}
	}
		$sth->{mysql_free_result}=1;
		$sth->finish();
	$cdate = localtime();
}

# ***********************************************************************
# This routine logs to the server_log table
# ***********************************************************************
sub log_server
{
	my ($camp_id,$server_name,$yahoo_server) = @_;
	my $server_id;
	my $yserver_id;
	my $sql;
	my $sth5;
	my $i;
	my $rows;

	print "LOG - $server_name - $yahoo_server\n";
	$sql = "select id from server_config where server=?";
##	connect_db2();
	$sth5 = $dbh->prepare($sql);
	$sth5->execute($server_name);
	($server_id) = $sth5->fetchrow_array();
	$sth5->finish();
	$sql = "select id from server_config where server=?";
##	connect_db2();
	$sth5 = $dbh->prepare($sql);
	$sth5->execute($yahoo_server);
	($yserver_id) = $sth5->fetchrow_array();
	$sth5->finish();

	$i=1;
	while ($i <= $#DOMAIN_CNT)
	{
		print "LOG - $i - $DOMAIN_CNT[$i]\n";
		if ($DOMAIN_CNT[$i] > 0)
		{
			if ($i != 3)
			{
				$sql = "insert into server_log(id,campaign_id,log_date,domain_id) values($server_id,$camp_id,curdate(),$i)";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
				$rows = $dbh->do($sql);
				$sql = "update server_log set scheduled_cnt=scheduled_cnt+$DOMAIN_CNT[$i] where id=$server_id and campaign_id=$camp_id and log_date=curdate() and domain_id=$i";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
				$rows = $dbh->do($sql);
			}
			else
			{
				$sql = "insert into server_log(id,campaign_id,log_date,domain_id) values($yserver_id,$camp_id,curdate(),$i)";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
				$rows = $dbh->do($sql);
				$sql = "update server_log set scheduled_cnt=scheduled_cnt+$DOMAIN_CNT[$i] where id=$yserver_id and campaign_id=$camp_id and log_date=curdate() and domain_id=$i";
unless ($dbh && $dbh->ping) {
print "connecting\n";
$util->db_connect();
$dbh = $util->get_dbh;
   }
				$rows = $dbh->do($sql);
			}
		}
		$DOMAIN_CNT[$i] = 0;
		$i++;
	}
}

##
## JES - 08/21 - Added logic so that can have one version of code for all servers
##
sub connect_db2
{
if ($host eq "sv-db-9.routename.com")
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:10.1.1.11', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "sv-db-10.routename.com")
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:10.1.1.12', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "datap1.routename.com")
{
# Connect to sv-db-4p
#
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:10.1.1.6', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "datap2.routename.com")
{
# Connect to sv-db-7
#
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:10.1.1.7', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "ymx2.routename.com")
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:sv-db-5p.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "ymx3.routename.com") 
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:sv-db-3p.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "ymx4.routename.com")
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:sv-db-4p.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
elsif ($host eq "ymx5.routename.com")
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:sv-db-8.routename.com', 'db_user', 'sp1r3V') or die "can't connect to db: $!";
}
}
else
{
unless ($dbh2 && $dbh2->ping) {
print "connecting to local database\n";
$dbh2=DBI->connect('DBI:mysql:new_mail:', 'mailer', '9wEBdEfY') or die "can't connect to db: $!";
}
}
}
