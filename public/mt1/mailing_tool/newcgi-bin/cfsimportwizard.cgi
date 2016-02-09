#!/usr/bin/perl
#===============================================================================
# Name   : cfsimportwizard.cgi
#
#--Change Control---------------------------------------------------------------
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
my $dbh;
my $exclude_isps=0;
my $chk_str;
my $oldsid;
my $rows;
my $mesg;
my @ispbox;
my $subjfile;
my $fromfile;
my $cid;
my $cname;
my $ISP;
my $tname;
my $isp_str="";
my $isp_ids="";
my $csubject;
my $sid;
my $aflag;
my $oflag;
my $internal_aflag;
my $temp_str;
my $copywriter;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
my $old_catid=$query->param('catid');
my @ADV=$query->param('adv_id');
my @addsubject=$query->param('addsubject');
my @addfrom=$query->param('addfrom');
@ispbox=$query->param('ispbox');
if ($old_catid eq "")
{
	$old_catid=-1;
}
$subjfile="/tmp/cfs_subject_".$user_id.".dat";
$fromfile="/tmp/cfs_from_".$user_id.".dat";
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	$ISP->{$cid}=$cname;
}
$sth->finish();
$mesg="";
my $submit=$query->param('submit');
if ($submit eq "")
{
	$exclude_isps=1;
	unlink($subjfile);
	unlink($fromfile);
}
elsif ($submit eq "Add Subjects/Froms")
{
	$isp_str="";
	$isp_ids="";
	foreach $tname (@ispbox)
	{
		$isp_str.=$ISP->{$tname}. ",";
		$isp_ids.=$tname.",";
	}
	chop($isp_str);
	chop($isp_ids);
	open(SUBJ,">>$subjfile");
	my $csubject=$query->param('csubject');
	$csubject=~ s/[\n\r\f\t]/\|/g ;
	$csubject=~ s/\|{2,999}/\|/g ;
	my @s_array = split '\|', $csubject;
	foreach $tname (@s_array)
	{
		print SUBJ "$tname|$isp_str|$isp_ids|\n";
	}	
	close(SUBJ);
	my $cfrom=$query->param('cfrom');
	open(FROM,">>$fromfile");
	$cfrom=~ s/[\n\r\f\t]/\|/g ;
	$cfrom=~ s/\|{2,999}/\|/g ;
	my @f_array = split '\|', $cfrom;
	foreach $tname (@f_array)
	{
		print FROM "$tname|$isp_str|$isp_ids|\n";
	}	
	close(FROM);
}
elsif ($submit eq "Add Subject/From to Advertiser")
{
	my $adv_id;
	my $sid;
	my $sname;
	my $isp_str;
	my $isp_ids;
	foreach $adv_id (@ADV)
	{
		my $temp_str="dontapprovesubject".$adv_id;
		my $dontappflag=$query->param($temp_str);
		$temp_str="addsubjectadv".$adv_id;
		my @SID=$query->param($temp_str);
		foreach $sid (@SID)
		{
			($sname,$isp_str,$isp_ids)=getSubj($sid);
			if ($sname ne "")
			{
				$sname=~s/'/''/g;
				$oldsid="";
				while ($oldsid eq "")
				{
					$sql="select subject_id from advertiser_subject where advertiser_id=? and advertiser_subject='".$sname."'";
					$sth=$dbhu->prepare($sql);
					$sth->execute($adv_id);
					($oldsid)=$sth->fetchrow_array();
					$sth->finish();
					if ($oldsid eq "")
					{
						if ($dontappflag eq "Y")
						{
							$sql="insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,status,original_flag) values($adv_id,'$sname','N','A','Y')";
						}
						else
						{
							$sql="insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,status,date_approved,approved_by,original_flag) values($adv_id,'$sname','Y','A',now(),'CFS','Y')";
						}
						$rows=$dbhu->do($sql);
						$mesg.="Subject: $sname added to Advertiser $adv_id|";
					}
				}
				my @IDS=split(',',$isp_ids);
				foreach my $isp (@IDS)
				{
					$sql="insert ignore into EmailClassSubject(class_id,subject_id) values($isp,$oldsid)";
					$rows=$dbhu->do($sql);
				}
			}
		}
		$temp_str="dontapprovefrom".$adv_id;
		$dontappflag=$query->param($temp_str);
		$temp_str="addfromadv".$adv_id;
		my @FID=$query->param($temp_str);
		foreach $sid (@FID)
		{
			($sname,$isp_str,$isp_ids)=getFrom($sid);
			if ($sname ne "")
			{
				$sname=~s/'/''/g;
				$oldsid="";
				while ($oldsid eq "")
				{
					$sql="select from_id from advertiser_from where advertiser_id=? and advertiser_from='".$sname."'";
					$sth=$dbhu->prepare($sql);
					$sth->execute($adv_id);
					($oldsid)=$sth->fetchrow_array();
					$sth->finish();
					if ($oldsid eq "")
					{
						if ($dontappflag eq "Y")
						{
							$sql="insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,status,original_flag) values($adv_id,'$sname','N','A','Y')";
						}
						else
						{
							$sql="insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,status,date_approved,approved_by,original_flag) values($adv_id,'$sname','Y','A',now(),'CFS','Y')";
						}
						$rows=$dbhu->do($sql);
						$mesg.="From: $sname added to Advertiser $adv_id|";
					}
				}
				my @IDS=split(',',$isp_ids);
				foreach my $isp (@IDS)
				{
					$sql="insert ignore into EmailClassFrom(class_id,from_id) values($isp,$oldsid)";
					$rows=$dbhu->do($sql);
				}
			}
		}
	}
	$exclude_isps=1;
	unlink($subjfile);
	unlink($fromfile);
}
#------  connect to the util database -----------
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<!-- saved from url=(0022)http://internet.e-mail -->
<html>
<head></head>
<script language="JavaScript">
function dispalert()
{
end_of_html
if ($mesg ne "")
{
	$mesg=~s/\|/\\n/g;
	print "alert('$mesg');\n";
}
print<<"end_of_html";
}
function selectall()
{
	refno=/ispbox/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
           	document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
	refno=/ispbox/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function unselectalladvsubj(adv)
{
	var1="addsubjectadv"+adv;
	var refno=new RegExp(var1);
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function unselectalladvfrom(adv)
{
	var1="addfromadv"+adv;
	var refno=new RegExp(var1);
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function unselectallsubj()
{
	refno=/addsubject/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
function unselectallfrom()
{
	refno=/addfrom/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
</script>
<body>

<div>
	<u><b>CFS Import Wizard</b></u><p>
<center><a target="_top" href="/cgi-bin/mainmenu.cgi" target=_top><img height="23" src="/images/home_blkline.gif" width="76" border="0"></a>
</center>
	<form name=adform method=post action="/cgi-bin/cfsimportwizard.cgi">
	<b>New Subject Lines:</b><br>
	<textarea name="csubject" rows="7" cols="82"></textarea></p>
	<p><b>New From Lines:</b><br>
	<textarea name="cfrom" rows="7" cols="82"></textarea><br></p>
	<p><a href="javascript:selectall()">Select All</a>
	<a href="javascript:unselectall()">UnSelect All</a><b><font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2"><br>Email Class:</font></b> 
end_of_html
$sql="select class_id,class_name from email_class where status='Active' order by class_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid,$cname)=$sth->fetchrow_array())
{
	$chk_str="CHECKED";
	if (($exclude_isps == 1) and ($cname eq "AOL" or $cname eq "Hotmail" or $cname eq "Yahoo"))
	{
		$chk_str="";
	}
	elsif ($exclude_isps == 0)
	{
		$chk_str=checkIsp($cid,@ispbox);
	}
	print "<input type=checkbox $chk_str value=$cid name=\"ispbox\"><b><font face=\"verdana,arial,helvetica,sans serif\" color=\"#509c10\" size=\"2\">$cname</font></b>";
}
$sth->finish();
print<<"end_of_html";
	<br><br><input value="Add Subjects/Froms" type="submit" name="submit"><br>
	<b>Adding Subject Lines:</b>
	<a href="javascript:unselectallsubj()">UnSelect All</a><br>
end_of_html
	my $i=1;
	open(SUBJ,"<$subjfile");
	while (<SUBJ>)
	{
		($tname,$isp_str,$isp_ids)=split('\|',$_);
		print "&nbsp;&nbsp;&nbsp;<input name=addsubject checked=checked value=$i type=checkbox>$tname - $isp_str<br>\n";
		$i++;
	}
	close(SUBJ);
	print "&nbsp;<br><b>Adding From Lines: </b>\n";
	print "<a href=\"javascript:unselectallfrom()\">UnSelect All</a><br>\n";
	my $i=1;
	open(FROM,"<$fromfile");
	while (<FROM>)
	{
		($tname,$isp_str,$isp_ids)=split('\|',$_);
		print "&nbsp;&nbsp;&nbsp;<input name=addfrom checked=checked value=$i type=checkbox>$tname - $isp_str<br>\n";
		$i++;
	}
	close(FROM);
print<<"end_of_html";
	<br><b>Category:</b><br>
	<select name="catid">
	<option value="-1" selected><-- SELECT ONE --></option>
end_of_html
	if ($old_catid == 0)
	{
		print "<option value=0 selected>ALL</option>\n";
	}
	else
	{
		print "<option value=0>ALL</option>\n";
	}
my $catid;
my $catname;
$sql="select category_id,category_name from category_info order by category_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($catid,$catname)=$sth->fetchrow_array())
{
	if ($old_catid == $catid)
	{
		print "<option selected value=$catid>$catname</option>\n";
	}
	else
	{
		print "<option value=$catid>$catname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
	</select> <input value="Update Advertisers" type="submit" name="submit"> <br>
	<br>
	<b>Advertiser:</b><br>
	<select name="adv_id" multiple="multiple" size="10">
end_of_html
if ($old_catid >= 0)
{
	if ($old_catid == 0)
	{
		$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' order by advertiser_name";
	}
	else
	{
		$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and test_flag='N' and category_id=$old_catid order by advertiser_name";
	}
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	my $aid;
	my $aname;
	while (($aid,$aname)=$sth->fetchrow_array())
	{
		$chk_str=checkAdv($aid,@ADV);
		print "<option $chk_str value=$aid>$aname</option>\n";
	}
}
print<<"end_of_html";
	</select> <input value="Submit" type="submit" name="submit">
<p><b>Uncheck each box to decline add of assets to campaign:</b></p>
end_of_html
if ($#ADV >= 0)
{
	my $adv_id;
	my $aname;
	foreach $adv_id (@ADV)
	{
		$sql="select advertiser_name from advertiser_info where advertiser_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($adv_id);	
		($aname)=$sth->fetchrow_array();
		$sth->finish();
		print "<u><b><a href=\"/cgi-bin/advertiser_disp2.cgi?pmode=U&puserid=$adv_id\" target=\"_blank\">$aname: </a> </b></u><br>\n";
		my $aurl;
		$sql="select url from advertiser_tracking where client_id=1 and daily_deal='N' and advertiser_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($adv_id);	
		($aurl)=$sth->fetchrow_array();
		$sth->finish();
		print "<b>Redirect URL:</b> $aurl<br>\n";
print<<"end_of_html";
	<br>
	<b>Subject Lines: </b><br>
end_of_html
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag,internal_approved_flag,copywriter from advertiser_subject where advertiser_id=$adv_id and status in ('A','I') order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($sid,$csubject,$aflag,$oflag,$internal_aflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $sid . " - " . $csubject. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
    	$temp_str = $temp_str . "- AA ";
    }
    else
    {
    	$temp_str = $temp_str . "- NA! ";
    }
    if ($internal_aflag eq "Y")
    {
    	$temp_str = $temp_str . "- IA)";
    }
    else
    {
    	$temp_str = $temp_str . ")";
    }
	print "&nbsp;&nbsp;&nbsp;$temp_str &nbsp;&nbsp;<a target=\"_blank\" href=\"/cgi-bin/edit_subject.cgi?aid=$adv_id&sid=$sid\">e</a><br>\n";
}
$sth->finish();
print<<"end_of_html";
	<br>
	<b>Adding Subject Lines: </b>&nbsp;&nbsp;<input type=checkbox name=dontapprovesubject${adv_id} value=Y>Add without Advertiser Approval&nbsp;&nbsp;
	<a href="javascript:unselectalladvsubj($adv_id)">UnSelect All</a><br>
end_of_html
	my $i=1;
	open(SUBJ,"<$subjfile");
	while (<SUBJ>)
	{
		($tname,$isp_str,$isp_ids)=split('\|',$_);
		print "&nbsp;&nbsp;&nbsp;<input name=addsubjectadv${adv_id} checked=checked value=$i type=checkbox>$tname<br>\n";
		$i++;
	}
	close(SUBJ);
print<<"end_of_html";
	<br>
	<b>From Lines:</b><br>
end_of_html
$sql = "select from_id,advertiser_from,approved_flag,original_flag,internal_approved_flag,copywriter from advertiser_from where advertiser_id=$adv_id and status in ('A','I') order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($sid,$csubject,$aflag,$oflag,$internal_aflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $sid . " - " . $csubject. " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($copywriter eq "Y")
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
    	$temp_str = $temp_str . "- AA ";
    }
    else
    {
    	$temp_str = $temp_str . "- NA! ";
    }
    if ($internal_aflag eq "Y")
    {
    	$temp_str = $temp_str . "- IA)";
    }
    else
    {
    	$temp_str = $temp_str . ")";
    }
	print "&nbsp;&nbsp;&nbsp;$temp_str &nbsp;&nbsp;<a target=\"_blank\" href=\"/cgi-bin/edit_from.cgi?aid=$adv_id&sid=$sid\">e</a><br>\n";
}
$sth->finish();
print<<"end_of_html";
	<br>
	<b>Adding From Lines: </b>&nbsp;&nbsp;<input type=checkbox name=dontapprovefrom${adv_id} value=Y>Add without Advertiser Approval&nbsp;&nbsp;
	<a href="javascript:unselectalladvfrom($adv_id)">UnSelect All</a><br>
end_of_html
	my $i=1;
	open(FROM,"<$fromfile");
	while (<FROM>)
	{
		($tname,$isp_str,$isp_ids)=split('\|',$_);
		print "&nbsp;&nbsp;&nbsp;<input name=addfromadv${adv_id} checked=checked value=$i type=checkbox>$tname<br>\n";
		$i++;
	}
	close(FROM);
	print "<br>";
	}
}
print<<"end_of_html";
	<input value="Add Subject/From to Advertiser" type="submit" name="submit"></div>
	</form>
<center><a target="_top" href="/cgi-bin/mainmenu.cgi" target=_top><img height="23" src="/images/home_blkline.gif" width="76" border="0"></a>
</center>
<script language="JavaScript">
dispalert();
</script>
</body>

</html>
end_of_html
exit();

sub checkIsp
{
	my ($cid,@ispbox)=@_;
	my $i=0;
	while ($i <= $#ispbox)
	{
		if ($cid == $ispbox[$i])
		{
			return "CHECKED";
		}
		$i++;
	}
	return "";
}

sub checkAdv
{
	my ($aid,@ADV)=@_;
	my $i=0;
	while ($i <= $#ADV)
	{
		if ($aid == $ADV[$i])
		{
			return "selected";
		}
		$i++;
	}
	return "";
}

sub getSubj
{
	my ($sid)=@_;
	my $i=1;
	open(SUBJ,"<$subjfile");
	while (<SUBJ>)
	{
		($tname,$isp_str,$isp_ids)=split('\|',$_);
		if ($i == $sid)
		{
			return($tname,$isp_str,$isp_ids);
		}
		$i++;
	}
	close(SUBJ);
	return("","","");
}
sub getFrom
{
	my ($fid)=@_;
	my $i=1;
	open(FROM,"<$fromfile");
	while (<FROM>)
	{
		($tname,$isp_str,$isp_ids)=split('\|',$_);
		if ($i == $fid)
		{
			return($tname,$isp_str,$isp_ids);
		}
		$i++;
	}
	close(FROM);
	return("","","");
}
