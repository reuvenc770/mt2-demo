#!/usr/bin/perl

# *****************************************************************************************
# category_brand_infoa.cgi
#
# this page displays top frame of the category brand association screen 
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
<script language="JavaScript">
function update_brand()
{
    var selObj = document.getElementById('clientid');
    var selIndex = selObj.selectedIndex;
    var selLength = catform.brand_id.length;
    while (selLength>0)
    {
        catform.brand_id.remove(selLength-1);
        selLength--;
    }
    catform.brand_id.length=0;
    parent.frames[2].location="/newcgi-bin/upd_brand_list.cgi?cid="+selObj.options[selIndex].value;
}
function addbrand(value,text)
{
    var newOpt = document.createElement("OPTION");
    newOpt.text=text;
    newOpt.value=value;
    catform.brand_id.add(newOpt);
}
</script>
</head>
<body>
<br>
<center><a href="/cgi-bin/new_list_category.cgi" target=_top><img src="/images/home_blkline.gif" border=0></a>
</center>
<font face="Verdana" size="2"><b>Category Selector:</b></font><br>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<form name=catform method=get action="/cgi-bin/disp_client_brand_info.cgi" target="bottom">
		<TABLE cellSpacing=0 cellPadding=0 width=300 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td width=20%><b>Client: </b></td>
		<td align=left><select name=clientid onChange="update_brand();">
end_of_html
$sql = "select user_id,company from user order by company"; 
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
		</td>
		</tr>
<tr><td><b>Brand:</b></td><td align=left><select name=brand_id></select>&nbsp;&nbsp;<input type=submit value="Go" name="action">&nbsp;&nbsp;<input type=submit value="Copy Selections From" name="action"></td></tr>
		<TR>
		<TD><IMG height=15 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
<script language="JavaScript">
update_brand();
</script>
</body>
</html>
end_of_html
