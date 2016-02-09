#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_mdelte.cgi
#
# this page deletes multiple ClientGroups 
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

my $submit= $query->param('submit');
my $addclientid=$query->param('addclientid');
my @gid= $query->param('cdel');
my $i=0;
while ($i <= $#gid)
{
	if ($submit eq "Delete Multiple Groups")
	{
		$sql = "update ClientGroup set status='D' where client_group_id=$gid[$i]";
		$rows = $dbhu->do($sql);
		if ($dbhu->err() != 0)
		{
			$errmsg = $dbhu->errstr();
			util::logerror("Deleting ClientGroup record $sql: $errmsg");
			exit(0);
		}
	}
	else
	{
		$sql="insert ignore into ClientGroupClients(client_group_id,client_id) values($gid[$i],$addclientid)";
		$rows = $dbhu->do($sql);
	}
	$i++;
}
print "Location: clientgroup_list.cgi\n\n";
$pms->clean_up();
exit(0);
