#!/usr/bin/perl

# ******************************************************************************
# chunking_domaina.cgi
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
my $pid=$query->param('pid');

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

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
		<TABLE cellSpacing=5 cellPadding=0 width="30%" border=0>
		<TBODY>
		<TR> 
		<Th bgcolor="#EBFAD1" >List Name</th>
		<Th bgcolor="#EBFAD1" >Domain</th>
		<Th bgcolor="#EBFAD1" >Record Count</th></tr>
end_of_html
my $dname;
my $lname;
my $reccnt;
my $sth1;
$sql = "select list_name,domain_name,record_cnt from list,list_cnt,email_domains,list_profile_list where list.list_id=list_cnt.list_id and list_cnt.domain_id=email_domains.domain_id and record_cnt > 0 and list.status='A' and list_profile_list.profile_id=$pid and list_profile_list.list_id=list.list_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($lname,$dname,$reccnt) = $sth->fetchrow_array())
{
	print "<tr><td align=center>$lname</td>";
	print "<td align=center>$dname</td>";
	print "<td align=center>$reccnt</td>";
	print "</tr>\n";
}

$sth->finish();

print << "end_of_html";
		</td></tr>
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
</body>
</html>
end_of_html

# exit function

$util->clean_up();
exit(0);
