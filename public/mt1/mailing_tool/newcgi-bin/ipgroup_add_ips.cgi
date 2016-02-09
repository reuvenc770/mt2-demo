#!/usr/bin/perl
# *****************************************************************************************
# ipgroup_add_ips.cgi
#
# this page adds IPs to a Ip Group 
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

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});
my $externalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

# get fields from the form

my $gid= $query->param('gid');
my $gname = $query->param('gname');
my $othrottle= $query->param('othrottle');
my $goodmail_enabled = $query->param('goodmail_enabled');
my $colo= $query->param('colo');
my $chunk= $query->param('chunk');
if ($chunk eq "")
{
	$chunk=0;
}
my $paste_ips = $query->param('paste_ips');
if ($othrottle eq "")
{
	$othrottle=0;
}
my @ips= $query->param('sel2');
my $cnt;
$sql="select count(*) from IpGroup where $userDataRestrictionWhereClause group_name=? and group_id != $gid and status='A'";
$sth=$dbhu->prepare($sql);
$sth->execute($gname);
($cnt)=$sth->fetchrow_array();
$sth->finish();
if ($cnt > 0)
{
	util::logerror("Group Name $gname already exists");
    exit(0);
}

$sql="update IpGroup set group_name='$gname',outbound_throttle=$othrottle,goodmail_enabled='$goodmail_enabled',colo='$colo',chunk=$chunk where $userDataRestrictionWhereClause group_id=$gid";
$rows = $dbhu->do($sql);

$sql = "delete from IpGroupIps where group_id=(select group_id from IpGroup where $userDataRestrictionWhereClause group_id=$gid limit 1)";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#ips)
{
	if($externalUser)
	{
	    $sql = qq|
        insert into
        IpGroupIps(group_id,ip_addr)
        values
        (
            (select group_id from IpGroup where $userDataRestrictionWhereClause group_id=$gid limit 1),
            (select ip from IpAttribute where $userDataRestrictionWhereClause ip='$ips[$i]' limit 1)
        )
    |;
	}
	else
	{
	    $sql = qq|
        insert into
        IpGroupIps(group_id,ip_addr)
        values
        (
            $gid,
            '$ips[$i]'
        )
    |;
	}
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
	if($externalUser)
	{
		$sql="select count(*) from ServerIp where ipRoleID=2 and ipStatusID=14 and ip=(select ip from IpAttribute where $userDataRestrictionWhereClause ip=? limit 1)";
	}
	else
	{
		$sql="select count(*) from ServerIp where ipRoleID=2 and ipStatusID=14 and ip=?";		
	}	 
	my $sth=$dbhu->prepare($sql);
	$sth->execute($list_ip);
	($cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($cnt > 0)
	{
		if($externalUser)
		{
			$sql = "insert ignore into IpGroupIps(group_id,ip_addr) values ((select group_id from IpGroup where $userDataRestrictionWhereClause group_id=$gid limit 1), (select ip from IpAttribute where $userDataRestrictionWhereClause ip='$list_ip' limit 1))";
		}
		else
		{
			$sql = "insert ignore into IpGroupIps(group_id,ip_addr) values ($gid, '$list_ip')";
		}
		$rows = $dbhu->do($sql);
	}
}
$sql="select group_name from IpGroup where $userDataRestrictionWhereClause group_id=?";
my $sth=$dbhu->prepare($sql);
$sth->execute($gid);
($gname)=$sth->fetchrow_array();
$sth->finish();

open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "IP Group Added/Changed<info\@zetainteractive.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: sysadmin.nyc\@zetainteractive.com\n";
print MAIL "Subject: IP Group $gname Added/Changed\n";
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";
print MAIL "X-Priority: 1\n";
print MAIL "X-MSMail-Priority: High\n";
print MAIL "IP Group $gname added/changed\n";
close MAIL;
print "Location: ipgroup_list.cgi\n\n";
$util->clean_up();
exit(0);
