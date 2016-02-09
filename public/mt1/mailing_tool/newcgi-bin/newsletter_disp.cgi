#!/usr/bin/perl
#===============================================================================
# Purpose: Edit newsletter data 
# Name   : newsletter_disp.cgi 
#
#--Change Control---------------------------------------------------------------
# 11/15/06  Jim Sobeck  Creation
# 12/18/06	Jim Sobeck	Re-write based on the new format
# 01/04/07	Jim Sobeck	Added Slots field
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
my ($nl_name,$nl_template,$nl_confirmation,$nl_confirm_cnt,$nl_from,$nl_subject,$aid,$profile_id,$brand_id,$nl_reminder_subject,$nl_reminder,$nl_from_address,$nl_reply_address,$nl_append_subject);
my $nl_slots;
my $send_confirm;
my $nl_id = $query->param('nl_id');
my $mode = $query->param('mode');
if ($mode eq "A")
{
	$nl_id=0;
	$nl_slots=3;
}
my $addr;
my $email_addr;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$nl_confirm_cnt=0;
#
$sql = "select nl_name,nl_template,nl_confirmation,nl_confirm_cnt,nl_from,nl_subject,advertiser_id,profile_id,brand_id,nl_reminder_subject,nl_reminder,nl_from_address,nl_reply_address,nl_append_subject,nl_slots,send_confirm from newsletter where nl_id=$nl_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($nl_name,$nl_template,$nl_confirmation,$nl_confirm_cnt,$nl_from,$nl_subject,$aid,$profile_id,$brand_id,$nl_reminder_subject,$nl_reminder,$nl_from_address,$nl_reply_address,$nl_append_subject,$nl_slots,$send_confirm) = $sth->fetchrow_array();
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Newsletter Setup</title>
</head>
<script language="JavaScript">
function preview(template)
{
    document.edit_newsletter.backto.value="/cgi-bin/nl_preview.cgi?nl_id=$nl_id&tid="+template;
    document.edit_newsletter.submit();
}
function add_article()
{
    document.edit_newsletter.backto.value="/cgi-bin/add_article.cgi?nl_id=$nl_id";
    document.edit_newsletter.submit();
}
function delete_article()
{
    document.edit_newsletter.backto.value="/cgi-bin/del_article.cgi?nl_id=$nl_id&sid="+document.edit_newsletter.article.value;
    document.edit_newsletter.submit();
}
</script>
<body>

<table align="center">
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="top">
<form name="edit_newsletter" method="post" action="upd_newsletter.cgi">
	<input style="font-size: 10px; " type="hidden" value="$nl_id" name="nl_id">
	<input style="font-size: 10px; " type="hidden" name="backto">

	<table width="100%" bgcolor="#FFFFFF" border="0" align="center" cellpadding="8">
	<tr>
		<td align="center" colspan="2" bgcolor="#DFDFDF">
		<font style="font-family: Trebuchet MS, Arial; font-size: 16px;">Newsletter Info</td>
	</tr>
	<tr>
	<td bgcolor="#FFFFFF" width="50%" valign="top">
	<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<p><b>Newsletter Name:</b><br>
	<input style="font-size: 10px; " maxLength="80" size="50" value="$nl_name" name="nl_name" style="font-size: 10px; "><br>
	<b><br>
	<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
end_of_html
if ($send_confirm eq "Y")
{
print<<"end_of_html";
	<p><b>Send Confirmation:&nbsp;<input type=radio name=send_confirm value="Y" checked>Yes&nbsp;&nbsp;<input type=radio name=send_confirm value="N">No
end_of_html
}
else
{
print<<"end_of_html";
	<p><b>Send Confirmation:&nbsp;<input type=radio name=send_confirm value="Y" >Yes&nbsp;&nbsp;<input type=radio name=send_confirm value="N" checked>No
end_of_html
}
print<<"end_of_html";
	</b><br>
	<b><br>
	<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<p><b>Mailing Slots:</b><br>
    <select name="nl_slots"><option value="0" selected>0</option>
end_of_html
my $i=1;
while ($i <= 20)
{
	if ($nl_slots == $i)
	{
        print "<option value=$i selected>$i</option>\n";
	}
	else
	{
        print "<option value=$i>$i</option>\n";
	}
    $i++;
}
print<<"end_of_html";
</select><br>
	<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<p><b>From Name:</b><br>
	<input style="font-size: 10px; " maxLength="80" size="50" value="$nl_from" name="nl_from"><b><p>From Address:</b><br>
	<input style="font-size: 10px; " maxLength="80" size="50" value="$nl_from_address" name="nl_from_address"><br><p>
	<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<b>Reply Email Address:</b><br>
	<input style="font-size: 10px; " maxLength="80" size="50" value="$nl_reply_address" name="nl_reply_address"></td>
	<td bgcolor="#FFFFFF" width="50%" valign="top">
	<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	</td>
	</tr>
	</table>


	<p><hr>
	<font style="font-family: Trebuchet MS, Arial; font-size: 16px; "> <b>1. CONFIRMATION E-MAIL SETUP</b>
	</font>
	<p><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<b>Confirmation Subject:</b><br>
	<input style="font-size: 10px; " maxLength="80" size="70" value="$nl_subject" name="nl_subject"><br>
	<br>

	<b>Confirmation Template:</b><br>
	<textarea name="nl_confirmation" rows="15" cols="82">$nl_confirmation</textarea>

</font><b>
							<a href="JavaScript:preview('C');"><br>
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></b><font style="font-family: Trebuchet MS, Arial; font-size: 12px; "><br><p><b>Reminder Subject:</b><br>
	<input style="font-size: 10px; " maxLength="80" size="70" value="$nl_reminder_subject" name="nl_reminder_subject"><br>


	<p><b>Reminder Template (2nd Confirmation - used when newsletter profile is
	flagged as &quot;reminder&quot;):</b><br>
	<textarea name="nl_reminder" rows="15" cols="82">$nl_reminder</textarea>
<b><a href="JavaScript:preview('R');"><br><img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></b><br>

	<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table1">
		<tr>
			<td bgcolor="#FFFFFF" valign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
				<b>Confirmation Advertiser: </b></td><td width="30"></td>
			<td><font style="font-family: Trebuchet MS, Arial; font-size: 10px; ">
	<select style="font-size: 10px; " name="aid">
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name";
my $s1=$dbhq->prepare($sql); 
$s1->execute();
my $temp_aid;
my $aname;
while (($temp_aid,$aname)=$s1->fetchrow_array())
{
	if ($temp_aid == $aid)
	{
		print "<option selected value=$temp_aid>$aname</option>\n";
	}
	else
	{
		print "<option value=$temp_aid>$aname</option>\n";
	}
}
$s1->finish();
print<<"end_of_html";
	</select><br>
&nbsp;</td>
	</tr>
	<tr>
	<br>
	<td bgcolor="#FFFFFF" valign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<b>Confirmation Brand: </b></td><td>&nbsp;</td>
	<td><font style="font-family: Trebuchet MS, Arial; font-size: 10px; "><select style="font-size: 10px; " name="bid">
end_of_html
$sql="select brand_id,brand_name from client_brand_info where brand_type='Newsletter' and status='A' order by brand_name";
my $s1=$dbhq->prepare($sql); 
$s1->execute();
my $temp_aid;
my $aname;
while (($temp_aid,$aname)=$s1->fetchrow_array())
{
	if ($temp_aid == $brand_id)
	{
		print "<option selected value=$temp_aid>$aname</option>\n";
	}
	else
	{
		print "<option value=$temp_aid>$aname</option>\n";
	}
}
$s1->finish();
print<<"end_of_html";
	</select><br>
&nbsp;</td>
	<br>
	</tr>
	<tr>
	<td bgcolor="#FFFFFF" valign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<b>Confirmation Profile: </b></td><td>&nbsp;</td>
	<td><font style="font-family: Trebuchet MS, Arial; font-size: 10px; "><select style="font-size: 10px; " name="pid">
end_of_html
$sql="select profile_id,profile_name,company from list_profile,user where list_profile.status='A' and profile_type='NEWSLETTER' and list_profile.client_id=user.user_id order by profile_name"; 
my $s1=$dbhq->prepare($sql); 
$s1->execute();
my $temp_aid;
my $aname;
my $company;
while (($temp_aid,$aname,$company)=$s1->fetchrow_array())
{
	if ($temp_aid == $profile_id)
	{
		print "<option selected value=$temp_aid>$aname - $company</option>\n";
	}
	else
	{
		print "<option value=$temp_aid>$aname - $company</option>\n";
	}
}
$s1->finish();
print<<"end_of_html";
	</select><br>
&nbsp;</td>
	</tr>
	<tr>
	<td bgcolor="#FFFFFF" valign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
	<b>Confirmation Count (per 24 hours): </b></td><td>&nbsp;</td>
	<td><input style="font-size: 10px; " maxLength="8" size="10" value="$nl_confirm_cnt" name="nl_confirm_cnt"><br>
	<br>
	<br>
	</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" colspan="3"><font style="font-family: Trebuchet MS, Arial; font-size: 16px; ">
		<hr>
		<b>2. NEWSLETTER E-MAIL SETUP</b><br>
		<font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
		(values are rotated w/ random start point)<br>
&nbsp;</td>
	</tr>
		<tr>
			<td bgcolor="#FFFFFF" vAlign="top"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
			<b>
			<a href="JavaScript:add_article();">Newsletter Articles</a>: <br>
			&nbsp;</b></font></td>
			<td bgcolor="#FFFFFF" valign="top">&nbsp;</td>
			<td bgcolor="#FFFFFF" valign="top"><select style="font-size: 10px; " name="article" id="article">
end_of_html
$sql="select nl_article.article_id,article_name from nl_article,article where nl_id=$nl_id and nl_article.article_id=article.article_id order by article_name";
my $sth1a=$dbhq->prepare($sql);
$sth1a->execute();
my $taid;
my $tname;
while (($taid,$tname) = $sth1a->fetchrow_array())
{
	print "<option value=$taid>$tname</option>\n";
}
$sth1a->finish();
print<<"end_of_html";
			</select><font style="font-family: Trebuchet MS, Arial; font-size: 12px; "><input style="font-size: 10px; " onclick="add_article();" type="button" value="Add"><input style="font-size: 10px; " onclick="delete_article();" type="button" value="Delete"><font style="font-family: Trebuchet MS, Arial; font-size: 12px; "> <br>
&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" vAlign="top"><b><font style="font-family: Trebuchet MS, Arial; font-size: 12px; ">
			Default subject tag:</b><br>(advertiser subject will be appended, if one exists)</td>
			<td>&nbsp;</td>
			<td><input style="font-size: 10px; " maxLength="80" size="70" value="$nl_append_subject" name="nl_append_subject"><br>&nbsp;</td>
		</tr>

	</table>
	<p>
	<b>Newsletter Template:</b><br>
	<textarea name="nl_template" rows="15" cols="82">$nl_template</textarea>
<br>
	<b>
							<a href="JavaScript:preview('N');">
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></b><br>
<hr>
&nbsp;</p>
	<p>
	<input style="font-size: 10px; " type="image" height="22" width="81" src="/images/save_rev.gif" border="0" name="I1"> <a href="/cgi-bin/newsletter_list.cgi"><img src="/images/cancel_blkline.gif" border="0"></a></p>
</form>
</font>

		</td>
	</tr>
</table>
</body>

</html>
end_of_html
