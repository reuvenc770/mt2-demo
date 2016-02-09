#!/usr/bin/perl

# ******************************************************************************
# schedule_copy.cgi
#
# this page is to copy a schedule
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
my $dbh;
my $images = $util->get_images_url;
my $company;
my $network_id;
my $sdate;
my $edate;
my $tdate;
my $stype=$query->param('stype');
if ($stype eq "")
{
	$stype="C";
}

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

# get information for the campaign to be copied

$sql = "select date_sub(curdate(),interval dayofweek(curdate())-1 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($sdate) = $sth->fetchrow_array();
$sth->finish();
$sql = "select date_add('$sdate',interval 6 day),date_add('$sdate',interval 7 day)";
$sth = $dbhq->prepare($sql);
$sth->execute();
($edate,$tdate) = $sth->fetchrow_array();
$sth->finish();

# print html page out
print "Content-type: text/html\n\n";
print << "end_of_html";
<html>
<head><title>Schedule Copy</title></head>
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
<h3>Copy A Schedule</h3>
<br>
end_of_html
if ($stype eq "N")
{
	print "<form method=post name=adform action=\"/cgi-bin/nl_schedule_copy_save.cgi\">\n";
}
else
{
	print "<form method=post name=adform action=\"/cgi-bin/schedule_copy_save.cgi\">\n";
}
print<<"end_of_html";
<input type=hidden name=stype value="$stype">
<table width=50%>
<tr><td><b>Start Date</b></td><td><input type=text name=sdate size=10 maxlength=10 value="$sdate"><td><b>End Date</b></td><td><input type=text name=edate size=10 maxlength=10 value="$edate"></td></tr>
<tr><td><b>To Date</b></td><td><input type=text name=tdate size=10 maxlength=10 value="$tdate"></td></tr>
<tr><td><b>Network</b></td></tr>
<tr><td vAlign="center" align="middle" colSpan="4"><input onclick="selectall()" type="button" value="Select All" name="SelectAll2"><input onclick="unselectall()" type="button" value="UnSelect All" name="UnSelectAll0"></td></tr>
end_of_html
if ($stype eq "N")
{
$sql = "select nl_id,nl_name from newsletter where nl_status='A' order by nl_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($network_id,$company) = $sth->fetchrow_array())
{
	print "<tr><td></td><td colspan=3><input type=checkbox name=network value=$network_id>$company</td></tr>\n";
}
$sth->finish();
}
else
{
$sql = "select user_id,company from user where status='A' and user_id in (select client_id from network_schedule) and user_id != 194 order by company"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($network_id,$company) = $sth->fetchrow_array())
{
	print "<tr><td></td><td colspan=3><input type=checkbox name=network value=$network_id>$company</td></tr>\n";
}
$sth->finish();
}
print<<"end_of_html";
<tr><td>&nbsp;</td></tr>
</table>
<a href="/cgi-bin/mainmenu.cgi" target=_top><img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"><input type="image" src="/images/save.gif" border="0" name="I1">
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
