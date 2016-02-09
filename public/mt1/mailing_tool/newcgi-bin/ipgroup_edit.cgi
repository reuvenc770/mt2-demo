#!/usr/bin/perl
# *****************************************************************************************
# ipgroup_edit.cgi
#
# this page is to edit an IpGroupi
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $util = $pms;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $ip;
my $gid= $query->param('gid');
my $user_id;
my $gname;
my $othrottle;
my $goodmail_enabled;
my $colo;
my $chunk;
my $images = $pms->get_images_url;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login
$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});
my $externalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;   
}


# read info for this IpGroup 

$sql = "select group_name,outbound_throttle,goodmail_enabled,colo,chunk from IpGroup where $userDataRestrictionWhereClause group_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($gid);
($gname,$othrottle,$goodmail_enabled,$colo,$chunk) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("Edit Ip Group");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

<script language="JavaScript" type="text/javascript">
<!--

var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);

function addOption(theSel, theText, theValue)
{
  var newOpt = new Option(theText, theValue);
  var selLength = theSel.length;
  theSel.options[selLength] = newOpt;
}

function deleteOption(theSel, theIndex)
{ 
  var selLength = theSel.length;
  if(selLength>0)
  {
    theSel.options[theIndex] = null;
  }
}

function moveOptions(theSelFrom, theSelTo)
{
  
  var selLength = theSelFrom.length;
  var selectedText = new Array();
  var selectedValues = new Array();
  var selectedCount = 0;
  
  var i;
  
  // Find the selected Options in reverse order
  // and delete them from the 'from' Select.
  for(i=selLength-1; i>=0; i--)
  {
    if(theSelFrom.options[i].selected)
    {
      selectedText[selectedCount] = theSelFrom.options[i].text;
      selectedValues[selectedCount] = theSelFrom.options[i].value;
      deleteOption(theSelFrom, i);
      selectedCount++;
    }
  }
  
  // Add the selected text/values in reverse order.
  // This will add the Options to the 'to' Select
  // in the same order as they were in the 'from' Select.
  for(i=selectedCount-1; i>=0; i--)
  {
    addOption(theSelTo, selectedText[i], selectedValues[i]);
  }
  
  if(NS4) history.go(0);
}
function selectAllOptions(selStr)
{
  var selObj = document.getElementById(selStr);
  for (var i=0; i<selObj.options.length; i++) {
    selObj.options[i].selected = true;
  }
}


//-->
</script>
		<FORM action="ipgroup_add_ips.cgi" method="post" onsubmit="selectAllOptions('sel2');">
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3>Group: <input type=text name=gname value="$gname" size=40></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Use this screen to Edit Ip Group.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<input type="hidden" name="gid" value="$gid">
Outbound Throttle: <input type=text size=3 name=othrottle value=$othrottle><br>
end_of_html
if ($goodmail_enabled eq "Y")
{
	print "Goodmail Enabled: <select name=goodmail_enabled><option value=N>No</option><option value=Y selected>Yes</option></select><br>\n";
}
else
{
	print "Goodmail Enabled: <select name=goodmail_enabled><option value=N selected>No</option><option value=Y >Yes</option></select><br>\n";
}
if ($colo eq "FORT")
{
	print "Colo: <select name=colo><option selected value=FORT>FORT</option><option value=NAC>NAC</option></select><br>\n";
}
else
{
	print "Colo: <select name=colo><option value=FORT>FORT</option><option selected value=NAC>NAC</option></select><br>\n";
}
print<<"end_of_html";
Chunk Size: <input type=text name=chunk size=3 maxlenght=3 value=$chunk><br><br>
<table border="0">
	<tr><td>Available IPs</td><td></td><td>IPs in Group</td><td width=30></td><td>Paste IPs (One per line)</td><td width=30></td><td>IPs Currently in Group</td></tr>
	<tr>
		<td>
			<select name=sel1 id=sel1 size="20" multiple="multiple">
end_of_html
if($externalUser)
{
	$sql="select ia.ip from ServerIp join IpAttribute ia on ia.ip=ServerIp.ip where $userDataRestrictionWhereClause ipRoleID=2 and ipStatusID=14 and ia.ip not in (select ip_addr from IpGroupIps where group_id=$gid) order by ip";
}
else
{
	$sql="select ip from ServerIp where ipRoleID=2 and ipStatusID=14 and ip not in (select ip_addr from IpGroupIps where group_id=$gid) order by ip";
}


$sth=$dbhu->prepare($sql);
$sth->execute();
while (($ip)=$sth->fetchrow_array())
{
	print "<option value=\"$ip\">$ip</option>";
}
$sth->finish();
print<<"end_of_html";
			</select>
		</td>
		<td align="center" valign="middle">
			<input type="button" value="--&gt;"
			 onclick="moveOptions(this.form.sel1, this.form.sel2);" /><br />
			<input type="button" value="&lt;--"
			 onclick="moveOptions(this.form.sel2, this.form.sel1);" />
		</td>
		<td>
			<select name=sel2 id=sel2 size="20" multiple="multiple">
end_of_html
my $ipstr="";
$sql="select ip_addr from IpGroupIps where group_id=(select group_id from IpGroup where $userDataRestrictionWhereClause group_id=? limit 1) order by ip_addr";
$sth=$dbhu->prepare($sql);
$sth->execute($gid);
while (($ip)=$sth->fetchrow_array())
{
	print "<option value=\"$ip\">$ip</option>";
	$ipstr=$ipstr.$ip."<br>";
}
$sth->finish();
print<<"end_of_html";
			</select>
		</td>
<td></td><td><textarea name=paste_ips rows=20 cols=15></textarea></td>
<td></td><td valign=top>$ipstr</td>
	</tr>
	<tr><td colspan=3 align=middle><input type=submit value="Save" name="submit"></td></tr>
</table>
</form>
	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=1>
end_of_html

$pms->footer();
exit(0);
