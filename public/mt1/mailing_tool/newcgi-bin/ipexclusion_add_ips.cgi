#!/usr/bin/perl
# *****************************************************************************************
# ipexclusion_add_ips.cgi
#
# this page adds IPs to a IpExclusionIps
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util= util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $dname;
my $gname;
my $IP;
my $comment;
my $ip;

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

# get fields from the form

my $gid= $query->param('gid');
my $gname= $query->param('gname');
$gname=~s/'/''/g;
if (($gid == 1) and ($user_id != 17) and ($user_id != 23))
{
	print "Location: ipexclusion_list.cgi\n\n";
	exit(0);
}
my $paste_ips = $query->param('paste_ips');
$sql="update IpExclusion set IpExclusion_name='$gname' where IpExclusionID=$gid";
$rows = $dbhu->do($sql);

$sql = "delete from IpExclusionIps where IpExclusionID=$gid";
$rows = $dbhu->do($sql);
$paste_ips=~ s/[ \n\r\f\t]/\|/g ;    
$paste_ips=~ s/\|{2,999}/\|/g ;           
my @ips_array = split '\|', $paste_ips;
my $list_ip;
my $cnt;
foreach $list_ip (@ips_array)
{
	$sql = "insert into IpExclusionIps(IpExclusionID,IpAddr) values ($gid,'$list_ip')";
	$rows = $dbhu->do($sql);
}
print "Location: ipexclusion_list.cgi\n\n";
$util->clean_up();
exit(0);
