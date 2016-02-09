#!/usr/bin/perl

# *****************************************************************************************
# footer_content_del.cgi
#
# this page deletes records from footer_content and content_category tables 
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
#
$sql = "delete from footer_content where content_id=$cid";
$sth = $dbh->do($sql);
$sql = "delete from content_category where content_id=$cid";
$sth = $dbh->do($sql);
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/footer_content_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
