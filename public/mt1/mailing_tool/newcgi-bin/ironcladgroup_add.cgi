#!/usr/bin/perl
# *****************************************************************************************
# ironcladgroup_add.cgi
#
# this page adds a new IronCladGroup 
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

my $gname= $query->param('gname');
if ($gname eq "")
{
	util::logerror("Group Name cannot be blank");
    $pms->clean_up();
    exit(0);
}

$gname = $dbhq->quote($gname);


$sql = "insert into IronCladGroup(groupName,status) values ($gname,'Active')";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting IronCladGroup record $sql: $errmsg");
	exit(0);
}
print "Location: ironcladgroup_list.cgi\n\n"; 
$pms->clean_up();
exit(0);
