#!/usr/bin/perl
# ******************************************************************************
# add_sending_client.cgi
#
# this page is to update the client_data_source table 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $images = $util->get_images_url;
my $fname;
my $network_id;
my $sdate;
my $edate;
my $tdate;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
my $client_id=$query->param('puserid');

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# print html page out
print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head><title>Manage Receiving Client Data</title></head>
<script language="JavaScript">
function selectall()
{
    refno=/network/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/network/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
</script>
<center>
<body>
<h3>Update Receiving Data</h3>
<br>
<form method=post name=adform action="/cgi-bin/client/add_sending_client_save.cgi">
<input type=hidden name=client_id value=$client_id>
<table width=50%>
<tr><td vAlign="center" align="middle" colSpan="2"><input onclick="selectall()" type="button" value="Select All" name="SelectAll2"><input onclick="unselectall()" type="button" value="UnSelect All" name="UnSelectAll0"></td>
<tr><td colspan=2 align=middle><b>Network</b></td></tr>
end_of_html
$sql = "select user_id,first_name from user where status='A' and user_id != $client_id order by first_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $company_str="";
my $cnt;
while (($network_id,$fname) = $sth->fetchrow_array())
{
	$sql="select count(*) from client_data_source where client_id=? and sending_client_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($client_id,$network_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		$company_str = $company_str . "<input type=checkbox checked name=network value=$network_id>$fname - $network_id<br>";
	}
	else
	{
		$company_str = $company_str . "<input type=checkbox name=network value=$network_id>$fname - $network_id<br>";
	}
}
$sth->finish();
print<<"end_of_html";
<tr><td colspan=2 valign=top>$company_str</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
<a href="/cgi-bin/mainmenu.cgi" target=_top><img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"><input type="image" src="/images/save.gif" border="0" name="I1">
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
