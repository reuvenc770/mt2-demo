#!/usr/bin/perl
# *****************************************************************************************
# daily_emails.pl
#
# Batch program that runs from cron to send the emails
# schedule the email
#
# History
# Jim Sobeck,   05/02/05,   Created
# *****************************************************************************************

# daily_emails.pl 

use strict;
use lib "/var/www/html/newcgi-bin";
use util;
use util_mail;

my $util = util->new;
my $STATE_USER = 0;
my $IP_USER = 0;
my $MALE_CID = 0;
my $sth;
my $sth1;
my $temp_str;
my $reccnt;
my $sth2;
my $sth2a;
my $dbh;
my $dbh1;
my $sql;
my $rows;
my $rec_id;
my $first_rec_id;
my $last_rec_id;
my $cdate = localtime();
my $program = "daily_emails_ib3.pl";
my $errmsg;
my $records_per_file;
my $max_files = 40000;
my $REDIR_ROTATION=250000;
my $IMG_ROTATION=500000;
my $cnt;
my $total_cnt;
my $list_str;
my $campaign_str;
my $temp_id;
my $list_cnt;
my $last_email_user_id;
my $max_emails;
my $filecnt;
my $clast60;
my $aolflag;
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
my $redir_cnt;
my $redir_ind;
my $chged_redir;
my $added_seeds;
my $img_domain;
my $img_cnt;
my $img_ind;
my $max_ind;
my $redir_max_ind;
my $sname;
my $trand;
my $domain_suppid;
my $addrec;
my $begin;
my $end;
my $bend;
my $sth3a;
my $sth3;
my $sth4;
my $client_id;
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
    my $link_id;
    my $global_link_id;
    my $name_str;
    my $subject_name_str;
    my $fullname;
    my $loc;
    my $email_type;
    my $the_email;
    my $filename;
    my $dname;
    my $footer_dname;
    my $days;
my $max_client_emails = 250000;
$chged_redir = 0;
#
#  Set up array for servers
#
my $sarr_cnt;
my $sarr1_cnt;
my $cnt2;
my $ycnt2;
my $last_aol;
my @sarry;
my @sarry1;
my $cnt2a;
my $yarry_cnt;
my $new_blocked_id;
my @creative_arr = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
my @subject_arr = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
my @from_arr = {0,0,0,0,0,0,0,0,0,0};
my $subject_str;
my $from_str;
my @arr;
my $arr_cnt;
my $footer_ind;
my $footer_domain;
my $max_footer_ind;
my $creative_ind = 0;
my $ycreative_ind = 0;
my $subject_ind = 0;
my $ysubject_ind = 0;
my $from_ind = 0;
my $yfrom_ind = 0;
my $aid;
my $cday;
my $bid;
my $turl_id;
my $brand_name;
my $sid;
my @DOMAIN_CNT = (0,0,0,0,0);
my $domain_id;

my $i=0;
while ($i <= $#DOMAIN_CNT)
{
    $DOMAIN_CNT[$i] = 0;
    $i++;
}

# connect to the util database

$| = 1;
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

#
# Send any mail that needs to be sent
#
send_powermail();

$util->clean_up();
exit(0);

# ***********************************************************************
# sub send_powermail
# ***********************************************************************

sub send_powermail
{
	my $campaign_id;
	my $got_camp;


	$sql = "select user_id from user where status='A' order by user_id";
	$sth3a = $dbhq->prepare($sql);
	$sth3a->execute();
	while (($client_id) = $sth3a->fetchrow_array())
	{
		$sql = "select min(client_brand_info.brand_id) from client_brand_info,brand_url_info where client_id=$client_id and client_brand_info.brand_id=brand_url_info.brand_id and url_type='O'"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($bid) = $sth->fetchrow_array();
		$sth->finish();
		#
		# Get the servers to send email to
		#
		$sarr_cnt = 0;
		$sql = "select server_name,rand() from brand_host,client_brand_info where brand_host.brand_id=client_brand_info.brand_id and client_id=? and server_type='O' and brand_host.brand_id=$bid and server_name != 'inv1' order by 2";
		$sth = $dbhq->prepare($sql);
		print "Sql <$sql>\n";
		$sth->execute($client_id);
		while (($sname,$trand) = $sth->fetchrow_array())
		{
			$sarry[$sarr_cnt] = $sname;
			print "Cnt - $sarr_cnt ==> $sname\n";
			$sarr_cnt++;
		}
		$sth->finish();

		print "Processing Client $client_id\n";
		if ($sarr_cnt > 0)
		{
##		$sql = "select list_id from list where list_name='Blocked - New' and user_id=$client_id"; 
		$sql = "select list_id from list where list_name='Blocked - New' and user_id=?"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute($client_id);
		($new_blocked_id) = $sth->fetchrow_array();
		$sth->finish();
		# Get the information for each client
		#
		$cday = 3;
		while ($cday > 0)
		{
			# Check to see if any campaigns to process
			$sql = "select daily_deals.campaign_id,max_emails,last60_flag,aol_flag,hotmail_flag,yahoo_flag,other_flag,open_flag,vendor_supp_list_id,category_id,server_id,campaign.advertiser_id from daily_deals,campaign,advertiser_info where daily_deals.campaign_id=campaign.campaign_id and campaign.advertiser_id=advertiser_info.advertiser_id and deleted_date is null and daily_deals.client_id=$client_id and daily_deals.cday=$cday and (sent_datetime < curdate() or sent_datetime is null) and hour(now()) >= hour(scheduled_datetime)"; 
			$sth4 = $dbhq->prepare($sql);
			$sth4->execute();
			print "Sql <$sql>\n";
			$got_camp = 0;
			$first_rec_id = 0;
			$last_rec_id=0;
			while (($campaign_id,$max_emails,$clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$openflag,$suppid,$catid,$content_id,$aid) = $sth4->fetchrow_array())
			{
				print "Got campaign $campaign_id\n";
				$got_camp = 1;
				$sql = "update campaign set sent_datetime=now() where campaign_id=$campaign_id";
				print "Last SQL = <$sql>\n";	
				$rows = $dbhu->do($sql);
				if ($dbhu->err() != 0)
				{
    				$errmsg = $dbhu->errstr();
       				print "Error updating campaign: $sql : $errmsg";
    				$util->errmail($dbh,$program,$errmsg,$sql);
				}
				$domain_suppid = 0; # May need to added back in

				$sql="select brand_name,brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info,client_brand_info where category_id=$catid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=$bid and category_brand_info.brand_id=client_brand_info.brand_id";
                $sth = $dbhq->prepare($sql);
                $sth->execute();
                ($brand_name,$sid,$subdomain_name) = $sth->fetchrow_array();
                $sth->finish();
                $subdomain_name =~ s/{{BRAND}}/$brand_name/g;
	
				# Send e-mail
		
				$last_email_user_id=1;
				$cdate = localtime();
				$records_per_file = 2300;

				print "Sending email for Campaign $campaign_id at $cdate\n";
				mail_send($campaign_id,$max_emails,$clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$openflag,$open_catid,$catid);
				print "Last Email User Id = $last_email_user_id\n";
			}
			$sth4->finish();
			if ($got_camp == 0)
			{
				print "ERROR: No camp for client $client_id and day $cday\n";
			}
			else
			{
				$sql = "select first_rec from daily_info where client_id=? and cdate=curdate() and send_day=? and rec_type=?";
				$sth3 = $dbhq->prepare($sql);
				$sth3->execute($client_id, $cday,"REG");
				if (($temp_id) = $sth3->fetchrow_array())
				{
				}
				else
				{
					$sql="insert into daily_info(client_id,cdate,send_day,first_rec,last_rec,rec_type) values($client_id,curdate(),$cday,$first_rec_id,$last_rec_id,'REG')";
					$rows = $dbhu->do($sql);
				}
				$sth3->finish();
			}
			$cday--;
		}
		}
	}
	$sth3a->finish();
}
# ***********************************************************************
# This routine is used for sending all email for a single campaign
# ***********************************************************************
sub mail_send
{
	my ($camp_id,$max_emails,$clast60,$aolflag,$hotmailflag,$yahooflag,$otherflag,$openflag,$open_catid,$catid) = @_;

	# Get the mail information for the campaign being used
	$filecnt = 1;

	print "Max files - $max_files\n";

##	$sql = "select domain_name from client_category_info where category_id=$catid and user_id=$client_id";
	$sql = "select domain_name from client_category_info where category_id=? and user_id=?";
	$sth3 = $dbhq->prepare($sql);
	$sth3->execute($catid, $client_id);
	if (($footer_dname) = $sth3->fetchrow_array())
	{
		print "Got Domain - <$footer_dname>\n";
	}
	else
	{
		$footer_dname="hiddensavings.com";
	}
	$sth3->finish();
	$dname="";

	$begin = 0;

    #
    # check for advertiser record first
    #
    $sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10 from advertiser_setup where advertiser_id=$aid and class_id=4";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    if (($creative_arr[0],$creative_arr[1],$creative_arr[2],$creative_arr[3],$creative_arr[4],$creative_arr[5],$creative_arr[6],$creative_arr[7],$creative_arr[8],$creative_arr[9],$creative_arr[10],$creative_arr[11],$creative_arr[12],$creative_arr[13],$creative_arr[14],$subject_arr[0],$subject_arr[1],$subject_arr[2],$subject_arr[3],$subject_arr[4],$subject_arr[5],$subject_arr[6],$subject_arr[7],$subject_arr[8],$subject_arr[9],$subject_arr[10],$subject_arr[11],$subject_arr[12],$subject_arr[13],$subject_arr[14],$from_arr[0],$from_arr[1],$from_arr[2],$from_arr[3],$from_arr[4],$from_arr[5],$from_arr[6],$from_arr[7],$from_arr[8],$from_arr
[9]) = $sth->fetchrow_array())
    {
        $sth->finish();
    }
    else
    {
        $sth->finish();
		$sql = "select creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10 from campaign where campaign_id=$camp_id";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($creative_arr[0],$creative_arr[1],$creative_arr[2],$creative_arr[3],$creative_arr[4],$creative_arr[5],$creative_arr[6],$creative_arr[7],$creative_arr[8],$creative_arr[9],$creative_arr[10],$creative_arr[11],$creative_arr[12],$creative_arr[13],$creative_arr[14],$subject_arr[0],$subject_arr[1],$subject_arr[2],$subject_arr[3],$subject_arr[4],$subject_arr[5],$subject_arr[6],$subject_arr[7],$subject_arr[8],$subject_arr[9],$subject_arr[10],$subject_arr[11],$subject_arr[12],$subject_arr[13],$subject_arr[14],$from_arr[0],$from_arr[1],$from_arr[2],$from_arr[3],$from_arr[4],$from_arr[5],$from_arr[6],$from_arr[7],$from_arr[8],$from_arr[9]) = $sth->fetchrow_array();
		$sth->finish();
	}
	#
	# Now setup the logs for the deal
	#
	$sql = "insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
    $rows = $dbhu->do($sql);
	$sql = "insert into campaign_daily_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
    $rows = $dbhu->do($sql);

	my $i;
		$cnt2 = 0;
		$cnt2a = 0;
		$ycnt2 = 0;
		$creative_ind = 0;
		$subject_ind = 0;
		$from_ind = 0;
		$ycreative_ind = 0;
		$ysubject_ind = 0;
		$yfrom_ind = 0;
##		$sql = "select url,url_id from brand_url_info,client_brand_info where brand_url_info.brand_id=client_brand_info.brand_id and url_type='O' and client_id=$client_id"; 
		$sql = "select url,url_id from brand_url_info,client_brand_info where brand_url_info.brand_id=client_brand_info.brand_id and url_type='O' and client_id=?"; 
		$sth3 = $dbhq->prepare($sql);
		$sth3->execute($client_id);
		($redir_domain,$turl_id) = $sth3->fetchrow_array();
		$sth3->finish();
        $redir_domain=~ tr/[A-Z]/[a-z]/;
##		$sql = "select url from brand_url_info,client_brand_info where brand_url_info.brand_id=client_brand_info.brand_id and url_type='OI' and client_id=$client_id"; 
		$sql = "select url from brand_url_info,client_brand_info where brand_url_info.brand_id=client_brand_info.brand_id and url_type='OI' and client_id=?"; 
		$sth3 = $dbhq->prepare($sql);
		$sth3->execute($client_id);
		($img_domain) = $sth3->fetchrow_array();
		$sth3->finish();
        $img_domain =~ tr/[A-Z]/[a-z]/;
##		$sql = "select advertiser_from from advertiser_from where from_id=$from_arr[$from_ind]";
		$sql = "select advertiser_from from advertiser_from where from_id=?";
		$sth3 = $dbhq->prepare($sql);
		$sth3->execute($from_arr[$from_ind]);
		($from_str) = $sth3->fetchrow_array();
		$sth3->finish();
##		$sql = "select advertiser_subject from advertiser_subject where subject_id=$subject_arr[$subject_ind]";
		$sql = "select advertiser_subject from advertiser_subject where subject_id=?";
		$sth3 = $dbhq->prepare($sql);
		$sth3->execute($subject_arr[$subject_ind]);
		($subject_str) = $sth3->fetchrow_array();
		$sth3->finish();
		open (OUTFILE, "> /var/www/util/tmpmailfiles/list_fa_$sarry[$cnt2]_${camp_id}_$filecnt.txt");
		printf OUTFILE "%d|%d|%d|%s|%d|%s|%s|%s|%s|%d|%d|%d|%d\n",$camp_id,$creative_arr[$creative_ind],$from_arr[$from_ind],$from_str,$subject_arr[$subject_ind],$subject_str,$redir_domain,$img_domain,$subdomain_name,$client_id,$bid,$sid,$turl_id;
		$redir_cnt = 0;
		$img_cnt = 0;
		$cnt = 0;
		$total_cnt = 0;
		#
		# Get link_id for advertiser_id
		#
##		$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=$client_id and daily_deal='Y'";
		$sql = "select link_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='Y'";
		$sth2 = $dbhq->prepare($sql);
		$sth2->execute($aid, $client_id);
		if (($global_link_id) = $sth2->fetchrow_array())
		{
			$sth2->finish();
		}
		else
		{
			$sth2->finish();
##			$sql = "select link_id from advertiser_tracking where advertiser_id=$aid and client_id=$client_id and daily_deal='N'";
			$sql = "select link_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'";
			$sth2 = $dbhq->prepare($sql);
			$sth2->execute($aid, $client_id);
			($global_link_id) = $sth2->fetchrow_array();
			$sth2->finish();
		}
		#
		# Add seeds if any
		#
##		$sql = "select email_addr from advertiser_seedlist where advertiser_id=$aid";
		$sql = "select email_addr from advertiser_seedlist where advertiser_id=?";
		$sth2 = $dbhq->prepare($sql);
		$sth2->execute($aid);
		while (($cemail) = $sth2->fetchrow_array())
		{
            printf OUTFILE "$cemail|H|0|XX|$cemail|Your Area|Your Area|$cemail|$global_link_id|\n";
		}
		$sth2->finish();
		
		process_list($camp_id,0,$new_blocked_id);

		print "Finished sending mail for $camp_id at $cdate\n";
#        my $t_str = "daily\@rapidtec.com";
#        printf OUTFILE "$t_str|H|0|XX|Eric|New York, NY|10016|Eric Rhapsody|$global_link_id|Eric\n";
        my $t_str = "Johnstevens2122\@yahoo.com";
        printf OUTFILE "$t_str|H|00000A|XX|John|New York, NY|10016|John Stevens|$global_link_id|John\n";
		close OUTFILE;
		log_server($camp_id,$sarry[$cnt2]);
}

sub process_list()
{
	my ($camp_id,$list_id,$blocked_list_id) = @_;
	my $cday1;
	my $temp_id;
	my $fid;
	my $lid;
	
	$cday1 = $cday - 1;
	print "Processing List $client_id - $list_id\n";
	$list_cnt = 0;
	#
	# Check to see if daily_info records exists
	#
	$sql = "select first_rec,last_rec from daily_info where client_id=? and cdate=curdate() and send_day=? and rec_type=?";
	$sth3 = $dbhq->prepare($sql);
	$sth3->execute($client_id, $cday,"REG");
	if (($fid,$lid) = $sth3->fetchrow_array())
	{
		$sth3->finish();
    	$sql = "select rec_id,email_addr,email_user_id,state,first_name,last_name,city,zip from daily_records_ib3 where client_id=$client_id and send_day=$cday and rec_id between $fid and $lid";
	}
	else
	{
		$sth3->finish();
		# Now get a list of all the members and start processing
    	$sql = "select rec_id,email_addr,email_user_id,state,first_name,last_name,city,zip from daily_records_ib3 where client_id=$client_id and send_day=$cday order by rec_id limit $max_client_emails";
	}
		$email_type="H";
		$sth = $dbhq->prepare($sql);
		print "$camp_id - <$sql>\n";
		$sth->execute();
        while (($rec_id,$cemail,$email_user_id,$state,$fname,$lname,$city,$zip) = $sth->fetchrow_array())
        {
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

			if ($email_type eq "")
			{
				$email_type = "H";
			}
			if ($state eq "")
			{
				$state = "XX";
			}
			$cemail =~ tr/[A-Z]/[a-z]/;
			$cemail =~ s/ //g;
			$_ = $cemail;
			$addrec = 0;
            if ((/\@aol.com/) || (/\@netscape.net/) || (/\@cs.com/) || (/\@netscape.com/) || (/\@wmconnect.com/))
			{
				$addrec = 0;
			}
			elsif (/\@hotmail.com/)
			{
				$addrec = 0;
			}
			elsif ((/\@msn.com/) || (/\@email.msn.com/))
			{
				$addrec = 0;
			}
			else
			{
    			$_ = $cemail;
    			if ((/\.com$/) || (/\.net$/) || (/\.ca$/))
    			{
					$addrec = 1;
				}
			}
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
				$list_cnt++;
				if (($addrec == 1) && ($suppid > 0))
				{
##					$sql = "select email_addr from vendor_supp_list where list_id=$suppid and email_addr='$cemail'";
					$sql = "select email_addr from vendor_supp_list where list_id=? and email_addr=?";
					$sth1 = $dbhq->prepare($sql);
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
                    $sth1 = $dbhq->prepare($sql);
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
					$_ = $cemail;
					$cnt++;
					if ($cnt > $records_per_file)
					{
						close OUTFILE;
						log_server($camp_id,$sarry[$cnt2]);
						$filecnt++;
						if ($filecnt > $max_files)
						{
							$sth->finish();
							$total_cnt--;
							return;
						}
						if ($list_id > 0)
						{
							$cnt2a++;
							if ($cnt2a == $sarr1_cnt)
							{
								$cnt2a = 0;
							}
						}
						else
						{
							$cnt2++;
							if ($cnt2 == $sarr_cnt)
							{
								$cnt2 = 0;
							}
						}
						#
						# Update counts in logs
						#
						$sql = "update campaign_log set sent_cnt=sent_cnt+$records_per_file where campaign_id=$camp_id and user_id=$client_id";
        				$rows = $dbhu->do($sql);
						$sql = "update campaign_daily_log set sent_cnt=sent_cnt+$records_per_file where campaign_id=$camp_id and user_id=$client_id and date_sent=curdate()";
						print "Sql <$sql>\n";
        				$rows = $dbhu->do($sql);
						$sql="insert into email_log(campaign_id,creative_id,subject_id,from_id) values($camp_id,$creative_arr[$creative_ind],$subject_arr[$subject_ind],$from_arr[$from_ind])";
						print "<$sql>\n";
        				$rows = $dbhu->do($sql);
						$sql = "update email_log set sent_cnt=sent_cnt+$records_per_file where from_id=$from_arr[$from_ind] and campaign_id=$camp_id and creative_id=$creative_arr[$creative_ind] and subject_id=$subject_arr[$subject_ind]";
        				$rows = $dbhu->do($sql);
        				if ($list_id > 0)
        				{
							open (OUTFILE, "> /var/www/util/tmpmailfiles/list_fa_$sarry1[$cnt2a]_${camp_id}_$filecnt.txt");
						}
						else
						{
							open (OUTFILE, "> /var/www/util/tmpmailfiles/list_fa_$sarry[$cnt2]_${camp_id}_$filecnt.txt");
						}
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
						$sql = "select advertiser_from from advertiser_from where from_id=$from_arr[$from_ind]";
						$sth3 = $dbhq->prepare($sql);
						$sth3->execute();
						($from_str) = $sth3->fetchrow_array();
						$sth3->finish();
						$sql = "select advertiser_subject from advertiser_subject where subject_id=$subject_arr[$subject_ind]";
						$sth3 = $dbhq->prepare($sql);
						$sth3->execute();
						($subject_str) = $sth3->fetchrow_array();
						$sth3->finish();
						printf OUTFILE "%d|%d|%d|%s|%d|%s|%s|%s|%s|%d|%d|%d|%d\n",$camp_id,$creative_arr[$creative_ind],$from_arr[$from_ind],$from_str,$subject_arr[$subject_ind],$subject_str,$redir_domain,$img_domain,$subdomain_name,$client_id,$bid,$sid,$turl_id;
						$cnt = 1;
					}
					$_ = $cemail;
           			$domain_id = 4;
           			if ((/\@aol.com/) || (/\@netscape.net/) || (/\@cs.com/) || (/\@netscape.com/) || (/\@wmconnect.com/))
            		{
                		$domain_id = 1;
            		}
            		elsif ((/\@hotmail.com/) || (/\@msn.com/) || (/\@email.msn.com/))
            		{
                		$domain_id = 2;
					}
					elsif (/\@yahoo.com/)
					{
						$domain_id = 3;
					}
					printf OUTFILE "%s|%s|%d|%s|%s|%s|%s|%s|%d|%s\n",$cemail,$email_type,$email_user_id,$state,$name_str,$loc,$zip,$fullname,$global_link_id,$subject_name_str;
					$DOMAIN_CNT[$domain_id]++;
					if ($first_rec_id == 0)
					{
						$first_rec_id=$rec_id;
					}
					$last_rec_id=$rec_id;		
					$last_email_user_id = $email_user_id;
				}
				else
				{
					$sql="delete from daily_records_ib3 where email_addr='$cemail' and client_id=$client_id";
					$rows = $dbhu->do($sql);
				}
	}
	$sth->finish();
	$cdate = localtime();
	$sql = "update campaign_log set sent_cnt=sent_cnt+$cnt where campaign_id=$camp_id and user_id=$client_id";
	$rows = $dbhu->do($sql);
	$sql = "update campaign_daily_log set sent_cnt=sent_cnt+$cnt where campaign_id=$camp_id and user_id=$client_id and date_sent=curdate()";
    $rows = $dbhu->do($sql);
	$sql="insert into email_log(campaign_id,creative_id,subject_id,from_id) values($camp_id,$creative_arr[$creative_ind],$subject_arr[$subject_ind],$from_arr[$from_ind])";
   	$rows = $dbhu->do($sql);
	$sql = "update email_log set sent_cnt=sent_cnt+$records_per_file where from_id=$from_arr[$from_ind] and campaign_id=$camp_id and creative_id=$creative_arr[$creative_ind] and subject_id=$subject_arr[$subject_ind]";
    $rows = $dbhu->do($sql);
}

# ***********************************************************************
# This routine logs to the server_log table
# ***********************************************************************
sub log_server
{
    my ($camp_id,$server_name) = @_;
    my $server_id;
    my $sql;
    my $sth5;
    my $i;
    my $rows;

    print "LOG - $server_name\n";
    $sql = "select id from server_config where server='$server_name'";
    $sth5 = $dbhq->prepare($sql);
    $sth5->execute();
    ($server_id) = $sth5->fetchrow_array();
    $sth5->finish();

    $i=1;
    while ($i <= $#DOMAIN_CNT)
    {
        print "LOG - $i - $DOMAIN_CNT[$i]\n";
        if ($DOMAIN_CNT[$i] > 0)
        {
            $sql = "insert into server_log(id,campaign_id,log_date,domain_id) values($server_id,$camp_id,curdate(),$i)";
            $rows = $dbhu->do($sql);
            $sql = "update server_log set scheduled_cnt=scheduled_cnt+$DOMAIN_CNT[$i] where id=$server_id and campaign_id=$camp_id and log_date=curdate() and domain_id=$i";
            $rows = $dbhu->do($sql);
        }
        $DOMAIN_CNT[$i] = 0;
        $i++;
    }
}
