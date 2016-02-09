#!/usr/bin/perl
#===============================================================================
# Name   : block_add.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/15/07  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $bid= $query->param('bid');
my $sql;
my $sth;
my $dbh;
my ($bname,$bhost,$vid,$mailing1,$mailing2);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#--------------------------------
if ($bid > 0)
{
	$sql="select block_name,block_host,variation_id,mailing_addr1,mailing_addr2 from block where block_id=$bid";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($bname,$bhost,$vid,$mailing1,$mailing2)=$sth->fetchrow_array();
	$sth->finish();
}
else
{
	$bname="";
	$bhost="";
	$vid=0;
	$mailing1="";
	$mailing2="";
}
        print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<title>Create a Block</title>

<style type="text/css">

body {
	background: url(bg.jpg) top center repeat-x #99D1F4;
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

.note {
	font-size: .8em;
  }

</style>

</head>

<body>
	<form method="post" action="block_upd.cgi">
	<input type=hidden name=bid value=$bid>
<div id="container">

	<h1>Create or Modify a Block</h1>

	<div id="form">
		<table>
		  <tr>
			<td class="label">Block Name:</td>
			<td class="field"><input class="field" id="" type="" name="bname" value="$bname" size="35" /></td>
		  </tr>
		  <tr>
			<td class="label">Block Host:</td>
			<td class="field"><input class="field" id="" type="" name="bhost" value="$bhost" size="35" /></td>
		  </tr>
		  <tr>
			<td class="label">Block Footer:</td>
			<td class="field">
				<select class="field" name=vid>
end_of_html
$sql="select variation_id,name from footer_variation where status='A' order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tvid;
my $tname;
while (($tvid,$tname)=$sth->fetchrow_array())
{
	if ($tvid == $vid)
	{
		print "<option value=$tvid selected>$tname</option>";
	}
	else
	{
		print "<option value=$tvid>$tname</option>";
	}
}
$sth->finish();		
print<<"end_of_html";		
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Address 1:</td>
			<td class="field"><input class="field" id="" type="" name="mailing1" value="$mailing1" size="35" /></td>
		  </tr>
		  <tr>
			<td class="label">Mailing Address 2:</td>
			<td class="field"><input class="field" id="" type="" name="mailing2" value="$mailing2" size="35" /></td>
		  </tr>
		</table>

		<div class="submit"><input class="submit" type="submit" name="submit" value="save it" /></div>
	</div>

</div>
	</form>
</body>
</html>
end_of_html
