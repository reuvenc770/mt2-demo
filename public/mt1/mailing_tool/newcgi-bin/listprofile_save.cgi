#!/usr/bin/perl

# *****************************************************************************************
# listprofile_upd.cgi
#
# this page saves the list selection
#
# History
# Jim Sobeck, 09/25/2006	Added logic for master field in list_profile table
# Jim Sobeck, 11/30/2006	Added logic for newsletter type
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
my $list_id;
my $profile_type;
my $iopt;
my $rows;
my $errmsg;
my $old_profile_name;
my $campaign_id;
my $client_id;
my $id;
my $campaign_name;
my $k;
my $cname;
my $status;
my $aid;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $list_cnt;

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


my $pid = $query->param('pid');
my $profile_name = $query->param('profile_name');

my $clast60 = $query->param('clast60');

my $max_emails = $query->param('max_emails');
my $loop_flag = $query->param('loop_flag');
my $add_new_month = $query->param('add_new_month');
my $add_from=$query->param('add_from');
if ($add_from eq "")
{
	$add_from=0;
}
my $amount_to_add =$query->param('amount_to_add');
my $clean_add =$query->param('clean_add');
my $randomize_records=$query->param('randomize_records');
my $master_flag=$query->param('master_flag');
if ($amount_to_add eq "")
{
	$amount_to_add=0;
}
if ($clean_add eq "")
{
	$clean_add=0;
}
if ($randomize_records eq "")
{
	$randomize_records="N";
}
if ($master_flag eq "")
{
	$master_flag="N";
}
my $tid=$query->param('third_party_id');
my $unique_id=$query->param('unique_id');
my $percent_sub=$query->param('percent_sub');
my $dupes_flag=$query->param('dupes_flag');
if ($dupes_flag eq "")
{
	$dupes_flag="Both";
}
my $start_day=$query->param('start_day');
if ($start_day eq "")
{
	$start_day=0;
}
my $end_day=$query->param('end_day');
if ($end_day eq "")
{
	$end_day=0;
}
my $opener_start=$query->param('opener_start');
if ($opener_start eq "")
{
	$opener_start=0;
}
my $opener_end=$query->param('opener_end');
if ($opener_end eq "")
{
	$opener_end=0;
}
my $clicker_start=$query->param('clicker_start');
if ($clicker_start eq "")
{
	$clicker_start=0;
}
my $clicker_end=$query->param('clicker_end');
if ($clicker_end eq "")
{
	$clicker_end=0;
}
my $start_date=$query->param('start_date');
my $end_date=$query->param('end_date');

my @DOM = $query->param('domains');
my @CLIENTTYPE= $query->param('clienttype');
my $i=0;

my $aolflag 	= "N";
my $yahooflag 	= "N";
my $hotmailflag = "N";
my $otherflag 	= "N";
my $comcastflag = "N";

#set flags for list_profile
while ($i <= $#DOM) {
	
	if ($DOM[$i] == 1){
		 $aolflag 	= "Y";
	}
	
	if ($DOM[$i] == 2){
		$hotmailflag = "Y";
	}	
	
	if ($DOM[$i] == 3){
		$yahooflag 	= "Y";
		
	}	
	
	if ($DOM[$i] == 4){
		$otherflag 	= "Y";
	}	
	
	if ($DOM[$i] == 6){
		$comcastflag = "Y";
	}	

	$i++;
} #end while

#my $aolflag = $query->param('AOL');
#if ($aolflag eq "")
#{
#	$aolflag="N";
#}
#my $yahooflag = $query->param('Yahoo');
#if ($yahooflag eq "")
#{
#	$yahooflag = "N";
#}
#my $yahooflag1 = $query->param('yahooflag1');
#if ($yahooflag1 eq "")
#{
#	$yahooflag1 = "N";
#}
#if ($yahooflag1 eq "M")
#{
#	$yahooflag = "M";
#}
#my $hotmailflag = $query->param('Hotmail');
#if ($hotmailflag eq "")
#{
#	$hotmailflag = "N";
#}
#my $comcastflag = $query->param('Comcast');
#if ($comcastflag eq "")
#{
#	$comcastflag = "N";
#}

my $open_click_ignore= $query->param('open_click_ignore');
if ($open_click_ignore eq "")
{
	$open_click_ignore = "Y";
}
my $send_dmseeds= $query->param('send_dmseeds');
if ($send_dmseeds eq "")
{
	$send_dmseeds= "N";
}
my $dmseed_cnt= $query->param('dmseed_cnt');
if ($dmseed_cnt eq "")
{
	$dmseed_cnt=0;
}
my $otherflag = $query->param('otherflag');
if ($otherflag eq "")
{
	$otherflag = "N";
}
if ($loop_flag eq "")
{
	$loop_flag = "N";
}
if ($add_new_month eq "")
{
	$add_new_month = "Y";
}
my $add_freq=$query->param('add_freq');
if ($add_freq eq "")
{
	$add_freq="DAILY";
}
my $nl_id=$query->param('nl_id');
if ($nl_id eq "")
{
	$nl_id=0;
}
my $nl_send=$query->param('nl_send');
if ($nl_send eq "")
{
	$nl_send="ALL";
}
my $nl_update=$query->param('nl_update');
#
# Get the old profile name for newsletter
#
$sql="select profile_name from list_profile where profile_id=$pid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($old_profile_name) = $sth->fetchrow_array();
$sth->finish();

## new profile_class - jp Fri May 12 12:40:21 EDT 2006
my $prof_class=0;

if ($aolflag eq 'Y' ) {
	$prof_class+=1 
}
elsif ($hotmailflag eq 'Y' ) {
	$prof_class+=2 
}

elsif ($otherflag eq 'Y' ) {
	$prof_class+=4 	
}
elsif ($yahooflag eq 'Y' ) {
	$prof_class+=8 
}
	
elsif ($yahooflag eq 'M' ) {
	$prof_class+=8 
}

elsif ($comcastflag eq 'Y' ) {
	$prof_class+=16 	
}

#all other ISPs 
else {
	$prof_class+=4 	
}

#delete all profiles and just add checked ones
$sql = "delete from list_profile_domain where profile_id = $pid"; 
$dbhu->do($sql);

my $i=0;
while ($i <= $#DOM) {
		$sql = "insert into list_profile_domain values($pid, $DOM[$i])"; 
		$dbhu->do($sql);
		$i++;
} #end while

$sql = "update list_profile set profile_name='$profile_name',day_flag='$clast60',
aol_flag='$aolflag',yahoo_flag='$yahooflag',other_flag='$otherflag',
hotmail_flag='$hotmailflag',max_emails=$max_emails,loop_flag='$loop_flag',
third_party_id=$tid,list_to_add_from=$add_from,amount_to_add=$amount_to_add,
unique_id=$unique_id,percent_sub=$percent_sub, profile_class='$prof_class',
add_freq='$add_freq',add_new_month='$add_new_month',clean_add='$clean_add',
randomize_records='$randomize_records',nl_id=$nl_id,nl_send='$nl_send',comcast_flag='$comcastflag',open_clickers_ignore_date='$open_click_ignore',send_dmseeds='$send_dmseeds',dmseed_cnt=$dmseed_cnt,start_day=$start_day,end_day=$end_day,dupes_flag='$dupes_flag',start_date='$start_date',end_date='$end_date',opener_start=$opener_start,opener_end=$opener_end,clicker_start=$clicker_start,clicker_end=$clicker_end where profile_id=$pid"; 
	$rows = $dbhu->do($sql);

	$sql="select profile_type,client_id from list_profile where profile_id=$pid";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($profile_type,$client_id) = $sth->fetchrow_array();
	$sth->finish();
	
	if ($client_id == 276)
	{
		$sql="delete from ListProfileClientType where profile_id=$pid";
		$rows=$dbhu->do($sql);
		$sql="delete from ListProfileClient where profile_id=$pid";
		$rows=$dbhu->do($sql);
		my $i=0;
		while ($i <= $#CLIENTTYPE)
		{
			$sql="insert into ListProfileClientType(profile_id,client_type) values($pid,'$CLIENTTYPE[$i]')"; 
			$rows = $dbhu->do($sql);
			$i++;
		}
		my @clients= $query->param('sel2');
		my $i=0;
		while ($i <= $#clients)
		{
    		$sql = "insert into ListProfileClient(profile_id,client_id) values ($pid,$clients[$i])";
    		$rows = $dbhu->do($sql);
    		$i++;
		}
	}
	

#
# check to see if another master profile for this client already
#
	if ($master_flag eq "Y")
	{
		$sql="select profile_id from list_profile where profile_id != $pid and status='A' and client_id=$client_id and master='Y'";
		$sth = $dbhu->prepare($sql);
		$sth->execute();
		my $temp_id;
		if (($temp_id) = $sth->fetchrow_array())
		{
		}
		else
		{
			$sql="update list_profile set master='Y' where profile_id=$pid";
			$rows=$dbhu->do($sql);
		}
		$sth->finish();
	}
	else
	{
		$sql="update list_profile set master='N' where profile_id=$pid";
		$rows=$dbhu->do($sql);
	}

	if ($profile_type eq 'CHUNK')
	{
		$sql = "delete from profile_chunk_domain where profile_id=$pid";
		$rows=$dbhu->do($sql);

		my @chunkits = $query->param('chunkdomain');
		foreach my $chunkit (@chunkits)
		{
			$sql="insert into profile_chunk_domain(profile_id,domain_id) values($pid,$chunkit)";
			$rows=$dbhu->do($sql);
		}
	}

	# Update lists of profile 
	# read all lists for this user to check for the checkbox field checked
	# on the previous screen.  If they are checked, add to list_profile_list table
	$sql = "delete from list_profile_list where profile_id=$pid";
	$rows = $dbhu->do($sql);

	$sql = "select list_id from list where status='A' and user_id in (select client_id from list_profile where profile_id=$pid)";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($list_id) = $sth->fetchrow_array())
	{
    	$iopt = $query->param("list_$list_id");
    	if ($iopt)
    	{
			$sql = "insert into list_profile_list (profile_id, list_id) values ($pid,$list_id)";
			$rows = $dbhu->do($sql);
			if ($dbhu->err() != 0)
			{
				$errmsg = $dbhu->errstr();
				util::logerror("Inserting list_profile_list record for $list_id: $errmsg");
				exit(0);
			}
		}
	}
	$sth->finish();
if (($profile_type eq "NEWSLETTER") && ($nl_update eq "Y"))
{
	$sql = "update list_profile set profile_name='$profile_name',day_flag='$clast60',aol_flag='$aolflag',yahoo_flag='$yahooflag',other_flag='$otherflag',hotmail_flag='$hotmailflag',max_emails=$max_emails,loop_flag='$loop_flag',third_party_id=$tid,list_to_add_from=$add_from,amount_to_add=$amount_to_add,unique_id=$unique_id,percent_sub=$percent_sub, profile_class='$prof_class',add_freq='$add_freq',add_new_month='$add_new_month',clean_add='$clean_add',randomize_records='$randomize_records',nl_id=$nl_id,nl_send='$nl_send',comcast_flag='$comcastflag',open_clickers_ignore_date='$open_click_ignore',send_dmseeds='$send_dmseeds',dmseed_cnt=$dmseed_cnt where profile_type='NEWSLETTER' and profile_name='$old_profile_name'"; 
	$rows = $dbhu->do($sql);

	$sql="delete from list_profile_domain where profile_id in (select profile_id from list_profile where profile_type='NEWSLETTER' and profile_name='$old_profile_name')";
	$rows = $dbhu->do($sql);
	my @DOM=$query->param('domains');
	my $i=0;
	while ($i <= $#DOM)
	{
		$sql="insert into list_profile_domain(profile_id,domain_id) select profile_id,$DOM[$i] from list_profile where profile_type='NEWSLETTER' and profile_name='$old_profile_name'";
		$rows = $dbhu->do($sql);
		$i++;
	}
}
elsif ($profile_type eq "NEWSLETTER")
{
	$sql="delete from list_profile_domain where profile_id=$pid"; 
	$rows = $dbhu->do($sql);
	my @DOM=$query->param('domains');
	my $i=0;
	while ($i <= $#DOM)
	{
		$sql="insert into list_profile_domain(profile_id,domain_id) values($pid,$DOM[$i])"; 
		$rows = $dbhu->do($sql);
		$i++;
	}
}
 
if (($tid > 0) && ($profile_type eq "3RDPARTY"))
{
	# Remove all third party transfer records for this profile
	#
	$sql="delete from third_party_transfer where profile_id=$pid and list_id not in (select list_id from list_profile_list where profile_id=$pid)";
	$rows = $dbhu->do($sql);
	print "Location: listprofile_list.cgi?tflag=3\n\n";
}
else
{
	if ($profile_type eq "CHUNK")
	{
		print "Location: listprofile_list.cgi?tflag=C\n\n";
	}
	elsif ($profile_type eq "NEWSLETTER")
	{
		print "Location: listprofile_list.cgi?tflag=L\n\n";
	}
	else
	{
		print "Location: listprofile_list.cgi\n\n";
	}
}
$util->clean_up();
exit(0);

