#!/usr/bin/perl

# ******************************************************************************
# copy_schedule.pl
#
# this page copies a schedule from the previous day 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $sth;
my $sth1;
my $sth3;
my $sql;
my $rows;
my $sdate;
my $edate;
my $images = $util->get_images_url;
my $company;
my $network_id;
my $daycnt;
my $exclude_from_brands_w_articles;
my $campaign_name;
my $disp_msg;
my $brand_id;
my $daycnt2;
my $daycnt1;
my $temp_date;
my $adv_id;
my $stime;
my $priority;
my $camp_id;
my $new_camp_id;
my $slot_id;
my $cname;
my $creative1;
my $subject1;
my $from1;
my $from_addr;
my $send_email;
my $sdate1;
my ($supp_name,$last_updated,$filedate);
my $suppid;
my $pid;
my $bid;
my $vsgID;
my $client_id;
my $catid;
my $aname;
my $mta_id;
my $tdate;
$mta_id=1;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

$disp_msg="";
$daycnt=0;
$sql = "select datediff('$tdate',curdate())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
#
$sql = "select date_sub(curdate(),interval 1 day),date_add(curdate(),interval 6 day)"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($sdate,$tdate) = $sth->fetchrow_array();
$sth->finish();
$edate=$sdate;
$sql = "select datediff('$edate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
$sql = "select datediff('$tdate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt1) = $sth->fetchrow_array();
$sth->finish();
print "Date diff $daycnt - $daycnt1\n";
#
open (MAIL1,"| /usr/sbin/sendmail -t");
$from_addr = "Schedule Copy <info\@spirevision.com>";
print MAIL1 "From: $from_addr\n";
print MAIL1 "To: jsobeck\@spirevision.com\n";
print MAIL1 "Subject: Copy Schedule\n";
my $date_str = $util->date(6,6);
print MAIL1 "Date: $date_str\n";
my $network;
my $tparty=10;
$sql="select client_id from CopyScheduleClient";
$sth3=$dbhq->prepare($sql);
$sth3->execute();
while (($network)=$sth3->fetchrow_array())
{
	#
	#	Delete all records for this network for specified time
	#
	$sql="select campaign_id,camp_schedule_info.slot_id,schedule_date from camp_schedule_info,schedule_info where camp_schedule_info.client_id=$network and camp_schedule_info.slot_type='3' and schedule_info.client_id=camp_schedule_info.client_id and schedule_info.slot_id=camp_schedule_info.slot_id and schedule_info.slot_type='3' and third_party_id=$tparty and status='A' and schedule_date >= '$tdate' and schedule_date <= date_add('$tdate',interval $daycnt day)";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	print "<$sql>\n";
	while (($camp_id,$slot_id,$temp_date) = $sth->fetchrow_array())
	{
		$sql = "update campaign set deleted_date=now() where campaign_id=$camp_id and status='C' and deleted_date is null";
		print "<$sql>\n";
		$rows = $dbhu->do($sql);
		$sql = "delete from camp_schedule_info where client_id=$network and campaign_id=$camp_id and slot_id=$slot_id and schedule_date='$temp_date' and slot_type='3'";
		print "<$sql>\n";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
#
	$sql="select camp_schedule_info.client_id,camp_schedule_info.campaign_id,camp_schedule_info.slot_id,schedule_date,campaign.advertiser_id,hour(schedule_info.schedule_time),advertiser_info.category_id,advertiser_info.advertiser_name,advertiser_info.exclude_from_brands_w_articles,schedule_info.brand_id,schedule_info.performance,campaign.campaign_name,advertiser_info.vendor_supp_list_id from camp_schedule_info,campaign,advertiser_info,schedule_info where camp_schedule_info.client_id=$network and camp_schedule_info.slot_type='3' and schedule_date >= '$sdate' and schedule_date <= '$edate' and camp_schedule_info.slot_id=schedule_info.slot_id and schedule_info.client_id=camp_schedule_info.client_id and schedule_info.slot_type='3' and schedule_info.status='A' and schedule_info.third_party_id=$tparty and camp_schedule_info.campaign_id=campaign.campaign_id and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.status='A' and deleted_date is null"; 
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	print "<$sql>\n";
	while (($client_id,$camp_id,$slot_id,$temp_date,$adv_id,$stime,$catid,$aname,$exclude_from_brands_w_articles,$brand_id,$priority,$campaign_name,$suppid) = $sth->fetchrow_array())
	{
		if ($priority == 0)
		{
				$priority=5;
		}
		#
		# Check for exclusions
		#
       	my $sth1a;
       	my $reccnt;
       	$sql = "select count(*) from client_category_exclusion,client_advertiser_exclusion where (client_category_exclusion.client_id=? and client_category_exclusion.category_id=?) or (client_advertiser_exclusion.client_id=? and client_advertiser_exclusion.advertiser_id=?)";
       	$sth1a = $dbhq->prepare($sql);
       	$sth1a->execute($client_id,$catid,$client_id,$adv_id);
       	($reccnt) = $sth1a->fetchrow_array();
       	$sth1a->finish();
       	#
       	if ($reccnt == 0)
       	{
			if ($exclude_from_brands_w_articles eq "Y")
			{
				$sql="select count(*) from brand_article ba, article a where ba.brand_id=? and ba.article_id=a.article_id and a.status='A'";
       			$sth1a = $dbhq->prepare($sql);
       			$sth1a->execute($brand_id);
       			($reccnt) = $sth1a->fetchrow_array();
       			$sth1a->finish();
			}
			if ($reccnt == 0)
			{
				$sql = "insert into campaign(user_id,max_emails,advertiser_id,id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,server_id,trigger_creative,last60_flag,aol_flag,yahoo_flag,hotmail_flag,other_flag,open_flag,list_cnt,open_category_id,disable_flag,profile_id,brand_id,campaign_name,status,scheduled_datetime,sent_datetime,scheduled_date,scheduled_time,campaign_type) select user_id,max_emails,campaign.advertiser_id,id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,1,trigger_creative,last60_flag,aol_flag,yahoo_flag,hotmail_flag,other_flag,open_flag,list_cnt,open_category_id,'N',profile_id,brand_id,campaign_name,'C',date_add(scheduled_datetime,interval $daycnt1 day),date_add(scheduled_datetime,interval $daycnt1 day),date(date_add(scheduled_datetime,interval $daycnt1 day)),time(date_add(scheduled_datetime,interval $daycnt1 day)),campaign_type from campaign,advertiser_info where campaign_id=$camp_id and deleted_date is null and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.status='A'"; 
				print "<$sql>\n";
				$rows = $dbhu->do($sql);

				$sql = "select max(campaign_id) from campaign where campaign_name=?"; 
				$sth1 = $dbhu->prepare($sql);
				$sth1->execute($campaign_name);
				($new_camp_id) = $sth1->fetchrow_array();
				$sth1->finish();
				print "New camp id - <$new_camp_id>\n";
#
#		Get the brand and profile id from the schedule_info table
#
				$sql = "select profile_id,brand_id, vsgID,mta_id from schedule_info where client_id=$network and slot_id=$slot_id and slot_type='3'"; 
				$sth1 = $dbhq->prepare($sql);
				$sth1->execute();
				($pid,$bid, $vsgID,$mta_id) = $sth1->fetchrow_array();
				$sth1->finish();
				$sql = "update campaign set profile_id=$pid,brand_id=$bid where campaign_id=$new_camp_id";
				$rows = $dbhu->do($sql);
#
#   Check to see if advertiser rotation setup
#
    			$sql = "select creative1_id,subject1,from1 from advertiser_setup where advertiser_id=? and class_id=4";
    			$sth1 = $dbhu->prepare($sql);
    			$sth1->execute($adv_id);
    			$send_email = 0;
    			if (($creative1,$subject1,$from1) = $sth1->fetchrow_array())
    			{
        			if (($creative1 == 0) || ($subject1 == 0) || ($from1 == 0))
        			{
            			$send_email = 1;
        			}
    			}
    			else
    			{
        			$send_email = 1;
    			}
    			$sth1->finish();

    			if ($send_email == 1)
    			{
    				$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=$new_camp_id";
    				$sth1 = $dbhu->prepare($sql);
    				$sth1->execute();
					($cname,$sdate1) = $sth1->fetchrow_array();
					$sth1->finish();
	        		open (MAIL,"| /usr/sbin/sendmail -t");
	        		$from_addr = "No Creative Rotation <info\@spirevision.com>";
			   	 	print MAIL "From: $from_addr\n";
			       	print MAIL "To: setup\@spirevision.com\n";
			       	print MAIL "Subject: Creative Rotation Missing\n";
			       	my $date_str = $util->date(6,6);
			       	print MAIL "Date: $date_str\n";
			       	print MAIL "X-Priority: 1\n";
			       	print MAIL "X-MSMail-Priority: High\n";
			       	print MAIL "Need Creative Rotation for $cname - scheduled for $sdate1\n";
		       		close MAIL;
    			}
#
# Check suppression for advertiser
#
				$sql = "select list_name,last_updated,filedate,datediff(curdate(),last_updated) from vendor_supp_list_info where list_id=?";
				$sth1 = $dbhq->prepare($sql);
				$sth1->execute($suppid);
				($supp_name,$last_updated,$filedate,$daycnt2) = $sth1->fetchrow_array();
				$sth1->finish();
				if ($supp_name ne "NONE")
				{
					if ($filedate eq "")
				    {
				    	if ($daycnt2 > 7)
				        {
					    	$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=?";
				   	 		$sth1 = $dbhu->prepare($sql);
				    		$sth1->execute($new_camp_id);
							($cname,$sdate1) = $sth1->fetchrow_array();
							$sth1->finish();
#				    		open (MAIL,"| /usr/sbin/sendmail -t");
#				    		$from_addr = "Out of Date Suppression <info\@spirevision.com>";
#				    		print MAIL "From: $from_addr\n";
#				    		print MAIL "To: setup\@spirevision.com\n";
#				    		print MAIL "Subject: $cname has a suppression file from $last_updated\n"; 
#				    		my $date_str = $util->date(6,6);
#				    		print MAIL "Date: $date_str\n";
#				    		if ($daycnt2 > 10)
#				        	{
#				    			print MAIL "X-Priority: 1\n";
#				    			print MAIL "X-MSMail-Priority: High\n";
#							}
#				    		print MAIL "$cname has a suppression file from $last_updated\n"; 
#				    		close MAIL;
						}
					}
					else
					{
				    	$sql = "select datediff(curdate(),'$filedate')";
				        $sth1 = $dbhq->prepare($sql) ;
				        $sth1->execute();
				        ($daycnt2) = $sth1->fetchrow_array();
				        $sth1->finish();
				        if ($daycnt2 > 7)
				        {
				    		$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=?";
				    		$sth1 = $dbhu->prepare($sql);
				    		$sth1->execute($new_camp_id);
							($cname,$sdate1) = $sth1->fetchrow_array();
							$sth1->finish();
#				    		open (MAIL,"| /usr/sbin/sendmail -t");
#				    		$from_addr = "Out of Date Suppression <info\@spirevision.com>";
#				    		print MAIL "From: $from_addr\n";
#				    		print MAIL "To: setup\@spirevision.com\n";
#				    		print MAIL "Subject: $cname has a suppression file from $filedate\n"; 
#				    		my $date_str = $util->date(6,6);
#				    		print MAIL "Date: $date_str\n";
#				        	if ($daycnt2 > 10)
#				        	{
#				    			print MAIL "X-Priority: 1\n";
#				    			print MAIL "X-MSMail-Priority: High\n";
#							}
#				    		print MAIL "$cname has a suppression file from $filedate\n"; 
#				    		close MAIL;
						}
					}
				}
				else
				{
#			   		open (MAIL,"| /usr/sbin/sendmail -t");
#			   		$from_addr = "No Suppression File <info\@spirevision.com>";
#			   		print MAIL "From: $from_addr\n";
#			   		print MAIL "To: setup\@spirevision.com\n";
#			   		print MAIL "Subject: $cname has no suppression file \n"; 
#			   		my $date_str = $util->date(6,6);
#			   		print MAIL "Date: $date_str\n";
#			   		print MAIL "X-Priority: 1\n";
#			   		print MAIL "X-MSMail-Priority: High\n";
#			   		print MAIL "$cname has no suppression file\n"; 
#			   		print MAIL "Suppression id $suppid\n"; 
#			   		print MAIL "Sent from 3rdparty copy schedule\n"; 
#			   		close MAIL;
				}
	    		if ($stime == 12)
	    		{
	        		$stime = $stime - 12;
	    		}
	    		elsif ($stime == 24)
	    		{
	        		$stime = $stime - 12;
	    		}
	        	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($new_camp_id,date_add('$temp_date',interval $daycnt1 day),$network)";
	        	$rows=$dbhu->do($sql);
				$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=?";
				$sth1 = $dbhu->prepare($sql);
				$sth1->execute($new_camp_id);
				($cname,$sdate1) = $sth1->fetchrow_array();
				$sth1->finish();
	        	$sql="insert into 3rdparty_campaign(third_party_id,client_id,brand_id,advertiser_id,deploy_name,vsgID, campaign_id,scheduled_datetime,mta_id,priority,status) values($tparty,$network,$bid,$adv_id,'$cname','$vsgID', $new_camp_id,'$sdate1',$mta_id,$priority,'START')";
	        	$rows=$dbhu->do($sql);
				$sql = "insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id) values($network,$slot_id,'3',date_add('$temp_date',interval $daycnt1 day),$new_camp_id)";
				$rows = $dbhu->do($sql);
			}
			else
			{
				$disp_msg="One or more campaigns not copied because of exclusion with brands with articles";
			}
		}
		else
		{
           	my $company_name;
            $sql="select company from user where user_id=$client_id";
            my $sth6a = $dbhq->prepare($sql) ;
            $sth6a->execute();
            ($company_name) = $sth6a->fetchrow_array();
            $sth6a->finish();
   			$sql = "select campaign_name from campaign where campaign_id=$camp_id";
   			$sth1 = $dbhu->prepare($sql);
   			$sth1->execute();
			($cname) = $sth1->fetchrow_array();
			$sth1->finish();
            open (MAIL,"| /usr/sbin/sendmail -t");
            my $from_addr = "Campaign Excluded <info\@spirevision.com>";
            print MAIL "From: $from_addr\n";
            print MAIL "To: schedule\@spirevision.com\n";
            print MAIL "Subject: Campaign Excluded for $aname\n";
            my $date_str = $util->date(6,6);
            print MAIL "Date: $date_str\n";
            print MAIL "X-Priority: 1\n";
            print MAIL "X-MSMail-Priority: High\n";
            print MAIL "Advertiser: $aname Campaign: $cname excluded for $company_name\n";
            close MAIL;
		}
	}
	$sql = "select company from user where user_id=$network"; 
	my $sth2 = $dbhq->prepare($sql);
	$sth2->execute();
	($company) = $sth2->fetchrow_array();
	$sth2->finish();
	print MAIL1 "Successfully Copied $company\n";
}
$sth3->finish();
if ($disp_msg ne "")
{
	print MAIL1 "\n\n$disp_msg\n";
}
close(MAIL1);
exit(0);
