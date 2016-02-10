#!/usr/bin/perl

# *****************************************************************************************
# ipgroupprofile_edit.cgi
#
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
my $DETAILS;
my $sql;
my $cid;
my $cname;
my $classid;
my $gid= $query->param('gid');
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
if ($gid > 0)
{
	$sql="select profileName,minNumGroups,minIpGroupSize,pType,useBulkIps from IpGroupProfile where IpProfileID=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($gid);
	$DETAILS=$sth->fetchrow_hashref();
	$sth->finish();
}
else
{
	$DETAILS->{profileName}="";
	$DETAILS->{minNumGroups}=10;
	$DETAILS->{minIpGroupSize}=3;
	$DETAILS->{pType}="Use Same MTA Only";
	$DETAILS->{useNodeOnly}="N";
	$DETAILS->{useCclassOnly}="N";
	$DETAILS->{useBulkIps}="N";
	$DETAILS->{nodes}="";
	$DETAILS->{seeds}="";
}

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Add/Edit IP Group Profile</title>

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
<center><h2>IPGroup Profile Edit</h2></center>
  <div id="form">
	<form method="post" name="campform" action="ipgroupprofile_save.cgi">
	<input type=hidden name=gid value=$gid>
	  <table>
		  <tr><td class="label">Name:</td><td class="field"><input class="field" size="30" value="$DETAILS->{profileName}" name=name /> </td></tr>
		  <tr><td class="label">Min # of Groups:</td><td class="field"><input class="field" size="5" value="$DETAILS->{minNumGroups}" name=minnumberipgroup /> </td></tr>
		  <tr><td class="label">Min IP Group Size:</td><td class="field"><input class="field" size="5" value="$DETAILS->{minIpGroupSize}" name=minipgroupsize /> </td></tr>
<tr><td class=label>Type: </td><td><select name=ptype>
end_of_html
my @PTYPE=("Use Same MTA Only","Use Same Node Only","Use Same C-Class Only");
foreach my $ptype (@PTYPE)
{
	if ($DETAILS->{pType} eq $ptype)
	{
		print "<option value=\"$ptype\" selected>$ptype</option>\n";
	}
	else
	{
		print "<option value=\"$ptype\" >$ptype</option>\n";
	}
}
if ($DETAILS->{useBulkIps} eq "Y")
{
	print "<tr><td class=label>Use Bulk IPs:</td> <td class=field> <input type=radio checked value=\"Y\" name=usebulkips>Yes&nbsp;&nbsp;<input type=radio value=\"N\" name=usebulkips>No&nbsp;&nbsp;</td></tr>\n";
}
else
{
	print "<tr><td class=label>Use Bulk IPs:</td> <td class=field> <input type=radio value=\"Y\" name=usebulkips>Yes&nbsp;&nbsp;<input type=radio checked value=\"N\" name=usebulkips>No&nbsp;&nbsp;</td></tr>\n";
}
if ($gid > 0)
{
	my $ipNode;
	my $em;
	my $tstr="";
	$sql="select ipNode from IpGroupProfileNode where IpProfileID=? order by ipNode";
	$sth=$dbhu->prepare($sql);
	$sth->execute($gid);
	while (($ipNode)=$sth->fetchrow_array())
	{
		$tstr=$tstr.$ipNode."\n";
	}
	$sth->finish();
	chop($tstr);
	$DETAILS->{nodes}=$tstr;
	#
	# Get seeds for profile
	#
	$tstr="";
	$sql="select emailAddr from IpGroupProfileSeed where IpProfileID=? order by emailAddr";
	$sth=$dbhu->prepare($sql);
	$sth->execute($gid);
	while (($em)=$sth->fetchrow_array())
	{
		$tstr=$tstr.$em."\n";
	}
	$sth->finish();
	chop($tstr);
	$DETAILS->{seeds}=$tstr;
}
print<<"end_of_html";
<tr><td class=label>Email Seeds to use(one per line):</td> <td class=field><textarea rows=10 cols=30 name=seeds>$DETAILS->{seeds}</textarea></td></tr>
<tr><td class=label>Nodes to use (one per line):</td> <td class=field><textarea rows=20 cols=30 name=nodes>$DETAILS->{nodes}</textarea></td></tr>
		</table>

		<div class="submit">
			<input class="submit" value="update it" type="submit" name=submit>
<br>
		</div>
	</form></div>
<center><a href=mainmenu.cgi><img src=/images/home_blkline.gif border=0></a></center>
</body></html>
end_of_html
exit(0);
