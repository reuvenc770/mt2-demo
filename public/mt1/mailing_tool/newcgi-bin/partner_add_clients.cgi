#!/usr/bin/perl
# *****************************************************************************************
# partner_add_clients.cgi
#
# this page adds clients to a Partner 
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

my $partner_id= $query->param('partner_id');
my $delay= $query->param('delay');
my $dupes= $query->param('dupes');
my $byPass = $query->param('byPass');
$delay=$delay*3600;
my @clients= $query->param('sel2');
my $mcid= $query->param('mcid');
$mcid=~s/ //g;

$sql = "update PartnerInfo set dupes_only='$dupes',byPass='$byPass' where partner_id=$partner_id";
$rows = $dbhu->do($sql);
$sql = "delete from PartnerClientInfo where partner_id=$partner_id";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#clients)
{
	$sql = "insert into PartnerClientInfo(partner_id,client_id,delay) values ($partner_id,'$clients[$i]',$delay)";
	$rows = $dbhu->do($sql);
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
		$sql = "insert into PartnerClientInfo(partner_id,client_id,delay) values ($partner_id,$m1[$i],$delay)";
		$rows = $dbhu->do($sql);
	}
	$i++;
}
print "Location: partner_list.cgi\n\n";
$pms->clean_up();
exit(0);
