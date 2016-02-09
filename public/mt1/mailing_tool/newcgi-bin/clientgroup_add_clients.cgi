#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_add_clients.cgi
#
# this page adds clients to a Client Group 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $dname;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# get fields from the form

my $gid= $query->param('gid');
my $gname = $query->param('gname');
my $excludeFromSuper = $query->param('excludeFromSuper');
if ($excludeFromSuper eq "")
{
	$excludeFromSuper="N";
}
$gname=~s/'/''/g;
my @clients= $query->param('sel2');
my $mcid= $query->param('mcid');
$mcid=~s/ //g;

$sql="update ClientGroup set group_name='$gname',excludeFromSuper='$excludeFromSuper'  where client_group_id=$gid";
$rows = $dbhu->do($sql);

my $i=0;
my $cstr="";
while ($i <= $#clients)
{
	$sql = "insert ignore into ClientGroupClients(client_group_id,client_id) values ($gid,$clients[$i])";
	$rows = $dbhu->do($sql);
	$cstr=$cstr.$clients[$i].",";
	$i++;
}
my @m1;
if ($mcid ne '')
{
    $mcid =~ s/[ \n\r\f\t]/\|/g ;
    $mcid =~ s/\|{2,999}/\|/g ;
    @m1= split '\|', $mcid;
}
$i=0;
my $cnt;
while ($i <= $#m1)
{
	$sql="select count(*) from user where user_id=? and status='A'";
	my $sth=$dbhu->prepare($sql);
	$sth->execute($m1[$i]);
	($cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($cnt > 0)
	{
		$sql = "insert ignore into ClientGroupClients(client_group_id,client_id) values ($gid,$m1[$i])";
		$rows = $dbhu->do($sql);
		$cstr=$cstr.$m1[$i].",";
	}
	$i++;
}
chop($cstr);
$sql = "delete from ClientGroupClients where client_group_id=$gid and client_id not in ($cstr)";
$rows = $dbhu->do($sql);
print "Location: clientgroup_list.cgi\n\n";
$pms->clean_up();
exit(0);
