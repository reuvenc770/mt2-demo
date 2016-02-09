#!/usr/bin/perl
# *****************************************************************************************
# listprofile_add.cgi
#
# this page is to add a new list profile 
#
# History
# Jim Sobeck, 5/31/05, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_id;
my $class_id;
my $class_name;
my $list_name;
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;
my $tflag=$query->param('tflag');
if ($tflag eq "")
{
	$tflag="N";
}

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add List Profile</title>
<script language="JavaScript">
function selectall1()
{
    refno=/domains/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function unselectall1()
{
    refno=/domains/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
</script>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table10">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="900" border="0" id="table11">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">
				<img src="/images/header.gif" border="0"></td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table12">
	
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
		</td>
	</tr>
	<tr>
		<td vAlign="top" align="left" bgColor="#999999">
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#999999" border="0" id="table13">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table14">
					
					<tr>
						<td><font face="Arial">
						<img height="5" src="/images/spacer.gif"></font></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr bgColor="#509c10">
				<td>
<SCRIPT language=JavaScript>

function selectall()
{
	refno=/list_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
           	document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
	refno=/list_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
</SCRIPT>

				<form name="adform" method="post" action="/cgi-bin/listprofile_ins.cgi">
				<input type=hidden name=tflag value=$tflag>
					<table cellSpacing="0" cellPadding="0" width="100%" bgColor="#e3fad1" border="0" id="table16">
						<tr bgColor="#509c10" height="15">
							<td width="14%">&nbsp;</td>
							<td align="left" width="138" height="15">&nbsp;</td>
							<td align="left" width="61" height="15">&nbsp;</td>
							<td width="75">&nbsp;</td>
							<td width="68">&nbsp;</td>
							<td width="63">&nbsp;</td>
							<td width="103">&nbsp;</td>
							<td width="118">&nbsp;</td>
							<td width="131">&nbsp;</td>
						</tr>
						<tr bgColor="#e3fad1">
							<td align="middle">
							<img height="3" src="/images/spacer.gif" width="3" colspan="7"></td>
						</tr>
						<tr>
							<td><font face="Arial" color="#509c10" size="2"><b>&nbsp;&nbsp;&nbsp;&nbsp; </b></font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="Arial" size="2" color="#509C10">
									<b>Network:</b></font></td>
						</tr><tr><td vAlign="center"><select name=network_id>
end_of_html
$sql = "select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $tid;
my $company;
while (($tid,$company) = $sth->fetchrow_array())
{
	print "<option value=$tid>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
						</select>
						</td>
						</tr>
									<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
							<tr>
							<td vAlign="center" align="left" colSpan="9">
							<font face="Arial" size="2" color="#509C10">
									<b>List Profile Name:</b></font></td>
						</tr>
									<tr>
							<td vAlign="center" align="left" colSpan="9">
							<input maxLength="40" size="40" name="profilename"></td>
						</tr>
						
										<tr>
							<td vAlign="center" align="left" colSpan="9">&nbsp;</td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9"><font face="Arial" size="2" color="#509C10">
									<b>Note: Do not select AOL for this network. </b>
							(pulled from a notes field in setup)</font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Send To:<input type="radio" value="N" name="clast60" checked>All&nbsp;&nbsp;&nbsp;<input type="radio" value="T" name="clast60" >Last 3 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="7" name="clast60" >Last 7 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="F" name="clast60" >Last 15 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="M" name="clast60">Last 30 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="Y" name="clast60">Last 60 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="9" name="clast60">Last 90 Days</font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">Send to All Openers/Clickers:<input type="radio" value="Y" name="open_click_ignore" checked>Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="N" name="open_click_ignore" >No</font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
end_of_html
if ($tflag eq "C")
{
print<<"end_of_html";
									Send To: 
									<input type="checkbox" value="Y" name="aolflag" checked>AOL&nbsp;<input type="checkbox" value="Y" name="yahooflag">Yahoo&nbsp;&nbsp;&nbsp;<input type="checkbox" value="Y" name="otherflag">Other Domains</font>&nbsp;<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type="checkbox" value="Y" name="hotmailflag">Hotmail/MSN&nbsp;&nbsp;&nbsp;<input type="checkbox" value=M name=yahooflag1>Yahoo Last 30/Openers&nbsp;</font></td>
end_of_html
}
elsif ($tflag =~ /L|N/)
{
print<<"end_of_html";
									Send To: <a href="javascript:selectall1();">Select All</a>&nbsp;&nbsp;<a href="javascript:unselectall1();">Unselect All</a>&nbsp;&nbsp;
end_of_html
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($class_id,$class_name)=$sth->fetchrow_array())
{
	print "<input type=checkbox value=$class_id id=domains name=domains>${class_name}&nbsp;";
}
$sth->finish();
print "</font></td>";
}
else
{
print<<"end_of_html";
									Send To: 
									<input type="checkbox" value="Y" name="aolflag">AOL&nbsp;<input type="checkbox" CHECKED value="Y" name="yahooflag">Yahoo&nbsp;&nbsp;&nbsp;<input type="checkbox" CHECKED value="Y" name="otherflag">Other Domains</font>&nbsp;<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><input type="checkbox" value="Y" name="hotmailflag">Hotmail/MSN&nbsp;&nbsp;&nbsp;<input type="checkbox" value=M name=yahooflag1>Yahoo Last 30/Openers&nbsp;</font></td>
end_of_html
}
print<<"end_of_html";
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9"><font face="verdana,arial,helvetica,sans serif" color="#509c10" size=2>Select Openers/Clickers/All Datatypes: <input type=radio value=Y name=alllist>Yes&nbsp;&nbsp<input type=radio checked value=N name=alllist>No</font></td>
						</tr>
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
end_of_html
if ($tflag eq "L")
{
	print "<tr><td vAlign=\"center\" align=\"left\" colSpan=\"9\"><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>\n";
	print "NewsLetter: <select name=nl_id>\n";
	$sql="select nl_id,nl_name from newsletter where nl_status='A' order by nl_name";
	my $s1=$dbhq->prepare($sql);
	$s1->execute();
	my $nl_id;
	my $nl_name;
	while (($nl_id,$nl_name)=$s1->fetchrow_array())
	{
		print "<option value=$nl_id>$nl_name</option>\n";
	}
	$s1->finish();
	print "</select></td></tr>\n";
	print "<tr><td vAlign=\"center\" align=\"left\" colSpan=\"9\"><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=2>\n";
	print "Send To: <input type=radio value=\"CONFIRMED\" name=nl_send checked>Confirmed&nbsp;&nbsp;&nbsp;<input type=radio value=\"UNCONFIRMED\" name=nl_send>Unconfirmed&nbsp;&nbsp;&nbsp;<input type=radio value=\"ALL\" name=nl_send>ALL</td></tr>\n";
print<<"end_of_html";
						<tr>
							<td vAlign="center" align="left" colSpan="9">
									&nbsp;</td>
						</tr>
end_of_html
}
print<<"end_of_html";
					</table>
					&nbsp;&nbsp;</P>
				</td>
			</tr>
			<tr bgColor="white">
				<td height="65">
				<table cellSpacing="0" cellPadding="7" width="100%" bgColor="white" border="0" id="table18">
					<tr>
						<td align="middle">
						<a href="/cgi-bin/mainmenu.cgi"><img src="/images/cancel.gif" border="0"></a>&nbsp;&nbsp;&nbsp;<input type="image" src="/images/save.gif" border="0" name="I1"></a></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
				</form>

</body>

</html>
end_of_html

# exit function
$util->clean_up();
exit(0);
