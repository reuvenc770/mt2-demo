#!/usr/bin/perl
# *****************************************************************************************
# delete_medical_info.cgi
#
# this page deletes the medical condition info 
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
my $cat_id;
my $cname;
my $info_type;
my $info_text;
my $info_id;

# connect to the pms database

###$pms->db_connect();

my ($dbhq,$dbhu)=$pms->get_dbh();
###$dbh = $pms->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# get fields from the form

$cat_id = $query->param('cat_id');
$info_id = $query->param('info_id');

$sql = "delete from medical_info where medical_id=$cat_id and info_id=$info_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Deleting medical info record $sql: $errmsg");
	exit(0);
}
print "Location: edit_medical.cgi?cat_id=$cat_id\n\n";
$pms->clean_up();
exit(0);
