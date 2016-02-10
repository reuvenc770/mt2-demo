#!/usr/bin/perl
#===============================================================================
# Purpose: Displays and maintains CategoryTrigger 
# Name   : new_category_trigger_list.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $cdate;
my $aid;
my $aname;
my $cid;
my $results;
my $cname;
my $MAX_TRIGGER=6;
my @TTYPE=("OPEN","CLICK","CONVERSION");
my $trigger_cnt;
my $trigger_delay;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $ctype=$query->param('ctype');
my $userid=$query->param('userid');
if ($ctype eq "")
{
	$ctype="OPEN";
}
if ($userid eq "")
{
	$userid=0;
}
$sql="select date_format(curdate(),'%m/%d/%Y')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($cdate) = $sth->fetchrow_array();
$sth->finish();

if ($userid == 0)
{
	$sql="select parmval from sysparm where parmkey='".$ctype."_TRIGGER_CNT'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($trigger_cnt) = $sth->fetchrow_array();
	$sth->finish();
	$sql="select parmval from sysparm where parmkey='".$ctype."_TRIGGER_DELAY'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($trigger_delay) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	my $lctype=lc $ctype;
	$sql="select ".$lctype."_trigger_cnt,".$lctype."_trigger_delay from user where user_id=$userid"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($trigger_cnt,$trigger_delay) = $sth->fetchrow_array();
	$sth->finish();
}

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Category Triggers</title>
<script language="Javascript">
function exportfile()
{
    var selObj = document.getElementById('ctype');
    var selIndex = selObj.selectedIndex;
    var selObj1 = document.getElementById('userid');
    var selIndex1 = selObj1.selectedIndex;
    var newwin = window.open("/cgi-bin/trigger_export.cgi?ctype="+selObj.options[selIndex].value+"&client_id="+selObj1.options[selIndex1].value, "Export", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
}
</script>
</head>
<body>
<form method=post action="new_category_trigger_list.cgi">
<table border="0" width="100%" id="table3">
	<tr>
		<td></td>
		<td>
		<p align="right"><b><font face="Verdana">Today is $cdate</font></b></td>
	</tr>
		<tr>
		<td><b>Client:</b> <select name=userid>
end_of_html
$sql = "select user_id,company from user where status='A' order by company"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $tid;
my $company;
if ($userid == 0)
{
	print "<option selected value=0>Default</option>\n";
}
else
{
	print "<option value=0>Default</option>\n";
}
while (($tid,$company) = $sth->fetchrow_array())
{
	if ($tid == $userid)
	{
		print "<option selected value=$tid>$company</option>\n";
	}
	else
	{
		print "<option value=$tid>$company</option>\n";
	}
}
$sth->finish;
print<<"end_of_html";
	</select>
	<tr>
	<td><font face="Verdana"><b>Trigger Type:&nbsp;</b></font><select name=ctype>
end_of_html
foreach my $ttype (@TTYPE)
{
	if ($ttype eq $ctype)
	{
		print "<option value=$ttype selected>$ttype</option>\n";
	}
	else
	{
		print "<option value=$ttype>$ttype</option>\n";
	}
}
print<<"end_of_html";
</select>&nbsp;<input type=submit value="Go">
<input type="button" value="Export Schedule" onClick="javascript:exportfile();">
</td></tr>
</table>
</form>
<form method=post action="/cgi-bin/category_triggers_params.cgi">
<input type=hidden name=ctype value=$ctype>
<input type=hidden name=userid value=$userid>
<table border="0" width="50%" id="table7">
<tr><td>Number of $ctype Triggers to Send: <td><t><input type=text name=trigger_cnt value="$trigger_cnt"></td></tr>
<tr><td>Time Delay(minutes): </td><td><input type=text name=trigger_delay value="$trigger_delay"><input type=submit value="Update Params"></td></tr>
</table>
</form>
<form method=post action="upload_triggers.cgi" encType=multipart/form-data>Trigger File: <input type=file name=upload_file><input type=submit value="Upload File">
</form>
<form method=post action="/cgi-bin/new_category_trigger_save.cgi">
<input type=hidden name=ctype value=$ctype>
<input type=hidden name=userid value=$userid>
<table border="0" width="100%" id="table7">
	<tr>
	<td><font face="Verdana"><b>Advertiser:&nbsp;</b></font><select name=usa_id>
end_of_html
$sql="select usa.usa_id,usa.name from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' order by usa.name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($aid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$aid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select></td>
</tr>
</table>
<table border="1" width="100%" id="table1">
<tr><th>Category</th>
end_of_html
my $i=1;
while ($i <= $MAX_TRIGGER)
{
	print "<th>Trigger $i</th>\n";
	$i++;
}
print "</tr>";
$sql="select category_id,category_name from category_info where status='A' order by category_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	print "<tr><td>$cname</td>";
#	$sql="select name,orderID from UniqueScheduleAdvertiser usa, CategoryTrigger ct where ct.trigger_type='$ctype' and usa.usa_id=ct.usa_id and ct.category_id=$cid and ct.client_id=$userid order by orderID";
	$sql="select advertiser_name,orderID from CategoryTrigger ct,campaign c, advertiser_info ai where ct.trigger_type='$ctype' and ct.campaign_id=c.campaign_id and c.advertiser_id=ai.advertiser_id and ct.category_id=$cid and ct.client_id=$userid order by orderID";
	$results = $dbhu->selectall_hashref($sql, 'orderID');
	$i=1;
	while ($i <= $MAX_TRIGGER)
	{
		if ($results->{$i})
		{
			print "<td><input type=checkbox name=cid value=\"$cid|$i\">$results->{$i}->{advertiser_name}</td>\n";
		}
		else
		{
			print "<td><input type=checkbox name=cid value=\"$cid|$i\"></td>\n";
		}
		$i++;
	}
	print "</tr>\n";
}
$sth->finish();
print<<"end_of_html";
	</table>

<p align="center">
					<input type=submit name=submit value="Save">&nbsp;&nbsp;<input type=submit name=submit value="Delete">&nbsp;&nbsp;<input type=submit name=submit value="Delete Client Settings">&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</form>
</body>

</html>
end_of_html
