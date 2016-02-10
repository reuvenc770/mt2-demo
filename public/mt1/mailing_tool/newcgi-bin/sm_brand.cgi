#!/usr/bin/perl
#===============================================================================per
# Name   : sm_brand.cgi 
#
#--Change Control---------------------------------------------------------------
# 06/18/07  Jim Sobeck  Creation
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
<title>Build a New StrongMail Brand</title>

<style type="text/css">

body {
	background: url(/images/bg.jpg) top center repeat-x #99D1F4;
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

   	<script language="JavaScript">
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
        if (document.sm_brand.brand_name.value == "")
        {
            alert("You MUST enter a value for the Brand Name field."); 
			document.sm_brand.brand_name.focus();
            return false;
        }
        if (document.sm_brand.website_domain.value == "")
        {
            alert("You MUST enter a value for the Main Website Domain field."); 
			document.sm_brand.website_domain.focus();
            return false;
        }
        if (document.sm_brand.client.value == 0)
        {
            alert("You MUST enter a select a Client."); 
			document.sm_brand.client.focus();
            return false;
        }
        if (document.sm_brand.block.value == 0)
        {
            alert("You MUST enter a select a Block."); 
			document.sm_brand.block.focus();
            return false;
        }
        if (document.sm_brand.template_id.value == 0)
        {
            alert("You MUST enter a select a Mailing Template."); 
			document.sm_brand.template_id.focus();
            return false;
        }
		return true;
	}
</script>
</head>

<body>
<div id="container">

	<h1>Build a StrongMail Brand</h1>
<center>
<form method="post" action="sm_upload_brand.cgi" encType=multipart/form-data>
Brand File: <input type=file name=upload_file><br>
<input type=submit value=Load>
</form>
</center>

	<div id="form">
<form method=post name=sm_brand action="sm_brand_save.cgi" method=post onsubmit="return ProcessForm();" ENCTYPE="multipart/form-data"> 
		<table>
		  <tr>
			<td class="label">Brand Name:</td>
			<td class="field"><input class="field" id="" type="" name="brand_name" value="" size="35" /></td>
		  </tr>
		  <tr>
			<td class="label">Client:</td>
			<td class="field">
				<select class="field" name=client>
					<option value=0>- select -</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $id;
my $company;
while (($id,$company) = $sth->fetchrow_array())
{
    print "<option value=$id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
	</select></td></tr>
		  <tr>
			<td class="label">Block Assignment:</td>
			<td class="field">
				<select class="field" name=block>
					<option value=0>- select -</option>
end_of_html
$sql="select block_id,block_name from block where status='A' order by block_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $bid;
my $bname;
while (($bid,$bname)=$sth->fetchrow_array())
{
	print "<option value=$bid>$bname</option>";
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Mailing Template:</td>
			<td class="field">
				<select class="field" name=template_id>
					<option value=0>- select -</option>
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	print "<option value=$tid>$tname</option>";
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Unsub Footer Image:</td>
			<td class="field"><input class="field" id="" type="file" name="unsub_img" value="" size="35" /></td>
		  </tr>
		  <tr>
			<td class="label">Main Website Domain:</td>
			<td class="field"><input class="field" id="" type="" name="website_domain" value="" size="35" /></td>
		  </tr>
<!--		  <tr>
			<td class="label">Mailing Domains:<br />
			<span class="note"><em>press "enter" after each domain</em></span></td>
			<td class="field"><textarea class="field" cols="35" rows="5" name="mailing_domain"></textarea></td>
		  </tr> -->
		  <tr>
			<td class="label">Rdns Urls:<br />
			<span class="note"><em>press "enter" after each domain</em></span></td>
			<td class="field"><textarea class="field" cols="35" rows="5" name="rdns_urls"></textarea></td>
		  </tr>
		  <tr>
			<td class="label">Enable auto-replacement for mailing/future/image domains?:</td>
			<td class="field">Yes <input type=radio value='1' checked name="enable_replace"> No <input type=radio value='0' name="enable_replace"></td>
		  </tr>
		  <tr>
			<td class="label">Use Future Mailing Domains?:</td>
			<td class="field">Yes <input type=radio value='Y' checked name="use_future"> No <input type=radio value='N' name="use_future"></td>
		  </tr>
		  <tr>
			<td class="label">Include wiki text:</td>
			<td class="field">Yes <input type=radio value='Y' checked name="use_wiki"> No <input type=radio value='N' name="use_wiki"></td>
		  </tr>
		  <tr>
			<td class="label">Number of Domains To Rotate?:</td>
			<td class="field"><select name="num_domains_rotate">
			<option selected value=1>1</option>
			<option value=2>2</option>
			<option value=3>3</option>
			<option value=4>4</option>
			<option value=5>5</option>
			<option value=6>6</option>
			<option value=7>7</option>
			<option value=8>8</option>
			<option value=9>9</option>
			<option value=10>10</option>
		  </tr>
		  <tr>
			<td class="label">Purpose:</td>
			<td class="field"><input type=radio checked value='Normal' name="purpose">Normal&nbsp;&nbsp;<input type=radio value='Daily' name="purpose">Daily&nbsp;&nbsp;&nbsp;<input type=radio value='Trigger' name="purpose">Trigger</font></td>
		  </tr>
		  <tr>
			<td class="label">Generate SPF:</td>
			<td class="field"><input type=radio checked value='Y' name="generateSpf">Yes&nbsp;&nbsp;<input type=radio value='N' name="generateSpf">No</font></td>
		  </tr>
		</table>

		<div class="submit"><input class="submit" type="submit" name="submit" value="build it" /></div>
	</div>
</form>
</div>
</body>
</html>
end_of_html
