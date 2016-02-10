#!/usr/bin/perl

# *****************************************************************************************
# advertiser_setup_top.cgi
#
# this page display main page for setting up creative info for an advertiser 
#
# History
# Jim Sobeck, 01/05/06, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sth2;
my $old_pid;
my $linkcnt;
my $temp_id;
my $company;
my $sql;
my $dbh;
my $aid = $query->param('aid');
my $cid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $cname;
my $sdate;
my $sdate1;
my $hour;
my $acatid;
my $content_id;

#------ connect to the util database ------------------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = 0;
##while (!$dbh)
##{
###$dbh = $util->get_dbh;
##}
##$dbh->{mysql_auto_reconnect}=1;

#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Advertiser Setup</title>
</head>

<body>
<center>
<table cellSpacing="0" cellPadding="0" bgColor="#ffffff" border="0" id="table1">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="719" border="0" id="table2">
			<tr>
				<td width="248" rowSpan="2">&nbsp;</td>
				<td width="328" >&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table3">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Advertiser Setup</font></b></td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td vAlign="top" align="center" bgColor="#999999">
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#999999" border="0" id="table4">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<form name="campform" method="post" action="/cgi-bin/advertiser_setupa_new.cgi" target="main">
					<input type="hidden" name="aid" value=$aid>
				<select name=did>
end_of_html
my $cid;
my $cname;
$sql = "select class_id,class_name from email_class where status='Active' order by class_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname) = $sth->fetchrow_array())
{
	if ($cid == 4)
	{
		print "<option selected value=$cid>$cname</option>\n";
	}
	else
	{		
		print "<option value=$cid>$cname</option>\n";
	}
}
$sth->finish;
print<<"end_of_html";
				</select>
	<input type=submit value="Load">
				</form>
			</td></tr></table>
			</td></tr></table>
</body>
</html>
end_of_html
exit(0);
