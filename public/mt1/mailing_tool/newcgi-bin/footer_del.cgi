#!/usr/bin/perl

# *****************************************************************************************
# footer_del.cgi
#
# this page updates information in the footer_variation table
#
# History
# Jim Sobeck, 06/14/05, Creation
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
my $vid = $query->param('vid');

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Update record into footer_variation 
#
$sql = "update footer_variation set status='D' where variation_id=$vid";
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
document.location="/cgi-bin/footer_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
