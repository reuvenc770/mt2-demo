#!/usr/bin/perl
#===============================================================================
# Name   : footer_content.cgi 
#
#--Change Control---------------------------------------------------------------
# 03/06/06  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $sth1;
my $dbh;
my $cid;
my $cname;
my $content_name;
my $content_html;
my $inactive_date;
my $modified_date;
my $content_date;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;
my $content_id=$query->param('cid');
if ($content_id eq '')
{
	$content_id=0;
	$content_name="";
	$content_html="";
	$inactive_date="";
	$modified_date="";
	$sql="select date_format(curdate(),'%m/%d/%y')";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($content_date) = $sth->fetchrow_array();
	$sth->finish();
}
else
{
	$sql="select content_name,date_format(content_date,'%m/%d/%y'),date_format(inactive_date,'%m/%d/%y'),content_html,modified_date from footer_content where content_id=$content_id";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	($content_name,$content_date,$inactive_date,$content_html,$modified_date) = $sth->fetchrow_array();
	$sth->finish();
	if ($inactive_date eq "00/00/00")
	{
		$inactive_date="";
	}
}
#--------------------------------
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Footer Content</title>
</head>

<body>

<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Edit 
						Footer Content</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="text-decoration: none" href="http://69.45.78.226:83/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="text-decoration: none" href="http://69.45.78.226:83/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table5">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>Footer Content</b> </font></td>
					</tr>
					<tr>
						<td><img height="3" src="/images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
					<tr>
						<td colSpan="10">
						&nbsp;</td>
					</tr>
				</table>
<SCRIPT language=JavaScript>
        function SaveFunc(btn)
        {
            document.campform.nextfunc.value = btn;
            document.campform.submit();
        }
</SCRIPT>

				<form name="campform" method="post" action="/cgi-bin/footer_content_sav.cgi">
				<input type=hidden name=content_id value="$content_id">
					<b>Category: (Select all categories that content applies to)</b><br>
											<select multiple size="5" name="catid">
end_of_html
$sql="select category_id,category_name from category_info order by category_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $temp_cid;
while (($cid,$cname) = $sth->fetchrow_array())
{
	if ($content_id > 0)
	{
		$sql="select category_id from content_category where content_id=$content_id and category_id=$cid";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		if (($temp_cid) = $sth1->fetchrow_array())
		{
			print "<option selected value=$cid>$cname</option>\n";
		}
		else
		{
			print "<option value=$cid>$cname</option>\n";
		}
		$sth1->finish();
	}
	else
	{
		print "<option value=$cid>$cname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
											</select><br>
					<br>
					<b>Content Name:</b><br>
					<input maxLength="255" size="50" value="$content_name" name="content_name"><br>
					<br>
					<b>Date of Content: (MM/DD/YY - Default = today) </b><br>
					<input maxLength="8" size="10" value="$content_date" name="content_date"><br>
					<br>
					<b>Inactive Date: (MM/DD/YY) </b><br>
					<input maxLength="8" size="10" name="inactivate_date" value="$inactive_date"><br>
                    <br>
                    <b>Last Modified Date: </b>$modified_date<br>
					<br>
					<b><u>HTML Code:</u><br>
					<textarea name="html_code" rows="15" cols="100">$content_html</textarea> 
					<table id="table8" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/mainmenu.cgi">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input type=image height="22" src="/images/save_rev.gif" width="81" border="0"></td>
<!--							<td align="middle" width="50%">
							<a href="/cgi-bin/footer_content_preview.cgi?cid=$content_id target=_blank">
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></td> -->
						</tr>
					</table>
				</form>
</td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html
