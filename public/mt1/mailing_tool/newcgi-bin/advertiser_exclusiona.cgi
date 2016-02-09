#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Clients
# File   : client_exclusion.cgi
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
my $mesg = $query->param('mesg');
my $puserid= $query->param('puserid');
my $cname= $query->param('cname');
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $category_name;
my $advertiser_name;
my $sname;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

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

print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Advertiser Exclusions</title>
<script language="JavaScript">
function addaid(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    campform.aid.add(newOpt);
}

function clear_advertiser()
{
    var selLength = campform.aid.length;
    while (selLength>0)
    {
        campform.aid.remove(selLength-1);
        selLength--;
    }
    campform.aid.length=0;
}
</script>
</head>
<body>

<table id="table9" cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0">
	<tr vAlign="top">
		<td noWrap align="left">
		<table id="table10" cellSpacing="0" cellPadding="0" width="800" border="0">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table id="table11" cellSpacing="0" cellPadding="0" width="100%" border="0">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Edit Advertiser Exclusions for $cname</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="text-decoration: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="text-decoration: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table id="table12" cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table id="table13" cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>Advertiser Exclusion </b>&nbsp;</font></td>
					</tr>
					<tr>
						<td><img height="3" src="spacer.gif"></td>
					</tr>
				</table>
				<table id="table14" cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0">
					<tr>
						<td colSpan="10">&nbsp;</td>
					</tr>
				</table>
</td></tr></table></td></tr>
<tr><td colspan=10>
			
				<form name="searchform" method="post" action="/cgi-bin/advertiser_exclusion_search.cgi" target=hidden>
					&nbsp;&nbsp;&nbsp;Search for Advertiser: </b>
					<input name="advertiser_name"><b>&nbsp;&nbsp;<input type=submit value="Search"></form> 
				<form name="campform" method="post" action="/cgi-bin/advertiser_exclusions_sav.cgi" target=_top>
				<input type=hidden name=puserid value=$puserid>
					&nbsp;&nbsp;&nbsp;Advertiser: (Select advertiser to exclude)</b>&nbsp;&nbsp;<select multiple size=5 name="aid">
end_of_html
#$sql="select advertiser_id,advertiser_name from advertiser_info where status in ('A','I','S") and advertiser_id not in (select advertiser_id from client_advertiser_exclusion where client_id=?) order by advertiser_name";
#$sth=$dbhq->prepare($sql);
#$sth->execute($puserid);
my $aid;
my $aname;
#while (($aid,$aname) = $sth->fetchrow_array())
#{
	#print "<option value=$aid>$aname</option>";
#}
#$sth->finish();
print<<"end_of_html";
					<br><br>
					<b><br>
&nbsp;</p>
					<table id="table17" cellPadding="5" width="66%" bgColor="white">
end_of_html
$sql="select client_advertiser_exclusion.advertiser_id,advertiser_name from client_advertiser_exclusion,advertiser_info where client_advertiser_exclusion.advertiser_id=advertiser_info.advertiser_id and client_advertiser_exclusion.client_id=? order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute($puserid);
while (($aid,$aname) = $sth->fetchrow_array())
{
	print "<tr><td align=middle width=47%><p align=left>$aname</td><td align=middle width=47%><a href=\"/cgi-bin/advertiser_exclusion_delete.cgi?aid=$aid&puserid=$puserid\" target=_top>Delete</a></td></tr>\n";
}
$sth->finish();
print<<"end_of_html";
					</table>
					<table id="table18" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<p align="left">&nbsp;</td>
							<td align="middle" width="47%">
							&nbsp;</td>
						</tr>
					</table>
					<table id="table16" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/client_exclusion.cgi" target=_top>
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input type="image" height="22" width="81" src="/images/save_rev.gif" border="0" name="I2"></td>
						</tr>
					</table>
				</form>
				</b></td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html
