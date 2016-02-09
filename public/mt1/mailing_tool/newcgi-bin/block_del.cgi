#!/usr/bin/perl

# *****************************************************************************************
# block_del.cgi
#
# this page updates information in the block table
#
# History
# Jim Sobeck, 06/15/07, Creation
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
my $bid = $query->param('bid');

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
# Update record into footer_variation 
#
$sql = "update block set status='D' where block_id=$bid";
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
document.location="/cgi-bin/block_list.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
