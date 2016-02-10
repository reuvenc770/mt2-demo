#!/usr/bin/perl
# ******************************************************************************
# rep_record_process_query.cgi
#
# this page displays the Record Processing Summary bottom frame 
#
# History
# Jim Sobeck, 1/15/08, Creation
# ******************************************************************************
# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $tstr;
my $sth1;
my $sql;
my $dbh;
my $errmsg;
my $curmonth;
my $exp_file;
my $cdate;
my ($other_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt, $comcast_cnt);
my $tcnt;
# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();
#
# check for login
my $user_id = util::check_security();
$user_id=1;
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
my @client=$query->param('client');
my $export=$query->param('export');
if ($export eq "Y")
{
	$sql="select date_format(now(),'%Y%m%d%H%i')";
	$sth=$dbhq->prepare($sql);
	$sth->execute();
	($cdate)=$sth->fetchrow_array();
	$sth->finish();
	$exp_file="rec_proc_".$cdate.".csv";
	open(EXPORT,">/data3/3rdparty/$exp_file");
	print EXPORT ",";
}
my @cmonth=$query->param('cmonth');
my $sday=$query->param('sday');
my $eday=$query->param('eday');
my @isp=$query->param('isp');
if (($#isp == -1) or (($#isp == 0) and ($isp[0] eq "")))
{
	$isp[0]="ALL";
	$isp[1]="AOL";
	$isp[2]="Yahoo";
	$isp[3]="Hotmail";
	$isp[4]="Comcast";
	$isp[5]="Others";
}
$sql="select date_format(curdate(),'%Y-%m')";
$sth=$dbhq->prepare($sql);
$sth->execute();
($curmonth)=$sth->fetchrow_array();
$sth->finish();
print "Content-type: text/html\n\n";
if ($export eq "Y")
{
}
else
{ 
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" = "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">

<title>Record Processing Comparative Report</title>

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
	width: 100%;
	margin: 0 auto;
	margin-top: 2em;
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
	width: 200px;
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
</head>
<body>
	<table>
		<tr class="label">
			<td></td>
end_of_html
}
my $i=0;
my $tstr;
while ($i <= $#cmonth)
{
	if ($cmonth[$i] == -1)
	{
		$tstr="Days $sday - $eday";
	}
	else
	{
		$tstr=$cmonth[$i]."-01";
		$sql="select date_format('$tstr','%b')";
		$sth=$dbhq->prepare($sql);
		$sth->execute();
		($tstr)=$sth->fetchrow_array();
		$sth->finish();
	}
	my $j=0;
	while ($j <= $#isp)
	{
		if ($cmonth[$i] eq $curmonth)
		{
			if ($export eq "Y")
			{
				print EXPORT "$tstr $isp[$j] Projected,";
			}
			else
			{
				print "<td>$tstr&nbsp;$isp[$j]<br>Projected</td>\n";
			}
		}
		else
		{
			if ($export eq "Y")
			{
				print EXPORT "$tstr $isp[$j],";
			}
			else
			{
				print "<td>$tstr<br>$isp[$j]</td>\n";
			}
		}		
		$j++;
	}
	$i++;
}
if ($export eq "Y")
{
	print EXPORT "\n";
}
else
{
	print "</tr>";
}
my $i=0;
if (($#client > 1) or ($client[0] != 0))
{
while ($i <= $#client)
{
	if ($client[$i] == 0)
	{
	}
	else
	{
		$sql="select username from user where user_id=?";
		$sth=$dbhq->prepare($sql);
		$sth->execute($client[$i]);
		($tstr)=$sth->fetchrow_array();
		$sth->finish();
		if ($export eq "Y")
		{
			print EXPORT "$tstr,";
		}
		else
		{
			print "<tr><td class=label>$tstr</td>";
		}
		my $k=0;
		while ($k <= $#cmonth)
		{
			my $sdate;
			if ($cmonth[$k] == -1)
			{
				$sql="select sum(others_cnt),sum(aol_cnt),sum(yahoo_cnt),sum(hotmail_cnt),sum(comcast_cnt) from record_processing where client_id=? and date_processed >= date_sub(curdate(),interval $eday day) and date_processed <= date_sub(curdate(),interval $sday day)";
				$sth=$dbhu->prepare($sql);
				$sth->execute($client[$i]);
			}
			else
			{
				$sdate=$cmonth[$k]."-01";
				$sql="select sum(others_cnt),sum(aol_cnt),sum(yahoo_cnt),sum(hotmail_cnt),sum(comcast_cnt) from record_processing where client_id=? and date_processed >= ? and date_processed < date_add(?,interval 1 month)";
				$sth=$dbhu->prepare($sql);
				$sth->execute($client[$i],$sdate,$sdate);
			}
			($other_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt,$comcast_cnt)=$sth->fetchrow_array();
			$sth->finish();
			if ($other_cnt eq "")
			{
				$other_cnt=0;
			}
			if ($aol_cnt eq "")
			{
				$aol_cnt=0;
			}
			if ($yahoo_cnt eq "")
			{
				$yahoo_cnt=0;
			}
			if ($hotmail_cnt eq "")
			{
				$hotmail_cnt=0;
			}
			if($comcast_cnt eq ""){
				$comcast_cnt=0;	
			}
			my $j=0;
			while ($j <= $#isp)
			{
				if ($isp[$j] eq "ALL")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(others_cnt)+sum(aol_cnt)+sum(yahoo_cnt)+sum(hotmail_cnt)+sum(comcast_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($client[$i],$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$other_cnt+$aol_cnt+$yahoo_cnt+$hotmail_cnt+$comcast_cnt;
					}
				}
				elsif ($isp[$j] eq "AOL")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(aol_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($client[$i],$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$aol_cnt;
					}
				}
				elsif ($isp[$j] eq "Yahoo")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(yahoo_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($client[$i],$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$yahoo_cnt;
					}
				}
				elsif ($isp[$j] eq "Hotmail")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(hotmail_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($client[$i],$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$hotmail_cnt;
					}
				}
				elsif ($isp[$j] eq "Comcast")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(comcast_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed <= curdate()";
						$sth=$dbhu->prepare($sql);
						$sth->execute($client[$i],$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$comcast_cnt;
					}
				}
				elsif ($isp[$j] eq "Others")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(others_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($client[$i],$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$other_cnt;
					}
				}                
				if ($export eq "Y")
				{
                	print EXPORT "$tcnt,";
				}
				else
				{
                	print "<td>$tcnt</td>\n";
				}
				$j++;
			}
			$k++;
		}
		if ($export eq "Y")
		{
           	print EXPORT "\n";
		}
		else
		{
			print "</tr>";
		}
	}
	$i++;
}
}
else
{
	my $clientid;
	$sql="select user_id,username from user where status='A' order by company";
	my $sthq=$dbhq->prepare($sql);
	$sthq->execute();
	while (($clientid,$tstr)=$sthq->fetchrow_array())
	{
		print "<tr><td class=label>$tstr</td>";
        my $k=0;
        while ($k <= $#cmonth)
        {
			my $sdate=$cmonth[$k]."-01";
			$sql="select sum(others_cnt),sum(aol_cnt),sum(yahoo_cnt),sum(hotmail_cnt),sum(comcast_cnt) from record_processing where client_id=? and date_processed >= ? and date_processed < date_add(?,interval 1 month)";
			$sth=$dbhu->prepare($sql);
			$sth->execute($clientid,$sdate,$sdate);
			($other_cnt,$aol_cnt,$yahoo_cnt,$hotmail_cnt,$comcast_cnt)=$sth->fetchrow_array();
			$sth->finish();
			if ($other_cnt eq "")
			{
				$other_cnt=0;
			}
			if ($aol_cnt eq "")
			{
				$aol_cnt=0;
			}
			if ($yahoo_cnt eq "")
			{
				$yahoo_cnt=0;
			}
			if ($hotmail_cnt eq "")
			{
				$hotmail_cnt=0;
			}
			if ($comcast_cnt eq "")
			{
				$comcast_cnt=0;
			}
                        my $j=0;
                        while ($j <= $#isp)
                        {
				if ($isp[$j] eq "ALL")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(others_cnt)+sum(aol_cnt)+sum(yahoo_cnt)+sum(hotmail_cnt)+sum(comcast_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($clientid,$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$other_cnt+$aol_cnt+$yahoo_cnt+$hotmail_cnt+$comcast_cnt;
					}
				}
				elsif ($isp[$j] eq "AOL")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(aol_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($clientid,$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$aol_cnt;
					}
				}
				elsif ($isp[$j] eq "Yahoo")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(yahoo_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($clientid,$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$yahoo_cnt;
					}
				}
				elsif ($isp[$j] eq "Hotmail")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(hotmail_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($clientid,$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$hotmail_cnt;
					}
				}
				elsif ($isp[$j] eq "Comcast")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(comcast_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($clientid,$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$comcast_cnt;
					}
				}
				elsif ($isp[$j] eq "Others")
				{
					if ($cmonth[$k] eq $curmonth)
					{
						$sql="select ((sum(others_cnt))/datediff(curdate(),'$sdate'))*datediff(date_add('$sdate',interval 1 month),'$sdate') from record_processing where client_id=? and date_processed >= ? and date_processed < date_sub(curdate(),interval 1 day)";
						$sth=$dbhu->prepare($sql);
						$sth->execute($clientid,$sdate);
						($tcnt)=$sth->fetchrow_array();
						$sth->finish();
						$tcnt=int($tcnt);
					}
					else
					{
						$tcnt=$other_cnt;
					}
				}
				if ($export eq "Y")
				{
           			print EXPORT "$tcnt,";
				}
				else
				{
                	print "<td>$tcnt</td>\n";
				}
                $j++;
			}
            $k++;
		}
		if ($export eq "Y")
		{
   			print EXPORT "\n";
		}
		else
		{
			print "</tr>";
		}
	}
	$sthq->finish(); 
}
if ($export eq "Y")
{
	close(EXPORT);
print<<"end_of_html";
<html><head><title>Record Processing Excel File</title></head>
<body>
<br>
<br>
<center>
<font size=2>Click <a href="/downloads/$exp_file">here</a> to download file.</font>
</center>
</body>
</html>
end_of_html
}
else
{
print<<"end_of_html";
	</table>

</body>
</html>
end_of_html
}
$util->clean_up();
exit(0);
