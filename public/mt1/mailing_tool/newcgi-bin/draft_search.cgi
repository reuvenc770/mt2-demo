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
my $cid;
my ($aname,$aid,$cname,$assigned_date,$due_date,$updated_date,$completed);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
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
		self.parent.location="/cgi-bin/edit_draft.cgi?cid="+cid;
	}
	function delete_creative(aid,cid)
	{
		self.parent.location="/cgi-bin/delete_draft.cgi?cid="+cid;
	}
	function approve_draft(cid)
	{
		self.parent.location="/cgi-bin/approve_draft.cgi?cid="+cid;
	}
	function preview_creative(cid) {
		window.open("/cgi-bin/camp_draft_preview.cgi?campaign_id="+cid+"&format=H",'Preview','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50');
	}
	</SCRIPT>

				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table6">
					<tr bgColor="#509c10" height="15">
						<td align="middle" width="100%" colSpan="9" height="15">
						<b>
						<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
						Draft Creatives</font></b></td>
					</tr>
					<tr>
						<td align="left" width="2%" bgColor="#ebfad1">&nbsp;</td>
						<td align="left" width="58%" bgColor="#ebfad1">
						<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
						<b>Advertiser Name</b></font></td>
						<td align="left" width="26%" bgColor="#ebfad1">
						<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
						<b>Creative Name</b></font></td>
						<td align="left" width="30%" bgColor="#ebfad1">
						<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
						<b>Functions</b></font></td>
						<td align="left" width="26%" bgColor="#ebfad1"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
						<b>Designer&nbsp;&nbsp;&nbsp; </b></font></td>
						<td align="left" width="26%" bgColor="#ebfad1"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
						<b>Assigned&nbsp;&nbsp;&nbsp;&nbsp; </b></font></td>
</td>
						<td align="left" width="15%" bgColor="#ebfad1">
						<b>
						<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
						Due_Date&nbsp;&nbsp;&nbsp; </font></b></td>
						<td align="left" width="9%" bgColor="#ebfad1"><b>
						<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
						Updated&nbsp;&nbsp;&nbsp; </font></b></td>
						<td align="left" width="1%" bgColor="#ebfad1"><b>
						<font face="verdana,arial,helvetica,sans serif" size="2" color="#509C10">
						Completed</font></b><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><b>&nbsp;&nbsp; </b></font></td>
					</tr>
end_of_html
$sql="select advertiser_name,draft_creative.advertiser_id,creative_name,creative_id,designer_name,assigned_date,due_date,updated_date,completed from draft_creative,advertiser_info,designer where draft_creative.advertiser_id=advertiser_info.advertiser_id and draft_creative.designer_id=designer.designer_id and draft_creative.status='A'";
my $adname=$query->param('adname');
if ($adname ne "")
{
	$sql = $sql . " and advertiser_info.advertiser_name like '%${adname}%'";
}
my $designer=$query->param('designer');
if ($designer > 0)
{
	$sql = $sql . " and draft_creative.designer_id=$designer";
}
my $assigned1=$query->param('assigned1');
if ($assigned1 ne "")
{
	my $assigned2=$query->param('assigned2');
	$sql = $sql . " and assigned_date $assigned1 date_sub(curdate(),interval $assigned2 day)";
}
my $due1=$query->param('due1');
if ($due1 ne "")
{
	my $due2=$query->param('due2');
	if ($due1 eq "<")
	{
		$sql = $sql . " and due_date >= curdate() and due_date $due1 date_add(curdate(),interval $due2 day)";
	}
	else
	{
		$sql = $sql . " and due_date $due1 date_add(curdate(),interval $due2 day)";
	}
}
my $updated1=$query->param('updated1');
if ($updated1 ne "")
{
	my $updated2=$query->param('updated2');
	$sql = $sql . " and updated_date $updated1 date_sub(curdate(),interval $updated2 day)";
}
my $needs_approval=$query->param('needs_approval');
if ($needs_approval ne "")
{
	$sql = $sql . " and draft_creative.completed='$needs_approval'";
}
$sql = $sql . " order by advertiser_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
open(LOG,">/tmp/jim.");
print LOG "<$sql>\n";
close(LOG);
while (($aname,$aid,$cname,$cid,$dname,$assigned_date,$due_date,$updated_date,$completed) = $sth->fetchrow_array())
{
print<<"end_of_html"
	<tr bgColor="#d6c6ff"><td align="left">&nbsp;</td><td align="left"><font face="Arial" color="#509c10" size="2"><a target="_blank" href="/cgi-bin/view_thumbnails.cgi?aid=$aid">$aname</a></font></td>
		<td align="left"><font face="Arial" color="#509c10" size="2"><a target="_top" href="/cgi-bin/edit_draft.cgi?cid=$cid">$cname</a></font></td>
end_of_html
if ($completed eq "Y")
{
print<<"end_of_html"
		<td align="left"><font face="Arial" color="#509c10" size="2"><input onclick="edit_creative($aid,$cid);" type="button" value="Edit"><input onclick="delete_creative($aid,$cid);" type="button" value="Delete"><input onclick="javascript:preview_creative($cid);" type="button" value="Preview"><input onclick="javascript:approve_draft($cid);" type="button" value="Approve"></font></td>
end_of_html
}
else
{
print<<"end_of_html"
		<td align="left"><font face="Arial" color="#509c10" size="2"><input onclick="edit_creative($aid,$cid);" type="button" value="Edit"><input onclick="delete_creative($aid,$cid);" type="button" value="Delete"><input onclick="javascript:preview_creative($cid);" type="button" value="Preview"></font></td>
end_of_html
}
print<<"end_of_html"
		<td align="left"><font face="Arial" size="2">$dname</font></td>
		<td align="left"><font face="Arial" size="2">$assigned_date</font></td>
		<td align="left"><font face="Arial" size="2">$due_date</font></td>
		<td align="left"><font face="Arial" size="2">$updated_date</font></td>
		<td align="left">$completed</td>
		</tr>
end_of_html
}
$sth->finish();
print<<"end_of_html";
				</table>
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
