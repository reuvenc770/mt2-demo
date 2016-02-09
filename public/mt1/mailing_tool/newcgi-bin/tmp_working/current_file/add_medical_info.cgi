#!/usr/bin/perl
# *****************************************************************************************
# add_medical_info.cgi
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

# connect to the pms database

$pms->db_connect();
$dbh = $pms->get_dbh;

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
$info_type = $query->param('info_type');
$info_text = $query->param('info_text');
$info_text = $dbh->quote($info_text);

$sql = "insert into medical_info(medical_id,type_id,text_str) values ($cat_id,'$info_type',$info_text)";
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Inserting medical info record $sql: $errmsg");
	exit(0);
}
print "Location: edit_medical.cgi?cat_id=$cat_id\n\n";
$pms->clean_up();
exit(0);
