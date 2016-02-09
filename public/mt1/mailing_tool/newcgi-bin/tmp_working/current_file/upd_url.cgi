#!/usr/bin/perl

# *****************************************************************************************
# ins_url.cgi
#
# this page adds records to brand_url_info 
#
# History
# Jim Sobeck, 06/15/05, Creation
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
my @url_array;

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
# Remove old subject information
#
my $uid = $query->param('uid');
my $type= $query->param('type');
#
my $curl = $query->param('curl');
$sql = "update brand_url_info set url='$curl' where url_id=$uid";
$sth = $dbh->do($sql);
#
# Display the confirmation page
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Brand URLs</title>
</head>
<body>
<p><b>URL successfully updated.  Close this window and refresh the main window to see the new URLs.</b></br>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
