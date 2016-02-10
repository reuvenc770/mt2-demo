#!/usr/bin/perl
# *****************************************************************************************
# clientgroup_edit.cgi
#
# this page is to edit an ClientGroup
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
my $gid= $query->param('gid');
my $user_id;
my $gname;
my $excludeFromSuper;
my $excludeFromSuperChecked;
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

$pms->getUserData({'userID' => $user_id});

if($pms->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

# read info for this ClientGroup 

$sql = "select group_name,excludeFromSuper from ClientGroup where $userDataRestrictionWhereClause client_group_id=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($gid);
($gname,$excludeFromSuper) = $sth->fetchrow_array();
$sth->finish();
$excludeFromSuperChecked="";
if ($excludeFromSuper eq "Y")
{
	$excludeFromSuperChecked="checked";
}


# print out the html page

util::header("Edit Client Group");

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
		<FORM action="clientgroup_add_clients.cgi" method="post" onsubmit="selectAllOptions('sel2');">
		<input type="hidden" name="gid" value="$gid">
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3>Group: <input type=text size=20 name=gname value="$gname">&nbsp;&nbsp;&nbsp;Exclude from Super&nbsp;<input type=checkbox value="Y" name=excludeFromSuper $excludeFromSuperChecked></FONT> </TD>
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
			Use this screen to Edit Client Group.</FONT><br>
			<strong><font color='red'>NB: Clients in highlighed in 'red' should only be added to client groups if you understand how they should be mailed.</font></strong>
			</TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>


<table border="0">
	<tr><td>Available Clients</td><td></td><td>Clients in Group</td><td>Manually Entered Client Ids</td></tr>
	<tr>
		<td>
		
		<style tyle=text/css>
			option.red {background-color: #cc0000;}
		</style>
		
			<select name=sel1 id=sel1 size="20" multiple="multiple">
end_of_html
my ($fname, $hasClientGroupRestriction);
$sql="select user_id,username, hasClientGroupRestriction from user where user_id not in (select client_id from ClientGroupClients where client_group_id=$gid) and status='A' order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($client,$fname,$hasClientGroupRestriction)=$sth->fetchrow_array())
{
	my $class = 'white';
	
	if($hasClientGroupRestriction)
	{
		$class="red";
	}
	
	print qq|<option class='$class' value=\"$client\">$fname</option>\n|;
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
$sql="select client_id, username from ClientGroupClients, ClientGroup, user u where ClientGroupClients.client_group_id=? and ClientGroupClients.client_id=u.user_id and ClientGroup.client_group_id=ClientGroupClients.client_group_id order by username";
$sth=$dbhu->prepare($sql);
$sth->execute($gid);
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
