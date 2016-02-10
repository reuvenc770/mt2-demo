#!/usr/bin/perl

# *****************************************************************************
# sm2_send_all_disp.cgi
#
# this page displays page to send all brand data 
#
# History
# Jim Sobeck, 05/29/08, Creation
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $images = $util->get_images_url;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
$user_id=1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

my $isExternalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

print "Content-type: text/html\n\n";
print<<"end_of_html";
<!-- saved from url=(0022)http://internet.e-mail -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Test Multiple Brands</title>

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

p {
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
<script language="JavaScript">
function add_header()
{
    var cpage = "/cgi-bin/mailHeaders/template_disp.cgi?pmode=A";
    var newwin = window.open(cpage, "MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50")
;
}
function edit_header()
{
    var url_value;
    url_value = document.campform.headerid.value;
    var cpage = "/cgi-bin/mailHeaders/template_disp.cgi?pmode=U&nl_id="+url_value;
    var newwin = window.open(cpage, "MTA", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50")
;
}
function add_nl()
{
        var newwin = window.open("/cgi-bin/template_disp.cgi?mode=A","AddNewsletter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_nl()
{
   	var selObj = document.getElementById('template_id');
    var selIndex = selObj.selectedIndex;
    var newwin = window.open("/cgi-bin/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"AddNewsletter", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
    newwin.focus();
}
</script>
</head>

<body>
<div id="container">
	<h1>Test Multiple Brands</h1>
	<h2><a href="sm2_list.cgi" target=_top>view/deploy tests</a> | <a href="sm2_list.cgi?type=D" target=_top>view deployed campaigns</a> | <a href="mainmenu.cgi" target=_top>go home</a></h2>

	<p>This tool will send a single generic test email to the specified email address(es) for every brand that is selected below.</p>

	<div id="form">
	<form method=post name="campform" action="sm2_send_all.cgi" target=_top>
		<table>
		  <tr>
			<td class="label">Email Address(es):</td>
			<td class="field"><input class="field" id="" type="" name="email" value="" size="50" /></td>
		  </tr>
		  <tr>
			<td class="label">Brand(s) <br /> <em class="note">(hold CTL to select multiple)</em>:</td>
			<td class="field">
				<select class="field" name="brandid" id="brandid" multiple="multiple" size="10">
					<option value=0>----- [select one or more] -----</option>
end_of_html
my $bid;
my $bname;
$sql="select brand_id,brand_name from client_brand_info where status='A' and third_party_id=10 and brand_type='3rd Party' order by brand_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($bid,$bname)=$sth->fetchrow_array())
{
	if($isExternalUser)
	{
		$bname = $bid;
	}
	print "<option value=$bid>$bname ($bid)</option>\n";
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label"></td>
			<td class="field"><input type="checkbox" name="selectall" value="Y" /> Select ALL brands? <em class="note">(use with caution)</em></td>
		  </tr>
		  <tr>
			<td class="label"><br />Email Header Template:</td>
			<td><br />
				<select class="field" name="headerid">
end_of_html
$sql="select templateID,templateName from mailingHeaderTemplate where $userDataRestrictionWhereClause status='A' order by templateID";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $tempid;
my $tempname;
while (($tempid,$tempname)=$sth->fetchrow_array())
{
	print "<option value=$tempid>$tempname</option>\n";
}
$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_header();"/>
				<input type="button" value="add new email header template" onClick="add_header();" />
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Template:</td>
			<td class="field">
				<select class="field" name="template_id" id="template_id">
end_of_html
$sql="select template_id,template_name from brand_template where $userDataRestrictionWhereClause status='A' order by template_id";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	print "<option value=$tid>$tname</option>\n";
}
$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_nl();" />
				<input type="button" value="add new template" onClick="add_nl();" target="_blank"/>
			</td>
		  </tr>
		  <tr>
			<td class="label">Include Wiki?</td>
			<td class="field"><input class="radio" type="radio" value="Y" checked name="wiki" />Yes <input class="radio" type="radio" value="N" name="wiki" />No</td>
		  </tr>
		</table>

		<div class="submit">
			<input class="submit" type="submit" name="submit" value="preview it" />
			<input class="submit" type="submit" name="submit" value="send it" />
		</div>
	</div>

</div>
</form>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
