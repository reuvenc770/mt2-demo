#!/usr/bin/perl

# *****************************************************************************************
# del_adv-domain.cgi
#
# this page updates information in the brand_advertiser_info table
#
# History
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
my $pmesg;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $csubject;
my @subject_array;

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
#
my $aid = $query->param('aid');
my $bid = $query->param('bid');
#
print "Content-tyep:text/html\n\n";
print<<"end_of_html";
<html><head></head><body>
end_of_html
# Delete record from brand_advertiser_info 
#
$sql = "delete from brand_advertiser_info where brand_id=$bid and advertiser_id=$aid";
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
    print "<h3>Error - Deleting advertiser info record: $sql - $errmsg</h3>";
}
else
{
	print "<h3>Successful Delete of Advertiser Domains</h3>";
}
print<<"end_of_html";
</body></html>
end_of_html
exit(0);
