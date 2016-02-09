#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a IPs for a brand
# File   : disp_brand_ips.cgi
#
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $ip;
my $vsgid;
my $reccnt;
my $bid=$query->param('brand_id');

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
print "Content-type:text/html\n\n";
print<<"end_of_html";
<html><head></head>
<body>
<center>
<table width=30% border=1><tr><th>IP</th><th>Status</th></tr>
end_of_html
$sql="select sic.ip from server_ip_config sic, brand_ip bi where bi.brandID=? and bi.ip=sic.ip order by sic.ip";
$sth=$dbhu->prepare($sql);
$sth->execute($bid);
while (($ip)=$sth->fetchrow_array())
{
	$sql="select count(*) from server_ip_failed where ip=?";
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($ip);
	($reccnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($reccnt > 0)
	{
		print "<tr><td>$ip</td><td><b>Failed</b></td></tr>\n";
	}
	else
	{
		print "<tr><td>$ip</td><td></td></tr>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</table>
</center>
</body>
</html>
end_of_html
