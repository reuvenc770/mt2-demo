#!/usr/bin/perl
#===============================================================================
# Name   : sm2_seedlist_save.cgi - saves seedlists for each mta 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $id;
my $em;
my $fld;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
my $seedgroup=$query->param('seedgroup');
my $seed_group_name=$query->param('seed_group_name');
if ($seed_group_name ne "")
{
	$seed_group_name=~s/'/''/g;
	$sql="insert into SM2SeedsGroup(SeedGroupName) values('$seed_group_name')";
	my $rows=$dbhu->do($sql);
	$sql="select max(SeedGroupID) from SM2SeedsGroup where SeedGroupName=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($seed_group_name);
	($seedgroup)=$sth->fetchrow_array();
	$sth->finish();
}
my $errors;
my $results;
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
my $params;
$params->{active}=1;
($errors, $results) = $serverInterface->getMtaServers($params);
for my $server (@$results)
{
	$id=$server->{'serverID'};
	$sql="delete from SM2Seeds where server_id=$id and SeedGroupID=$seedgroup";
	my $rows=$dbhu->do($sql);

	$fld="seed_".$id;
	$em=$query->param($fld);
	$sql="insert into SM2Seeds(server_id,email_addr,SeedGroupID) values($id,'$em',$seedgroup)";
	my $rows=$dbhu->do($sql);
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html><head><title>SM2 Seeds Saved</title>
<style type="text/css">
body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
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
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

td.label {
	width: 40%;
	text-align: right;
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
	margin-top: 1em;
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
<div id="container">
	<h1>Seed Addresses Saved</h1>
	<h2><a href="sm2_send_all_list.cgi">View Send All Tests</a> | <a href="sm2_list.cgi" target=_top>view/deploy tests</a> | <a href="sm2_list.cgi?type=D" target=_top>view deployed campaigns</a> | <a href="sm2_seedlist.cgi">SM2 Seeds</a> | <a href="mainmenu.cgi" target=_top>go home</a></h2>
<br>
<center><h4>SM2 Seeds Saved</h4></center>
</body>
</html>
end_of_html
