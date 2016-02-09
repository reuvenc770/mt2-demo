#!/usr/bin/perl
# ******************************************************************************
# camp_send_test_sm_save.cgi
#
# writes a record to test_emails
#
# History
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
my $rows;
my $errmsg;
my $user_id;
my $campaign_id = $query->param('campaign_id');
my $email_addr = $query->param('email_addr');
my $wiki= $query->param('wiki');
my $url= $query->param('url');
my $max_emails;
my $ip_addr;
my $aid;
my $brand_id;
my $creative_id;
my $subject_id;
my $subject;
my $server_id;
my $server_name;
my $vsgID;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

if ($email_addr ne "")
{
	$sql="select advertiser_id,brand_id from campaign where campaign_id=?";
	$sth=$dbhq->prepare($sql);
	$sth->execute($campaign_id);
	($aid,$brand_id)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select creative1_id,subject1 from advertiser_setup where advertiser_id=$aid and class_id=4";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($creative_id,$subject_id)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select advertiser_subject from advertiser_subject where subject_id=$subject_id";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($subject)=$sth->fetchrow_array();
	$sth->finish();

#	$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O'"; 
#	$sth=$dbhq->prepare($sql);
#	$sth->execute();
#	($url)=$sth->fetchrow_array();
#	$sth->finish();

	my $sql=qq^SELECT sc.id AS servID, sc.server, sic.ip FROM server_config sc, server_ip_config sic,brand_ip bi WHERE sc.id=sic.id AND inService=1 AND sc.type='strmail' AND sic.ip=bi.ip and bi.brandID=? and sic.ip not in (select ip from server_ip_failed) ORDER BY RAND() limit 1^;
	my $sthServ=$dbhq->prepare($sql);
	$sthServ->execute($brand_id);
	($server_id,$server_name,$vsgID)=$sthServ->fetchrow_array();
	$sthServ->finish();

	$sql="insert into test_strongmail(creative_id,subject,url,submit_datetime,brand_id,test_type,email_addr,campaign_id,servID,vsgID,wiki) values($creative_id,'$subject','$url',now(),$brand_id,'CAMPAIGN','$email_addr',$campaign_id,$server_id,'$vsgID','$wiki')";
	open(LOG,">/tmp/test.log");
	print LOG "<$sql>\n";
	close(LOG);
	my $rows=$dbhu->do($sql);
}
print "Location: mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
