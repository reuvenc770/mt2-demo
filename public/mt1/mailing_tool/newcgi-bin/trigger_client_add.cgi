#!/usr/bin/perl
# *****************************************************************************************
# trigger_client_add.cgi
#
# this page assigns a trigger setting to a client 
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

my $client_id= $query->param('cid');
my $dd_id = $query->param('dd_id');
my $ttype= $query->param('ttype');
my $fld;
if (($ttype eq "") or ($ttype eq "click"))
{
	$fld="dd_id";
}
elsif ($ttype eq "open")
{
	$fld="dd_id_open";
}
elsif ($ttype eq "conversion")
{
	$fld="dd_id_conversion";
}
$sql = "update user set $fld=$dd_id where user_id=$client_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating user record $sql: $errmsg");
	exit(0);
}
print "Location: trigger_client.cgi\n\n";
$pms->clean_up();
exit(0);
