#!/usr/bin/perl
#===============================================================================
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sql1;
my $sth;
my $dbh;
my $uid;
my $fname;
my $company;
my $curl=$query->param('name');
my $redir_url;
my ($turl_id,$oldid,$newid);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Source URL Data Type</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font: .75em/1.3em Tahoma, Arial, sans-serif;
	color: #4d4d4d;
  }

h1, h2 {
	font-family: 'Trebuchet MS', Arial, san-serif;
	text-align: center;
	font-weight: normal;
  }

h1 {
	font-size: 2em;
  }

h2 {
	font-size: 1.2em;
  }

h4 {
	font-weight: normal;
	margin: 1em 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

a:link, a:visited {
	color: #33f;
	text-decoration: none;
  }

a:hover, a:focus {
	color: #66f;
	text-decoration: underline;
  }

div.filter {
	text-align: center;
  }

div.filter select {
	font: 11px/14px Tahoma, Arial, sans-serif;
  }

#container {
	width: 90%;
	padding-top: 5%;
	width: expression( document.body.clientWidth < 1025 ? "1024px" : "auto" ); /* set min-width for IE */
	min-width: 1024px;
	margin: 0 auto;
  }

div.overflow {
	/* overflow: auto; */
  }

table {
	background: #FFF;
	border: 1px solid #666;
	width: 780px;
	margin: 0 auto;
	margin-bottom: .5em;
  }

table td {
	padding: .325em;
	border: 1px solid #ABC;
	text-align: center;
  }

table .label {
	font-weight: bold;
	color: #000;
  }

table tr.alt {
	background: #DDD;
  }

table tr.label {
	background: #6C3;
  }

table td.label {
	text-align: left;
	background: #6C3;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }

</style>

</head>

<body>
<center><a href=source_url_lookup.cgi><img src=/images/home_blkline.gif border=0></a></center>
<div class="overflow">
	<table width=90%>
		<tr class="label">
			<td>
			Client
			</td>

			<td>
			Source URL
			</td>

			<td>
			Count	
			</td>

			<td>
			Source Data Type
			</td>

		</tr>
end_of_html
#$sql="select su.url_id,company,type_str,url,su.datatype_id,sum(url_count) from source_url su, datatypes, user,SourceUrlSummary sus where su.client_id=user.user_id and user.status='A' and su.datatype_id=datatypes.datatype_id and su.url='$curl' and su.url_id=sus.url_id and su.client_id=sus.client_id and sus.effectiveDate >= date_sub(curdate(),interval 30 day) group by 1,2,3,4,5 order by company";
#
# 01/20/2014 - JES - Changed to remove date range per Winnie Shen
#
#$sql="select su.url_id,company,type_str,url,su.datatype_id,sum(url_count) from source_url su, datatypes, user,SourceUrlSummary sus where su.client_id=user.user_id and user.status='A' and su.datatype_id=datatypes.datatype_id and su.url='$curl' and su.url_id=sus.url_id and su.client_id=sus.client_id group by 1,2,3,4,5 order by company";
#
# 01/21/2014 - JES - Changed to include inactive clients
#
$sql="select su.url_id,company,type_str,url,su.datatype_id,sum(url_count) from source_url su, datatypes, user,SourceUrlSummary sus where su.client_id=user.user_id and su.datatype_id=datatypes.datatype_id and su.url='$curl' and su.url_id=sus.url_id and su.client_id=sus.client_id group by 1,2,3,4,5 order by company";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cnt=0;
my $url_id;
my $url;
my $old_datatypeid;
my $reccnt;
my $type_str;
$sql1="";
while (($url_id,$company,$type_str,$url,$old_datatypeid,$reccnt)=$sth->fetchrow_array())
{
	$sql1=$sql1.$url_id.",";
	$_=$url;
	if (/http/)
	{
		$redir_url=$url;
	}
	else
	{
		$redir_url="http://".$url;
	}
	if ($cnt % 2)
	{
		print "<tr>";
	}
	else
	{
		print "<tr class=alt>";
	}
	print "<td>$company</td> <td><a href=\"$redir_url\" target=\"_blank\">$url</a></td><td>$reccnt</td><td>$type_str</td>";
	print "</tr>\n";
	$cnt++;
}
$sth->finish();
chop($sql1);
print<<"end_of_html";
	</table>
</div>
</body>
</html>
end_of_html
