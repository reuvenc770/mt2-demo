#!/usr/bin/perl
# *****************************************************************************************
# ipexclusion_edit.cgi 
#
# this page is to exclude IPs
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
my $ip;
my $user_id;
my $gname;

my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}
my $gid=$query->param('gid');
if (($gid == 1) and ($user_id != 17) and ($user_id != 23))
{
	print "Location: ipexclusion_list.cgi\n\n";
	exit(0);
}
$sql="select IpExclusion_name from IpExclusion where IpExclusionID=$gid";
$sth=$dbhu->prepare($sql);
$sth->execute();
($gname)=$sth->fetchrow_array();
$sth->finish();

util::header("Ip Exclusions (sendall)");

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
		<FORM action="ipexclusion_add_ips.cgi" method="post" onsubmit="selectAllOptions('sel2');">
		<input type=hidden name=gid value=$gid>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Use this screen to Edit Ips to Exclude.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

<table border="0">
	<tr><td>Name:</td><td><input type=text name=gname size=30 maxlength=30 value="$gname"></td></tr>
	<tr><td colspan=2>Excluded IPs (One per line)</td></tr>
end_of_html
$sql="select IpAddr from IpExclusionIps where IpExclusionID=$gid"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
my $ipstr="";
while (($ip)=$sth->fetchrow_array())
{
	$ipstr=$ipstr.$ip."\n";
}
$sth->finish();
print<<"end_of_html";
<td></td><td><textarea name=paste_ips rows=20 cols=30>$ipstr</textarea></td>
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
