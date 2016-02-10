#!/usr/bin/perl

# *****************************************************************************************
# ipgroupprofile_save.cgi
#
# this page saves to IpGroupProfile 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

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
my $cnt;
my %checked = ( 'on' => 'Y', '' => 'N' );

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


my $gid = $query->param('gid');
my $profileName= $query->param('name');
my $minnumberipgroup=$query->param('minnumberipgroup');
my $minipgroupsize=$query->param('minipgroupsize');
my $ptype=$query->param('ptype');
my $usebulkips=$query->param('usebulkips');
if ($usebulkips eq "")
{
	$usebulkips="N";
}
$sql="select count(*) from IpGroupProfile where IpProfileID != $gid and profileName='$profileName' and profileStatus='Active'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($cnt)=$sth->fetchrow_array();
$sth->finish();
if ($cnt > 0)
{
	print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h2>An Ip Group Profile with the name <b>$profileName</b> already exists.  Profile not added</h2>
</body>
</html>
end_of_html
exit();
}

if ($gid > 0)
{
	$sql="update IpGroupProfile set profileName='$profileName',minNumGroups=$minnumberipgroup,minIpGroupSize=$minipgroupsize,pType='$ptype',useBulkIps='$usebulkips' where IpProfileID=$gid";
	$rows=$dbhu->do($sql);
	$sql="delete from IpGroupProfileNode where IpProfileID=$gid";
	$rows=$dbhu->do($sql);
	$sql="delete from IpGroupProfileSeed where IpProfileID=$gid";
	$rows=$dbhu->do($sql);
}
else
{
	$sql="insert into IpGroupProfile(profileName,minNumGroups,minIpGroupSize,pType,useBulkIps,profileStatus) values('$profileName',$minnumberipgroup,$minipgroupsize,'$ptype','$usebulkips','Active')";
	$rows=$dbhu->do($sql);
	$sql="select LAST_INSERT_ID()";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($gid)=$sth->fetchrow_array();
	$sth->finish();
}
my $seeds=$query->param('seeds');
$seeds=~ s/[ \n\r\f\t]/\|/g ;
$seeds=~ s/\|{2,999}/\|/g ;
my @seeds_array = split '\|', $seeds;
foreach my $seed (@seeds_array)
{
	$sql="insert ignore into IpGroupProfileSeed(IpProfileID,emailAddr) values($gid,'$seed')";
	$rows=$dbhu->do($sql);
}

my $nodes=$query->param('nodes');
$nodes=~ s/[ \n\r\f\t]/\|/g ;
$nodes=~ s/\|{2,999}/\|/g ;
my @nodes_array = split '\|', $nodes;
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
my $params = {};
$params->{'active'}=1;

foreach my $node (@nodes_array)
{
	# Check to make sure specified IP is actually active node
	$params->{'managementIp'}=$node;
	my ($errors, $results) = $serverInterface->getNodeServers($params);
	my $cnt=$#{$results};
	if ($cnt >= 0)
	{
		$sql="insert ignore into IpGroupProfileNode(IpProfileID,ipNode) values($gid,'$node')";
		$rows=$dbhu->do($sql);
	}
}

print "Location: ipgroupprofile_list.cgi\n\n";
exit(0);

