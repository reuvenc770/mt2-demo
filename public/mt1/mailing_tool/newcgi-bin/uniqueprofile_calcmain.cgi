#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_calcmain.cgi
#
# this page display pages to allow checking to see how much a profile would mail 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $dbh;
my $sql;
my $time=30;
my $cstatus;
my $DETAILS;
my ($client,$ctype,$sdate,$edate);
my $osdate;
my $oedate;
my $csdate;
my $cedate;
my $fname;
my $record_count;
my $open_record_count;
my $click_record_count;
my $cid= $query->param('cid');
my $url="http://mailingtool.routename.com:83/cgi-bin/uniqueprofile_calcmain.cgi?cid=$cid";
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql="select * from UniqueCheck where check_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($cid);
$DETAILS=$sth->fetchrow_hashref();
$sth->finish();

if (($DETAILS->{status} eq "A") or ($DETAILS->{status} eq "P"))
{
print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="refresh" content="$time;URL=$url">
<title>Mailing System EMail System</title>
</head>
<body><center><h4>Calculating...<h3>
</body>
</html>
end_of_html
exit
}
else
{
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Calc Deploy Mailing Count</title>

<style type="text/css">

body {
	background: top center repeat-x #99D1F4; font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
  }

h1 {
	text-align: center;
	font-weight: normal;
	font-size: 1.5em;
  }

h2 {
	text-align: center;
	font-weight: normal;
	font-size: 1em;
  }

#container {
	width: 70%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	width: 100%;
	padding: 1em;
	text-align: left;
  }

#form table {
	width: 100%;
	margin: 0 auto;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

td.label {
	width: 40%;
	text-align: right;
	font-weight: bold;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	font-size: .9em;
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
	margin-top: 1em;
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
	font-weight: normal;
  }

</style>
</head>

<body>
<center><a href="/cgi-bin/mainmenu.cgi"><img src="/images/home_blkline.gif" border=0></a></center>
<br>
  <div id="form">
end_of_html
$sql="select ed.class_id,class_name from email_class ed, UniqueCheckIsp where ed.class_id=UniqueCheckIsp.class_id and UniqueCheckIsp.check_id=$cid and UniqueCheckIsp.client_id=0 and ed.status='Active' order by class_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $class_id;
my $cname;
my $isp_str;
while (($class_id,$cname)=$sth->fetchrow_array())
{
	$isp_str=$isp_str." ".$cname;
}
$sth->finish();
print "<b>ISPs: </b>$isp_str</br>\n";
print<<"end_of_html";
	  <table>
	<tr><th>Client Name</th><th>Opener Requested</th><th>Openers Range</th><th>Clicker Requested</th><th>Clickers Range</th><th>Deliverable Requested</th><th>Deliverable Range</th></tr>
end_of_html
my $line;
my $file="/var/www/util/data/".$cid.".log";
open(RPT,"<$file");
while (<RPT>)
{
	$line=$_;
	($client,$osdate,$oedate,$csdate,$cedate,$sdate,$edate)=split(',',$line);
	$sql="select username from user where user_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($client);
	($fname)=$sth->fetchrow_array();
	$sth->finish();	
	$sql="select record_cnt,open_record_cnt,click_record_cnt from UniqueCheckClient where check_id=? and client_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cid,$client);
	($record_count,$open_record_count,$click_record_count)=$sth->fetchrow_array();
	$sth->finish();	

	print "<tr><td>$fname</td><td>$open_record_count</td>";
	if ($osdate eq "NOTENOUGH")
	{
		print "<td>Only got <b>$oedate</b> records</td>\n";
	}
	else
	{
		print "<td>$osdate to $oedate</td>\n";
	}
	if ($csdate eq "NOTENOUGH")
	{
		print "<td>Only got <b>$cedate</b> records</td>\n";
	}
	else
	{
		print "<td>$click_record_count</td><td>$csdate to $cedate</td>";
	}
	if ($sdate eq "NOTENOUGH")
	{
		print "<td>Only got <b>$edate</b> records</td>\n";
	}
	else
	{
		print "<td>$record_count</td><td>$sdate to $edate</td>";
	}
}
close(RPT);
print<<"end_of_html";
		</table>

	</div>

</body></html>
end_of_html
exit(0);
}
