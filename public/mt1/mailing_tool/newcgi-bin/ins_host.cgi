#!/usr/bin/perl

# *****************************************************************************************
# ins_host.cgi
#
# this page adds records to brand_host
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
my $curl;
my $ip_addr;
my @url_array;
my $btype;
my $nl_id;

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
# Remove old subject information
#
my $bid = $query->param('bid');
my $type= $query->param('type');
my $upd= $query->param('upd');
$sql="select brand_type,nl_id from client_brand_info where brand_id=?";
my $sth2=$dbhu->prepare($sql);
$sth2->execute($bid);
($btype,$nl_id)=$sth2->fetchrow_array();
$sth2->finish();
#
if (($type ne "A") and ($type ne "T"))
{
my $url_list = $query->param('curl');
$url_list =~ s/[\n\r\f\t]/\|/g ;
$url_list =~ s/\|{2,999}/\|/g ;
@url_array = split '\|', $url_list;
foreach $curl (@url_array)
{
$curl =~ s/'/''/g;
$curl =~ s/\x96/-/g;
#
$sql = "insert into brand_host(brand_id,server_type,server_name) values($bid,'$type','$curl')"; 
$sth = $dbhu->do($sql);
if (($btype eq "Newsletter") and ($upd eq "Y"))
{
$sql = "insert into brand_host(brand_id,server_type,server_name) select brand_id,'$type','$curl' from client_brand_info where nl_id=$nl_id and status='A' and brand_id != $bid"; 
$sth = $dbhu->do($sql);
}
}
}
else
{
	my $sname;
	my @ip_addr = $query->param('ip_addr');
	my $server_id = $query->param('server_id');
	open(LOG,">/tmp/k.");
	print LOG "<$server_id>\n";
	close(LOG);
	$sql="select server from server_config where id=$server_id"; 
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	($sname)=$sth1->fetchrow_array();
	$sth1->finish();
	foreach my $c1 (@ip_addr)
	{
		$sql = "insert into brand_host(brand_id,server_type,server_name,ip_addr) values($bid,'$type','$sname','$c1')"; 
		$sth = $dbhu->do($sql);
		if (($btype eq "Newsletter") and ($upd eq "Y"))
		{
			$sql = "insert into brand_host(brand_id,server_type,server_name,ip_addr) select brand_id,'$type','$sname','$c1' from client_brand_info where nl_id=$nl_id and status='A' and brand_id != $bid"; 
			$sth = $dbhu->do($sql);
		}
	}
}
#
# Display the confirmation page
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Brand Hosts</title>
</head>
<body>
<p><b>Hosts successfully added.  Close this window and refresh the main window to see the new Hosts.</b></br>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
