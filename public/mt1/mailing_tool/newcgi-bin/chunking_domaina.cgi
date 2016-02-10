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
		<form method=post action="/cgi-bin/chunking_domain_sav.cgi" target=bottom>
		<input type=hidden name=pid value=$pid>
		<TABLE cellSpacing=5 cellPadding=0 width="30%" border=0>
		<TBODY>
		<TR> 
		<Th bgcolor="#EBFAD1" >Domain</th>
		<Th bgcolor="#EBFAD1" >Amount to Add</th>
		<Th bgcolor="#EBFAD1" >Max Amount</th>
</tr>
end_of_html
my $did;
my $dname;
my $add_amount;
my $max_amount;
my $sth1;
$sql = "select domain_id,domain_name from email_domains where chunked=1 order by domain_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($did,$dname) = $sth->fetchrow_array())
{
	print "<tr><td align=center>$dname</td>";
	if ($pid > 0)
	{
		$sql="select add_amount,max_amount from profile_chunk_add where (profile_id=? or profile_id=0) and domain_id=? order by profile_id desc";
	}
	else
	{
		$sql="select add_amount,max_amount from profile_chunk_add where profile_id=? and domain_id=?";
	}
	$sth1=$dbhq->prepare($sql);
	$sth1->execute($pid,$did);
	if (($add_amount,$max_amount) = $sth1->fetchrow_array())
	{
	}
	else
	{
		$add_amount=0;
		$max_amount=-1;
	}
	$sth1->finish();
	print "<td align=center><input type=text name=\"add_$did\" value=\"$add_amount\" size=5></td>";
	print "<td align=center><input type=text name=\"max_$did\" value=\"$max_amount\" size=5></td>";
	print "</tr>\n";
}

$sth->finish();

print << "end_of_html";
		</select></td>
		<td colspan=3 align=center><input type=submit name="go" value="Save"></td></tr>
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
