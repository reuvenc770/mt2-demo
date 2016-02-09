#!/usr/bin/perl

# *****************************************************************************************
# listprofile_upd.cgi
#
# this page saves the list selection
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
my $aid;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $list_cnt;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

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
my $aolflag = $query->param('aolflag');
my $max_emails = $query->param('max_emails');
my $loop_flag = $query->param('loop_flag');
my $add_from=$query->param('add_from');
my $amount_to_add =$query->param('amount_to_add');
if ($amount_to_add eq "")
{
	$amount_to_add=0;
}
my $tid=$query->param('third_party_id');
my $unique_id=$query->param('unique_id');
if ($aolflag eq "")
{
	$aolflag="N";
}
my $yahooflag = $query->param('yahooflag');
if ($yahooflag eq "")
{
	$yahooflag = "N";
}
my $yahooflag1 = $query->param('yahooflag1');
if ($yahooflag1 eq "")
{
	$yahooflag1 = "N";
}
if ($yahooflag1 eq "M")
{
	$yahooflag = "M";
}
my $hotmailflag = $query->param('hotmailflag');
if ($hotmailflag eq "")
{
	$hotmailflag = "N";
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
#
# Check to see if another profile already setup for this client
#
#if ($tid > 0)
#{
#	my $reccnt;
#	$sql="select count(*) from list_profile where status='A' and profile_id != $pid and third_party_id=$tid and client_id=(select client_id from list_profile where profile_id=$pid)";
#	$sth = $dbh->prepare($sql);
#	$sth->execute();
#	($reccnt) = $sth->fetchrow_array();
#	$sth->finish();
#	if ($reccnt > 0)
#	{
#		my $mesg="There is already a profile defined for that Third Party Mailer for this client";
#		print "Location: /cgi-bin/listprofile_edit.cgi?pid=$pid&mesg=$mesg\n\n";
#		exit(0);
#	}
#}
$sql = "update list_profile set profile_name='$profile_name',day_flag='$clast60',aol_flag='$aolflag',yahoo_flag='$yahooflag',other_flag='$otherflag',hotmail_flag='$hotmailflag',max_emails=$max_emails,loop_flag='$loop_flag',third_party_id=$tid,list_to_add_from=$add_from,amount_to_add=$amount_to_add,unique_id=$unique_id where profile_id=$pid"; 
	$rows = $dbh->do($sql);

	# Update lists of profile 
	# read all lists for this user to check for the checkbox field checked
	# on the previous screen.  If they are checked, add to list_profile_list table
	$sql = "delete from list_profile_list where profile_id=$pid";
	$rows = $dbh->do($sql);

	$sql = "select list_id from list where status='A' and user_id in (select client_id from list_profile where profile_id=$pid)";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($list_id) = $sth->fetchrow_array())
	{
    	$iopt = $query->param("list_$list_id");
    	if ($iopt)
    	{
			$sql = "insert into list_profile_list (profile_id, list_id) values ($pid,$list_id)";
			$rows = $dbh->do($sql);
			if ($dbh->err() != 0)
			{
				$errmsg = $dbh->errstr();
				util::logerror("Inserting list_profile_list record for $list_id: $errmsg");
				exit(0);
			}
		}
	}
	$sth->finish();
print "Location: listprofile_list.cgi\n\n";
$util->clean_up();
exit(0);
