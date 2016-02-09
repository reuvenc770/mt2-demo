#!/usr/bin/perl
#-----  include Perl Modules ---------
use strict;
use lib "/var/www/html/newcgi-bin";
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $sql;
my $sth;
my $dbh;
my $rows;
my $tid;
my $newtid;
my $maintestID;
my $MAINID;
my $newmainTestID;
#------  connect to the util database -----------
my $dbhu=DBI->connect('DBI:mysql:new_mail:masterdb.i.routename.com', 'db_user', 'sp1r3V');

$sql="select test_id,mainTestID from test_campaign where campaign_type='SENDALL' and send_date=curdate() and schedule_everyday='Y' order by mainTestID"; 
$sth = $dbhu->prepare($sql);
$sth->execute();
while (($tid,$maintestID) = $sth->fetchrow_array())
{
	$newmainTestID=0;
	if ($maintestID > 0)
	{
		$newmainTestID=$MAINID->{$maintestID};
	}
	$sql="insert into test_campaign(status,email_addr,copies_to_send,mailing_domain,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,server_id,send_time,permutation_flag,mainTestID,encrypt_link,split_emails,trace_header_id,mail_from,isoConvertSubject,isoConvertFrom,SeedGroupID,batchSize,waitTime,IpExclusionID,campaign_type,schedule_everyday) select 'START',email_addr,copies_to_send,mailing_domain,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,date_add(curdate(),interval 1 day),mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,server_id,send_time,permutation_flag,$newmainTestID,encrypt_link,split_emails,trace_header_id,mail_from,isoConvertSubject,isoConvertFrom,SeedGroupID,batchSize,waitTime,IpExclusionID,campaign_type,schedule_everyday from test_campaign where test_id=$tid"; 
	$rows=$dbhu->do($sql);
    $sql="select last_insert_id()";
    my $sth1=$dbhu->prepare($sql);
    $sth1->execute();
    ($newtid)=$sth1->fetchrow_array();
    $sth1->finish();
	print "Added $newtid\n";
	if ($maintestID == 0)
	{
		$MAINID->{$tid}=$newtid;
	}
	$sql="insert into SendAllTestDomain(test_id,mailing_domain) select $newtid,mailing_domain from SendAllTestDomain where test_id=$tid";
	$rows=$dbhu->do($sql);
	$sql="insert into SendAllTestIp(test_id,ip_addr) select $newtid,ip_addr from SendAllTestIp where test_id=$tid";
	$rows=$dbhu->do($sql);
}
$sth->finish();
