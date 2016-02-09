#!/usr/bin/perl
# *****************************************************************************************
# combinedataexport_pause.cgi
#
# this page pauses a DataExportCombine
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

$sql = "update DataExportCombine set status='Paused' where combineID=$gid";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Pausing DataExportCombine record $sql: $errmsg");
	exit(0);
}
print "Location: combinedataexport_list.cgi\n\n";
$pms->clean_up();
exit(0);
