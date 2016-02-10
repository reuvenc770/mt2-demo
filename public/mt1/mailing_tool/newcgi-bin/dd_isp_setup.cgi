#!/usr/bin/perl

# *****************************************************************************************
# dd_isp_setup.cgi
#
# this page display bottom frame for setting up a Daily Deal 
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
my $dd_id = $query->param('dd_id');
my $classid = $query->param('classid');
my $cid;
my $cname;
my $isp_name;
my $ctype;
my $customClientID;
my $IPGROUP;
my $IPG;
my @WEEKDAY=("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");


#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
#
my $sql="select class_name from email_class where class_id=? and status='Active'";
$sth=$dbhq->prepare($sql);
$sth->execute($classid);
($isp_name)=$sth->fetchrow_array();
$sth->finish();
$sql="select settingType,customClientID from DailyDealSetting where dd_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($dd_id);
($ctype,$customClientID)=$sth->fetchrow_array();
$sth->finish();

$sql="select group_id,domain,template_id,header_id,footer_id,seedlist,wikiID,mailingHeaderID,article_id,mail_from,hotmailDomain,ramp_up_freq,ramp_up_email_cnt,cap_volume,return_path,useRdns from DailyDealSettingDetail where dd_id=? and class_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($dd_id,$classid);
my $DETAILS=$sth->fetchrow_hashref();
$sth->finish();

my $wDay;
my $group_id;
$sql="select weekDay,group_id from DailyDealSettingDetailIpGroup where dd_id=? and class_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($dd_id,$classid);
while (($wDay,$group_id)=$sth->fetchrow_array())
{
	$IPGROUP->{$wDay}=$group_id;
}
$sth->finish();


print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Edit $ctype Deal Setting</title>

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
	width: 80%;
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
<center><b>$isp_name</b></center>
  <div id="form">
	<form method="post" name="campform" action="dd_save_settings.cgi">
	<input type=hidden name=dd_id value=$dd_id>
	<input type=hidden name=ctype value=$ctype>
	  <table>
end_of_html
my $i=0;
my $tid;
my $tname;
$sql="select group_id,group_name from IpGroup where status='Active' order by group_name"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
while (($tid,$tname) = $sth9->fetchrow_array())
{
	$IPG->{$i}{tid}=$tid;
	$IPG->{$i}{tname}=$tname;
	$i++;
}
if ($ctype eq "Trigger")
{
	print "<tr><td class=\"label\">Ip Group:</td><td class=\"field\"><select name=group_id><option value=0 selected>None</option>";
	foreach (keys %{$IPG})
	{
		$tid=$IPG->{$_}{tid};
		$tname=$IPG->{$_}{tname};
		if ($tid == $DETAILS->{group_id})
		{
			print "<option selected value=$tid>$tname</option>\n";
		}
		else
		{
			print "<option value=$tid>$tname</option>\n";
		}
	}
	print "</select></td></tr>";
}
else
{
$i=0;
while ($i <= 6)
{
	print "<tr><td class=\"label\">$WEEKDAY[$i] Ip Group:</td><td class=\"field\"><select name=group_id_$i><option value=0 selected>None</option>";
	my $ind=$i+1;
	if ($ind > 6)
	{
		$ind=1;
	}
	if ($i == 0)
	{
		$ind=7;
	}
  	foreach (sort {$a<=>$b} keys %{$IPG}) 
	{
		$tid=$IPG->{$_}{tid};
		$tname=$IPG->{$_}{tname};
		if ($tid == $IPGROUP->{$ind})
		{
			print "<option selected value=$tid>$tname</option>\n";
		}
		else
		{
			print "<option value=$tid>$tname</option>\n";
		}
	}
	print "</select></td></tr>";
	$i++;
}
}
print<<"end_of_html";
		  <tr>
			<td class="label">Domain:</td>
			<td class="field">
			<select name=domain>
end_of_html
my $tid;
my $tname;
$sql="select domain from brand_available_domains where brandID=4581 order by domain"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
while (($tname) = $sth9->fetchrow_array())
{
	if ($tname eq $DETAILS->{domain})
	{
		print "<option selected value=\"$tname\">$tname</option>\n";
	}
	else
	{
		print "<option value=\"$tname\">$tname</option>\n";
	}
}
$sth9->finish();
print<<"end_of_html";
</select>
</td>
		  </tr>
		  <tr>
			<td class="label">Use Hotmail Domains:</td>
			<td class="field">
end_of_html
if ($DETAILS->{hotmailDomain} eq "Y")
{
	print "<input type=radio name=hotmailDomain value=Y checked>Yes&nbsp;&nbsp;<input type=radio name=hotmailDomain value=N>No\n";
}
else
{
	print "<input type=radio name=hotmailDomain value=Y>Yes&nbsp;&nbsp;<input type=radio name=hotmailDomain value=N checked>No\n";
}
print<<"end_of_html";
	</td></tr>
		  <tr>
			<td class="label">Use Rdns:</td>
			<td class="field">
end_of_html
if ($DETAILS->{useRdns} eq "Y")
{
	print "<input type=radio name=useRdns value=Y checked>Yes&nbsp;&nbsp;<input type=radio name=useRdns value=N>No\n";
}
else
{
	print "<input type=radio name=useRdns value=Y>Yes&nbsp;&nbsp;<input type=radio name=useRdns value=N checked>No\n";
}
print<<"end_of_html";
	</td></tr>
		  <tr>
		<td class="label" valign="top">Content Domains:<br /></td>
		<td class="field">
			<select name=content_domain multiple="multiple" size=5>
end_of_html
$sql="select domain from brand_available_domains where brandID in (5197,731) order by domain"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tname) = $sth9->fetchrow_array())
{
	$sql="select count(*) from DailyDealSettingContentDomain where dd_id=? and class_id=? and domain_name=?";
	my $sth2d=$dbhq->prepare($sql);
	$sth2d->execute($dd_id,$classid,$tname); 
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
</select>&nbsp;&nbsp;<textarea name=pcontent_domain id=pcontent_domain rows=5 cols=50></textarea>
</td>
												</tr>
		  <tr>
			<td class="label" valign="top">Seed to Inject:<br /></td>
			<td class="field">
				<input type=text size=50 maxlength=50 name=seedlist value="$DETAILS->{seedlist}">
			</td>
		  </tr>
		  <tr>
			<td class="label" valign="top">Cap Volume(0 for unlimited):<br /></td>
			<td class="field">
				<input type=text size=7 maxlength=7 name=cap_volume value="$DETAILS->{cap_volume}">
			</td>
		  </tr>
		  <tr>
			<td class="label" valign="top">Return Path:<br /></td>
			<td class="field">
				<input type=text size=20 maxlength=80 name=return_path value="$DETAILS->{return_path}">
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
		<td class="label" valign="top">Mailing Template:<br /></td>
		<td class="field">
			<select name=template_id>
end_of_html
my $tid;
my $tname;
$sql="select template_id,template_name from brand_template where status='A' order by template_name"; 
my $sth9 = $dbhq->prepare($sql);
$sth9->execute();
my $cnt;
while (($tid,$tname) = $sth9->fetchrow_array())
{
	if ($tid == $DETAILS->{template_id})
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
			<td class="label">Header Template:</td>
			<td class="field">
				<select class="field" name="header_template" id="header_template">
end_of_html
$sql="select templateID,templateName from mailingHeaderTemplate where status='A' order by templateName";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
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
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Header:</td>
			<td class="field">
				<select class="field" name="header_id" id="header_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select header_id,header_name from Headers where status='A' order by header_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $DETAILS->{header_id})
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Footer:</td>
			<td class="field">
				<select class="field" name="footer_id" id="footer_id">
<option value=0 checked>--SELECT ONE--</option>
end_of_html
$sql="select footer_id,footer_name from Footers where status='A' order by footer_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $DETAILS->{footer_id})
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
		  <tr>
			<td class="label">Wiki:</td>
			<td class="field">
				<select class="field" name="wiki_id" id="wiki_id">
<option value=0 checked>NONE</option>
end_of_html
$sql="select wikiID,templateName from wikiTemplate where status='A' order by templateName";
$sth=$dbhu->prepare($sql);
$sth->execute();
my $tid;
my $tname;
while (($tid,$tname)=$sth->fetchrow_array())
{
	if ($tid == $DETAILS->{wikiID})
	{
		print "<option selected value=$tid>$tname</option>\n";
	}
	else
	{
		print "<option value=$tid>$tname</option>\n";
	}
}
$sth->finish();

print<<"end_of_html";
				</select>
			</td>
		  </tr>
            <tr>
            <td class="label">Mail From:</td>
    <td><input type=text name=mail_from size=50 maxlength=50 value="$DETAILS->{mail_from}"></td></tr>
		  <tr>
			<td class="label">Article:</td>
			<td>
				<select name="article_id">
end_of_html
$sql="select article_id,article_name from article where status='A' order by article_name";
$sth=$dbhq->prepare($sql);
$sth->execute();
my $template_id;
my $tname;
while (($template_id,$tname)=$sth->fetchrow_array())
{
	if ($DETAILS->{article_id} == $template_id)
	{
		print "<option selected value=$template_id>$tname</option>\n";
	}
	else
	{
		print "<option value=$template_id>$tname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
				</select>
			</td>
		  </tr>
end_of_html

if ($customClientID > 0)
{
	my $keyID;
	my $valueID;
	my $key;
	my $value;
	my $oldkeyID=0;
	$sql="SELECT crk.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName FROM ClientRecordCustomData cd JOIN ClientRecordKeys crk on cd.clientRecordKeyID = crk.clientRecordKeyID JOIN ClientRecordValues crv on cd.clientRecordValueID = crv.clientRecordValueID where clientID=? group by crk.clientRecordKeyID, crv.clientRecordValueID, clientRecordKeyName, clientRecordValueName";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($customClientID);
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
  			print "<tr><td class=label>$key</td><td class=field><select name=cdata size=5 multiple>"; 
			$oldkeyID=$keyID;
		}
		my $cnt;
		$cnt=0;
		if ($dd_id > 0)
		{
			$sql="select count(*) from DailyDealSettingCustom where dd_id=? and class_id=? and clientRecordKeyID=? and clientRecordValueID=?";
			my $sthp=$dbhu->prepare($sql);
			$sthp->execute($dd_id,$classid,$keyID,$valueID);
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
}
print<<"end_of_html";
        <tr><td colspan=2 align=center><a href="javascript:selectall();">Select All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:unselectall();">Unselect All</a><br></td></tr>
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
		print "<input class=radio type=checkbox checked name=isps id=isps value=$cid />$cname/\n";
	}
	else
	{
		print "<input class=radio type=checkbox name=isps id=isps value=$cid />$cname/\n";
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
if ($dd_id != 1)
{
			print "<input value=\"restore defaults\" type=\"submit\" name=submit>\n";
}
print<<"end_of_html";
		</div>
	</form></div>

</body></html>
end_of_html
exit(0);
