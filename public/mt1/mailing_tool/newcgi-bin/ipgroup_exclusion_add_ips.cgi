#!/usr/bin/perl
# *****************************************************************************************
# ipgroup_exclusion_add_ips.cgi
#
# this page adds IPs to a IpGroupExclusion 
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

my $paste_ips = $query->param('paste_ips');
my @ips= $query->param('sel2');
$sql="select ip,comments from IpGroupExclusion";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($ip,$comment)=$sth->fetchrow_array())
{
	$IP->{$ip}=$comment;
}
$sth->finish();
$sql = "delete from IpGroupExclusion";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#ips)
{
	$comment=$IP->{$ips[$i]};
	$comment=~s/'/''/g;
	$sql = "insert into IpGroupExclusion(ip,comments) values ('$ips[$i]','$comment')";
	$rows = $dbhu->do($sql);
	$i++;
}
$paste_ips=~ s/[ \n\r\f\t]/\|/g ;    
$paste_ips=~ s/\|{2,999}/\|/g ;           
my @ips_array = split '\|', $paste_ips;
my $list_ip;
my $cnt;
foreach $list_ip (@ips_array)
{
	$sql="select count(*) from ServerIp where ipRoleID=2 and ipStatusID=14 and ip=?"; 
	my $sth=$dbhu->prepare($sql);
	$sth->execute($list_ip);
	($cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($cnt > 0)
	{
		$comment="";
		if ($IP->{$list_ip})
		{
			$comment=$IP->{$list_ip};
		}
		$comment=~s/'/''/g;
		$sql = "insert into IpGroupExclusion(ip,comments) values ('$list_ip','$comment')";
		$rows = $dbhu->do($sql);
	}
}
print "Location: ipgroup_exclusion.cgi\n\n";
$util->clean_up();
exit(0);
