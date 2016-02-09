#!/usr/bin/perl
# *****************************************************************************************
# unique_attribution_del.cgi
#
# this page deletes a UniqueAttribution
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

my $sid= $query->param('sid');
my $level;
$sql = "select level from UniqueAttribution where client_id=$sid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($level)=$sth->fetchrow_array();
$sth->finish();

$sql = "delete from UniqueAttribution where client_id=$sid";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Deleting UniqueAttribution record $sql: $errmsg");
	exit(0);
}
$sql="update UniqueAttribution set level=level-1 where level >= $level";
$rows = $dbhu->do($sql);
print "Location: unique_attribution.cgi\n\n";
$pms->clean_up();
exit(0);
