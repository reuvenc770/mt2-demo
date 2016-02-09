#!/usr/bin/perl

# *****************************************************************************************
# list_category.cgi
#
# this page displays the list of campaign categories and lets the user edit / add
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
my $sth1;
my $sid;
my $sname;
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

util::header("Campaign Categories");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#999999" border="0" id="table4">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="760" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td>
<table cellSpacing="0" cellPadding="0" width="760" bgColor="#ffffff" border="0" id="table9">
					<tr>
						<td>
						<form method="post" action="/cgi-bin/add_category.cgi">
							<input type="hidden" value="1" name="userid0">
							<b><a href="/cgi-bin/category_brand_info.cgi">Select Subdomains 
							by Client</a></b></form>
						</td>
					</tr>
				</table>
						</td>
					</tr>
					<tr>
						<td vAlign="top" align="middle">
						<a href="/cgi-bin/mainmenu.cgi">
						<img src="/images/home_blkline.gif" border="0"></a></td>
					</tr>
					<tr>
						<td>
						<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
					Select a category to edit or add a new category</font></td>
					</tr>
					<tr>
						<td>
						<img height="15" src="/images/spacer.gif"></td>
					</tr>
				</table>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<TABLE cellSpacing=0 cellPadding=3 width="83%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD width="48%" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Category Names</b></font></TD>
		<td align="middle" colSpan="2" height="15">
				<p align="left">
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
						<b>Brand Subdomains</b></font></td>
		</TR>
end_of_html

# read info about the lists

$sql = "select category_info.category_id,category_name from category_info order by category_name"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($cat_id,$category_name) = $sth->fetchrow_array())
{
	$reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg";
    }
    else
    {
        $bgcolor = "$alt_light_table_bg";
    }
print<<"end_of_html";
<form method=get action="/cgi-bin/brandsubdomain.cgi">
<input type=hidden name=cid value=$cat_id>
	<TR bgColor=$bgcolor><TD align=left><font color="#509C10" face="verdana,arial,helvetica,sans serif" size="2"><A HREF="add_brandsubdomain.cgi?cid=$cat_id">$category_name</a>
			</font></TD> 
 		<TD><select name=brandid>
end_of_html
$sql = "select subdomain_id,subdomain_name from brandsubdomain_info where category_id=$cat_id order by subdomain_name";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($sid,$sname) = $sth1->fetchrow_array())
{
	print "<option value=$sid>$sname</option>\n";
}
$sth1->finish();
print<<"end_of_html";
</select>
</TD> 
<td width="21%">
<input type=submit name=btn1 value="Add"><input type=submit name=btn1 value="Edit"><input type=submit name=btn1 value="Delete"></td></tr></form>
end_of_html
}

$sth->finish();

print << "end_of_html";
		<TR>
		<TD colspan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD colspan=3>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
			<td width=50% align="center">
				<a href="mainmenu.cgi">
				<img src="$images/home_blkline.gif" border=0></a></TD>
			</tr>
			</table>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
</table>
</body>
</html>
end_of_html

# exit function

$pms->clean_up();
exit(0);
