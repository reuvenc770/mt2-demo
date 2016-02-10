#!/usr/bin/perl
#===============================================================================
# Name   : source_url.cgi - Allows re-classification of urls 
#
#--Change Control---------------------------------------------------------------
# 03/06/08  Jim Sobeck  Creation
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
my $uid;
my $fname;
my $company;
my @CID=$query->param('cid');
my @OLDDID=$query->param('did');
my $submit=$query->param('submit');
my $export_to_excel=$query->param('export_to_excel');
my $rows;
my $redir_url;
my ($turl_id,$oldid,$newid);

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();

if ($submit eq "modify it")
{
	my $assign_did=$query->param('assign_did');
	if ($assign_did > 0)
	{
		my @UID=$query->param('url');
		my $i=0;
		while ($i <= $#UID)
		{
			$sql="update source_url set datatype_id=$assign_did where url_id=$UID[$i]";
			$rows=$dbhu->do($sql);
			$i++;
		}
	}
}
elsif ($submit eq "save it")
{
	my $sequel=$query->param('sequel');
	my @URL=split(',',$sequel);
	my $i=0;
	my $url_id;
	while ($i <= $#URL)
	{
		my $tstr="dataid_".$URL[$i];
		my $did=$query->param($tstr);
		($turl_id,$oldid,$newid)=split('\|',$did);
		if ($oldid != $newid)
		{
			$sql="update source_url set datatype_id=$newid where url_id=$turl_id";
			$rows=$dbhu->do($sql);
		}
		$i++;
	}
}

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Source URL Data Type</title>

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

<script language="JavaScript">
function selectall()
{
    refno=/url/;
    for (var x=0; x < document.urlform.length; x++)
    {
        if ((document.urlform.elements[x].type=="checkbox") && (refno.test(document.urlform.elements[x].name)))
        {
            document.urlform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/url/;
    for (var x=0; x < document.urlform.length; x++)
    {
        if ((document.urlform.elements[x].type=="checkbox") && (refno.test(document.urlform.elements[x].name)))
        {
            document.urlform.elements[x].checked = false;
        }
    }
}

function chgvalue()
{
	document.urlform.submit.value="modify it";
}
function chgvalue1(sql)
{
	document.urlform.submit.value="save it";
	document.urlform.sequel.value=sql;
}
</script>
</head>

<body>
<center><a href=mainmenu.cgi><img src=/images/home_blkline.gif border=0></a></center>
<div id="container">

	<h1>Source URL Data Type</h1>
	<h2><b>Select report options</b> (hold SHIFT or CTL to select multiple options in a field):</h2>
<form method=post name=urlform action="source_url.cgi">
<input type=hidden name=submit value="filter it">
<input type=hidden name=sequel value="">
	<div class="filter">
		<select size="5" name=cid multiple="multiple">
			<option>-- SELECT CLIENT(s) --</option>
end_of_html
if (($#CID == -1) or (check_user(0)))
{
			print "<option value=0 selected>ALL</option>";
}
else
{
			print "<option value=0>ALL</option>";
}

$sql="select user_id,username,company from user where status='A' order by username";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($uid,$fname,$company)=$sth->fetchrow_array())
{
	if (check_user($uid))
	{
		print "<option selected value=$uid>$fname ($company)</option>\n";
	}
	else
	{
		print "<option value=$uid>$fname ($company)</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select>

		<select size="5" name=did multiple="multiple">
			<option>-- SELECT DATA TYPE(s) --</option>
end_of_html
if (($#OLDDID == -1) or (check_datatype(0)))
{
	print "<option selected value=0>ALL</option>\n";
}
else
{
	print "<option value=0>ALL</option>\n";
}
$sql="select datatype_id,type_str from datatypes order by type_str"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
my $did;
my $type_str;
while (($did,$type_str)=$sth->fetchrow_array())
{
	if (check_datatype($did))
	{
		print "<option selected value=$did>$type_str</option>\n";
	}
	else
	{
		print "<option value=$did>$type_str</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</select>

		<h2><input type="checkbox" name=export_to_excel value=Y /> Export to Excel file?</h2>

		<input class="submit" type="submit" value="Filter it" />
	</div>
end_of_html
if ($#CID >= 0)
{
   	if ($export_to_excel ne "Y")
	{
print<<"end_of_html";
<h4><strong>select:</strong> <a href="javascript:selectall();">all</a>, <a href="javascript:unselectall();">none</a></h4>

<div class="overflow">
	<table width=90%>
		<tr class="label">
			<td>select</td>

			<td>
			Client
			</td>

			<td>
			Source URL
			</td>

			<td>
			Count	
			</td>

			<td>
			Source Data Type
			</td>

			<td>
			New Source Data Type
			</td>
		</tr>
end_of_html
	}
	else
	{
		open(LOG,">/data3/3rdparty/url.csv");
	}
$sql="select url_id,company,type_str,url,source_url.datatype_id,source_url.reccnt from source_url, datatypes, user where source_url.client_id=user.user_id and user.status='A' and source_url.datatype_id=datatypes.datatype_id";
my $alluser=check_user(0);
if (($#CID >= 0) and ($alluser == 0))
{
	my $i=0;
	my $cid_str="";
	while ($i <= $#CID)
	{
		$cid_str=$cid_str.$CID[$i].",";
		$i++;
	}
	chop($cid_str);
	$sql=$sql." and source_url.client_id in ($cid_str)";
}
my $alldid=check_datatype(0);
if (($#OLDDID >= 0) and ($alldid == 0))
{
	my $i=0;
	my $did_str="";
	while ($i <= $#OLDDID)
	{
		$did_str=$did_str.$OLDDID[$i].",";
		$i++;
	}
	chop($did_str);
	$sql=$sql." and source_url.datatype_id in ($did_str)";
}
$sth=$dbhu->prepare($sql);
$sth->execute();
my $cnt=0;
my $url_id;
my $url;
my $old_datatypeid;
my $reccnt;
$sql1="";
while (($url_id,$company,$type_str,$url,$old_datatypeid,$reccnt)=$sth->fetchrow_array())
{
	$sql1=$sql1.$url_id.",";
	$_=$url;
	if (/http/)
	{
		$redir_url=$url;
	}
	else
	{
		$redir_url="http://".$url;
	}
	if ($export_to_excel eq "Y")
	{
		print LOG "$company,$url,$type_str,$reccnt\n";
	}
	else
	{
		if ($cnt % 2)
		{
			print "<tr>";
		}
		else
		{
			print "<tr class=alt>";
		}
		print "<td><input type=checkbox name=url value=$url_id /></td> <td>$company</td> <td><a href=\"$redir_url\" target=\"_blank\">$url</a></td><td>$reccnt</td><td>$type_str</td>";
		print "<td><select name=dataid_${url_id}>";
		$sql="select datatype_id,type_str from datatypes order by type_str"; 
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		while (($did,$type_str)=$sth1->fetchrow_array())
		{
			if ($did == $old_datatypeid)
			{
				print "<option selected value=\"$url_id|$old_datatypeid|$did\">$type_str</option>\n";
			}
			else
			{
				print "<option value=\"$url_id|$old_datatypeid|$did\">$type_str</option>\n";
			}
		}
		$sth1->finish();
		print "</tr>\n";
		$cnt++;
	}
}
$sth->finish();
chop($sql1);
if ($export_to_excel eq "Y")
{
	close(LOG);
print<<"end_of_html";
<center>
<h3>Click <a href="/downloads/url.csv" target=_blank>here</a> to download file
</center>
end_of_html
}
else
{
print<<"end_of_html";
	</table>

	<div class="filter">
		<h2><strong>Assign Selected URLs to New Data Type:</strong>
		<select name=assign_did>
			<option value=0>- select -</option>
end_of_html
$sql="select datatype_id,type_str from datatypes where switch_to='Y' order by type_str"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($did,$type_str)=$sth->fetchrow_array())
{
	print "<option value=$did>$type_str</option>\n";
}
$sth->finish();
print<<"end_of_html";
		</select>
		</h2>

		<input class="submit" type="submit" value="Modify It" onClick="chgvalue();" />
		<input class="submit" type="submit" value="Save It" onClick="chgvalue1('$sql1');" />
	</div>
end_of_html
}
print<<"end_of_html";
</div>
</form>
end_of_html
}
print<<"end_of_html";
</div>
</body>
</html>
end_of_html

sub check_user
{
	my ($uid)=@_;
	my $i=0;
	while ($i <= $#CID)
	{
		if ($CID[$i] == $uid)
		{
			return 1;
		}
		$i++;
	}
	return 0;
}
sub check_datatype
{
	my ($did)=@_;
	my $i=0;
	while ($i <= $#OLDDID)
	{
		if ($OLDDID[$i] == $did)
		{
			return 1;
		}
		$i++;
	}
	return 0;
}
