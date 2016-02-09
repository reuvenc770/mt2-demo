#!/usr/bin/perl
#===============================================================================
# Name   : sm2_history_schedule.cgi - Allows scheduling of history campaign 
#
#--Change Control---------------------------------------------------------------
# 02/25/08  Jim Sobeck  Creation
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
my $sth1;
my $dbh;
my $tid;
my $startdate;
my $startdate1;
my $sdate;
my $edate;
my $cday;
my $orig_date;
my $cstatus;
my $cdate;
my $daycnt;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$startdate=$query->param('startdate');
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Build History Schedule</title>

<style type="text/css">

BODY {
    FONT-SIZE: 0.9em; BACKGROUND: url(http://www.affiliateimages.com/temp/green_bg.jpg) #B9F795 repeat-x center top; COLOR: #4d4d4d; FONT-FAMILY: "Trebuchet MS", Tahoma, Arial, sans-serif
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

h4 {
	font-weight: normal;
	font-size: .8em;
	margin: 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

#container {
	width: 90%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	padding-top: 1em;
	width: 100%;
	text-align: left;
  }

#form h3 {
	margin: 0;
	padding: 0;
  }

#form p.nextprev {
	float: right;
	margin: 0;
	padding: 0;
  }

#form table {
	clear: both;
	border: 1px solid #aaa;
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	border: 1px solid #ccc;
	padding: .25em;
  }

.inactive {
	color: #aaa;
  }

td.label {
	width: 6%;
	font-weight: bold;
	text-align: center;
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
    refno=/sch_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
    refno=/empty_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function selectday(cday)
{
	var daystr='sch_'+cday;
	var daystr1='empty_'+cday;
    for (var x=0; x < document.adform.length; x++)
    {
		var str=document.adform.elements[x].name;
        if ((document.adform.elements[x].type=="checkbox") && (str.substr(0,5) == daystr))
        {
            if (document.adform.elements[x].checked)
			{ 
            	document.adform.elements[x].checked = false;
			}
			else
			{
            	document.adform.elements[x].checked = true;
			}
		}
        if ((document.adform.elements[x].type=="checkbox") && (str.substr(0,7) == daystr1))
        {
            if (document.adform.elements[x].checked)
			{ 
            	document.adform.elements[x].checked = false;
			}
			else
			{
            	document.adform.elements[x].checked = true;
			}
        }
    }
}
function unselectall()
{
    refno=/sch_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
    refno=/empty_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function selectsch()
{
    refno=/sch_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
function selectempty()
{
    refno=/empty_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = true;
        }
    }
}
</script>
</head>

<body>
<div id="container">
  <h1>Schedule History Building Campaigns</h1>

	<h2><a href="/sm2_build_history.html">build new history campaign</a> | <a href="sm2_list.cgi?type=H">view all history campaigns</a> | <a href="sm2_history_copy_schedule.cgi">Copy Schedule</a></h2>
end_of_html
if ($startdate eq "")
{
    $sql="select date_format(date_sub(curdate(),interval dayofweek(curdate())-1 day),'%m/%d/%Y'),date_sub(curdate(),interval dayofweek(curdate())-1 day),date_sub(curdate(),interval dayofweek(curdate())-1 day)";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($sdate,$cday,$orig_date) = $sth->fetchrow_array();
    $sth->finish();
    $sql="select date_format(date_add(curdate(),interval 7-dayofweek(curdate())
day),'%m/%d/%Y')";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($edate) = $sth->fetchrow_array();
    $sth->finish();
    $sql="select date_add(curdate(),interval 8-dayofweek(curdate()) day)";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($startdate) = $sth->fetchrow_array();
    $sth->finish();
    $sql="select date_sub(curdate(),interval 7+dayofweek(curdate())-1 day)";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($startdate1) = $sth->fetchrow_array();
    $sth->finish();
}
else
{
    $cday = $startdate;
    $sql="select date_format('$startdate','%m/%d/%Y'),date_sub('$startdate',interval dayofweek('$startdate')-1 day)";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($sdate,$orig_date) = $sth->fetchrow_array();
    $sth->finish();
    $sql="select date_format(date_add('$startdate',interval 6 day),'%m/%d/%Y')";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($edate) = $sth->fetchrow_array();
    $sth->finish();
    $sql="select date_sub('$startdate',interval 7 day)";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($startdate1) = $sth->fetchrow_array();
    $sth->finish();
    $sql="select date_add('$startdate',interval 7 day)";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($startdate) = $sth->fetchrow_array();
    $sth->finish();
}
print<<"end_of_html";
<div id="form">

	  <form method="post" name="adform" action="sm2_save_schedule.cgi" target="_top">

		<p class="nextprev"><a href="sm2_history_schedule.cgi?startdate=$startdate1">prev</a> | <a href="sm2_history_schedule.cgi?startdate=$startdate">next</a></p>
end_of_html
print "<h3>Schedule for $sdate - $edate</h3>\n";
print<<"end_of_html";


		<h4><strong>select:</strong> <a href="javascript:selectall()">all</a>, <a href="javascript:unselectall()">none</a>, <a href="javascript:selectsch()">all scheduled</a>, <a href="javascript:selectempty()">all empty</a></h4>

	    <table>
          <tr>
			<td class="label">ID</td>
end_of_html
my $i=0;
my $tday;
my $tdate;
while ($i < 7)
{
	$sql="select day(date_add('$orig_date',interval $i day)),date_format(date_add('$startdate',interval $i day),'%a')";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($tdate,$tday)=$sth->fetchrow_array();
	$sth->finish();
	print "<td><a href=\"javascript:selectday($i);\">$tday</a> / <strong>$tdate</strong></td>\n";
	$i++;
}
print<<"end_of_html";
          </tr>
end_of_html
$sql="select test_id from test_campaign where history_status='Active' and campaign_type='HISTORY' order by test_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($tid)=$sth->fetchrow_array())
{
	print "<tr><td class=label><a href=\"sm2_function.cgi?tid=$tid&submit=edit\">$tid</a></td>\n";
	$i=0;
	while ($i < 7)
	{
		$sql="select datediff(date_add('$orig_date',interval $i day),curdate()),date_add('$orig_date',interval $i day)"; 
		$sth1=$dbhu->prepare($sql);
		$sth1->execute();
		($daycnt,$cdate)=$sth1->fetchrow_array();
		$sth1->finish();

		$sql="select status from test_schedule where schedule_date='$cdate' and test_id=$tid";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute();
		if (($cstatus)=$sth1->fetchrow_array())
		{
			if ($daycnt < 0)
			{
				if ($cstatus eq "START")
				{
					print "<td> scheduled</td>\n";
				}
				else
				{
					print "<td> sent</td>\n";
				}
			}
			else
			{
				if ($cstatus eq "START")
				{
					print "<td><input type=checkbox name=sch_${i}_box value=\"$tid|$cdate\"> scheduled</td>\n";
				}
				else
				{
					print "<td> sent</td>\n";
				}
			}
		}
		else
		{
			if ($daycnt < 0)
			{
				print "<td> <span class=\"inactive\">(empty)</span></td>\n";
			}
			else
			{		
				print "<td><input type=checkbox name=empty_${i}_box value=\"$tid|$cdate\" /> <span class=\"inactive\">(empty)</span></td>\n";
			}
		}
		$sth1->finish();
		$i++;
	}
	print "</tr>";
}
$sth->finish();
print<<"end_of_html";
        </table>
	    <div class="submit">
        <input class="submit" name="submit" value="schedule it" type="submit">
        <input class="submit" name="submit" value="unschedule it" type="submit">
	    </div>
  </form></div>

</div>

</body></html>
end_of_html
