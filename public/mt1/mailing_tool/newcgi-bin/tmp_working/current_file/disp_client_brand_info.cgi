#!/usr/bin/perl

# *****************************************************************************************
# disp_client_brand_info.cgi
#
# this page displays middle frame of the category brand association screen 
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
my $sth2;
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
my $clientid= $query->param('clientid');
my $brand_id = $query->param('brand_id');
if ($brand_id eq "")
{
	$brand_id=0;
}
my $action= $query->param('action');
my $sid;
my $sname;

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

if ($action eq "Go")
{
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
<form method=get action="/cgi-bin/sav_client_brand_info.cgi">
<table>
<tr><td size=5>&nbsp;&nbsp;</td>
<td>
<input type=hidden name=clientid value=$clientid>
<input type=hidden name=brand_id value=$brand_id>
<p>
end_of_html
$sql="select category_id,category_name from category_info order by category_name";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($cat_id,$category_name) = $sth->fetchrow_array())
{
	print "<font face=\"Verdana\" size=2><b>$category_name</b></font><br>\n";
	$sql = "select subdomain_id,subdomain_name from brandsubdomain_info where category_id=$cat_id";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	print "<table border=0>\n";
	while (($sid,$sname) = $sth1->fetchrow_array())
	{
		$sql = "select count(*) from category_brand_info where subdomain_id=$sid and brand_id=$brand_id";
		$sth2 = $dbh->prepare($sql);
		$sth2->execute();
		($reccnt) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($reccnt > 0)
		{
			print "<tr><td width=\"288\"><font face=\"Verdana\">&nbsp;&nbsp;<input type=\"checkbox\" checked name=\"brandid_$sid\" size=\"40\" value=\"Y\"><font size=\"2\">$sname</font></font></td></tr>\n";
		}
		else
		{
			print "<tr><td width=\"288\"><font face=\"Verdana\">&nbsp;&nbsp;<input type=\"checkbox\" name=\"brandid_$sid\" size=\"40\" value=\"Y\"><font size=\"2\">$sname</font></font></td></tr>\n";
		}
	}
	$sth1->finish();
	print "<tr><td>&nbsp;</td></tr></table>\n";
}
$sth->finish();
print<<"end_of_html";
<p>
<input type="image" height="22" src="/images/save_rev.gif" width="81" border="0">
</form>
</td></tr></table>
</body>
</html>
end_of_html
}
else
{
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
    var selObj = document.getElementById('from_clientid');
    var selIndex = selObj.selectedIndex;
    var selLength = catform.brand_id.length;
    while (selLength>0)
    {
        catform.brand_id.remove(selLength-1);
        selLength--;
    }
    catform.brand_id.length=0;
    parent.frames[2].location="/newcgi-bin/upd_brand_list1.cgi?cid="+selObj.options[selIndex].value;
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
<font face="Verdana" size="2"><b>Brand to Copy From:</b></font><br>
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>
		<form name=catform method=get action="/cgi-bin/copy_client_brand_info.cgi" target="bottom">
		<input type=hidden name=to_bid value=$brand_id>
		<input type=hidden name=to_client value=$clientid>
		<TABLE cellSpacing=0 cellPadding=0 width=300 bgColor=#ffffff border=0>
		<TBODY>
		<tr>
		<td width=20%><b>Client: </b></td>
		<td align=left><select name=from_clientid onChange="update_brand();">
end_of_html
$sql = "select user_id,company from user order by company"; 
$sth = $dbh->prepare($sql);
$sth->execute();
my $tid;
my $company;
while (($tid,$company) = $sth->fetchrow_array())
{
	if ($tid == $clientid)
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
<tr><td><b>Brand:</b></td><td align=left><select name=brand_id></select>&nbsp;&nbsp;<input type=submit value="Copy" name="action">&nbsp;&nbsp;</td></tr>
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
}
