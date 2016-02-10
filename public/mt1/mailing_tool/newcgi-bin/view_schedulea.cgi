#!/usr/bin/perl

# *****************************************************************************************
# view_schedulea.cgi
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
my $user_id;
my $link_id;
my $refurl;
my $bgcolor;
my $reccnt;
my $images = $pms->get_images_url;
my $alt_light_table_bg = $pms->get_alt_light_table_bg;
my $light_table_bg = $pms->get_light_table_bg;
my $table_text_color = $pms->get_table_text_color;
my $status_name;
my $status;
my $cat_id;
my $category_name;
my $domain_name;
my $userid = $query->param('userid');
my $stype=$query->param('stype');
if ($stype eq "")
{
	$stype="C";
}
if ($userid eq "")
{
	$userid = 1;
}

# connect to the pms database

###$pms->db_connect();

my ($dbhq,$dbhu)=$pms->get_dbh();
###$dbh = $pms->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# print out the html page
print "Content-type: text/plain\n\n";
print << "end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>New Page 2</title>
</head>
<body>
<br>
<center><a href="/cgi-bin/mainmenu.cgi" target=_top><img src="/images/home_blkline.gif" border=0></a>
</center>
<font face="Verdana" size="2"><b>Network Selector:</b></font><br>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<form name=catform method=get action="/cgi-bin/disp_client_schedule.cgi" target="bottom">
		<input type=hidden name=stype value="$stype">
		<TABLE cellSpacing=0 cellPadding=0 width=300 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td width=20%><b>Client: </b></td>
		<td align=left><select name=clientid>
end_of_html
$sql = "select user_id,company from user where status='A' order by company"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
my $tid;
my $company;
while (($tid,$company) = $sth->fetchrow_array())
{
	if ($tid == $userid)
	{
		print "<option selected value=$tid>$company</option>\n";
	}
	else
	{
		print "<option value=$tid>$company</option>\n";
	}
}
$sth->finish;
print<<"end_of_html";
	</select>
&nbsp;&nbsp;<input type=submit value="Go" name="action"></td></tr>
		</TBODY>
		</TABLE>
		</form>
</body>
</html>
end_of_html
