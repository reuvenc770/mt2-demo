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

$pms->db_connect();
$dbh = $pms->get_dbh;

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

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<form method=get action=list_category.cgi>
		<TABLE cellSpacing=0 cellPadding=0 width=300 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td>Client: </td>
		<td align=left><select name=userid>
end_of_html
$sql = "select user_id,company from user order by company"; 
$sth = $dbh->prepare($sql);
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
	</select>&nbsp;&nbsp;<input type=submit value="Go">
		</td>
		</tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
		<TABLE cellSpacing=0 cellPadding=0 width=760 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td><form method=POST action=add_category.cgi><input type=hidden name=userid value=$userid>Campaign Name: <input type=text name=cname size=25 maxlength=25>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Domain Name:&nbsp;&nbsp;<input type=text name=dname size=25 maxlength=50>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value="Add Category"></form>
		</td>
		</tr>
		<tr>
<td align="center" valign="top">
                <a href="mainmenu.cgi">
                <img src="$images/home_blkline.gif" border=0></a></TD>
		</tr>
		<TR>
		<TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Select a category to edit or add a new category</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		<TABLE cellSpacing=0 cellPadding=3 width="50%" border=0>
		<TBODY>
		<TR bgColor="#509C10" height=15>
		<TD colspan="5" align=center height=15>
			<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
			<b>Category Names</b></font></TD>
		</TR>
end_of_html

# read info about the lists

$sql = "select category_info.category_id,category_name,domain_name from category_info,client_category_info where category_info.category_id=client_category_info.category_id and user_id = $userid order by category_name"; 
$sth = $dbh->prepare($sql);
$sth->execute();
while (($cat_id,$category_name,$domain_name) = $sth->fetchrow_array())
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

	if ($cat_id > 0)
	{
		if ($domain_name ne "")
		{
	print qq { <TR bgColor=$bgcolor> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			<A HREF="edit_category.cgi?cat_id=$cat_id&userid=$userid">$category_name</a>&nbsp&nbsp;</font><font color=black>($domain_name)
			</font></TD> 
 		<TD>&nbsp;</TD> 
 		</TR> \n };
		}
		else
		{
	print qq { <TR bgColor=$bgcolor> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			<A HREF="edit_category.cgi?cat_id=$cat_id&userid=$userid">$category_name</a>
			</font></TD> 
 		<TD>&nbsp;</TD> 
 		</TR> \n };
		}
	}
	else
	{
		print qq { <TR bgColor=$bgcolor> 
		<TD align=left><font color="#509C10" 
			face="verdana,arial,helvetica,sans serif" size="2"> 
 			$category_name
			</font></TD> 
 		<TD>&nbsp;</TD> 
 		</TR> \n };
	}
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
<TR>
<TD noWrap align=left height=17>
end_of_html

$pms->footer();

# exit function

$pms->clean_up();
exit(0);
