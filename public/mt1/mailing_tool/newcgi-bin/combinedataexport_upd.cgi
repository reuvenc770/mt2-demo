#!/usr/bin/perl
# *****************************************************************************************
# combinedataexport_upd.cgi
#
# this page adds a updates DataExportCombine
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
my $fields="";

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
my $pid= $query->param('pid');
my $filename= $query->param('pname');
my $ftpFolder= $query->param('ftpFolder');
if ($filename eq "")
{
	util::logerror("Filename cannot be blank");
    $pms->clean_up();
    exit(0);
}
my @F= $query->param('efile');
my $fieldsToExport="";
my $tempstr;
foreach my $fld (@F)
{
	if ($fld > 0)
	{
		$sql="select fieldsToExport from DataExport where exportID=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($fld);
		($tempstr)=$sth->fetchrow_array();
		$sth->finish();
		if ($fieldsToExport eq "")
		{
			$fieldsToExport=$tempstr;
		}
		elsif ($fieldsToExport ne $tempstr)
		{
			util::logerror("Fields To Export Do Not Match '$fieldsToExport' '$tempstr'<br>Combine not updated or saved.");
	    	$pms->clean_up();
	    	exit(0);
		}
	}
}

if ($pid > 0)
{
	$sql = "update DataExportCombine set fileName='$filename',ftpFolder='$ftpFolder' where combineID=$pid";
}
else
{
	$sql = "insert into DataExportCombine(fileName,ftpFolder) values('$filename','$ftpFolder')"; 
}
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating DataExportCombine record $sql: $errmsg");
	exit(0);
}
if ($pid == 0)
{
	$sql="select LAST_INSERT_ID()";
	my $sth=$dbhu->prepare($sql);
	$sth->execute();
	($pid)=$sth->fetchrow_array();
	$sth->finish();
}
else
{
	$sql="delete from DataExportCombineJoin where combineID=$pid";
	$rows=$dbhu->do($sql);
}
foreach my $fld (@F)
{
	if ($fld > 0)
	{
		$sql="insert into DataExportCombineJoin(combineID,exportID) values($pid,$fld)";
		$rows=$dbhu->do($sql);
	}
}
print "Location: combinedataexport_list.cgi\n\n";
$pms->clean_up();
exit(0);
