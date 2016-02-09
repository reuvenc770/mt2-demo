#!/usr/bin/perl
use strict;
use CGI;
use lib "/var/www/html/newcgi-bin";
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $saction=$query->param('saction');
my $sql;
my $sth;
my $sth1;
my $dbh;
my $aid;
my $uid;

#------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

if ($saction eq "AID")
{
	$aid=$query->param('aid');
	$uid=util::buildUID($aid,$dbhu);
	my $aspireurl=$util->getAspireURL();
	print "Location: ".$aspireurl."io/index.cgi?uid=$uid\n\n";
	exit;
}
elsif ($saction eq "IOID")
{
	my $ioid=$query->param('ioaid');
	$uid=util::buildUID($ioid,$dbhu);
	my $aspireurl=$util->getAspireURL();
	print "Location: ".$aspireurl."io/adv-admin.cgi?uid=$uid\n\n";
	exit;
}
my $cstatus=$query->param('cstatus');

print "Content-type: text/html\n\n";
print<<"end_of_html";
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Spire Vision IO Admin</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="/css/style.css" type="text/css" />
	<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
	
	<script type="text/javascript">
	\$(document).ready(function() {

	});
    function setsaction(val)
    {
        document.ioform.saction.value=val;
    }
	</script>
	
</head>

<body>

<div class="wrapper">

	<div class="container">
	
		<div id="container">
	
			<div class="header-3">
				<h1>IO Search Page</h1>
			</div> <!-- end header-3 -->
			
			<form action="spv-admin.cgi" name=ioform method="post">
			<input type=hidden name=saction>
				<fieldset class="blue-box">
					<div class="outer">
					<div class="inner">
					<h1>Spire Vision Admin</h1>
					<ul>
						<li>View IO by Advertiser</li>						
						<li><select name=aid> 
end_of_html
my $ioid;
my $aname;
my $sdate;
$sql="select ioID,advertiser_name from IO,advertiser_info ai where IO.advertiser_id=ai.advertiser_id";
$sql=$sql." order by advertiser_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($ioid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$ioid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="submit" value="Show" onClick="javascript:setsaction('AID');"></li>
					</ul>
					<ul>
						<li>View IO by Campaign</li>
						<li><select name=ioaid> 
end_of_html
my $ioaid;
my $aname;
$sql="select IOAdvertiserID,AdvertiserName from IOAdvertiser order by AdvertiserName"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($ioaid,$aname)=$sth->fetchrow_array())
{
	print "<option value=$ioaid>$aname</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="submit" value="Show" onClick="javascript:setsaction('IOID');"></li>
					</ul>
					<ul>
						<li>View IO by Status</li>
						<li><select name=cstatus>
end_of_html
my @C=("spv","adv","complete","unsigned","canceled","traffic");
my @C1=("Signed by SPV","Signed by ADV","Signed Complete","Unsigned","Canceled","Has traffic/Incomplete IO");
my $i=0;
while ($i <= $#C)
{
	if ($C[$i] eq $cstatus)
	{
		print "<option selected value=$C[$i]>$C1[$i]</option>\n";
	}
	else
	{
		print "<option value=$C[$i]>$C1[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
							</select><input type="submit" value="Show"  onClick="javascript:setsaction('STAT');">
						</li>
					</ul>
					</div>
					</div>
				</fieldset>
			</form>
end_of_html
if ($saction eq "STAT")
{
	$sql="select ioID,advertiser_name,IO.advertiser_id from IO,advertiser_info ai where IO.advertiser_id=ai.advertiser_id";
	if ($cstatus eq "spv")
	{
		$sql=$sql." and IO.cancelIO='N' and ioID in (select ioID from IOSignature where signType='Spirevision') and ioID not in (select ioID from IOSignature where signType='Advertiser')";
	}
	elsif ($cstatus eq "adv")
	{
		$sql=$sql." and IO.cancelIO='N' and ioID not in (select ioID from IOSignature where signType='Spirevision') and ioID in (select ioID from IOSignature where signType='Advertiser')";
	}
	elsif ($cstatus eq "complete")
	{
		$sql=$sql." and IO.cancelIO='N' and ioID in (select ioID from IOSignature where signType='Spirevision') and ioID in (select ioID from IOSignature where signType='Advertiser')";
	}
	elsif ($cstatus eq "unsigned")
	{
		$sql=$sql." and IO.cancelIO='N' and ioID not in (select distinct ioID from IOSignature)";
	}
	elsif ($cstatus eq "canceled")
	{
		$sql=$sql." and IO.cancelIO='Y'";
	}
	elsif ($cstatus eq "traffic")
	{
		$sql=$sql." and advertiser_id in (select distinct advertiser_id from campaign where deleted_date is null and scheduled_date >= date_sub(curdate(),interval 7 day)";
	}
	$sql=$sql." order by advertiser_name";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $cnt;
	print "<table><tr><th>Advertiser</th></tr>\n";
	while (($ioid,$aname,$aid)=$sth->fetchrow_array())
	{
		$cnt=1;
		if ($cstatus eq "traffic")
		{
			$sql="select count(*) from IOSignature where ioID=?";
			my $sth1=$dbh->prepare($sql);
			$sth1->execute($ioid);
			($cnt)=$sth1->fetchrow_array();
			$sth1->finish();
		}
		if ($cnt < 2)
		{
			$uid=util::buildUID($ioid,$dbhu);}
			my $aspireurl=$util->getAspireURL();
			print "<tr><td><a href=\"${aspireurl}cgi-bin/io/index.cgi?uid=$uid\">$aname</a></td></tr>\n";
		}
	}
	$sth->finish();
}
print<<"end_of_html";
	</table>
		</div> <!-- end container -->
	
	</div> <!-- end container -->

</div> <!-- end wrapper -->

</body>
</html>
end_of_html
