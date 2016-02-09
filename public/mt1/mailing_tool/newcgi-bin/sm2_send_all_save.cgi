#!/usr/bin/perl

# *****************************************************************************************
# sm2_send_all_save.cgi
#
# this page inserts records into SendAllTest 
#
# History
# Jim Sobeck, 03/06/09, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $id;
my $em;
my $tid;
my $images = $util->get_images_url;
my $freestyle_code;
my $subject;
my $fromline;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $email = $query->param('email');
my $adv_id = $query->param('adv_id');
my $creative = $query->param('creative');
my $csubject= $query->param('csubject');
my $cfrom = $query->param('cfrom');
my $ctype = $query->param('type');
my $content_domain = $query->param('content_domain');
if ($ctype eq "F")
{
	$ctype="SENDALL-FREESTYLE";
	$adv_id=0;
	$creative=0;
	$csubject=0;
	$cfrom=0;
	$freestyle_code= $query->param('creative');
	$subject= $query->param('subject');
	$fromline= $query->param('fromline');
    $freestyle_code=~s/'/''/g;
    $subject=~s/'/''/g;
    $fromline=~s/'/''/g;
}
else
{
	$ctype="SENDALL";
	$freestyle_code='';
	$subject='';
	$fromline='';
}	
my $split_emails = $query->param('split_emails');
my $server_id = $query->param('server_id');
my $copies = $query->param('copies');
my $domain= $query->param('domainid');
my $proxyGroupID= $query->param('proxyGroupID');
if ($proxyGroupID eq "")
{
	$proxyGroupID=0;
}
my $cname= $query->param('cname');
my $mail_from = $query->param('mail_from');
my $convert_subject = $query->param('convert_subject');
if ($convert_subject eq "")
{
	$convert_subject="N";
}
my $convert_from = $query->param('convert_from');
if ($convert_from eq "")
{
	$convert_from="N";
}
my $convert_subjectu= $query->param('convert_subjectu');
if ($convert_subjectu eq "")
{
	$convert_subjectu="N";
}
my $convert_fromu= $query->param('convert_fromu');
if ($convert_fromu eq "")
{
	$convert_fromu="N";
}
my $wiki = $query->param('wiki');
my $include_open = $query->param('include_open');
my $permutation_flag = $query->param('permutation_flag');
if ($include_open eq "")
{
	$include_open="Y";
}
my $useRdns= $query->param('useRdns');
if ($useRdns eq "")
{
	$useRdns="N";
}
my $ipgroup_id = $query->param('ipgroup_id');
my $encrypt_link= $query->param('encrypt_link');
if ($encrypt_link eq "")
{
	$encrypt_link="Y";
}
my $newMailing= $query->param('newMailing');
if ($newMailing eq "")
{
	$newMailing="Y";
}
my $injectorID= $query->param('injectorID');
if ($injectorID eq "")
{
	$injectorID=0;
}
my $template_id= $query->param('template_id');
my $header_id = $query->param('header_id');
my $footer_id = $query->param('footer_id');
my $trace_header_id= $query->param('trace_header_id');
my $submit= $query->param('submit');
my $mailingHeaderID = $query->param('mailingHeaderID');
my $wikiTemplateID = $query->param('wikiTemplateID') || 0;
my $mdomain=$query->param('mdomain');
my $mip=$query->param('mip');
if ($mdomain ne '') 
{
	$domain="PAIR UP ALL";
#	$server_id=1;
}
my $shour=$query->param('shour');
my $sdate=$query->param('sdate');
my $schedule_everyday=$query->param('schedule_everyday');
if ($schedule_everyday eq "")
{
	$schedule_everyday="N";
}
my $am_pm=$query->param('am_pm');
if ($am_pm eq "PM")
{
	if ($shour < 12)
	{
    	$shour = $shour + 12;
	}
}
else
{
	if ($shour == 12)
	{
		$shour="00";
	}
    elsif ($shour < 10)
    {
        $shour = "0" . $shour;
    }
}
my $thour = $shour . ":00:00";
my $seedgroup=$query->param('seedgroup');
if ($seedgroup eq "")
{
	$seedgroup=1;
}
my $batchSize=$query->param('batchSize');
if ($batchSize eq "")
{
	$batchSize=0;
}
my $waitTime=$query->param('waitTime');
if ($waitTime eq "")
{
	$waitTime=0;
}
my $ipexclusionid=$query->param('ipexclusionid');
#
if ($cname eq "")
{
	my $aname;
	$sql="select advertiser_name from advertiser_info where advertiser_id=$adv_id";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($aname)=$sth->fetchrow_array();
	$sth->finish();
	if ($wiki eq "Y")
	{
		$cname = $aname . " with Wiki";
	}
	else
	{
		$cname = $aname;
	}
}
if ($server_id > 0)
{
	$sql="insert into test_campaign(userID, status,email_addr,copies_to_send,mailing_domain,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,server_id,send_time,permutation_flag,mainTestID,encrypt_link,split_emails,trace_header_id,mail_from,isoConvertSubject,isoConvertFrom,batchSize,waitTime,IpExclusionID,campaign_type,schedule_everyday,useRdns,group_id,freestyle_code,subject,fromline,content_domain,utf8ConvertSubject,utf8ConvertFrom,newMailing,injectorID,proxyGroupID) values($user_id, 'START','$email',$copies,'$domain','$cname',$adv_id,$creative,$csubject,$cfrom,$template_id,'$wiki','$sdate',$mailingHeaderID,$wikiTemplateID,$header_id,$footer_id,'$include_open',$server_id,'$thour','$permutation_flag',0,'$encrypt_link','$split_emails',$trace_header_id,'$mail_from','$convert_subject','$convert_from',$batchSize,$waitTime,$ipexclusionid,'$ctype','$schedule_everyday','$useRdns',$ipgroup_id,'$freestyle_code','$subject','$fromline','$content_domain','$convert_subjectu','$convert_fromu','$newMailing',$injectorID,$proxyGroupID)";
	my $rows=$dbhu->do($sql);
	$sql="select last_insert_id()";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	($tid)=$sth1->fetchrow_array();
	$sth1->finish();
	if (($mdomain ne '') or ($mip ne ''))
	{
		if ($mdomain ne '')
		{
    		$mdomain=~ s/[ \n\r\f\t]/\|/g ;
    		$mdomain=~ s/\|{2,999}/\|/g ;
    		my @domain= split '\|', $mdomain;
			my $i=0;
			while ($i <= $#domain)
			{
				$sql="insert into SendAllTestDomain(userID,test_id,mailing_domain) values($user_id, $tid,'$domain[$i]')";
				my $rows=$dbhu->do($sql);
				$i++;
			}
		}
		if (($mip ne '') and ($ipgroup_id == 0))
		{
    		$mip =~ s/[ \n\r\f\t]/\|/g ;
    		$mip =~ s/\|{2,999}/\|/g ;
    		my @ip= split '\|', $mip;
			my $i=0;
			while ($i <= $#ip)
			{
				my $icnt;
				$sql="select count(*) from IpExclusionIps where IpAddr=? and IpExclusionID in (1,$ipexclusionid)";
				my $sthq=$dbhu->prepare($sql);
				$sthq->execute($ip[$i]);
				($icnt)=$sthq->fetchrow_array();
				$sthq->finish();
				if ($icnt == 0)
				{
					$sql="insert into SendAllTestIp(userID, test_id,ip_addr) values($user_id, $tid,'$ip[$i]')";
					my $rows=$dbhu->do($sql);
				}
				$i++;
			}
		}
	}
}
else
{
	my $mainTestID=0;
	my $errors;
	my $results;
	my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
	my $params;
	$params->{active}=1;
	($errors, $results) = $serverInterface->getMtaServers($params);
	for my $server (@$results)
	{
		$id=$server->{'serverID'};
		# if mta02 then skip
		if ($id == 2172) 
		{
			next;
		}
		#mta34 skip
		if ($id == 732) 
		{
			next;
		}
		if ($email eq "")
		{
			$sql="select email_addr from SM2Seeds where server_id=? and SeedGroupID=?";
			my $sth1=$dbhu->prepare($sql);
			$sth1->execute($id,$seedgroup);
			($em)=$sth1->fetchrow_array();
			$sth1->finish();
		}
		else
		{
			$em=$email;
		}
		$sql="insert into test_campaign(userID, status,email_addr,copies_to_send,mailing_domain,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,server_id,send_time,permutation_flag,mainTestID,encrypt_link,split_emails,trace_header_id,mail_from,isoConvertSubject,isoConvertFrom,SeedGroupID,batchSize,waitTime,IpExclusionID,campaign_type,schedule_everyday,useRdns,group_id,freestyle_code,subject,fromline,content_domain,utf8ConvertSubject,utf8ConvertFrom,newMailing,injectorID,proxyGroupID) values($user_id, 'START','$em',$copies,'$domain','$cname',$adv_id,$creative,$csubject,$cfrom,$template_id,'$wiki','$sdate',$mailingHeaderID,$wikiTemplateID,$header_id,$footer_id,'$include_open',$id,'$thour','$permutation_flag',$mainTestID,'$encrypt_link','$split_emails',$trace_header_id,'$mail_from','$convert_subject','$convert_from',$seedgroup,$batchSize,$waitTime,$ipexclusionid,'$ctype','$schedule_everyday','$useRdns',$ipgroup_id,'$freestyle_code','$subject','$fromline','$content_domain','$convert_subjectu','$convert_fromu','$newMailing',$injectorID,$proxyGroupID)";
		my $rows=$dbhu->do($sql);
		$sql="select last_insert_id()";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute();
		($tid)=$sth1->fetchrow_array();
		$sth1->finish();
		if ($mainTestID == 0)
		{
			$mainTestID=$tid;
		}
		if (($mdomain ne '') or ($mip ne ''))
		{
			if ($mdomain ne '')
			{
	    		$mdomain=~ s/[ \n\r\f\t]/\|/g ;
	    		$mdomain=~ s/\|{2,999}/\|/g ;
	    		my @domain= split '\|', $mdomain;
				my $i=0;
				while ($i <= $#domain)
				{
					$sql="insert into SendAllTestDomain(userID, test_id,mailing_domain) values($user_id,$tid,'$domain[$i]')";
					my $rows=$dbhu->do($sql);
					$i++;
				}
			}
			if (($mip ne '') and ($ipgroup_id == 0))
			{
	    		$mip =~ s/[ \n\r\f\t]/\|/g ;
	    		$mip =~ s/\|{2,999}/\|/g ;
	    		my @ip= split '\|', $mip;
				my $i=0;
				while ($i <= $#ip)
				{
					my $icnt;
					$sql="select count(*) from IpExclusionIps where IpAddr=? and IpExclusionID in (1,$ipexclusionid)";
					my $sthq=$dbhu->prepare($sql);
					$sthq->execute($ip[$i]);
					($icnt)=$sthq->fetchrow_array();
					$sthq->finish();
					if ($icnt == 0)
					{
						$sql="insert into SendAllTestIp(userID, test_id,ip_addr) values($user_id, $tid,'$ip[$i]')";
						my $rows=$dbhu->do($sql);
					}
					$i++;
				}
			}
		}
	}
}
print "Content-type: text/html\n\n";
if ($submit ne "save it and continue")
{
print<<"end_of_html";
<html>
<head></head>
<body>
<center>
<h2>Campaign <b>$cname</b> has been scheduled to be sent.</h2>
<br>
<a href="/sm2_send_all.html">Back To Send All</a>
</center>
</body></html>
end_of_html
}
else
{
print<<"end_of_html";
<HTML>
<TITLE>Send Test to All MTAs</TITLE>
<frameset rows="*,0" border=1 width=0 frameborder=no framespacing=0>
  <frame src="/cgi-bin/sm2_build_send_all.cgi?testid=$tid" name="main" marginwidth=0 marginheight=0 scrolling=auto>
  <frame src="/blank.html" name="hidden" marginwidth=0 marginheight=0 scrolling=no resize=no>
</frameset>
</html>
end_of_html
}
$util->clean_up();
exit(0);
