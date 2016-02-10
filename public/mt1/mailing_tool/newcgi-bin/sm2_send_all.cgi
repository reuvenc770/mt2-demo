#!/usr/bin/perl

# *****************************************************************************
# sm2_send_all.cgi
#
# this page inserts records into test_campaign 
#
# History
# Jim Sobeck, 05/08/08, Creation
# ******************************************************************************

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
my $sid;
my $rows;
my $errmsg;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
$user_id=1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $camp_type="TEST";
my $email=$query->param('email');;
my $copies = 1; 
my $cname;
my $adv_id=305;
my $creative = 17025; 
my $wiki = $query->param('wiki'); 
my $template_id= $query->param('template_id');
my $headerid= $query->param('headerid');
my $selectall= $query->param('selectall');
if ($selectall eq "")
{
	$selectall="N";
}
my @brandid = $query->param('brandid');
my $cdate;
my $bid;
my $bname;
my $cid;
my $ip;
my $tid;
my $domain;
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<center>
end_of_html
if ($selectall eq "Y")
{
	$sql="select brand_id,brand_name,client_id,curdate() from client_brand_info where status='A' and third_party_id=10 and brand_type='3rd Party'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($bid,$bname,$cid,$cdate)=$sth->fetchrow_array())
	{
		$sql="select ip FROM brand_ip where brandID=? and ip not in (select ip from server_ip_failed) ORDER BY RAND()";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($bid);
		if (($ip)=$sth1->fetchrow_array())
		{
			$sql="select url from brand_url_info where brand_id=? and url_type='O' order by rand()"; 
			my $sth2=$dbhu->prepare($sql);
			$sth2->execute($bid);
			($domain)=$sth2->fetchrow_array();
			$sth2->finish();
#			$email=$bname."_".$ip."\@leaddimension.com";
			$cname="ALL Test ".$bname."(".$ip.")"; 
			$sql="insert into test_campaign(userID, campaign_type,status,campaign_id,email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID) values($user_id, '$camp_type','START',0,'$email',$copies,$cid,$bid,'$domain','$ip','$cname',$adv_id,$creative,0,0,$template_id,'$wiki',curdate(),$headerid)";
			my $rows=$dbhu->do($sql);
			$sql="select max(test_id) from test_campaign where campaign_name='$cname' and campaign_type='$camp_type'";
			$sth2=$dbhu->prepare($sql);
			$sth2->execute();
			($tid)=$sth2->fetchrow_array();
			$sth2->finish();
			print "<h2>Campaign <b>$cname</b>($tid) has been scheduled to be sent.</h2>\n";
		}
	}
}
else
{
    foreach my $bid (@brandid)
    {
		$sql="select brand_name,client_id,curdate() from client_brand_info where brand_id=?"; 
		$sth=$dbhu->prepare($sql);
		$sth->execute($bid);
		if (($bname,$cid,$cdate)=$sth->fetchrow_array())
		{
			$sql="select ip FROM brand_ip where brandID=? and ip not in (select ip from server_ip_failed) ORDER BY RAND()";
			my $sth1=$dbhu->prepare($sql);
			$sth1->execute($bid);
			if (($ip)=$sth1->fetchrow_array())
			{
				$sql="select url from brand_url_info where brand_id=? and url_type='O' order by rand()"; 
				my $sth2=$dbhu->prepare($sql);
				$sth2->execute($bid);
				($domain)=$sth2->fetchrow_array();
				$sth2->finish();
#			$email=$bname."_".$ip."\@leaddimension.com";
				$cname="ALL Test ".$bname."(".$ip.")"; 
				$sql="insert into test_campaign(userID, campaign_type,status,campaign_id,email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID) values($user_id, '$camp_type','START',0,'$email',$copies,$cid,$bid,'$domain','$ip','$cname',$adv_id,$creative,0,0,$template_id,'$wiki',curdate(),$headerid)";
				my $rows=$dbhu->do($sql);
				$sql="select max(test_id) from test_campaign where campaign_name='$cname' and campaign_type='$camp_type'";
				$sth2=$dbhu->prepare($sql);
				$sth2->execute();
				($tid)=$sth2->fetchrow_array();
				$sth2->finish();
				print "<h2>Campaign <b>$cname</b>($tid) has been scheduled to be sent.</h2>\n";
			}
		}
	}
}
print "<br>";
print "<a href=\"/sm2_build_test.html\">Add Another Strongmail Test</a>&nbsp;&nbsp;&nbsp;<a href=\"sm2_list.cgi\">Home</a>\n";
print<<"end_of_html";
</center>
</body></html>
end_of_html
$util->clean_up();
exit(0);
