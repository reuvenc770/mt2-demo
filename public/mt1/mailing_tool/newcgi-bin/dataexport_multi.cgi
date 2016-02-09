#!/usr/bin/perl
#===============================================================================
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $rows;
my $exid;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my @exarr=$query->param('exarr');
my $f=$query->param('submit');
my $showsupp=$query->param('showsupp');
my $exportType=$query->param('exportType');
if ($f eq "Pause")
{
	foreach $exid (@exarr)
	{
		$sql="update DataExport set status='Paused' where exportID=$exid and status='Active'"; 
		$rows=$dbhu->do($sql);
	}
}
elsif ($f eq "Re-pull Immediately")
{
	foreach $exid (@exarr)
	{
		$sql="update DataExport set lastUpdated=date_sub(curdate(),interval 1 day),serverID=0,pid=0 where exportID=$exid and status='Active'"; 
		$rows=$dbhu->do($sql);
	}
}
elsif ($f eq "Activate")
{
	foreach $exid (@exarr)
	{
		$sql="update DataExport set status='Active' where exportID=$exid and status!='Active'"; 
		$rows=$dbhu->do($sql);
	}
}
print "Location: /cgi-bin/dataexport_list.cgi?showshupp=$showsupp&exportType=$exportType\n\n";
