#!/usr/bin/perl
#===============================================================================
# Purpose: Top frame of edit_sent.html page 
# Name   : edit_sent.cgi 
#
#--Change Control---------------------------------------------------------------
# 09/28/05  Jim Sobeck  Creation
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

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Sent</title>
</head>

<body><br><b><font face="Verdana">Edit Sent</font></b><font face="Verdana"><b>:</b></font><br>
<form name=campform method=get action="/cgi-bin/edit_sent.cgi" target="bottom">
<table border="1" width="35%" id="table24">
	<tr>
		<td>
									<b><font face="Verdana" size="2">Network:</font></b></td>
		<td>
											<font face="Arial" color="#509c10" size="2">
											<select name="client_id">
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
											</select></font><input type="submit" value="Go"> </td>
	</tr>
	<tr>
		<td>
									<b><font face="Verdana" size="2">Campaigns:</font></b></td>
		<td>
									<font size="1">
													<select name="cmonth">
					<option value="0" selected>Current Month</option>
					<option value="1">Last Month</option>
</select></font></td>
	</tr>
	</table>
<p align="left">
						<a href="/cgi-bin/mainmenu.cgi" target=_top>
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>

</body>
</html>
end_of_html
