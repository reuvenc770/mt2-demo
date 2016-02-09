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
my $tid= $query->param('tid');
my $client_id = $query->param('client_id');
#
# Check to see if another profile already setup for this client
#
#if ($tid > 0)
#{
#	my $reccnt;
#	$sql="select count(*) from list_profile where status='A' and third_party_id=$tid and client_id=$client_id";
#	$sth = $dbh->prepare($sql);
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
$sql="insert into list_profile(profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,status,max_emails,last_email_user_id,loop_flag,third_party_id) select '$profile_name',$client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag,'A',max_emails,0,loop_flag,$tid from list_profile where profile_id=$pid"; 
my $rows=$dbh->do($sql);
open(LOG,">/tmp/jim.a");
print LOG "<$sql>\n";
close(LOG);
#
my $new_pid;
$sql="select profile_id from list_profile where profile_name='$profile_name' and client_id=$client_id and third_party_id=$tid";
$sth = $dbh->prepare($sql);
$sth->execute();
($new_pid) = $sth->fetchrow_array();
$sth->finish();
#
$sql="insert into list_profile_list(profile_id,list_id) select $new_pid,list_id from list_profile_list where profile_id=$pid"; 
my $rows=$dbh->do($sql);
print "Location: listprofile_list.cgi?tflag=Y\n\n";
$util->clean_up();
exit(0);
