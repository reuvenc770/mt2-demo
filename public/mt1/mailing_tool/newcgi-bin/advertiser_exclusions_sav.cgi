#!/usr/bin/perl

# *****************************************************************************************
# advertiser_exclusions_sav.cgi
#
# this page inserts/updates information in the client_advertiser_exclusion table
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
my $year;
my $mon;
my $mday;

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
my $puserid = $query->param('puserid');
my @aid = $query->param('aid');
#
foreach my $adv_id (@aid)
{
	$sql="insert into client_advertiser_exclusion(client_id,advertiser_id) values($puserid,$adv_id)";
	$sth = $dbhu->do($sql);
}
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/client_exclusion.cgi";
</script>
</body></html>
end_of_html
$util->clean_up();
exit(0);
