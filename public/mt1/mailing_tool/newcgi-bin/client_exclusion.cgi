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
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $category_name;
my $advertiser_name;
my $sname;
my $company;
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
<title>Client Exclusions</title>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table1">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="1200" border="0" id="table2">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">
				<img src="/images/header.gif" border="0"></td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
					<tr>
						<td align="left"><b><font face="Arial" size="2">
						&nbsp;&nbsp;&nbsp;&nbsp;Date: Mar 24, 2006 at 17:24:32</font></b></td>
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
		</td>
	</tr>
	<tr><td colspan=7 align=center><a href="client_exclusion_export.cgi?etype=category">Export Category Exclusion</a>&nbsp;&nbsp;<a href="client_exclusion_export.cgi?etype=advertiser">Export Advertiser Exclusion</a></td></tr>
<tr><td colspan=7>&nbsp;&nbsp:</td></tr>
<tr><td colspan=7 align=center><form method="post" action="client_exclusion_upload.cgi" encType=multipart/form-data><input type=hidden name=utype value="category">Client Category File: <input type=file name=upload_file><br><input type=submit value="Load Client Category Exclusion"></form><td></tr>
<tr><td colspan=7 align=center><form method="post" action="client_exclusion_upload.cgi" encType=multipart/form-data><input type=hidden name=utype value="advertiser">Client Advertiser File: <input type=file name=upload_file><br><input type=submit value="Load Client Advertiser Exclusion"></form></td></tr>
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<table cellSpacing="0" cellPadding="0" bgColor="#ffffff" border="0" id="table4">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="1200" bgColor="#ffffff" border="0" id="table5">
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				<center>
				<br>
&nbsp;<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table6">
					<tr bgColor="#509c10" height="15">
						<td align="middle" colSpan="7" height="15"><b>
						<font face="Verdana,Arial,Helvetica,sans-serif" color="#FFFFFF">
						Advertiser/Category Exclusion</font></b></td>
					</tr>
					<tr>
						<td align="left" width="2%" bgColor="#ebfad1">&nbsp;</td>
						<td align="left" bgColor="#ebfad1"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>Client ID</b></font></td>
						<td align="left" width="10%" bgColor="#ebfad1"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>Name</b></font></td>
						<td align="left" bgColor="#ebfad1"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>Client Type</b></font></td>
						<td align="left" bgColor="#ebfad1"><b>
						<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
						Advertisers Excluded</font></b></td>
						<td align="left" bgColor="#ebfad1">
						<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
						<b>Categories Excluded</b></font></td>
						<td align="middle" bgColor="#ebfad1">&nbsp;</td>
					</tr>
end_of_html
	$sql = "select user_id, first_name,client_type from user where status='A' order by first_name";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	my $puserid;
	my $bgcolor;
	my $client_type;
	while (($puserid, $company,$client_type) = $sth->fetchrow_array())
	{
        $reccnt++;
        if ( ($reccnt % 2) == 0 )
        {
            $bgcolor = "#EBFAD1" ;     # Light Green
        }
        else
        {
            $bgcolor = "#d6c6ff" ;     # Light Yellow
        }
		print "<tr bgColor=\"$bgcolor\"><td align=\"left\">&nbsp;</td><td align=left>$puserid</td><td align=\"left\"><font face=\"Arial\" size=\"2\">$company</font></td><td align=left>$client_type</td>\n";
		#
		# Get advertisers 
		#
		$sql="select advertiser_name from advertiser_info,client_advertiser_exclusion where advertiser_info.advertiser_id=client_advertiser_exclusion.advertiser_id and client_id=? order by advertiser_name";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute($puserid);
		print "<td align=left>";
		while (($advertiser_name) = $sth1->fetchrow_array())
		{
			print "$advertiser_name<br>\n";
		}
		$sth1->finish();

		#
		# Get categories
		#
		$sql="select category_name from category_info,client_category_exclusion where category_info.category_id=client_category_exclusion.category_id and client_id=? order by category_name";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute($puserid);
		print "</td><td>";
		while (($category_name) = $sth1->fetchrow_array())
		{
			print "$category_name<br>\n";
		}
		$sth1->finish();
		print "</td><td align=\"left\"><font face=\"Arial\" size=2><a href=\"/cgi-bin/advertiser_exclusion.cgi?puserid=$puserid&cname='$company'\">Advertiser</a><br><a href=\"/cgi-bin/category_exclusion.cgi?puserid=$puserid&cname='$company'\">Category</a> </font></td></tr>\n";
	}
	$sth->finish();
print<<"end_of_html";
					<tr>
						<td align="middle" colSpan="5"><br>
						<a href="/cgi-bin/mainmenu.cgi">
						<img height="21" hspace="7" src="/images/home_blkline.gif" width="72" border="0" name="BtnHome"></a> 
						</td>
					</tr>
				</table>
				</center></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td><br>
&nbsp;<p align="center"><img src="/images/footer.gif" border="0"></td>
	</tr>
</table>

</body>

</html>
end_of_html

#------------------------
# End Main Logic
#------------------------

