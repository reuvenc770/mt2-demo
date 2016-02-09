#!/usr/bin/perl
#===============================================================================
# Purpose: op frame of draft.html page 
# Name   : draft.cgi 
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
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Draft Creative</title>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table1" width="1006">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table2">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Date: $temp_str </font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="TEXT-DECORATION: none" href="http://69.45.78.226:83/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="TEXT-DECORATION: none" href="http://69.45.78.226:83/cgi-bin/wss_support_form.cgi">
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
	<tr>
		<td vAlign="top" align="left" bgColor="#ffffff">
		<table cellSpacing="0" cellPadding="0" bgColor="#ffffff" border="0" id="table4">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table5">
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
<SCRIPT language=JavaScript>
	function add_creative(aid)
	{
		self.parent.location="/cgi-bin/add_creative.cgi?backto=creative&aid="+aid;
	}
	function edit_creative(aid,cid)
	{
		self.parent.location="/cgi-bin/edit_creative.cgi?backto=creative&aid="+aid+"&cid="+cid;
	}
	function delete_creative(aid,cid)
	{
		self.parent.location="/cgi-bin/delete_creative.cgi?backto=creative&aid="+aid+"&cid="+cid;
	}
	function preview_creative(cid) {
		window.open("/cgi-bin/camp_draft_preview.cgi?campaign_id="+cid+"&format=H",'Preview','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50');
	}
	</SCRIPT>
<form method=post action="/cgi-bin/draft_search.cgi" target=middle>
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table6">
					<tr>
						<td align="middle" colSpan="9">
						<font face="verdana,arial,helvetica,sans serif" color="#509c10">
						<b><p>Draft Creative</b></font><table id="table7" width="100%" border="1"></p>
							<a href="/cgi-bin/add_draft.cgi" target=_top>
							<img border="0" src="/images/add.gif" width="76" height="23"></a>
						<a href="/cgi-bin/mainmenu.cgi" target=_top>
						<img src="/images/home_blkline.gif" border="0"></a><tr>
								<td width="134"><b>Advertiser Name</b></td>
								<td><input maxLength="30" name="adname"> </td>
								<td width="134"><b>
								<font face="Verdana" size="2">Designer:</font></b></td>
								<td><select name="designer">
								<option value=0 selected></option>
end_of_html
$sql="select designer_id,designer_name from designer order by designer_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($did,$dname) = $sth->fetchrow_array())
{
    print "<option value=$did>$dname</option>\n";
}
$sth->finish();
print<<"end_of_html";
								</select></td>
								</tr>
							<tr>
								<td width="134"><b>
								<font face="Verdana" size="2">Assigned:</font></b></td>
								<td><select name="assigned1">
								<option value selected></option>
								<option value="=">=</option>
								<option value="&lt;">&gt;</option>
								<option value="&gt;">&lt;</option>
								</select><select name="assigned2">
								<option value selected></option>
								<option value="1">1 Day</option>
								<option value="2">2 Days</option>
								<option value="3">3 Days</option>
								<option value="4">4 Days</option>
								<option value="5">5 Days</option>
								<option value="6">6 Days</option>
								<option value="7">1 Week</option>
								<option value="14">2 Weeks</option>
								<option value="30">1 Month</option>
								</select></td>
								<td width="134"><b>
								<font face="Verdana" size="2">Due Date:</font></b></td>
								<td><select name="due1">
								<option value selected></option>
								<option value="=">=</option>
								<option value="&lt;">&lt;</option>
								<option value="&gt;">&gt;</option>
								</select><select name="due2">
								<option value selected></option>
								<option value="1">1 Day</option>
								<option value="2">2 Days</option>
								<option value="3">3 Days</option>
								<option value="4">4 Days</option>
								<option value="5">5 Days</option>
								<option value="6">6 Days</option>
								<option value="7">1 Week</option>
								<option value="14">2 Weeks</option>
								<option value="30">1 Month</option>
								</select></td>
							</tr>
							<tr>
								<td width="134"><b>
								<font face="Verdana" size="2">Updated:</font></b></td>
								<td><select name="updated1">
								<option value selected></option>
								<option value="=">=</option>
								<option value="&lt;">&gt;</option>
								<option value="&gt;">&lt;</option>
								</select><select name="updated2">
								<option value selected></option>
								<option value="1">1 Day</option>
								<option value="2">2 Days</option>
								<option value="3">3 Days</option>
								<option value="4">4 Days</option>
								<option value="5">5 Days</option>
								<option value="6">6 Days</option>
								<option value="7">1 Week</option>
								<option value="14">2 Weeks</option>
								<option value="30">1 Month</option>
								</select></td>
								<td width="134"><b>
								<font face="Verdana" size="2">Needs Approval:</font></b></td>
								<td><select name="needs_approval">
								<option value selected></option>
								<option value="Y">Y</option>
								<option value="N">N</option>
								</select></td>
								</tr>
							<tr>
								<td width="134">
								<input type="submit" value="Submit" name="B28"></td>
								<td>&nbsp;</td>
								<td width="134">
								&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				</form>
				<p>
						<a href="/cgi-bin/mainmenu.cgi">
						<img src="/images/home_blkline.gif" border="0"></a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html
