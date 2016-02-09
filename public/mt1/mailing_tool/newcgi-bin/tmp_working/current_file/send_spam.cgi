#!/usr/bin/perl

# *****************************************************************************************
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $html_template;
my $sth1;
my $BASE_DIR;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get the fields from the form 

my $campaign_id = $query->param('cid');
my $aid = $query->param('aid');
my $to_addr = $query->param('cemail');
my $returnto = $query->param('returnto');
my $format = $query->param('format');
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
my @args = ("${BASE_DIR}newcgi-bin/test_spam.sh","$campaign_id","$to_addr");
system(@args) == 0 or die "system @args failed: $?";
if ($returnto eq "preview")
{
	print "Location: camp_preview.cgi?campaign_id=$campaign_id&format=$format\n\n";
}
else
{
	print "Location: edit_creative.cgi?cid=$campaign_id&aid=$aid\n\n";
}
exit(0);
