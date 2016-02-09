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
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Add Draft Creative</title>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table9">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table10">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table11">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Add 
						Draft Creative</font></b></td>
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
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table12">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table13">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>Add Draft Creative</b></font></td>
					</tr>
					<tr>
						<td>
						<img height="3" src="/images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table14">
					<tr>
						<td>
						&nbsp;</td>
					</tr>
				</table>
				<form name="campform" method="post" action="/cgi-bin/insert_draft.cgi">
					<br>
					<b>Advertiser:</b> <br>
					<select name="aid">
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status in ('A','S') order by advertiser_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($did,$dname) = $sth->fetchrow_array())
{
    print "<option value=$did>$dname</option>\n";
}
$sth->finish();
print<<"end_of_html";
					</select><br><b>Designer: </b><br><select name="designer">
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
								</select><br>
					<b>Due Date: (YYYY-MM-DD) </b><br>
					<input maxLength="10" size="10" name="due_date"><b><br>Creative Name: </b><br>
					<input maxLength="255" size="50" value="Unnamed" name="creative_name"><br><br>
					<b>&nbsp; 
					<table id="table15" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/draft.html"><img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input type=image height="22" src="/images/save_rev.gif" width="81" border="0"></td>
							<td align="middle" width="50%">
							&nbsp;</td>
						</tr>
					</table>
				</form>
				<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<p>&nbsp;</p>
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
