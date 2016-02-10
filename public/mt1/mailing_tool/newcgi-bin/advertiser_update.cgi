#!/usr/bin/perl

# *****************************************************************************************
# advertiser_update.cgi
#
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sth1;
my $sql;
my $rows;
my $s= $query->param('s');
my @chkbox= $query->param('chkbox');
my $function= $query->param('function');
my $find_str = $query->param('find_str');
my $replace_str = $query->param('replace_str');
my $the_rest;
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $varFld= $query->param('varFld');

foreach my $cid (@chkbox)
{
	if ($function eq "FIND")
	{
		$sql="update advertiser_info set $varFld=replace($varFld,'$find_str','$replace_str') where advertiser_id=$cid"; 
		$rows=$dbhu->do($sql);
		if ($varFld eq "auto_cake_creativeID")
		{
			my $auto_cake_creativeid;
			my $offer_type;
			$sql="select auto_cake_creativeID,offer_type from advertiser_info where advertiser_id=$cid";
			$sth=$dbhu->prepare($sql);
			$sth->execute();
			($auto_cake_creativeid,$offer_type)=$sth->fetchrow_array();
			$sth->finish();
			my @CID=split(',',$auto_cake_creativeid);
			my $redir_domain=$util->getConfigVal("CAKE_REDIR_DOMAIN");
			my $cake_offerID;

			$sql="select offerID from CakeCreativeOfferJoin where creativeID=$CID[0]";
			$sth=$dbhu->prepare($sql);
			$sth->execute();
			($cake_offerID)=$sth->fetchrow_array();
			$sth->finish();
			my $tpixel="http://".$redir_domain."/p.ashx?o=".$cake_offerID."&t=TRANSACTION_ID";
	       	$sql="update advertiser_info set hitpath_tracking_pixel='$tpixel' where advertiser_id=$cid";
	       	my $rows = $dbhu->do($sql);
	       	$sql="update advertiser_info set cake_creativeID=$CID[0] where advertiser_id=$cid";
	       	my $rows = $dbhu->do($sql);
			$sql="delete from advertiser_tracking where advertiser_id=$cid and daily_deal='N'";
	       	$rows = $dbhu->do($sql);
	
			my $cake_subaffiliateID;
			my $sidcnt=1;
			foreach my $tsid (@CID)
			{
				$sql = "select cakeAffiliateID,user_id,cakeSubAffiliateID from user where cakeSubAffiliateID != '' and cakeSubAffiliateID > 0 and cakeAffiliateID > 0 and status='A'";
				my $sth2 = $dbhq->prepare($sql);
				$sth2->execute();
				my $mid;
				my $client_id;
				while (($mid,$client_id,$cake_subaffiliateID) = $sth2->fetchrow_array())
				{
					my $temp_url="http://".$redir_domain."/?a=".$mid."&c=".$tsid."&s1=".$cake_subaffiliateID."&s2={{EMAIL_USER_ID}}_{{CRID}}_{{F}}_{{S}}_{{TID}}&s3={{EMAIL_ADDR}}&s4={{CID}}&s5={{BINDING_ID}}_{{MID}}_{{HEADER}}_{{FOOTER}}";
					if ($offer_type eq "CPC")
					{
						$temp_url.="&p=c";
					}
					my $lid;
					$sql="select max(link_id) from links where refurl='$temp_url'";
					$sth1 = $dbhq->prepare($sql) ;
					$sth1->execute();
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
					if ($lid > 0)
					{
					}
					else
					{
						$sql="insert into links(refurl,date_added) values('$temp_url',now())";
						$rows = $dbhu->do($sql);
						$sql="select max(link_id) from links where refurl='$temp_url'";
						$sth1 = $dbhu->prepare($sql) ;
						$sth1->execute();
						($lid) = $sth1->fetchrow_array();
						$sth1->finish();
					}
					#
					# Insert record into advertiser_tracking
					#
					$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($cid,'$temp_url','$mid',curdate(),$client_id,$lid,'N',$sidcnt)";
					$rows = $dbhu->do($sql);
				}
				$sth2->finish();
				$sidcnt++;
			}
			$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_id=$cid) where advertiser_id=$cid";
			$rows = $dbhu->do($sql);
		}
	}
	elsif ($function eq "Active")
	{
		$sql="update advertiser_info set status='A',inactive_date='0000-00-00',test_flag='N' where advertiser_id=$cid and status!='A'";
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Paused")
	{
		$sql="update advertiser_info set status='I',test_flag='P' where advertiser_id=$cid";
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Inactive")
	{
		my $dcnt;
		if (($find_str eq "0000-00-00") or ($find_str eq ""))
		{
			$sql="update advertiser_info set inactive_date='$find_str' where advertiser_id=$cid";
		}
		else
		{
        	$sql="select datediff(curdate(),'$find_str')"; 
        	$sth1 = $dbhq->prepare($sql);
        	$sth1->execute($cid);
        	($dcnt) = $sth1->fetchrow_array();
        	$sth1->finish;
			if ($dcnt >= 0)
			{
				$sql="update advertiser_info set status='I',inactive_date='$find_str',test_flag='N' where advertiser_id=$cid and (status!='I' or test_flag!='N')";
			}
			else
			{
				$sql="update advertiser_info set inactive_date='$find_str' where advertiser_id=$cid";
			}
		}
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Replace")
	{
		$sql="update advertiser_info set advertiser_name=replace(advertiser_name,'$find_str','$replace_str') where advertiser_id=$cid"; 
		$rows=$dbhu->do($sql);
	}
	elsif ($function eq "Add")
	{
		$sql="update advertiser_info set advertiser_name=concat('$find_str',advertiser_name) where advertiser_id=$cid"; 
		$rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/adv_findreplace_save.cgi?s=$s&varFld=$varFld\n\n";
