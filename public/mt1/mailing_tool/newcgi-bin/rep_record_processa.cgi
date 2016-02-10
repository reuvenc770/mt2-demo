#!/usr/bin/perl
# ******************************************************************************
# rep_record_processa.cgi
#
# this page displays the Record Processing Summary top frame 
#
# History
# Jim Sobeck, 1/10/08, Creation
# ******************************************************************************
# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $errmsg;
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
#
# check for login
my $user_id = util::check_security();
$user_id=1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" = "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">

<title>Record Processing Comparative Report</title>

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
	width: 100%;
	margin: 0 auto;
	margin-top: 2em;
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
<script language="JavaScript">
function chkform()
{
	if ((recform.cmonth.selectedIndex== -1) || (recform.cmonth.selectedIndex== 0))
	{
		alert('You must select one or more months');
		return false;
	}
	return true;
}
</script>

</head>
<body>
<div id="container">
	<h1>Record Processing Comparative Report</h1>
	<h2><b>Select report options</b> (hold SHIFT or CTRL to select multiple options in a field):</h2>
<form method="post" action="rep_record_process_query.cgi" target="bottom" name="recform" onSubmit="return chkform();">
<div id="filter">
<center>
		<select name="client" size="5" multiple="multiple">
			<option value="">-- SELECT CLIENT(s) --</option>
end_of_html
$sql="select user_id,first_name,company,username from user where status='A' order by username";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $uid;
my $fname;
my $company;
my $username;
while (($uid,$fname,$company,$username)=$sth->fetchrow_array())
{
	print "<option value=$uid>$username</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select>
		<select name="cmonth" size="5" multiple="multiple">
			<option value="0">-- SELECT MONTH(s) --</option>
			<option value="-1">Custom</option>
end_of_html
my $cdate;
my $cdate1;
$sql="select date_format(curdate(),'%M %Y'),date_format(curdate(),'%Y-%m')";
$sth=$dbhq->prepare($sql);
$sth->execute();
($cdate,$cdate1)=$sth->fetchrow_array();
$sth->finish();
print "<option value=$cdate1>$cdate (current month projected)</option>\n";
my $i=1;
while ($i <= 6)
{
	$sql="select date_format(date_sub(curdate(),interval $i month),'%M %Y'),date_format(date_sub(curdate(),interval $i month),'%Y-%m')";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($cdate,$cdate1)=$sth->fetchrow_array();
	$sth->finish();
	print "<option value=$cdate1>$cdate </option>\n";
	$i++;
}
print<<"end_of_html";
		</select>
		<select name="isp" size="5" multiple="multiple">
			<option value="">-- SELECT ISP(s) --</option>
			<option value="ALL">Only ALL</option>
			<option value="AOL">AOL</option>
			<option value="Yahoo">Yahoo</option>
			<option value="Hotmail">Hotmail</option>
			<option value="Others">Others</option>
		</select>
<center>
		Start Day: <input type=text name=sday size=5>&nbsp;&nbsp;End Day: <input type=text name=eday size=5>&nbsp;&nbsp;<i>Only if Custom chosen</i>
		<h2><input type="checkbox" name="export" value="Y" /> Export to Excel file?</h2>
		<input class="submit" type="submit" value="Filter it" />&nbsp;&nbsp;<a href="/cgi-bin/mainmenu.cgi" target=_top><img height="22" src="/images/home_blkline.gif" width="81" border="0"></a>
</div>
</form>
</div>
</body>
</html>
end_of_html

$util->clean_up();
exit(0);
