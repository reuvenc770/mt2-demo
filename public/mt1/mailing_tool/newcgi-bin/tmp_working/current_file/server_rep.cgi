#!/usr/bin/perl

# ******************************************************************************
# server_rep.cgi
# ******************************************************************************
#
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
my $list_id;
my $cserver;
my $list_name;
my $bgcolor;
my $reccnt;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $light_table_bg = $util->get_light_table_bg;
my $table_text_color = $util->get_table_text_color;
my $status_name;
my $status;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# print out the html page
print "Content-type: text/html\n\n";
print << "end_of_html";
<html><head><title></title><head>
<body>
<center>
<br>
		<form method=get action="/cgi-bin/server_repa.cgi" target=bottom>
		<TABLE cellSpacing=5 cellPadding=0 width="40%" border=0>
		<TBODY>
		<TR> 
		<TD bgcolor="#EBFAD1" ><b>Server</b></td>
		<TD bgcolor="#EBFAD1" ><select name=cserver>
		<option value=0>ALL</option>
end_of_html

$sql = "select id,server from server_config where inService=1 order by server"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($list_id,$cserver) = $sth->fetchrow_array())
{
	print "<option value=$list_id>$cserver</option>\n";
}

$sth->finish();

print << "end_of_html";
		</select></td>
		<td bgcolor="#EBFAD1">Date: <select name=cmonth><option value=C>Today</option><option value=Y>Yesterday</option><option value=7>Last 7</option><option value=0>Current Month</option><option value=1>Last Month</option></select></td>
		<td><input type=submit name="go" value="go"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi" target=_top>
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
</body>
</html>
end_of_html

# exit function

$util->clean_up();
exit(0);
