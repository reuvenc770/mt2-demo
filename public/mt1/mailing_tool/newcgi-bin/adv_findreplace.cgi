#!/usr/bin/perl
#===============================================================================
# Name   : creative_findreplace.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $sth1;
my $aname;
my $taid;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $aid= $query->param('aid');
if ($aid eq "")
{
	$aid=0;
}
my $cstatus1= $query->param('cstatus1');
my $cstatus2;
my $cstatus3;
if ($cstatus1 eq "")
{
	$cstatus1="A";
	$cstatus2="";
	$cstatus3="";
}
else
{
	$cstatus2= $query->param('cstatus2');
	$cstatus3= $query->param('cstatus3');
}
#------ connect to the util database ------------------

my ($dbhq,$dbhu)=$util->get_dbh();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>View Advertiser</h3>
<br>
<form method=post action="/cgi-bin/adv_findreplace_save.cgi" target=_blank>
<table border=0 width=60%>
end_of_html
	print "<tr><td align=right><b>Advertisers</b></td><td><select name=aid multiple=multiple size=15>";
	$sql = "select advertiser_id,advertiser_name from advertiser_info where ";
	if ($cstatus1 ne "")
	{
		if ($cstatus1 eq "T")
		{
			$sql = $sql . " ((status='A' and test_flag='Y')";
		}
		elsif ($cstatus1 eq "P")
		{
			$sql = $sql . " ((status='I' and test_flag='P')";
		}
		elsif ($cstatus1 eq "I")
		{
			$sql = $sql . " ((status='I' and test_flag='N')";
		}
		else
		{
			$sql = $sql . " ((status='$cstatus1')";
		}
		if ($cstatus2 eq "T")
		{
			$sql = $sql . " or (status='A' and test_flag='Y')";
		}
		elsif ($cstatus2 eq "P")
		{
			$sql = $sql . " or (status='I' and test_flag='P')";
		}
		elsif ($cstatus2 ne "")
		{
			$sql = $sql . " or (status='$cstatus2')";
		}
		if ($cstatus3 eq "T")
		{
			$sql = $sql . " or (status='A' and test_flag='Y')";
		}
		elsif ($cstatus3 eq "P")
		{
			$sql = $sql . " or (status='I' and test_flag='P')";
		}
		elsif ($cstatus3 ne "")
		{
			$sql = $sql . " or (status='$cstatus3')";
		}
		$sql=$sql . ")";
	}
	$sql = $sql . " order by advertiser_name";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	while (($taid,$aname) = $sth1->fetchrow_array())
	{
		print "<option value=$taid>$aname ($taid)</option>\n";
	}
	$sth1->finish;
	print "</select></td></tr>";
print<<"end_of_html";
<tr><td align=right>Search For</td><td><select name=search_for><option value="CakeCreativeID">Cake Creative IDs</option>
<option value="Campaign Notes">Campaign Notes</option>
<option value="Advertiser URL">Advertiser URL</option>
<option value="Landing Page">Landing Page</option>
<option value="Advertiser Unsubscribe URL">Advertiser Unsubscribe URL</option>
<option value="Hitpath Tracking Pixel">Hitpath Tracking Pixel</option>
<option value="Suppression URL">Suppression URL</option>
<option value="Direct Suppression URL">Direct Suppression URL</option>
<option value="Unsubscribe Text">Unsubscribe Text</option>
<option value="Friendly Advertiser Name">Friendly Advertiser Name</option>
</select>&nbsp;&nbsp;<input type=text name=search_str size=30>&nbsp;&nbsp;<select name=search_chk><option value=contains>Contains</option>
<option value=doesnotcontain>Does not Contain</option>
</select></td></tr>
<tr><td align=right>Suppression URL:</td><td><select name=suppURL><option value="">ALL</option>
<option value=Y>Selected</option>
<option value=N>Unselected</option>
</select></td></tr>
<tr><td align=right>Unsubscribe To Use:</td><td><select name=unsub_use><option value="">ALL</option>
<option value=IMAGE>Image</option>
<option value=TEXT>Text</option>
</select></td></tr>
<tr><td align=right>Suppression File:</td><td><select name=suppFile><option value="">ALL</option>
end_of_html
$sql="select distinct list_id,list_name from vendor_supp_list_info vs, advertiser_info ai where ai.vendor_supp_list_id=vs.list_id and ai.md5_suppression='N' and vs.md5_suppression='N' order by list_name";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($taid,$aname) = $sth1->fetchrow_array())
{
	print "<option value=$taid>$aname</option>\n";
}
$sth1->finish;
print<<"end_of_html";
</select></td></tr>
<tr><td align=right>MD5 Suppression File:</td><td><select name=md5suppFile><option value="">ALL</option>
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where md5_suppression='Y' and vendor_supp_list_id=0 order by advertiser_name";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($taid,$aname) = $sth1->fetchrow_array())
{
	print "<option value=$taid>$aname</option>\n";
}
$sth1->finish;
print<<"end_of_html";
</select></td></tr>
<tr><td align=right>MD5:</td><td><select name=md5_suppression><option value="">ALL</option>
<option value=Y>Yes</option>
<option value=N>No</option>
</select></td></tr>
<tr><td align=right>Inactive Date(yyyy-mm-dd):</td><td><input type=text name=inactive_date size=10 maxlength=10>&nbsp;&nbsp;<select name=inactive_chk><option value=contains>Contains</option>
<option value=doesnotcontain>Does not Contain</option>
</select></td></tr>
<tr><td colspan=2 align=middle><input type=submit value="View Results"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);

