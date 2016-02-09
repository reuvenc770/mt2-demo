#!/usr/bin/perl
#===============================================================================
# Purpose: edit draft creative screen 
# Name   : edit_draft.cgi 
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
my $name;
my $sql;
my $sth;
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $did;
my $dname;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my $temp_str=$util->date(0,0);
my ($dbhq,$dbhu)=$util->get_dbh();
my $cid=$query->param('cid');
my ($aname,$cname,$notes,$completed,$inactive_date,$thumbnail,$html_code);
$sql="select advertiser_name,creative_name,notes,completed,inactive_date,thumbnail,html_code from draft_creative,advertiser_info where draft_creative.advertiser_id=advertiser_info.advertiser_id and creative_id=$cid";
$sth=$dbhq->prepare($sql);
$sth->execute();
($aname,$cname,$notes,$completed,$inactive_date,$thumbnail,$html_code) = $sth->fetchrow_array();
$sth->finish();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Creative</title>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Edit 
						Creative</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table5">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>Creative Information</b> </font></td>
					</tr>
					<tr>
						<td>
						<img height="3" src="/images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
					<tr>
						<td colSpan="10">
						<font face="verdana,arial,helvetica,sans serif" color="#50" size="2" 9C10>
						To UPDATE the creative information please make the 
						appropriate changes and select <b>Save</b>. </font></td>
					</tr>
				</table>
<SCRIPT language=JavaScript>
        function SaveFunc(btn)
        {
            document.campform.nextfunc.value = btn;
            document.campform.submit();
        }
</SCRIPT>

				<form name="campform" method="post" encType="multipart/form-data" action="/cgi-bin/upd_draft.cgi">
					<input type="hidden" value="$cid" name="cid">
					<input type="hidden" value="" name="nextfunc">
					<br>
					Advertiser: <b>$aname</b><br>
					<br>
					<b>Creative Name:</b><br>
					<input maxLength="255" size="50" value="$cname" name="creative_name"><br><br>
					<b>Notes:</b><br>
					<b>
					<textarea name="notes" rows="9" cols="100">$notes</textarea></b><br><br>
					<b>Completed </b>
end_of_html
if ($completed eq "Y")
{
	print "<input type=checkbox value=Y name=completed checked><br>\n";
}
else
{
	print "<input type=checkbox value=Y name=completed><br>\n";
}
print<<"end_of_html";
					<br>
					<b>Inactive Date: (YYYY/MM/DD) </b><br>
					<input maxLength="10" size="10" value="$inactive_date" name="inactivate_date"><br>
					<br>
end_of_html
if ($thumbnail ne "")
{
print<<"end_of_html";
					<b>Thumbnail for Creative:(<a target="_blank" href="http://www.affiliateimages.com/images/thumbnail/$thumbnail">$thumbnail</a>)<br>
end_of_html
}
else
{
print<<"end_of_html";
					<b>Thumbnail for Creative:<br>
end_of_html
}
print<<"end_of_html";
					<input type="file" maxLength="255" size="50" name="thumbnail"><br><br>
					<u>HTML Code:</u><br>
					<textarea name="html_code" rows="15" cols="100">$html_code</textarea><br>&nbsp; 
					<table id="table8" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="JavaScript:SaveFunc('home');">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<a href="JavaScript:SaveFunc('save');">
							<img height="22" src="/images/save_rev.gif" width="81" border="0"></a></td>
							<td align="middle" width="50%">
							<a href="JavaScript:SaveFunc('preview');">
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></td>
						</tr>
					</table>
				</form>
				<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:SaveFunc('spam');">Run 
				Spam Report</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;
				<p>&nbsp;</p>
				<br>
&nbsp;<p align="center">
				<img src="/images/footer.gif" border="0"></b></td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html
