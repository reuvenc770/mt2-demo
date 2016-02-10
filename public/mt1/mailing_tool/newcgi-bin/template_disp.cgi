#!/usr/bin/perl
#===============================================================================
# Purpose: Edit template data 
# Name   : template_disp.cgi 
#
#--Change Control---------------------------------------------------------------
# 05/31/07  Jim Sobeck  Creation
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
my $dbh;
my $template_name;
my $html_code;
my $notes;
my $nl_id = $query->param('nl_id');
my $mode = $query->param('mode');
if ($mode eq "A")
{
	$nl_id=0;
}
my $addr;
my $email_addr;
my $typeID;

my $externalUser = 0;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

$util->getUserData({'userID' => $user_id});

my $userDataRestrictionWhereClause = '';

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
    
    $externalUser=1;
}

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select template_name,html_code,notes,mailingTemplateTypeID from brand_template where $userDataRestrictionWhereClause template_id=$nl_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($template_name,$html_code,$notes,$typeID) = $sth->fetchrow_array();
$sth->finish();
$html_code=CGI::escapeHTML($html_code);

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CUSTOM HTML TEMPLATE SETUP</title>
<script language="JavaScript">
function preview(template)
{
    document.edit_template.backto.value="/cgi-bin/brand_template_preview.cgi?temp_id=$nl_id&aid="+document.edit_template.adv_id.value+"&bid="+document.edit_template.brand_id.value;
    document.edit_template.submit();
}
</script>
</head>

<body>

<table align="center" id="table2">
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<form name="edit_template" method="post" action="/newcgi-bin/upd_template.cgi" accept-charset="UTF-8">
		<input type=hidden name=temp_id value=$nl_id>
		<input type=hidden name=backto value="">
			<font style="FONT-SIZE: 12px; FONT-FAMILY: Trebuchet MS, Arial">
			<p>&nbsp;</p>
			<table id="table3" cellSpacing="0" cellPadding="0" width="100%" border="0">
				<tr>
					<td bgColor="#ffffff"><b>CUSTOM HTML TEMPLATE SETUP</b></td>
				</tr>
			</table>
			<p><b>Template name:</b>&nbsp;&nbsp;<input type=text size=50 maxlength=80 name=template_name value="$template_name"> 
			<p><b>Template Type:</b>&nbsp;&nbsp;<select name=template_type> 
end_of_html
$sql="select mailingTemplateTypeID,mailingTemplateTypeLabel from MailingTemplateType order by mailingTemplateTypeLabel";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tlabel;
while (($tid,$tlabel)=$sth->fetchrow_array())
{
	if ($tid == $typeID)
	{
		print "<option value=$tid selected>$tlabel</option>\n";
	}
	else
	{
		print "<option value=$tid>$tlabel</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>
			<p><b>HTML code for custom template: </b>(<a href="/tag.html" target=_blank>list of template tags</a>)&nbsp;&nbsp;Host Images: <input type=checkbox checked name=host_images value=Y><br>
			<textarea name="nl_template" rows="15" cols="82">$html_code</textarea> <br>
			<p><b>Notes: </b><br>
			<textarea name="notes" rows="5" cols="82">$notes</textarea> <br>
<br>
			<b>preview with: </b><br>
<b>Brand: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="brand_id">
end_of_html
$sql="select brand_id,brand_name from client_brand_info where status ='A' order by brand_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $bid;
my $bname;
while (($bid,$bname)=$sth->fetchrow_array())
{
	if($externalUser)
	{
		$bname = $bid;
	}
	
	print "<option value=$bid>$bname</option>";
}
$sth->finish();
print<<"end_of_html";
			</select> <b>
<br>
Advertiser: <select name="adv_id">
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status in ('A') order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth->fetchrow_array())
{
	if($externalUser)
	{
		$aname = $aid;
	}
	
	print "<option value=$aid>$aname</option>";
}
$sth->finish();
print<<"end_of_html";
			</select> <b>
			<font style="font-size: 12px; font-family: 'Trebuchet MS', Arial">
			<a href="JavaScript:preview('N');">
			<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></font></b></p>
			</font>
		</td>
	</tr>
</table>
<div align="center">
		<p class="txt" align="center">
		<input type="image" src="/images/save.gif" name="I1">
		<a href="template_list.cgi">
		<img src="/images/cancel.gif" border="0"></a> </p>
	</form>
	</b>
	<p></div>
</body>

</html>
end_of_html
