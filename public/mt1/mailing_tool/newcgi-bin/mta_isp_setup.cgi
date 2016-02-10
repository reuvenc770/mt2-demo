#!/usr/bin/perl

# *****************************************************************************************
# mta_isp_setup.cgi
#
# this page display bottom frame for setting up a mta
#
# History
# Jim Sobeck, 03/27/08, Creation
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
my $mta_id = $query->param('mta_id');
my $classid = $query->param('classid');
my $cid;
my $cname;
my $isp_name;
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
my $sql="select class_name from email_class where class_id=? and status='Active'";
$sth=$dbhq->prepare($sql);
$sth->execute($classid);
($isp_name)=$sth->fetchrow_array();
$sth->finish();
$sql="select inj_qty,pause_time,max_records_per_ip,ip_rotation_type,ip_rotation_times,domain_type,domain_times,fromline_type,fromline_times,subjectline_type,subjectline_times,creative_type,creative_times,seed_times,seedlist,seed_type,wikiID,ramp_up,encrypt_link,use_random_batch,variance,oneliner_type,oneliner_times,action_type,action_times,mailingHeaderID,newMailing from mta_detail where mta_id=? and class_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($mta_id,$classid);
my $DETAILS=$sth->fetchrow_hashref();
$sth->finish();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Edit MTA Settings</title>

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
<script language="Javascript">
function open_ramp()
{
var cpage="/cgi-bin/mta_ramp_up.cgi?mta_id=$mta_id&class_id=$classid";
var newwin = window.open(cpage, "RampUp", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
}
</script>
</head>

<body>
<center><b>$isp_name</b></center>
  <div id="form">
	<form method="post" name="campform" action="mta_save_settings.cgi">
	<input type=hidden name=mta_id value=$mta_id>
	  <table>
		  <tr>
			<td class="label">Injection Quantity:</td>
			<td class="field"><input class="field" size="7" value="$DETAILS->{inj_qty}" name=inj_qty /> <em class="note">(number of records per batch)</em></td>
		  </tr>
          <tr>
            <td class="label">Use Random Batch:</td>
end_of_html
if ($DETAILS->{use_random_batch} eq "Y")
{
print<<"end_of_html";
            <td class="field"><input type=radio value="Y" checked name=use_random_batch />Yes&nbsp;&nbsp; <input type=radio value="N" name=use_random_batch />No</td>
end_of_html
}
else
{
print<<"end_of_html";
            <td class="field"><input type=radio value="Y" name=use_random_batch />Yes&nbsp;&nbsp; <input type=radio value="N" checked name=use_random_batch />No</td>
end_of_html
}
print<<"end_of_html";
</tr>
		  <tr>
			<td class="label">Batch Size Variance(Percentage):</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{variance}" name=variance /> 
			</td>
		  </tr>
		  <tr>
			<td class="label">Pause Time Between Files:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{pause_time}" name=pause_time /> seconds <em class="note">(enter zero for continuous)</em>
			</td>
		  </tr>
		  <tr>
			<td class="label">Max # Records per IP:</td>
			<td class="field">
				<input class="field" size="7" value="$DETAILS->{max_records_per_ip}" name=max_records_per_ip /> <em class="note">(enter -1 for no limit)</em>
			</td>
		  </tr>
		  <tr>
			<td class="label">Ramp Up<br>(Note: <b>If this is set to Yes, it overrides the value in Max # Records per IP</b>):</td>
			<td class="field">
end_of_html
if ($DETAILS->{ramp_up} eq "Y")
{
	print "<input name=ramp_up type=radio value=\"Y\" checked>Yes&nbsp;&nbsp;<input type=radio name=ramp_up value=\"N\">No\n";
}
else
{
	print "<input name=ramp_up type=radio value=\"Y\">Yes&nbsp;&nbsp;<input type=radio name=ramp_up value=\"N\" checked>No\n";
}
print<<"end_of_html";
			&nbsp;&nbsp;&nbsp;&nbsp;<input type=button value="Edit Ramp Up" onClick="open_ramp();"></td>
		  </tr>
		  <tr>
			<td class="label"><br />IP Rotation:</td>
			<td class="field"><br />
				Every <input class="field" size="5" value="$DETAILS->{ip_rotation_times}" name=ip_rotation_times /> 

end_of_html
show_select2("ip_rotation_type",$DETAILS->{ip_rotation_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Domain Rotation:</td>
			<td class="field">
				Every <input class="field" size="2" value="$DETAILS->{domain_times}" name=domain_times /> 

end_of_html
show_select2("domain_type",$DETAILS->{domain_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">From Line Rotation:</td>
			<td class="field">
				<input class="field" size="2" value="$DETAILS->{fromline_times}" name=fromline_times /> time(s)

end_of_html
show_select("fromline_type",$DETAILS->{fromline_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Subject Line Rotation:</td>
			<td class="field">
				<input class="field" size="2" value="$DETAILS->{subjectline_times}" name=subjectline_times /> time(s)

end_of_html
show_select("subjectline_type",$DETAILS->{subjectline_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Creative Rotation:</td>
			<td class="field">
				<input class="field" size="2" value="$DETAILS->{creative_times}" name=creative_times /> time(s)

end_of_html
show_select("creative_type",$DETAILS->{creative_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Header Oneliner Rotation:</td>
			<td class="field">
				<input class="field" size="2" value="$DETAILS->{oneliner_times}" name=oneliner_times /> time(s)

end_of_html
show_select("oneliner_type",$DETAILS->{oneliner_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">Action Rotation:</td>
			<td class="field">
				<input class="field" size="2" value="$DETAILS->{action_times}" name=action_times /> time(s)

end_of_html
show_select("action_type",$DETAILS->{action_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label" valign="top"><br />Seed Injection:</td>
			<td class="field"><br />
				Every <input class="field" size="2" value="$DETAILS->{seed_times}" name=seed_times /> 
end_of_html
show_select1("seed_type",$DETAILS->{seed_type});		
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label" valign="top">Seeds to Inject:<br /><span class="note">(separate emails with line breaks)</span></td>
			<td class="field">
				<textarea class="field" cols="35" rows="4" name=seedlist>$DETAILS->{seedlist}</textarea>
			</td>
		  </tr>
		  <tr>
		<td class="label" valign="top">Mailing Templates:<br /></td>
		<td class="field">
			<select name=template_id multiple="multiple" size=5>
end_of_html
my $tid;
my $tname;
$sql="select template_id,template_name from brand_template where status='A' order by template_name"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tid,$tname) = $sth9->fetchrow_array())
{
	$sql="select count(*) from mta_templates where mta_id=? and class_id=? and template_id=?";
	my $sth2d=$dbhq->prepare($sql);
	$sth2d->execute($mta_id,$classid,$tid); 
	($cnt)=$sth2d->fetchrow_array();
	$sth2d->finish();
	if ($cnt > 0)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
												</tr>
		  <tr>
		<td class="label" valign="top">Header Template:<br /></td>
		<td class="field">
			<select name=header_template_id size=1>
end_of_html
my $tid;
my $tname;
$sql="select templateID,templateName from mailingHeaderTemplate where status='A' order by templateName"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tid,$tname) = $sth9->fetchrow_array())
{
	if ($tid == $DETAILS->{mailingHeaderID})
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
												</tr>
		  <tr>
		<td class="label" valign="top">Headers:<br /></td>
		<td class="field">
			<select name=header_id multiple="multiple" size=5>
end_of_html
my $tid;
my $tname;
$sql="select header_id,header_name from Headers where status='A' order by header_name"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tid,$tname) = $sth9->fetchrow_array())
{
	$sql="select count(*) from mta_headers where mta_id=? and class_id=? and header_id=?";
	my $sth2d=$dbhq->prepare($sql);
	$sth2d->execute($mta_id,$classid,$tid); 
	($cnt)=$sth2d->fetchrow_array();
	$sth2d->finish();
	if ($cnt > 0)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
												</tr>
		  <tr>
		<td class="label" valign="top">Footers:<br /></td>
		<td class="field">
			<select name=footer_id multiple="multiple" size=5>
end_of_html
my $tid;
my $tname;
$sql="select footer_id,footer_name from Footers where status='A' order by footer_name"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tid,$tname) = $sth9->fetchrow_array())
{
	$sql="select count(*) from mta_footers where mta_id=? and class_id=? and footer_id=?";
	my $sth2d=$dbhq->prepare($sql);
	$sth2d->execute($mta_id,$classid,$tid); 
	($cnt)=$sth2d->fetchrow_array();
	$sth2d->finish();
	if ($cnt > 0)
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
												</tr>
		  <tr>
		<td class="label" valign="top">Content Domains:<br /></td>
		<td class="field">
			<select name=content_domain multiple="multiple" size=5>
			<option value="">NONE</option>
end_of_html
$sql="select domain from brand_available_domains where brandID in (5197,731) order by domain"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tname) = $sth9->fetchrow_array())
{
	$sql="select count(*) from mta_content_domain where mta_id=? and class_id=? and domain_name=?";
	my $sth2d=$dbhq->prepare($sql);
	$sth2d->execute($mta_id,$classid,$tname); 
	($cnt)=$sth2d->fetchrow_array();
	$sth2d->finish();
	if ($cnt > 0)
	{
		print "<option selected value=$tname>$tname</option>\n";
	}
	else
	{
		print "<option value=$tname>$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
												</tr>
		  <tr>
		<td class="label" valign="top">Wiki:<br /></td>
		<td class="field">
			<select name=wiki_id>
	<option value=0>None</option>
end_of_html
my $wid;
my $wname;
$sql="select wikiID,templateName from wikiTemplate where status='A' order by templateName"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
while (($wid,$wname) = $sth9->fetchrow_array())
{
	if ($wid == $DETAILS->{wikiID})
	{
		print "<option selected value=$wid>$wname</option>\n";
	}
	else
	{
		print "<option value=$wid>$wname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
												</tr>
		  <tr>
			<td class="label">Encrypt Links:</td>
			<td class="field">
end_of_html
if ($DETAILS->{encrypt_link} eq "Y")
{
	print "<input name=encrypt_link type=radio value=\"Y\" checked>Yes&nbsp;&nbsp;<input type=radio name=encrypt_link value=\"N\">No\n";
}
else
{
	print "<input name=encrypt_link type=radio value=\"Y\">Yes&nbsp;&nbsp;<input type=radio name=encrypt_link value=\"N\" checked>No\n";
}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label">New Mailing:</td>
			<td class="field">
end_of_html
if ($DETAILS->{newMailing} eq "Y")
{
	print "<input name=newMailing type=radio value=\"Y\" checked>Yes&nbsp;&nbsp;<input type=radio name=newMailing value=\"N\">No\n";
}
else
{
	print "<input name=newMailing type=radio value=\"Y\">Yes&nbsp;&nbsp;<input type=radio name=newMailing value=\"N\" checked>No\n";
}
print<<"end_of_html";
			</td>
		  </tr>
		  <tr>
			<td class="label"><br />ISPs to Update:</td>
			<td class="field"><br />
end_of_html
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
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
print<<"end_of_html";
			</td>
		  </tr>

		</table>

		<div class="submit">
			<input class="submit" value="update it" type="submit" name=submit>
<br>
end_of_html
if ($mta_id != 1)
{
			print "<input value=\"restore defaults\" type=\"submit\" name=submit>\n";
}
print<<"end_of_html";
		</div>
	</form></div>

</body></html>
end_of_html
exit(0);

sub show_select
{
	my ($fld,$val)=@_;
        print "<select class=field name=$fld>\n";
	if ($val eq "per batch")
	{
        	print "<option selected value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
	elsif ($val eq "per mailing")
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option selected value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
	else
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option selected value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
        print "</select>\n";
}
sub show_select2
{
	my ($fld,$val)=@_;
        print "<select class=field name=$fld>\n";
	if ($val eq "per batch")
	{
        	print "<option selected value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"batch(s)\">batch(s)</option>\n";
        	print "<option value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
	elsif ($val eq "per mailing")
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option selected value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"batch(s)\">batch(s)</option>\n";
        	print "<option value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
	elsif ($val eq "batch(s)")
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option selected value=\"batch(s)\">batch(s)</option>\n";
        	print "<option value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
	else
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"batch(s)\">batch(s)</option>\n";
        	print "<option selected value=\"performance based (daily)\">performance based (daily)</option>\n";
	}
        print "</select>\n";
}
sub show_select1
{
	my ($fld,$val)=@_;
        print "<select class=field name=$fld>\n";
	if ($val eq "per batch")
	{
        	print "<option selected value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"batch(s)\">batch(s)</option>\n";
        	print "<option value=\"minutes\">minutes</option>\n";
	}
	elsif ($val eq "per mailing")
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option selected value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"batch(s)\">batch(s)</option>\n";
        	print "<option value=\"minutes\">minutes</option>\n";
	}
	elsif ($val eq "batch(s)")
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option selected value=\"batch(s)\">batch(s)</option>\n";
        	print "<option value=\"minutes\">minutes</option>\n";
	}
	else
	{
        	print "<option value=\"per batch\">per batch</option>\n";
        	print "<option value=\"per mailing\">per mailing</option>\n";
        	print "<option value=\"batch(s)\">batch(s)</option>\n";
        	print "<option selected value=\"minutes\">minutes</option>\n";
	}
        print "</select>\n";
}
