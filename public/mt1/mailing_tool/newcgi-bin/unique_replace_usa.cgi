#!/usr/bin/perl
#===============================================================================
# Name   : unique_replace_usa.cgi - Change usa for a deploy 
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
my $sth;
my $dbh;
my $cname;
my $cstatus;
my $shour;
my $smin;
my $sdate1;
my $uidstr=$query->param('uidstr');
my $gsm=$query->param('gsm');
my $sord=$query->param('sord');
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
<title>Change USA for Uniques Campaign</title>

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

.centered {
	text-align: center;
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

div.submit {
	text-align: center;
	padding: 1em 0;
  }

input.submit {
	margin-top: .25em;
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
</script>
</head>

<body>
<div id="container">

	<h1>Change USA for Uniques Campaign</h1>
	<h2><a href="unique_list.cgi" target=_top>view/edit/deploy uniques campaigns</a> | <a href="unique_deploy_list.cgi" target=_top>deployed unique campaigns</a> | <a href="/cgi-bin/mainmenu.cgi" target=_top>go home</a></h2>

	<div id="form">
	<form name="campform" method=post action="/cgi-bin/unique_replace_usa_save.cgi" target=_top>
	<input type=hidden name=uidstr value=$uidstr>
	<input type=hidden name=gsm value=$gsm>
	<input type=hidden name=sord value=$sord>
		<table>
		  <tr>
			<td class="label">New USA:</td>
			<td><select name=usaid>
end_of_html
$sql="select usa.usa_id,usa.name from UniqueScheduleAdvertiser usa, advertiser_info ai where usa.advertiser_id=ai.advertiser_id and ai.status!='I' order by name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $uid;
my $uname;
while (($uid,$uname)=$sth->fetchrow_array())
{
	print "<option value=$uid>$uname</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select></td></tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="Update USA" />
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
