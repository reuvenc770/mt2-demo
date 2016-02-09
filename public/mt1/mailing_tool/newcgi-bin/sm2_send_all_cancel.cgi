#!/usr/bin/perl

# *****************************************************************************************
# sm2_send_all_cancel.cgi
#
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $id;
my $em;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $tid= $query->param('tid');
open(LOG,">>/tmp/sm2_cancel.log");
print LOG "<$user_id> <$tid>\n";
close(LOG);
$sql="update test_campaign set status='CANCELLED' where $userDataRestrictionWhereClause test_id=$tid and status != 'PULLED'";
my $rows=$dbhu->do($sql);
$sql="update test_campaign set status='CANCELLED' where $userDataRestrictionWhereClause mainTestID=$tid and status != 'PULLED'";
my $rows=$dbhu->do($sql);
print "Content-type: text/html\n\n";
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<center>
<h2>Campaign <b>$tid</b> has been cancelled.</h2>
<br>
<a href="sm2_send_all_list.cgi">Back To Send All</a>
</center>
</body></html>
end_of_html
$util->clean_up();
exit(0);
