#!/usr/bin/perl
#===============================================================================
# Purpose: Adds URLs for a Brand 
# Name   : add_host.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/15/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $dbh;
my $website;
my $bid = $query->param('bid');
my $utype= $query->param('type');
my $upd= $query->param('upd');
my $url;
my $url_str;
my $ip_addr;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
#
$sql = "select server_name,ip_addr from brand_host where brand_id=$bid and server_type='$utype'"; 
$sth = $dbhq->prepare($sql);

if ($utype eq "O")
{
	$url_str = "Others";
}
elsif ($utype eq "Y")
{
	$url_str = "Yahoo";
}
elsif ($utype eq "C")
{
	$url_str = "Cleanser";
}
elsif ($utype eq "A")
{
	$url_str = "AOL";
}
elsif ($utype eq "T")
{
	$url_str = "Test AOL";
}
elsif ($utype eq "H")
{
	$url_str = "Hotmail";
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Hosts</title>
</head>
<body>
<script Language="JavaScript">
function update_server()
{
   	parent.frames[1].location="/newcgi-bin/upd_chunk_server.cgi?bid=$bid";
}
function addSERVER(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    listform.server_id.add(newOpt);
}
function update_ip()
{
    var selObj = document.getElementById('server_id');
	if (selObj)
	{
    	var selIndex = selObj.selectedIndex;
	}
    var selLength = listform.ip_addr.length;
    while (selLength>0)
    {
        listform.ip_addr.remove(selLength-1);
        selLength--;
    }
    listform.ip_addr.length=0;
	if (selIndex >= 0)
	{
    	parent.frames[1].location="/newcgi-bin/upd_ip_addr.cgi?bid=$bid&sid="+selObj.options[selIndex].value;
	}
}
function addIP(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    listform.ip_addr.add(newOpt);
}
</script>
<p><b>Current $url_str Hosts: </b></br>
end_of_html
$sth->execute();
while (($url,$ip_addr) = $sth->fetchrow_array())
{
	if (($utype eq "A") or ($utype eq "T"))
	{
		print "&nbsp;&nbsp;&nbsp;$url - $ip_addr<br>\n";
	}
	else
	{
		print "&nbsp;&nbsp;&nbsp;$url<br>\n";
	}
}
$sth->finish();
print<<"end_of_html";
<form name="listform" action="/cgi-bin/ins_host.cgi" method="post">
<input type=hidden name=bid value="$bid">
<input type=hidden name=type value="$utype">
<input type=hidden name=upd value="$upd">
end_of_html
if (($utype eq "A") or ($utype eq "T"))
{
	print "&nbsp;&nbsp;Hostname: <select name=server_id onChange=\"update_ip();\"></select><br><br>\n";
	print "&nbsp;&nbsp;IP Addr: &nbsp;&nbsp;<select multiple name=ip_addr size=3></select><br><br>\n";
}
else
{
print<<"end_of_html";
<p><b>Hosts: (Hit ENTER after each one) </b><br>
<textarea name="curl" rows="7" cols="82"></textarea></p>
<p>
end_of_html
}
print<<"end_of_html";
<input type=image height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
<script Language="JavaScript">
	update_server();
</script>
</body>
</html>
end_of_html
