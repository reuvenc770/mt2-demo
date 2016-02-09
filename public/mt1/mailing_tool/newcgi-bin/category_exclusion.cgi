#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Clients
# File   : category_exclusion.cgi
#
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $mesg = $query->param('mesg');
my $puserid= $query->param('puserid');
my $cname= $query->param('cname');
my ($sth, $reccnt, $sql, $dbh ) ;
my $cat_id;
my $cat_name;
my $sth1;
my $sth1a;
my $category_name;
my $advertiser_name;
my $sname;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Edit Category Exclusions</title>
</head>

<body>

<table id="table9" cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0">
	<tr vAlign="top">
		<td noWrap align="left">
		<table id="table10" cellSpacing="0" cellPadding="0" width="800" border="0">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table id="table11" cellSpacing="0" cellPadding="0" width="100%" border="0">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Edit Category Exclusions for $cname</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="text-decoration: none" href="/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="text-decoration: none" href="/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table id="table12" cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table id="table13" cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>Category Exclusion </b>&nbsp;</font></td>
					</tr>
					<tr>
						<td><img height="3" src="spacer.gif"></td>
					</tr>
				</table>
				<table id="table14" cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0">
					<tr>
						<td colSpan="10">&nbsp;</td>
					</tr>
				</table>
<SCRIPT language=JavaScript>
        function SaveFunc(btn)
        {
            document.campform.nextfunc.value = btn;
            document.campform.submit();
        }
</SCRIPT>

				<form name="campform" method="post" action="/cgi-bin/category_exclusions_sav.cgi" target=_top>
					<input type="hidden" value="$puserid" name="puserid"><b>
					Category: (Select all categories to exclude)</b><br>
					<select multiple size="5" name="catid">
end_of_html
$sql="select category_id,category_name from category_info order by category_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $temp_cid;
while (($cat_id,$cat_name) = $sth->fetchrow_array())
{
        $sql="select category_id from client_category_exclusion where client_id=$puserid and category_id=$cat_id";
        $sth1 = $dbhq->prepare($sql) ;
        $sth1->execute();
        if (($temp_cid) = $sth1->fetchrow_array())
        {
            print "<option selected value=$cat_id>$cat_name</option>\n";
        }
        else
        {
            print "<option value=$cat_id>$cat_name</option>\n";
        }
        $sth1->finish();
}
$sth->finish();
print<<"end_of_html";
					</select><br>
					<br>
					<b><br>
&nbsp;<table id="table15" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/client_exclusion.cgi" target=_top>
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input type="image" height="22" width="81" src="/images/save_rev.gif" border="0" name="I1"></td>
							<!--							<td align="middle" width="50%">
							<a href="/cgi-bin/footer_content_preview.cgi?cid=3 target=_blank">
							<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></td> -->
						</tr>
					</table>
				</form>
				</b></td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html
