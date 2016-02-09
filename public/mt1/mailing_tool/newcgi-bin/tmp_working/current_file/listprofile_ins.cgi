#!/usr/bin/perl
# *****************************************************************************************
# listprofile_ins.cgi
#
# this page writes a record to the list_profile table 
#
# History
# Jim Sobeck, 5/31/05, Creation
# *****************************************************************************************

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
my $errmsg;
my $user_id;
my $list_name;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $network_id = $query->param('network_id');
my $profilename = $query->param('profilename');
my $clast60 = $query->param('clast60');
my $aolflag = $query->param('aolflag');
if ($aolflag eq "")
{
	$aolflag = "N";
}
my $yahooflag = $query->param('yahooflag');
if ($yahooflag eq "")
{
	$yahooflag = "N";
}
my $yahooflag1 = $query->param('yahooflag1');
if ($yahooflag1 eq "M")
{
	$yahooflag = "M";
}
my $otherflag = $query->param('otherflag');
if ($otherflag eq "")
{
	$otherflag = "N";
}
my $hotmailflag = $query->param('hotmailflag');
if ($hotmailflag eq "")
{
	$hotmailflag = "N";
}
$sql = "insert into list_profile(profile_name,client_id,day_flag,aol_flag,yahoo_flag,other_flag,hotmail_flag) values('$profilename',$network_id,'$clast60','$aolflag','$yahooflag','$otherflag','$hotmailflag')";
my $rows=$dbh->do($sql);
#
#	 Get the id just added
#
my $profile_id;
$sql = "select max(profile_id) from list_profile where profile_name='$profilename' and client_id=$network_id";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($profile_id) = $sth->fetchrow_array();
$sth->finish();
print "Location: /cgi-bin/listprofile_edit.cgi?pid=$profile_id\n\n";
