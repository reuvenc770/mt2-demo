#!/usr/bin/perl
#===============================================================================
# Name   : replace_advertiser.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $sth1;
my $aname;
my $taid;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $aid= $query->param('aid');
if ($aid eq "")
{
	$aid=0;
}
my $cstatus1= $query->param('cstatus1');
my $cstatus2;
my $cstatus3;
if ($cstatus1 eq "")
{
	$cstatus1="T";
	$cstatus2="A";
	$cstatus3="P";
}
else
{
	$cstatus2= $query->param('cstatus2');
	$cstatus3= $query->param('cstatus3');
}
#------ connect to the util database ------------------

my ($dbhq,$dbhu)=$util->get_dbh();
if ($aid > 0)
{
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($aname) = $sth1->fetchrow_array();
$sth1->finish;
}

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>Replace Advertiser</h3>
<br>
<form method=get action="/cgi-bin/replace_advertiser_save.cgi" target=_top>
end_of_html
if ($aid > 0)
{
print<<"end_of_html";
<input type=hidden name=aid value=$aid>
end_of_html
}
print<<"end_of_html";
<table border=0 width=60%>
end_of_html
if ($aid > 0)
{
print<<"end_of_html";
<tr><td><b>Replace Advertiser</b></td><td>$aname</td></tr>
end_of_html
}
else
{
	print "<tr><td align=right><b>Replace Advertiser</b></td><td><select name=aid>";
	$sql = "select advertiser_id,advertiser_name from advertiser_info where ";
	if ($cstatus1 ne "")
	{
		if ($cstatus1 eq "T")
		{
			$sql = $sql . " ((status='A' and test_flag='Y')";
		}
		elsif ($cstatus1 eq "P")
		{
			$sql = $sql . " ((status='I' and test_flag='P')";
		}
		elsif ($cstatus1 eq "I")
		{
			$sql = $sql . " ((status='I' and test_flag='N')";
		}
		else
		{
			$sql = $sql . " ((status='$cstatus1')";
		}
		if ($cstatus2 eq "T")
		{
			$sql = $sql . " or (status='A' and test_flag='Y')";
		}
		elsif ($cstatus2 eq "P")
		{
			$sql = $sql . " or (status='I' and test_flag='P')";
		}
		elsif ($cstatus2 ne "")
		{
			$sql = $sql . " or (status='$cstatus2')";
		}
		if ($cstatus3 eq "T")
		{
			$sql = $sql . " or (status='A' and test_flag='Y')";
		}
		elsif ($cstatus3 eq "P")
		{
			$sql = $sql . " or (status='I' and test_flag='P')";
		}
		elsif ($cstatus3 ne "")
		{
			$sql = $sql . " or (status='$cstatus3')";
		}
		$sql=$sql . ")";
	}
	$sql = $sql . " order by advertiser_name";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	while (($taid,$aname) = $sth1->fetchrow_array())
	{
		print "<option value=$taid>$aname ($taid)</option>\n";
	}
	$sth1->finish;
	print "</select></td></tr>";
}
print<<"end_of_html";
<tr><td align=right><b>With</b></td><td><select name=new_aid>
end_of_html
	$sql = "select advertiser_id,advertiser_name from advertiser_info where ";
	if ($cstatus1 ne "")
	{
		if ($cstatus1 eq "T")
		{
			$sql = $sql . " ((status='A' and test_flag='Y')";
		}
		elsif ($cstatus1 eq "P")
		{
			$sql = $sql . " ((status='I' and test_flag='P')";
		}
		elsif ($cstatus1 eq "I")
		{
			$sql = $sql . " ((status='A' and test_flag='N')";
		}
		else
		{
			$sql = $sql . " ((status='$cstatus1')";
		}
		if ($cstatus2 eq "T")
		{
			$sql = $sql . " or (status='A' and test_flag='Y')";
		}
		elsif ($cstatus2 eq "P")
		{
			$sql = $sql . " or (status='I' and test_flag='P')";
		}
		elsif ($cstatus2 ne "")
		{
			$sql = $sql . " or (status='$cstatus2')";
		}
		if ($cstatus3 eq "T")
		{
			$sql = $sql . " or (status='A' and test_flag='Y')";
		}
		elsif ($cstatus3 eq "P")
		{
			$sql = $sql . " or (status='I' and test_flag='P')";
		}
		elsif ($cstatus3 ne "")
		{
			$sql = $sql . " or (status='$cstatus3')";
		}
		$sql=$sql . ")";
	}
	$sql = $sql . " order by advertiser_name";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($taid,$aname) = $sth1->fetchrow_array())
{
	print "<option value=$taid>$aname ($taid)</option>\n";
}
$sth1->finish;
print<<"end_of_html";
</select>
</td></tr>
<tr><td align=right><b>For Clients</b></td><td><select name=client_id size=5 multiple>
<option value=0>ALL</option>
end_of_html
my $uid;
my $uname;
$sql = "select user_id,first_name from user where status='A' order by first_name";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
while (($uid,$uname) = $sth1->fetchrow_array())
{
	print "<option value=$uid>$uname</option>\n";
}
$sth1->finish;
print<<"end_of_html";
</select>
<tr><td colspan=2 align=middle><input type=radio name=ctype value="ALL" checked>All&nbsp;&nbsp;<input type=radio name=ctype value="DAILY">Daily Deals Only&nbsp;&nbsp;<input type=radio ctype value="TRIGGER">Triggers Deals Only</td></tr>
<tr><td colspan=2 align=middle><input type=submit value="Replace"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);

