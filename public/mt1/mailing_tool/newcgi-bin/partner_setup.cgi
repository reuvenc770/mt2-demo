#!/usr/bin/perl
# *****************************************************************************************
# partner_setup.cgi
#
# this page is to edit a PartnerClientInfo 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $client;
my $delay;
my $dupes;
my $byPass;
my $partner_id= $query->param('partner_id');
my $user_id;
my $gname;
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

# read info for this ClientGroup 

$sql = "select partner_name,dupes_only,byPass from PartnerInfo where partner_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($partner_id);
($gname,$dupes,$byPass) = $sth->fetchrow_array();
$sth->finish();
$sql = "select delay from PartnerClientInfo where partner_id=? limit 1"; 
$sth = $dbhq->prepare($sql);
$sth->execute($partner_id);
($delay) = $sth->fetchrow_array();
$sth->finish();
if ($delay eq "")
{
	$delay=0;
}
$delay=$delay/3600;

# print out the html page

util::header("Edit Partner");

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
		<FORM action="partner_add_clients.cgi" method="post" onsubmit="selectAllOptions('sel2');">
		<input type="hidden" name="partner_id" value="$partner_id">
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3>Partner: <b>$gname</b></FONT> </TD>
		</TR>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3>Delay(hours): <input type=text size=3 name=delay value="$delay"></FONT> </TD>
		</TR>
end_of_html
if ($dupes eq "Yes")
{
print<<"end_of_html";
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Dupes Only: <input type=radio name=dupes value="Yes" checked>Yes&nbsp;&nbsp:<input type=radio name=dupes value="No">No</FONT> </TD></TR>
end_of_html
}
else
{
print<<"end_of_html";
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Dupes Only: <input type=radio name=dupes value="Yes">Yes&nbsp;&nbsp:<input type=radio name=dupes value="No" checked>No</FONT> </TD></TR>
end_of_html
}
if ($byPass eq "Y")
{
print<<"end_of_html";
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>ByPass Regular Processing: <input type=radio name=byPass value="Y" checked>Yes&nbsp;&nbsp:<input type=radio name=byPass value="N">No</FONT> </TD></TR>
end_of_html
}
else
{
print<<"end_of_html";
		<TR><TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>ByPass Regular Processing: <input type=radio name=byPass value="Y">Yes&nbsp;&nbsp:<input type=radio name=byPass value="N" checked>No</FONT> </TD></TR>
print<<"end_of_html";
end_of_html
}
print<<"end_of_html";
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
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
			Use this screen to Edit Partner Clients.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>


<table border="0">
	<tr><td>Available Clients</td><td></td><td>Clients Assigned</td><td>Manually Entered Client Ids</td></tr>
	<tr>
		<td>
			<select name=sel1 id=sel1 size="20" multiple="multiple">
end_of_html
my $fname;
$sql="select user_id,first_name from user where user_id not in (select client_id from PartnerClientInfo where partner_id=$partner_id) and status='A' order by first_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($client,$fname)=$sth->fetchrow_array())
{
	print "<option value=\"$client\">$fname - $client</option>";
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
$sql="select client_id,first_name from PartnerClientInfo,user u where partner_id=? and PartnerClientInfo.client_id=u.user_id order by first_name";
$sth=$dbhu->prepare($sql);
$sth->execute($partner_id);
while (($client,$fname)=$sth->fetchrow_array())
{
	print "<option value=\"$client\">$fname</option>";
}
$sth->finish();
print<<"end_of_html";
			</select>
		</td>
		<td><textarea name=mcid cols=10 rows=20></textarea></td> 
	</tr>
	<tr><td colspan=3 align=middle><input type=submit value="Save" name="submit"></td></tr>
</table>
	</TD>
	</TR>
	</TBODY>
	</TABLE>
</form>

</TD>
</TR>
<TR>
<TD noWrap align=left height=1>
end_of_html

$pms->footer();
exit(0);
