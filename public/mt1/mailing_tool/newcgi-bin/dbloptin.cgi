#!/usr/bin/perl
# *****************************************************************************************
# dbloptin.cgi
#
# this page is for adding/editing Double Option campaigns 
#
# History
# Jim Sobeck, 03/31/08, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $cname;
my $cday;
my $subject;
my $fromline;
my $header_image;
my $header_id;
my $footer_id;
my $content_str;
my $template_id;
my $client_id;
my ($uid,$fname,$company);
my $id=$query->param('id');
my $c=$query->param('c');
if ($c eq "")
{
	$c=0;
}

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
        print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
if ($id > 0)
{
	$sql="select campaign_name,client_id,cday,subject,fromline,header_image,content_str,template_id,header_id,footer_id from double_optin where id=$id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($cname,$client_id,$cday,$subject,$fromline,$header_image,$content_str,$template_id,$header_id,$footer_id)=$sth->fetchrow_array();
	$sth->finish();
}
else
{
	$cname="";
	$cday=1;
	$subject="";
	$fromline="";
	$header_image="";
	$content_str="";
	$template_id=1;
	$client_id=0;
	$header_id=0;
	$footer_id=0;
}
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Build a Double Opt-in Confirmation Campaign</title>

<style type="text/css">

body {
	background: top center repeat-x #99D1F4;
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
<script language="JavaScript">
function add_nl()
{
        var newwin = window.open("/cgi-bin/template_disp.cgi?mode=A","Template", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function edit_nl()
{
        var selObj = document.getElementById('template_id');
    var selIndex = selObj.selectedIndex;
        var newwin = window.open("/cgi-bin/template_disp.cgi?mode=U&nl_id="+selObj.options[selIndex].value,"Template", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1");
        newwin.focus();
}
function delete_headerimg()
{
    if (ProcessForm())
    {
    	document.campform.submit.value="delete img";
    	document.campform.submit();
    }
}
function ProcessForm()
{
	var iopt;
    // validate your data first
    iopt = check_mandatory_fields();
    if (iopt == 0)
    {
    	return false;
    }

    // if ok, go on to save
    return true;
}

function check_mandatory_fields()
{
	if (document.campform.cname.value == "")
    {
    	alert("You MUST enter a value for the Campaign Name field."); 
		document.campform.cname.focus();
        return false;
    }
    if (document.campform.subject.value == "")
    {
    	alert("You MUST enter a value for the Subject field."); 
		document.campform.subject.focus();
        return false;
    }
    if (document.campform.fromline.value == "")
    {
    	alert("You MUST enter a value for the From field."); 
		document.campform.subject.focus();
        return false;
    }
	return true;
}
</script>
</head>

<body>
<div id="container">
  <h1>Build a Double Opt-In Confirmation Campaign</h1>
	<h2><a href="dbloptin_list.cgi">view all double opt-in confirmation campaigns</a> | <a href="/newcgi-bin/mainmenu.cgi">go home</a></h2>

  <div id="form">
	<form method="post" name="campform" onsubmit="return ProcessForm();" action="dbloptin_save.cgi" ENCTYPE="multipart/form-data">
	<input type=hidden name=id value=$id>
	  <table>

		  <tr>
			<td class="label">Double Opt-In Campaign Name:</td>
			<td class="field">
				<input class="field" size="35" name=cname value="$cname" />
			</td>
		  </tr>
		  <tr>
			<td class="label">Client:</td>
			<td class="field">
end_of_html
if (($client_id == 0) or ($c == 1))
{
	print "<select name=client_id>\n";
	$sql="select user_id,first_name,company from user where status='A' and double_optin='Y' order by company";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($uid,$fname,$company)=$sth->fetchrow_array())
	{
		if ($uid == $client_id)
		{
			print "<option selected value=$uid>$company ($fname)</option>\n";
		}
		else
		{
			print "<option value=$uid>$company ($fname)</option>\n";
		}
	}
	$sth->finish();
	print "</select>\n";
}
else
{
	print "<input type=hidden name=client_id value=\"$client_id\">\n";
	$sql="select first_name,company from user where status='A' and double_optin='Y' order by company";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($fname,$company)=$sth->fetchrow_array();
	$sth->finish();
	print "$company ($fname)\n";
}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Day #:</td>
			<td class="field">
				<select class="field" name=cday>
end_of_html
my $i=1;
while ($i <= 5)
{
	if ($cday == $i)
	{
		print "<option value=$i selected>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Subject Line:</td>
			<td class="field"><br />
				<input class="field" size="50" name=subject value="$subject" />
			</td>
		  </tr>
		  <tr>
			<td class="label">From Line:</td>
			<td class="field">
				<input class="field" size="50" name=fromline value="$fromline" />
			</td>
		  </tr>
		  <tr>
			<td class="label">Header:</td>
			<td class="field">
				<select class="field" name="header_id" id="header_id">
end_of_html
$sql="select header_id,header_name from Headers where status='A' order by header_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $header_id)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Footer:</td>
			<td class="field">
				<select class="field" name="footer_id" id="footer_id">
end_of_html
$sql="select footer_id,footer_name from Footers where status='A' order by footer_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $footer_id)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label" valign="top">Required email body content (in HTML):<br /><span class="note">(<b>MUST</b> set variable {{DOPTIN_CONTENT}} in email template)</span></td>
			<td class="field">
				<textarea class="field" cols="50" rows="6" name=content_str>$content_str</textarea>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Mailing Template:</td>
			<td class="field"><br />
				<select class="field" name="template_id" id="template_id">
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $template_id)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
				<input type="button" value="edit" onClick="edit_nl();" />
				<input type="button" value="add new template" onClick="add_nl();" target="_blank"/>
			</td>
		  </tr>
		</table>

		<div class="submit">
			<input class="submit" value="preview it" name="submit" type="submit">
			<input class="submit" value="save it" name="submit" type="submit">
		</div>
	</form></div>

</div>
</body></html>
end_of_html
exit(0);
