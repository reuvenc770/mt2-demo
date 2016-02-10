#!/usr/bin/perl
# *****************************************************************************************
# update_medical_info.cgi
#
# this page saves the medical condition info changes
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
$info_type = $query->param('info_type');
$info_text = $query->param('info_text');
$info_text = $dbhq->quote($info_text);

$sql = "update medical_info set type_id='$info_type',text_str=$info_text where medical_id=$cat_id and info_id=$info_id";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Updating medical info record $sql: $errmsg");
	exit(0);
}
print "Location: edit_medical.cgi?cat_id=$cat_id\n\n";
$pms->clean_up();
exit(0);
