#!/usr/bin/perl
#===============================================================================
# Name   : creative_findreplace.cgi 
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
	$cstatus1="A";
	$cstatus2="";
	$cstatus3="";
}
else
{
	$cstatus2= $query->param('cstatus2');
	$cstatus3= $query->param('cstatus3');
}
#------ connect to the util database ------------------

my ($dbhq,$dbhu)=$util->get_dbh();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>View Advertiser</h3>
<br>
<form method=post action="/cgi-bin/creative_findreplace_save.cgi" target=_blank>
<table border=0 width=60%>
end_of_html
	print "<tr><td align=right><b>Advertisers</b></td><td><select name=aid multiple=multiple size=15>";
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
print<<"end_of_html";
<tr><td align=right>Asset </td><td><select name=asset><option value="Creative">Creative</option>
<option value="From">From</option>
<option value="Subject">Subject</option>
</select></td></tr>
<tr><td align=right>Assets To Use</td><td><select name=ctype><option value="">ALL</option>
<option value="A" selected>Active</option><option value="I">Inactive</option></select></td></tr>
<tr><td align=right>Asset name:</td><td><input type=text name=cname size=30></td></tr>
<tr>
            <td><b>Text/Tags within</b></td><td colspan=3><select name=climit><option value="">Contains</option><option value="not">Does Not Contain</option></select>&nbsp;&nbsp;<input type=text name=tstr size=100></td>
</tr>
<tr><td align=right>Asset IDs</td><td><textarea name=cids rows=20 cols=15></textarea></td></tr>
<tr><td align=right>Cake Creative IDs</td><td><textarea name=cakeids rows=5 cols=15></textarea></td></tr>
<tr><td align=right>Cake Offer ID:</td><td><input type=text name=cake_offerID size=30></td></tr>
<tr><td align=right>SID:</td><td><input type=text name=sid size=30></td></tr>
<tr><td colspan=2 align=middle><input type=submit value="View Assets"></td></tr>
</table>
</form>
</body>
</html>
end_of_html
exit(0);

