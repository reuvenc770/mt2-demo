#!/usr/bin/perl

#******************************************************************************
# unique_replace_usa_save.cgi
#
# this page updates USA for deploy 
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
my $aid;
my $rows;
my $errmsg;
my $tracking_id;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my $uidstr = $query->param('uidstr');
my $gsm= $query->param('gsm');
my $sord= $query->param('sord');
my $usaid= $query->param('usaid');
my @U=split('\|',$uidstr);
$sql="select advertiser_id from UniqueScheduleAdvertiser where usa_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($usaid);
($aid)=$sth->fetchrow_array();
$sth->finish();

foreach my $uid (@U)
{
	# check to see if uid is active or not
	my $cstatus;
	$sql="select status from unique_campaign where unq_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($uid);
	($cstatus)=$sth->fetchrow_array();
	$sth->finish();

	if (($cstatus eq "START") or ($cstatus eq "PENDING") or ($cstatus eq "PRE-PULLING") or ($cstatus eq "PAUSED") or ($cstatus eq "INJECTING"))
	{
		$sql="update campaign set advertiser_id=$aid  where id='$uid'"; 
		$rows=$dbhu->do($sql);
		$sql="update unique_campaign set advertiser_id=$aid where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="delete from UniqueCreative where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueCreative select $uid,creative_id,rowID from UniqueAdvertiserCreative where usa_id=$usaid";
		$rows=$dbhu->do($sql);
		$sql="delete from UniqueSubject where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueSubject select $uid,subject_id,rowID from UniqueAdvertiserSubject where usa_id=$usaid";
		$rows=$dbhu->do($sql);
		$sql="delete from UniqueFrom where unq_id=$uid";
		$rows=$dbhu->do($sql);
		$sql="insert into UniqueFrom select $uid,from_id,rowID from UniqueAdvertiserFrom where usa_id=$usaid";
		$rows=$dbhu->do($sql);
		if ($cstatus eq "INJECTING")
		{
			$sql="update unique_campaign set status='RESUME',pause_flag=1 where unq_id=$uid";
			$rows=$dbhu->do($sql);
		}
	}
}

print "Location: /cgi-bin/unique_deploy_list.cgi?gsm=$gsm&sord=$sord\n\n";
