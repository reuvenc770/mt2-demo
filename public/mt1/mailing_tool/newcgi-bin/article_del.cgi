#!/usr/bin/perl

# *****************************************************************************************
# article_del.cgi
#
# this page deletes records from article table 
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
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $cid = $query->param('cid');

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
$sql = "update article set status='D' where article_id=$cid";
$sth = $dbhu->do($sql);
$sql = "delete from brand_article where article_id=$cid";
$sth = $dbhu->do($sql);
$sql = "delete from article_headline where article_id=$cid";
$sth = $dbhu->do($sql);
$sql = "delete from article_blurb where article_id=$cid";
$sth = $dbhu->do($sql);
$sql = "delete from article_subject where article_id=$cid";
$sth = $dbhu->do($sql);
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/article_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
