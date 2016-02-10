#!/usr/bin/perl

# *****************************************************************************************
# uniqueprofile_edit.cgi
#
# this page display pages to allow editing of a Unique Profile 
#
# History
# Jim Sobeck, 12/16/08, Creation
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
my $pid= $query->param('pid');
my $uid= $query->param('uid');
if ($uid eq "")
{
	$uid=0;
}
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
if ($pid > 0)
{
	$sql="select profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,complaint_control,cc_aol_send,cc_yahoo_send,cc_hotmail_send,cc_other_send,send_international,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,start_record,end_record,ramp_up_freq,subtract_days,add_days,max_end_date,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,send_confirmed,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,ramp_up_email_cnt,min_age,max_age,gender,multitype,multi_start,multi_end,multi_cnt,DeliveryDays,DivideRangeByIsps,ProfileForClient,emailListCntOperator,emailListCnt,groupSuppListID,useLastCategory from UniqueProfile where profile_id=?"; 
	$sth=$dbhq->prepare($sql);
	$sth->execute($pid);
	$DETAILS=$sth->fetchrow_hashref();
	$sth->finish();
}
else
{
	$DETAILS->{profile_name}="";
	$DETAILS->{opener_start}=0;
	$DETAILS->{opener_end}=0;
	$DETAILS->{clicker_start}=0;
	$DETAILS->{clicker_end}=0;
	$DETAILS->{deliverable_start}=0;
	$DETAILS->{deliverable_end}=0;
	$DETAILS->{convert_start}=0;
	$DETAILS->{convert_end}=0;
	$DETAILS->{opener_start1}=0;
	$DETAILS->{opener_end1}=0;
	$DETAILS->{clicker_start1}=0;
	$DETAILS->{clicker_end1}=0;
	$DETAILS->{deliverable_start1}=0;
	$DETAILS->{deliverable_end1}=0;
	$DETAILS->{convert_start1}=0;
	$DETAILS->{convert_end1}=0;
	$DETAILS->{opener_start2}=0;
	$DETAILS->{opener_end2}=0;
	$DETAILS->{clicker_start2}=0;
	$DETAILS->{clicker_end2}=0;
	$DETAILS->{deliverable_start2}=0;
	$DETAILS->{deliverable_end2}=0;
	$DETAILS->{convert_start2}=0;
	$DETAILS->{convert_end2}=0;
	$DETAILS->{deliverable_factor}=0;
	$DETAILS->{complaint_control}='Disable';
	$DETAILS->{cc_aol_send}=0;
	$DETAILS->{cc_yahoo_send}=0;
	$DETAILS->{cc_hotmail_send}=0;
	$DETAILS->{cc_other_send}=0;
	$DETAILS->{send_international}='Y';
	$DETAILS->{send_confirmed}='Y';
	$DETAILS->{ramp_up_freq}=0;
	$DETAILS->{ramp_up_email_cnt}=0;
	$DETAILS->{subtract_days}=0;
	$DETAILS->{add_days}=0;
	$DETAILS->{max_end_date}=180;
	$DETAILS->{min_age}=0;
	$DETAILS->{max_age}=0;
	$DETAILS->{gender}="";
	$DETAILS->{multitype}="N/A";
	$DETAILS->{multi_start}=0;
	$DETAILS->{multi_end}=0;
	$DETAILS->{multi_cnt}=0;
	$DETAILS->{ProfileForClient}=$uid;
	$DETAILS->{emailListCntOperator}="<";
	$DETAILS->{emailListCnt}=0;
	$DETAILS->{groupSuppListID}=0;
	$DETAILS->{useLastCategory}="N";
}

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Add/Edit Unique Profile</title>

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
<script language="JavaScript">
function selectall()
{
    refno=/isps/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
    refno=/isps/;
    for (var x=0; x < document.campform.length; x++)
    {
        if ((document.campform.elements[x].type=="checkbox") && (refno.test(document.campform.elements[x].name)))
        {
            document.campform.elements[x].checked = false;
        }
    }
}
</script>
</head>

<body>
  <div id="form">
	<form method="post" name="campform" action="uniqueprofile_save.cgi">
	<input type=hidden name=pid value=$pid>
	<input type=hidden name=uid value=$DETAILS->{ProfileForClient}>
	  <table>
end_of_html
if ($DETAILS->{ProfileForClient} > 0)
{
	my $uname;
	$sql="select username from user where user_id=?";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($DETAILS->{ProfileForClient});
	($uname)=$sth1->fetchrow_array();
	$sth1->finish();
	print "<tr><td class=label>Profile For Client:</td><td class=field><b>$uname</b></td></tr>";
}
print<<"end_of_html";
		  <tr>
			<td class="label">Profile Name:</td>
			<td class="field"><input class="field" size="30" value="$DETAILS->{profile_name}" name=profile_name /> </td>
		  </tr>
		  <tr>
			<td class="label">Openers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{opener_start_date}" name=ostart_date /> to <input class="field" size="8" value="$DETAILS->{opener_end_date}" name=oend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Openers Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{opener_start}" name=ostart /> to <input class="field" size="7" value="$DETAILS->{opener_end}" name=oend />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{opener_start1}" name=ostart1 /> to <input class="field" size="7" value="$DETAILS->{opener_end1}" name=oend1 />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{opener_start2}" name=ostart2 /> to <input class="field" size="7" value="$DETAILS->{opener_end2}" name=oend2 /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{clicker_start_date}" name=cstart_date /> to <input class="field" size="8" value="$DETAILS->{clicker_end_date}" name=cend_date /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Clickers Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{clicker_start}" name=cstart /> to <input class="field" size="7" value="$DETAILS->{clicker_end}" name=cend />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{clicker_start1}" name=cstart1 /> to <input class="field" size="7" value="$DETAILS->{clicker_end1}" name=cend1 />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{clicker_start2}" name=cstart2 /> to <input class="field" size="7" value="$DETAILS->{clicker_end2}" name=cend2 />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{deliverable_start_date}" name=dstart_date /> to <input class="field" size="8" value="$DETAILS->{deliverable_end_date}" name=dend_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{deliverable_start}" name=dstart /> to <input class="field" size="7" value="$DETAILS->{deliverable_end}" name=dend />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{deliverable_start1}" name=dstart1 /> to <input class="field" size="7" value="$DETAILS->{deliverable_end1}" name=dend1 />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{deliverable_start2}" name=dstart2 /> to <input class="field" size="7" value="$DETAILS->{deliverable_end2}" name=dend2 />
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Date Range(yyyy-mm-dd):</td>
			<td class="field">
				<input class="field" size="8" value="$DETAILS->{convert_start_date}" name=convert_start_date /> to <input class="field" size="8" value="$DETAILS->{convert_end_date}" name=convert_end_date />
			</td>
		  </tr>
		  <tr>
			<td class="label">Convert Range:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{convert_start}" name=convert_start /> to <input class="field" size="7" value="$DETAILS->{convert_end}" name=convert_end />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{convert_start1}" name=convert_start1 /> to <input class="field" size="7" value="$DETAILS->{convert_end1}" name=convert_end1 />&nbsp;&nbsp;or&nbsp;&nbsp;<input class="field" size="7" value="$DETAILS->{convert_start2}" name=convert_start2 /> to <input class="field" size="7" value="$DETAILS->{convert_end2}" name=convert_end2 />
			</td>
		  </tr>
		  <tr>
			<td class="label">Deliverable Factor:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{deliverable_factor}" name=dfactor /><em class="note">(Set to zero to disable)</em>
			</td>
		  </tr>
		  <tr>
			<td class="label">Range(leave blank to include all, first record is 1):</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{start_record}" name=rstart /> to <input class="field" size="7" value="$DETAILS->{end_record}" name=rend />
			</td>
		  </tr>
		  <tr>
			<td class="label">Ramp Up Frequency (Days - set to zero for no ramp up):</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{ramp_up_freq}" name=ramp_up_freq /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Ramp Up Email Count (Set to zero for email ramp up):</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{ramp_up_email_cnt}" name=ramp_up_email_cnt /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Ramp Up Date Range:</td>
			<td class="field">
				Begin Date minus <input class="field" size="7" value="$DETAILS->{subtract_days}" name=subtract_days> days to End Date plus <input class="field" size="7" value="$DETAILS->{add_days}" name=add_days> days
			</td>
		  </tr>
		  <tr>
			<td class="label">Max End Date(Only applies to ramp up):</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{max_end_date}" name=max_end_date /> 
			</td>
		  </tr>
end_of_html
if ($DETAILS->{complaint_control} eq 'Enable')
{
	print "<tr><td class=\"label\">Complaint Control:</td><td class=\"field\"><input class=\"radio\" type=radio value=\"Enable\" name=complaint_control checked/>Enable&nbsp;&nbsp;<input class=\"radio\" type=radio value=\"Disable\" name=complaint_control />Disable&nbsp;&nbsp</td></tr>\n";
}
else
{
	print "<tr><td class=\"label\">Complaint Control:</td><td class=\"field\"><input class=\"radio\" type=radio value=\"Enable\" name=complaint_control />Enable&nbsp;&nbsp;<input class=\"radio\" type=radio value=\"Disable\" name=complaint_control checked/>Disable&nbsp;&nbsp</td></tr>\n";
}
print<<"end_of_html";
		  <tr>
			<td class="label">Complaint AOL Send Cnt:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{cc_aol_send}" name=cc_aol />
			</td>
		  </tr>
		  <tr>
			<td class="label">Complaint Yahoo Send Cnt:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{cc_yahoo_send}" name=cc_yahoo />
			</td>
		  </tr>
		  <tr>
			<td class="label">Complaint Hotmail Send Cnt:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{cc_hotmail_send}" name=cc_hotmail />
			</td>
		  </tr>
		  <tr>
			<td class="label">Complaint Other Send Cnt:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{cc_other_send}" name=cc_other />
			</td>
		  </tr>
		  <tr>
			<td class="label">Send International:</td>
			<td class="field">
end_of_html
			if ($DETAILS->{send_international} eq "Y")
			{
				print "<input type=radio checked value=Y name=send_international />Yes&nbsp;&nbsp;&nbsp;<input type=radio value=N name=send_international />No\n";
			}
			else
			{
				print "<input type=radio value=Y name=send_international />Yes&nbsp;&nbsp;&nbsp;<input type=radio checked value=N name=send_international />No\n";
			}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Divide Range Between Isps:</td>
			<td class="field">
end_of_html
			if ($DETAILS->{DivideRangeByIsps} eq "Y")
			{
				print "<input type=radio checked value=Y name=DivideRangeByIsps />Yes&nbsp;&nbsp;&nbsp;<input type=radio value=N name=DivideRangeByIsps />No\n";
			}
			else
			{
				print "<input type=radio value=Y name=DivideRangeByIsps />Yes&nbsp;&nbsp;&nbsp;<input type=radio checked value=N name=DivideRangeByIsps />No\n";
			}
print<<"end_of_html";
			</td>
		  </tr>
        <tr><td colspan=2 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
		  <tr>
			<td class="label"><br />ISPs to Send:</td>
			<td class="field"><br />
end_of_html
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($pid >0)
	{
		$sql="select class_id from UniqueProfileIsp where profile_id=? and class_id=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($pid,$cid);
		($classid)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=isps value=$cid />$cname/\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=isps value=$cid />$cname/\n";
	}		
}
$sth->finish();
my $surl="";
my $zips="";
my $sids="";
my $cakeids="";
if ($pid > 0)
{
	my $url;
	$sql="select source_url from UniqueProfileUrl where profile_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($pid);
	while (($url)=$sth->fetchrow_array())
	{
		$surl.=$url."\n";
	}
	$sth->finish();
	my $zip;
	$sql="select zip from UniqueProfileZip where profile_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($pid);
	while (($zip)=$sth->fetchrow_array())
	{
		$zips.=$zip."\n";
	}
	$sth->finish();
	my $sid;
	$sql="select sid from UniqueProfileSid where profile_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($pid);
	while (($sid)=$sth->fetchrow_array())
	{
		$sids.=$sid."\n";
	}
	$sth->finish();
	$sql="select cake_creativeID from UniqueProfileCakeCreativeID where profile_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($pid);
	while (($sid)=$sth->fetchrow_array())
	{
		$cakeids.=$sid."\n";
	}
	$sth->finish();
}
print<<"end_of_html";
			</td>
		  </tr>
		<TR><TD class="label">Source URL: </FONT></td><td><textarea name=surl cols=80 rows=10>$surl</textarea></td></tr> 
		<TR><TD class="label">Zips: </FONT></td><td><textarea name=zips cols=80 rows=10>$zips</textarea></td></tr> 
		  <tr><td class="label">Age:</td> <td class="field"> <input class="field" size="7" value="$DETAILS->{min_age}" name=min_age /> to <input class="field" size="7" value="$DETAILS->{max_age}" name=max_age /> </td> </tr>
end_of_html
if ($DETAILS->{gender} eq "")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" name=gender>Female&nbsp;&nbsp;<input type=radio checked value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" name=gender>Empty</td></tr>\n";
}
elsif ($DETAILS->{gender} eq "M")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" checked name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" name=gender>Female&nbsp;&nbsp;<input type=radio value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" name=gender>Empty</td></tr>\n";
}
elsif ($DETAILS->{gender} eq "F")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" checked name=gender>Female&nbsp;&nbsp;<input type=radio value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" name=gender>Empty</td></tr>\n";
}
elsif ($DETAILS->{gender} eq "Empty")
{
	print "<tr><td class=label>Gender:</td> <td class=field> <input type=radio value=\"M\" name=gender>Male&nbsp;&nbsp;<input type=radio value=\"F\" name=gender>Female&nbsp;&nbsp;<input type=radio value=\"\" name=gender>N/A&nbsp;&nbsp;<input type=radio value=\"Empty\" checked name=gender>Empty</td></tr>\n";
}
	print "<tr><td class=label>Dont mail anybody delivered in last</td> <td class=field> <input type=text size=3 maxlength=3 value=\"$DETAILS->{DeliveryDays}\" name=DeliveryDays> days (Specify zero to mail everybody)</td></tr>\n";
	print "<tr><td class=label>Only Mail Emails: </td> <td class=field><select name=emailListCntOperator id=emailListCntOperator>";
my @OP=("<","=",">");
foreach my $mt (@OP)
{
	if ($mt eq $DETAILS->{emailListCntOperator})
	{ 
		print "<option value=\"$mt\" selected>$mt</option>";
	}
	else
	{
		print "<option value=\"$mt\">$mt</option>";
	}
}
print "</select>&nbsp;<input type=text size=3 name='emailListCnt' id='emailListCnt' value=$DETAILS->{emailListCnt} maxlength=3> times in email list (Zero - to disable function)</td></tr>\n";
my @MTYPE=("opener","clicker","converter");
print "<tr><td class=label>Multi-Action Type</td><td class=field>";
foreach my $mt (@MTYPE)
{
	$_=$DETAILS->{multitype};
	if (/$mt/)
	{
		print "<input name=multitype type=checkbox checked value=\"$mt\">$mt&nbsp;&nbsp;\n";
	}
	else
	{
		print "<input name=multitype type=checkbox value=\"$mt\">$mt&nbsp;&nbsp;\n";
	}
}
print<<"end_of_html";
	</td></tr>
		  <tr><td class="label">Multi Date Range:</td> <td class="field"> <input class="field" size="7" value="$DETAILS->{multi_start}" name=multi_start /> to <input class="field" size="7" value="$DETAILS->{multi_end}" name=multi_end /> </td> </tr>
		  <tr><td class="label">Number of Actions for Multi:</td> <td class="field"> <input class="field" size="7" value="$DETAILS->{multi_cnt}" name=multi_cnt /></td> </tr>
		  <tr><td class="label">Offer Categories to Include(If none selected, then defaults to ALL.  None should be selected if wish to use SIDs or Cake Creative IDs):</td> <td class="field">
end_of_html
$sql="select category_id,category_name from category_info where status='A' order by category_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $catid;
my $catname;
my $catcnt;
$catcnt=0;
while (($catid,$catname)=$sth->fetchrow_array())
{
	my $cnt;
	$sql="select count(*) from UniqueProfileCategory where profile_id=? and category_id=?";
	my $sth5=$dbhu->prepare($sql);
	$sth5->execute($pid,$catid);
	($cnt)=$sth5->fetchrow_array();
	$sth5->finish();
	if ($cnt > 0)
	{
		print "<input type=checkbox name=category value=$catid checked>$catname&nbsp;&nbsp;\n";
	}
	else
	{
		print "<input type=checkbox name=category value=$catid>$catname&nbsp;&nbsp;\n";
	}
	$catcnt++;
	if ($catcnt >= 10)
	{
		print "<br>";
		$catcnt=0;
	}

}
$sth->finish();
print "</td></tr>";
print qq^<tr><td class="label">Use Last Category:</td> <td class="field">^;
if ($DETAILS->{useLastCategory} eq "Y")
{
	print "<input type=checkbox value=Y checked name=useLastCategory>";
}
else
{
	print "<input type=checkbox value=Y name=useLastCategory>";
}
print "</td> </tr>";
print<<"end_of_html";
		<TR><TD class="label">SIDs: </FONT></td><td><textarea name=sids cols=10 rows=10>$sids</textarea></td></tr> 
		<TR><TD class="label">Cake Creative IDs: </FONT></td><td><textarea name=cakeids cols=10 rows=10>$cakeids</textarea></td></tr> 
		  <tr>
			<td class="label">Group Suppression List:</td>
			<td class="field">
				<select class="field" name="groupSuppListID" id="groupSuppListID">
<option value=0 checked>None</option>
end_of_html
$sql="select list_id,list_name from vendor_supp_list_info where status='A' and suppressionType='Group' order by list_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $slid;
my $sname;
while (($slid,$sname)=$sth->fetchrow_array())
{
	if ($slid == $DETAILS->{groupSuppListID})
	{
		print "<option selected value=$slid>$sname</option>\n";
	}
	else
	{
		print "<option value=$slid>$sname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />Last Action Country:</td>
			<td class="field"><br />
end_of_html
$sql="select countryID,countryCode from Country where visible=1 order by countryCode"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($pid >0)
	{
		$sql="select countryID from UniqueProfileCountry where profile_id=? and countryID=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($pid,$cid);
		($classid)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=country value=$cid >$cname\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=country value=$cid >$cname\n";
	}		
}
$sth->finish();
print<<"end_of_html";
		  <tr>
			<td class="label"><br />Devices:</td>
			<td class="field"><br />
end_of_html
$sql="select userAgentStringLabelID,userAgentStringLabel from UserAgentStringsLabel order by 2"; 
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	if ($pid >0)
	{
		$sql="select userAgentStringLabelID from UniqueProfileUA where profile_id=? and userAgentStringLabelID=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($pid,$cid);
		($classid)=$sth1->fetchrow_array();
		$sth1->finish();
	}
	if ($cid == $classid)
	{
		print "<input class=radio type=checkbox checked name=ua value=$cid >$cname\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=ua value=$cid >$cname\n";
	}		
}
$sth->finish();
#if ($DETAILS->{ProfileForClient} > 0)
#{
	my $keyID;
	my $valueID;
	my $key;
	my $value;
	my $oldkeyID=0;
	#$sql="SELECT crk.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName FROM ClientRecordCustomData cd JOIN ClientRecordKeys crk on cd.clientRecordKeyID = crk.clientRecordKeyID JOIN ClientRecordValues crv on cd.clientRecordValueID = crv.clientRecordValueID where clientID=? group by crk.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName";
	$sql="select crdv.clientRecordKeyID,crv.clientRecordValueID,clientRecordKeyName,crdv.clientRecordValueName from ClientRecordDisplayValues crdv JOIN ClientRecordKeys crk on crdv.clientRecordKeyID=crk.clientRecordkeyID join ClientRecordValues crv on crdv.clientRecordValueName=crv.clientRecordValueName group by crdv.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName order by clientRecordKeyName,crdv.clientRecordValueName";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($keyID,$valueID,$key,$value)=$sth1->fetchrow_array())
	{
		if ($keyID != $oldkeyID)
		{
			if ($oldkeyID != 0)
			{
				print "</select></td></tr>\n";
			}
			else
			{
  				print "<tr><td colspan=2 align=middle><font size=+1>Custom Data</font></td></tr>"; 
			}
  			print "<tr><td class=label>$key</td><td class=field><select name=cdata size=10 multiple>"; 
			$oldkeyID=$keyID;
		}
		my $cnt;
		$cnt=0;
		if ($pid > 0)
		{
			$sql="select count(*) from UniqueProfileCustom where profile_id=? and clientRecordKeyID=? and clientRecordValueID=?";
			my $sthp=$dbhu->prepare($sql);
			$sthp->execute($pid,$keyID,$valueID);
			($cnt)=$sthp->fetchrow_array();
			$sthp->finish();
		}
		if ($cnt > 0)
		{
			print "<option selected value=\"$keyID|$valueID\">$value</option>";
		}
		else
		{
			print "<option value=\"$keyID|$valueID\">$value</option>";
		}
	}
	$sth1->finish();
	if ($oldkeyID != 0)
	{
		print "</select></td></tr>\n";
	}
#}
print<<"end_of_html";
		</table>

		<div class="submit">
			<input class="submit" value="update it" type="submit" name=submit>
<br>
		</div>
	</form></div>

</body></html>
end_of_html
exit(0);
