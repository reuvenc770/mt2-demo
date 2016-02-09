#!/usr/bin/perl
#===============================================================================
# Purpose: Top frame of rep advertiser creative/subject raw report 
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
my $aid=$query->param('aid');
my $sql;
my $sth;
my $id;
my $company;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Schedule Details</title>
</head>

<body>
<form name=campform method=get action="/cgi-bin/rep_adv_subject_creative_raw.cgi" target="bottom">
<input type=hidden name=aid value=$aid>
<table border="0" width="60%" id="table24">
	<tr>
		<td>
									<b><font face="Verdana" size="2">Client:</font></b></td>
		<td>
											<font face="Arial" color="#509c10" size="2">
											<select name="clientid" id="client_id">
<option value=0 selected>ALL</option>
end_of_html
$sql="select user_id,first_name from user where status='A' order by first_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($id,$company) = $sth->fetchrow_array())
{
	print "<option value=$id>$company</option>\n";
}
$sth->finish();
my $sdate;
$sql="select curdate()"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($sdate)=$sth->fetchrow_array();
$sth->finish();
print<<"end_of_html";
											</select></font></td>
<td>Start Date: <input type=text name=sdate size=11 maxlength=10 value='$sdate'></td>
<td>End Date: <input type=text name=edate size=11 maxlength=10 value='$sdate'></td>
<td><input type="submit" value="Go"> </td>
	</tr>
	</table>
</body>
</html>
end_of_html
