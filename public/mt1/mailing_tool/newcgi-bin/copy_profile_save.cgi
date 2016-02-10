#!/usr/bin/perl

# *****************************************************************************************
# copy_profile_save.cgi
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
my $list_id;
my $iopt;
my $rows;
my $errmsg;
my $campaign_id;
my $id;
my $campaign_name;
my $k;
my $cname;
my $status;
my $old_client_id;

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
my $tid= $query->param('tid');
my $client_id = $query->param('client_id');
#
# Check to see if another profile already setup for this client
#
#if ($tid > 0)
#{
#	my $reccnt;
#	$sql="select count(*) from list_profile where status='A' and third_party_id=$tid and client_id=$client_id";
#	$sth = $dbhq->prepare($sql);
#	$sth->execute();
#	($reccnt) = $sth->fetchrow_array();
#	$sth->finish();
#	if ($reccnt > 0)
#	{
#		my $mesg="There is already a profile defined for that Third Party Mailer for this client";
#		print "Location: /cgi-bin/copy_profile.cgi?pid=$pid&mesg=$mesg\n\n";
#		exit(0);
#	}
#}
#
# Create new profile
#
$sql="insert into list_profile(profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,status,max_emails,last_email_user_id,loop_flag,third_party_id,profile_type,profile_class,add_freq,add_new_month,clean_add,randomize_records,nl_id,nl_send,comcast_flag,open_clickers_ignore_date) select '$profile_name',$client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,'A',max_emails,0,loop_flag,$tid,profile_type,profile_class,add_freq,add_new_month,clean_add,randomize_records,nl_id,nl_send,comcast_flag,open_clickers_ignore_date from list_profile where profile_id=$pid"; 
my $rows=$dbhu->do($sql);
#
my $new_pid;
$sql="select profile_id from list_profile where profile_name='$profile_name' and client_id=$client_id and third_party_id=$tid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($new_pid) = $sth->fetchrow_array();
$sth->finish();
#
$sql="select client_id from list_profile where profile_id=$pid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($old_client_id) = $sth->fetchrow_array();
$sth->finish();
#
if ($old_client_id == $client_id)
{
	$sql="insert into list_profile_list(profile_id,list_id) select $new_pid,list_id from list_profile_list where profile_id=$pid"; 
	my $rows=$dbhu->do($sql);
}
else
{
	$sql="select list_name from list_profile_list,list where profile_id=$pid and list_profile_list.list_id=list.list_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	my $tname;
	while (($tname) = $sth->fetchrow_array())
	{
		$sql="select list_id from list where list_name=? and user_id=?";
		my $sth1a=$dbhq->prepare($sql);
		$sth1a->execute($tname,$client_id);
		my $tlist;
		if (($tlist) = $sth1a->fetchrow_array())
		{
			$sql="insert into list_profile_list(profile_id,list_id) values($new_pid,$tlist)";
			my $rows=$dbhu->do($sql);
		}
		$sth1a->finish();
	}
	$sth->finish();
}
print "Location: listprofile_list.cgi?tflag=3\n\n";
$util->clean_up();
exit(0);
