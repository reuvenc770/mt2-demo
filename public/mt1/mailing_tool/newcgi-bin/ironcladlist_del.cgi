#!/usr/bin/perl
# *****************************************************************************************
# ironcladlist_del.cgi
#
# this page deletes a IronCladList
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
my $listID = $query->param('listID');

$sql="delete from IronCladList where listID=$listID";
$rows = $dbhu->do($sql);
$sql="delete from IronCladGroupLists where IronCladGroupID=$gid and listID=$listID";
$rows = $dbhu->do($sql);

print "Location: ironcladgroup_edit.cgi?gid=$gid\n\n"; 
$pms->clean_up();
exit(0);
