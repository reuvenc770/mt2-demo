#!/usr/bin/perl
#===============================================================================
# Name   : usa_export.cgi 
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
my $filename;
my $sth1;
my $usa_id;
my $usaname;
my $aname;
my $t1;
my $max_cnt=0;
my $cnt;
my $C;
my $S;
my $F;
my $lastUpdated;
my $usaType;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
srand();
my $rid=int(rand()*9999);
$filename="usa_".$rid."_".$user_id.".csv";
open(LOG,">/data3/3rdparty/$filename");
print LOG "usa_id,USA Name,Adv_name,C_code,F_code,S_code,LastUpdated,uSA Type,\n";

$sql="select usa_id,name,advertiser_name,lastUpdated,usaType from UniqueScheduleAdvertiser usa,advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status !='I' order by usa_id"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($usa_id,$usaname,$aname,$lastUpdated,$usaType)=$sth->fetchrow_array())
{
	undef($C);
	undef($S);
	undef($F);
	$cnt=0;
	$sql="select uac.creative_id from UniqueAdvertiserCreative uac,creative c where usa_id=? and uac.creative_id=c.creative_id and c.status='A' order by rowID,1";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($usa_id);
	while (($t1)=$sth1->fetchrow_array())
	{
		$C->{$cnt}=$t1;
		$cnt++;
	}
	$sth1->finish();
	$max_cnt=$cnt;
	$cnt=0;
	$sql="select uas.subject_id from UniqueAdvertiserSubject uas,advertiser_subject a1 where usa_id=? and uas.subject_id=a1.subject_id and a1.status='A' order by rowID,1";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($usa_id);
	while (($t1)=$sth1->fetchrow_array())
	{
		$S->{$cnt}=$t1;
		$cnt++;
	}
	$sth1->finish();
	if ($cnt > $max_cnt)
	{
		$max_cnt=$cnt;
	}
	$cnt=0;
	$sql="select uaf.from_id from UniqueAdvertiserFrom uaf,advertiser_from af where usa_id=? and uaf.from_id=af.from_id and af.status='A' order by rowID,1";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($usa_id);
	while (($t1)=$sth1->fetchrow_array())
	{
		$F->{$cnt}=$t1;
		$cnt++;
	}
	$sth1->finish();
	if ($cnt > $max_cnt)
	{
		$max_cnt=$cnt;
	}
	my $i=0;
	while ($i < $max_cnt)
	{
		print LOG "$usa_id,$usaname,$aname,$C->{$i},$F->{$i},$S->{$i},$lastUpdated,$usaType,\n";
		$i++;
	}
}
$sth->finish();
close(LOG);

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Exported USA</title>

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
<center>
<h4><a href="/downloads/$filename">Click here</a> to download file</h4>
</center>
<br>
</body>
</html>
end_of_html
