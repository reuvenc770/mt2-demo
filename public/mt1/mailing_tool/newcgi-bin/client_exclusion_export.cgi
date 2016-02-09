#!/usr/bin/perl
#===============================================================================
# File   : client_exclusion_export.cgi
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
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $sth1a;
my $category_name;
my $advertiser_name;
my $sname;
my $company;
my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $etype=$query->param('etype');
if ($etype eq "")
{
	$etype="category";
}

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
srand();
my $rid=int(rand()*9999);
my $filename="client_exclusion_".$rid."_".$user_id.".csv";
open(LOG,">/data3/3rdparty/$filename");

$sql = "select user_id, first_name,client_type from user where status='A' order by first_name";
$sth = $dbhq->prepare($sql) ;
$sth->execute();
my $puserid;
my $client_type;
while (($puserid, $company,$client_type) = $sth->fetchrow_array())
{
	print LOG "$puserid,$company,$client_type,";
	if ($etype eq "category")
		{
		#
		# Get categories
		#
		$sql="select category_name from category_info,client_category_exclusion where category_info.category_id=client_category_exclusion.category_id and client_id=? order by category_name";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute($puserid);
		while (($category_name) = $sth1->fetchrow_array())
		{
			print LOG "$category_name:";
		}
		$sth1->finish();
		print LOG "\n";
	}
	else
	{
        $sql="select advertiser_info.advertiser_id,advertiser_name,sid from advertiser_info,client_advertiser_exclusion where advertiser_info.advertiser_id=client_advertiser_exclusion.advertiser_id and client_id=? order by advertiser_name";
        $sth1 = $dbhq->prepare($sql) ;
        $sth1->execute($puserid);
		my $aid;
		my $sid;
        while (($aid,$advertiser_name,$sid) = $sth1->fetchrow_array())
        {
            print LOG "$aid:";
        }
        $sth1->finish();
		print LOG "\n";
	}
}
$sth->finish();
close(LOG);
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Exported Client Exclusion</title>

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
