#!/usr/bin/perl
# ******************************************************************************
# camp_send_nl_test_save.cgi
#
# writes a record to test_strongmail 
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
my $TEST_CID=182;
my $nl_id= $query->param('nl_id');
my $email_addr = $query->param('email_addr');
my $max_emails;
my $ip_addr;
my $aid;
my $brand_id;
my $creative_id;
my $subject_id;
my $subject;
my $url;
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
	$sql="select advertiser_id from creative where creative_id=?";
	$sth=$dbhq->prepare($sql);
	$sth->execute($TEST_CID);
	($aid)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select brand_id from client_brand_info where nl_id=? and client_id=64 and status='A'";
	$sth=$dbhq->prepare($sql);
	$sth->execute($nl_id);
	($brand_id)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select subject1 from advertiser_setup where advertiser_id=$aid and class_id=4";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($subject_id)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select advertiser_subject from advertiser_subject where subject_id=$subject_id";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($subject)=$sth->fetchrow_array();
	$sth->finish();

	$sql="select url from brand_url_info where brand_id=$brand_id and url_type='O'"; 
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($url)=$sth->fetchrow_array();
	$sth->finish();

	my $sql=qq^SELECT sc.id AS servID, sc.server, sic.ip FROM server_config sc, server_ip_config sic, brand_ip bi WHERE sc.id=sic.id AND inService=1 AND sc.type='strmail' AND sic.ip=bi.ip and bi.brandID=? and sic.ip not in (select ip from server_ip_failed) ORDER BY RAND() limit 1^;
	my $sthServ=$dbhq->prepare($sql);
	$sthServ->execute($brand_id);
	($server_id,$server_name,$vsgID)=$sthServ->fetchrow_array();
	$sthServ->finish();

	$sql="insert into test_strongmail(creative_id,subject,url,submit_datetime,brand_id,test_type,email_addr,campaign_id,servID,vsgID,nl_id) values($TEST_CID,'$subject','$url',now(),$brand_id,'NEWSLETTER','$email_addr',0,$server_id,'$vsgID',$nl_id)";
	open(LOG,">/tmp/s.s");
	print LOG "<$sql>\n";
	close(LOG);
	my $rows=$dbhu->do($sql);
}
print "Location: mainmenu.cgi\n\n";
$util->clean_up();
exit(0);
