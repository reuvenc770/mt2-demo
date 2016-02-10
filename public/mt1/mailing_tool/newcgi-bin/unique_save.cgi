#!/usr/bin/perl

#******************************************************************************
# unique_save.cgi
#
# this page inserts records into unique_campaign 
#
# History
# Jim Sobeck, 05/19/09, Creation
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $usaType;
my $sid;
my $rows;
my $errmsg;
my $tracking_id;
my $vendor_supp_list_id;
my $images = $util->get_images_url;

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

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $uid = $query->param('uid');
my $sid = $query->param('sid');
if ($sid eq "")
{
	$sid=0;
}
my $diffcnt = $query->param('diffcnt');
my $ctype= $query->param('type');
my $camp_type="TEST";
my $email = $query->param('cemail');
my $nl_id = $query->param('nl_id');
my @domain= $query->param('domainid');
my @cdomain= $query->param('cdomainid');
my $pastedomain= $query->param('pastedomainid');
my $cpastedomain= $query->param('cpastedomainid');
my $savepaste=$pastedomain;
my $csavepaste=$cpastedomain;
my $ip= $query->param('ipid');
my $cname= $query->param('cname');
my $adv_id = $query->param('adv_id');
my $randomType='';
$_=$adv_id;
if (/RAND/)
{
	$randomType=$adv_id;
	$adv_id=0;
}
my $cat_id;
my $cusa= $query->param('cusa');
my $deployFileName=$query->param('deployFileName');
my $deployPoolID=$query->param('deployPoolID');
my $deployPoolChunkID=$query->param('deployPoolChunkID');
if ($deployPoolChunkID eq "")
{
	$deployPoolChunkID=0;
}
my $adknowledgeDeploy=$query->param('adknowledgeDeploy');
if ($adknowledgeDeploy eq "")
{
	$adknowledgeDeploy=0;
}
my $injectorID=$query->param('injectorID');
if ($injectorID eq "")
{
	$injectorID=0;
}
my $attributed_client=$query->param('attributed_client');
my @creative;
my @csubject;
my @cfrom;
my $md5_suppression;
my $daycnt;
my $mlupd;
my $countryCode;
if ($cusa eq "")
{
	$cusa=0;
}
# Check to make sure suppression specified
if ($adv_id > 0)
{
	$sql="select vendor_supp_list_id,md5_suppression,datediff(curdate(),md5_last_updated),md5_last_updated,countryCode,category_id from advertiser_info a,Country c where a.countryID=c.countryID and advertiser_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($adv_id);
	($vendor_supp_list_id,$md5_suppression,$daycnt,$mlupd,$countryCode,$cat_id)=$sth->fetchrow_array();
	$sth->finish();
	
	if ($md5_suppression eq "Y")
	{
		if (($daycnt > 10) or ($mlupd eq ""))
		{
			if ($countryCode eq "US")
			{
				print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<center>
<h3>Deploy not saved because Suppression List is older than 10 days for Advertiser<h3>
</center>
</body>
</html>
end_of_html
			exit();
			}
		}
	}
	else
	{
	if ($vendor_supp_list_id <= 1)
	{
		if ($countryCode eq "US")
		{
			print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<center>
<h3>Deploy not saved because no Suppression List specified for Advertiser and country=US<h3>
</center>
</body>
</html>
end_of_html
			exit();
		}
	}
	else
	{
		if ($countryCode eq "US")
		{
			my $lupd;
			my $ccnt;
			$sql="select datediff(curdate(),last_updated),last_updated from vendor_supp_list_info where list_id=?"; 
			$sth=$dbhu->prepare($sql);
			$sth->execute($vendor_supp_list_id);
			($ccnt,$lupd)=$sth->fetchrow_array();
			$sth->finish();
			if (($ccnt > 10) or ($lupd eq ""))
			{
				print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<center>
<h3>Deploy not saved because Suppression List is older than 10 days for Advertiser<h3>
</center>
</body>
</html>
end_of_html
				exit();
			}
		}
	}
	}
}

if ($cusa > 0)
{
	$sql="select usaType from UniqueScheduleAdvertiser where usa_id=?";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	($usaType)=$sth->fetchrow_array();
	$sth->finish();

	my $i;
	my $t1;
	$i=0;
	$sql="select subject_id from UniqueAdvertiserSubject where usa_id=? order by rowID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	while (($t1)=$sth->fetchrow_array())
	{
		$csubject[$i]=$t1;
		$i++;
	}
	$sth->finish();
	$i=0;
	$sql="select from_id from UniqueAdvertiserFrom where usa_id=? order by rowID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	while (($t1)=$sth->fetchrow_array())
	{
		$cfrom[$i]=$t1;
		$i++;
	}
	$sth->finish();
	$i=0;
	$sql="select creative_id from UniqueAdvertiserCreative where usa_id=? order by rowID";
	$sth=$dbhu->prepare($sql);
	$sth->execute($cusa);
	while (($t1)=$sth->fetchrow_array())
	{
		$creative[$i]=$t1;
		$i++;
	}
	$sth->finish();
	if (($#creative < 0) or ($#cfrom < 0) or ($#csubject < 0))
	{
			print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<center>
<h3>Deploy not saved because Creative, Subject, or From Missing for USA<h3>
</center>
</body>
</html>
end_of_html
		exit();
	}
}
else
{
	@creative = $query->param('creative');
	@csubject= $query->param('csubject');
	@cfrom = $query->param('cfrom');
}
if ($adv_id == 0)
{
	$creative[0]=0;
	$csubject[0]=0;
	$cfrom[0]=0;
}
my $wiki = $query->param('wiki');
if ($wiki eq "")
{
	$wiki="N";
}
my $utype = $query->param('utype');
my $randomize= $query->param('randomize');
if ($randomize eq "")
{
	$randomize="N";
}
my $jlogProfileID= $query->param('jlogProfileID');
if ($jlogProfileID eq "")
{
	$jlogProfileID=0;
}
my $use_master = $query->param('use_master');
if ($use_master eq "")
{
	$use_master="N";
}
if ($use_master eq "Y")
{
	$domain[0]="";
}
my $log_camp=$query->param('log_camp');
if ($log_camp eq "")
{
	$log_camp="Off";
}
my $prepull=$query->param('prepull');
if ($prepull eq "")
{
	$prepull="N";
}
my $include_name = $query->param('include_name');
if ($include_name eq "")
{
	$include_name="N";
}
my $ConvertSubject = $query->param('ConvertSubject');
if ($ConvertSubject eq "")
{
	$ConvertSubject="None";
}
my $ConvertFrom = $query->param('ConvertFrom');
if ($ConvertFrom eq "")
{
	$ConvertFrom="None";
}
my $useRdns= $query->param('useRdns');
if ($useRdns eq "")
{
	$useRdns="N";
}
my $newMailing= $query->param('newMailing');
if ($newMailing eq "")
{
	$newMailing="N";
}
my $CutMail = $query->param('CutMail');
if ($CutMail eq "")
{
	$CutMail="N";
}
my $subdomain = $query->param('subdomain');
if ($subdomain eq "")
{
	$subdomain="N";
}
my $profile_id= $query->param('profileid');
my $mta_id= $query->param('mta_id');
my $surl= $query->param('surl');
my $input_url= $query->param('input_url');
my $zip= $query->param('zip');
my $mail_from = $query->param('mail_from');
my $use_mail_from = $query->param('use_mail_from');
if ($use_mail_from eq "")
{
	$use_mail_from="N";
}
my $return_path= $query->param('return_path');
$mail_from=~s/'/''/g;
if ($zip eq "")
{
	$zip="ALL";
}
my $template_id= $query->param('template_id');
my $article_id = $query->param('article_id');
if ($article_id eq "")
{
	$article_id=0;
}
my $header_id= $query->param('header_id');
if ($header_id eq "")
{
	$header_id=0;
}
my $trace_header_id= $query->param('trace_header_id');
my $groupSuppListID= $query->param('groupSuppListID');
if ($groupSuppListID eq "")
{
	$groupSuppListID=0;
}
my $footer_id= $query->param('footer_id');
#my $headerid= $query->param('headerid');
my $submit= $query->param('submit');
my $group_id= $query->param('group_id');
if ($group_id eq "")
{
	$group_id=0;
}
my $client_group_id= $query->param('client_group_id');
if ($client_group_id eq "")
{
	$client_group_id=0;
}
my $dup_client_group_id= $query->param('dup_client_group_id');
if ($dup_client_group_id eq "")
{
	$dup_client_group_id=0;
}
my $sdate=$query->param('sdate');
my $dupes_flag =$query->param('dupes_flag');
if ($dupes_flag eq "")
{
	$dupes_flag="N/A";
}
my $stime=$query->param('stime');
my $smin=$query->param('smin');
my $am_pm=$query->param('am_pm');
my $stoptime=$query->param('stoptime');
my $stopmin=$query->param('stopmin');
my $stop_am_pm=$query->param('stop_am_pm');
my $mailingHeaderID=$query->param('header_template_id');
if ($mailingHeaderID eq "")
{
	$mailingHeaderID=0;
}
if ($stime eq "")
{
	$stime="00";
	$am_pm="AM";
}
if ($smin eq "")
{
	$smin="00";
}
if ($am_pm eq "PM")
{
	$stime = $stime + 12;
    if ($stime >= 24)
    {
    	$stime = 12;
    }
}
elsif (($am_pm eq "AM") && ($stime == 12))
{
	$stime = 0;
}
if (length($smin) == 1)
{
	$smin="0".$smin;
}
$stime = $stime . ":".$smin;
if ($stoptime eq "")
{
	$stoptime="00";
	$stop_am_pm="AM";
}
if ($stopmin eq "")
{
	$stopmin="00";
}
if ($stop_am_pm eq "PM")
{
	$stoptime = $stoptime + 12;
    if ($stoptime >= 24)
    {
    	$stoptime = 12;
    }
}
elsif (($stop_am_pm eq "AM") && ($stoptime == 12))
{
	$stoptime = 0;
}
if (length($stopmin) == 1)
{
	$stopmin="0".$stopmin;
}
$stoptime = $stoptime . ":".$stopmin;
if ($sid > 0)
{
    $sql="select client_group_id,ip_group_id,date_add(curdate(),interval $diffcnt day),schedule_time,end_time,profile_id,template_id,slot_type,log_campaign,randomize_records,mta_id,source_url,zip,mail_from,use_master,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID from UniqueSlot where $userDataRestrictionWhereClause slot_id=?";
    $sth=$dbhu->prepare($sql);
    $sth->execute($sid);
    ($client_group_id,$group_id,$sdate,$stime,$stoptime,$profile_id,$template_id,$utype,$log_camp,$randomize,$mta_id,$surl,$zip,$mail_from,$use_master,$return_path,$prepull,$ConvertSubject,$ConvertFrom,$jlogProfileID)=$sth->fetchrow_array();
    $sth->finish();
}
if (($utype ne "Hotmail") and ($utype ne "Chunking") and ($utype ne "Hotmail Domain"))
{
#$sql="select cgc.client_id from client_category_exclusion,ClientGroupClients cgc where client_category_exclusion.client_id=cgc.client_id and client_category_exclusion.category_id=? and cgc.client_group_id=? union select cgc.client_id from client_advertiser_exclusion,ClientGroupClients cgc where client_advertiser_exclusion.client_id=cgc.client_id and client_advertiser_exclusion.advertiser_id=? and cgc.client_group_id=?";
$sql="select cgc.client_id from client_category_exclusion,ClientGroupClients cgc where client_category_exclusion.client_id=cgc.client_id and client_category_exclusion.category_id=? and cgc.client_group_id=?"; 
$sth=$dbhu->prepare($sql);
$sth->execute($cat_id,$client_group_id);
my $tcid;
my $tstr="";
while (($tcid)=$sth->fetchrow_array())
{
	$tstr=$tstr.$tcid.",";
}
$sth->finish();
chop($tstr);
if ($tstr ne "")
{
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h3>Error: The following clients: $tstr are excluded by Category or Advertiser in the Client Group..<br>The Campaign will not be saved and/or deployed.</h3>
</body>
</html>
end_of_html
exit();
}
}
my $exflag;
if ($adv_id > 0)
{
	$sql="select substr(exclude_days,dayofweek(date_add('$sdate',interval 6 day)),1) from advertiser_info where advertiser_id=?";;
	$sth=$dbhu->prepare($sql);
	$sth->execute($adv_id);
	($exflag)=$sth->fetchrow_array();
	$sth->finish();
}
else
{
	$exflag="N";
}

if ($exflag eq "Y")
{
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h3>Error: Advertiser excludes scheduled on specified day of week.<br>The Campaign will not be saved and/or deployed.</h3>
</body>
</html>
end_of_html
exit();
}

my $cnt;
$sql="select count(*) from ClientGroupClients cgc,user u where cgc.client_group_id=? and cgc.client_id=u.user_id and u.client_type='ESP'"; 
$sth=$dbhu->prepare($sql);
$sth->execute($client_group_id);
($cnt)=$sth->fetchrow_array();
$sth->finish();
if (($cnt > 0) and ($groupSuppListID == 0))
{
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h3>Error: Group Suppression List must be specified if Client Group contains ESP Clients.<br>The Campaign will not be saved and/or deployed.</h3>
</body>
</html>
end_of_html
exit();
}
$sql="select count(*) from UniqueProfile up,ClientGroupClients cgc where up.ProfileForClient!=cgc.client_id and up.profile_id=? and cgc.client_group_id=? and up.ProfileForClient > 0";
$sth=$dbhu->prepare($sql);
$sth->execute($profile_id,$client_group_id);
($cnt)=$sth->fetchrow_array();
$sth->finish();
if ($cnt > 0) 
{
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h3>Error: Selected Profile is specific to one client and Client Group contains other clints..<br>The Campaign will not be saved and/or deployed.</h3>
</body>
</html>
end_of_html
exit();
}
#
# Check to make sure if dup_client_group_id set and dupes_flag set that the client_group only for
# the one client
#
if ($dupes_flag ne "N/A")
{
	my $cnt;
	$sql="select count(*) from ClientGroupClients where client_group_id=? and client_id != 276";
	$sth=$dbhu->prepare($sql);
	$sth->execute($client_group_id);
	($cnt)=$sth->fetchrow_array();
	$sth->finish();
	if ($cnt > 0)
	{
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
<html><head><title>Error</title></head>
<body>
<h3>Error: Duplicates Only and Uniques can only be applied if the client group only contains the SpireMain client<br>The Campaign will not be saved and/or deployed.</h3>
</body>
</html>
end_of_html
exit();
	}
}
if ($pastedomain ne '')
{
	$pastedomain =~ s/[ \n\r\f\t]/\|/g ;    
	$pastedomain =~ s/\|{2,999}/\|/g ;           
	@domain= split '\|', $pastedomain;
}
if ($cpastedomain ne '')
{
	$cpastedomain =~ s/[ \n\r\f\t]/\|/g ;    
	$cpastedomain =~ s/\|{2,999}/\|/g ;           
	@cdomain= split '\|', $cpastedomain;
}
#
# if sid sent then get client_group_id and group_id and time for deal
#
if ($sid > 0)
{
	#
	# Remove all campaigns
	#
	if ($uid > 0)
	{
		$sql="update campaign set deleted_date=now() where id='$uid' and scheduled_date='$sdate'";
		my $rows=$dbhu->do($sql); 
		$sql="delete from current_campaigns where campaign_id in (select campaign_id from campaign where id='$uid' and scheduled_date='$sdate')";
		$rows=$dbhu->do($sql); 
	}
}
if ($surl eq "")
{
	$surl="ALL";
}
if ($input_url ne "")
{
	$surl=$input_url;
}
#
if ($cname eq "")
{
	my $aname;
	if ($adv_id > 0)
	{
		$sql="select advertiser_name from advertiser_info where advertiser_id=$adv_id";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($aname)=$sth->fetchrow_array();
		$sth->finish();
	}
	else
	{
		$aname=$randomType;
	}
	if ($wiki eq "Y")
	{
		$cname = $aname . " with Wiki";
	}
	else
	{
		$cname = $aname;
	}
}
if ($uid > 0)
{
	$sql="delete from UniqueCreative where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueSubject where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueFrom where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueDomain where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueContentDomain where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueContentPasted where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$sql="delete from UniqueAttributedClient where unq_id=$uid";
	$rows=$dbhu->do($sql);
	$domain[0]=~s/,//g;
	if (($submit eq "preview it") or ($submit eq "check with spam assassin")) 
	{
		$sql="update unique_campaign set email_addr='$email',nl_id=$nl_id,mailing_domain='$domain[0]',mailing_ip='$ip',campaign_name='$cname',advertiser_id=$adv_id,creative_id=$creative[0],subject_id=$csubject[0],from_id=$cfrom[0],mailing_template=$template_id,include_wiki='$wiki',mta_id=$mta_id,include_name='$include_name',random_subdomain='$subdomain',profile_id=$profile_id,group_id=$group_id,client_group_id=$client_group_id,send_date='$sdate',send_time='$stime',stop_time='$stoptime',header_id=$header_id,footer_id=$footer_id,trace_header_id=$trace_header_id,dup_client_group_id=$dup_client_group_id,dupes_flag='$dupes_flag',slot_type='$utype',log_campaign='$log_camp',prepull='$prepull',use_master='$use_master',pasted_domains='$savepaste',randomize_records='$randomize',source_url='$surl',article_id=$article_id,zip='$zip',mail_from='$mail_from',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',useRdns='$useRdns',newMailing='$newMailing',return_path='$return_path',groupSuppListID=$groupSuppListID,jlogProfileID=$jlogProfileID,deployFileName='$deployFileName',deployPoolID=$deployPoolID,deployPoolChunkID=$deployPoolChunkID,adknowledgeDeploy=$adknowledgeDeploy,injectorID=$injectorID,use_mail_from='$use_mail_from',mailingHeaderID=$mailingHeaderID,RandomType='$randomType',CutMail='$CutMail' where unq_id=$uid";
	}
	elsif ($submit eq "send a test") 
	{
		$sql="update unique_campaign set email_addr='$email',nl_id=$nl_id,mailing_domain='$domain[0]',mailing_ip='$ip',campaign_name='$cname',advertiser_id=$adv_id,creative_id=$creative[0],subject_id=$csubject[0],from_id=$cfrom[0],mailing_template=$template_id,include_wiki='$wiki',mta_id=$mta_id,include_name='$include_name',random_subdomain='$subdomain',profile_id=$profile_id,status='START',send_date=curdate(),group_id=$group_id,client_group_id=$client_group_id,header_id=$header_id,trace_header_id=$trace_header_id,footer_id=$footer_id,dup_client_group_id=$dup_client_group_id,dupes_flag='$dupes_flag',slot_type='$utype',log_campaign='$log_camp',prepull='$prepull',use_master='$use_master',pasted_domains='$savepaste',randomize_records='$randomize',source_url='$surl',article_id=$article_id,zip='$zip',mail_from='$mail_from',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',useRdns='$useRdns',newMailing='$newMailing',return_path='$return_path',groupSuppListID=$groupSuppListID,jlogProfileID=$jlogProfileID,deployFileName='$deployFileName',deployPoolID=$deployPoolID,deployPoolChunkID=$deployPoolChunkID,adknowledgeDeploy=$adknowledgeDeploy,injectorID=$injectorID,use_mail_from='$use_mail_from',mailingHeaderID=$mailingHeaderID,RandomType='$randomType',CutMail='$CutMail' where unq_id=$uid";
	}
	elsif ($submit eq "save it")
	{
		$sql="update unique_campaign set email_addr='$email',nl_id=$nl_id,mailing_domain='$domain[0]',mailing_ip='$ip',campaign_name='$cname',advertiser_id=$adv_id,creative_id=$creative[0],subject_id=$csubject[0],from_id=$cfrom[0],mailing_template=$template_id,include_wiki='$wiki',mta_id=$mta_id,include_name='$include_name',random_subdomain='$subdomain',profile_id=$profile_id,group_id=$group_id,client_group_id=$client_group_id,send_date='$sdate',send_time='$stime',stop_time='$stoptime',header_id=$header_id,trace_header_id=$trace_header_id,footer_id=$footer_id,dup_client_group_id=$dup_client_group_id,dupes_flag='$dupes_flag',slot_type='$utype',log_campaign='$log_camp',prepull='$prepull',use_master='$use_master',pasted_domains='$savepaste',randomize_records='$randomize',source_url='$surl',zip='$zip',mail_from='$mail_from',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',useRdns='$useRdns',newMailing='$newMailing',return_path='$return_path',groupSuppListID=$groupSuppListID,jlogProfileID=$jlogProfileID,deployFileName='$deployFileName',deployPoolID=$deployPoolID,deployPoolChunkID=$deployPoolChunkID,adknowledgeDeploy=$adknowledgeDeploy,injectorID=$injectorID,use_mail_from='$use_mail_from',mailingHeaderID=$mailingHeaderID,RandomType='$randomType',CutMail='$CutMail' where unq_id=$uid";
	}
	elsif (($submit eq "deploy it") or ($submit eq "Deploy it and continue"))
	{
		$camp_type="DEPLOYED";
		$sql="update unique_campaign set email_addr='$email',nl_id=$nl_id,mailing_domain='$domain[0]',mailing_ip='$ip',campaign_name='$cname',advertiser_id=$adv_id,creative_id=$creative[0],subject_id=$csubject[0],from_id=$cfrom[0],mailing_template=$template_id,include_wiki='$wiki',mta_id=$mta_id,include_name='$include_name',random_subdomain='$subdomain',profile_id=$profile_id,status='START',send_date='$sdate',campaign_type='DEPLOYED',group_id=$group_id,client_group_id=$client_group_id,send_time='$stime',stop_time='$stoptime',header_id=$header_id,trace_header_id=$trace_header_id,footer_id=$footer_id,dup_client_group_id=$dup_client_group_id,dupes_flag='$dupes_flag',slot_type='$utype',log_campaign='$log_camp',prepull='$prepull',use_master='$use_master',pasted_domains='$savepaste',randomize_records='$randomize',source_url='$surl',article_id=$article_id,zip='$zip',mail_from='$mail_from',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',useRdns='$useRdns',newMailing='$newMailing',return_path='$return_path',groupSuppListID=$groupSuppListID,jlogProfileID=$jlogProfileID,deployFileName='$deployFileName',deployPoolID=$deployPoolID,deployPoolChunkID=$deployPoolChunkID,adknowledgeDeploy=$adknowledgeDeploy,injectorID=$injectorID,use_mail_from='$use_mail_from',mailingHeaderID=$mailingHeaderID,RandomType='$randomType',CutMail='$CutMail' where unq_id=$uid";
		add_campaigns($uid,$profile_id,$sdate,$stime,$client_group_id,$log_camp,$group_id);
	}
}
else
{
	if (($submit eq "preview it") or ($submit eq "check with spam assassin")) 
	{
		$sql="insert into unique_campaign(userID, campaign_type,campaign_id,email_addr,nl_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,mta_id,include_name,random_subdomain,profile_id,group_id,client_group_id,send_date,send_time,stop_time,header_id,trace_header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,log_campaign,prepull,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,ConvertSubject,ConvertFrom,useRdns,newMailing,return_path,groupSuppListID,jlogProfileID,deployFileName,deployPoolID,deployPoolChunkID,adknowledgeDeploy,injectorID,use_mail_from,mailingHeaderID,RandomType,CutMail) values($user_id, '$camp_type',0,'$email',$nl_id,'$domain[0]','$ip','$cname',$adv_id,$creative[0],$csubject[0],$cfrom[0],$template_id,'$wiki',$mta_id,'$include_name','$subdomain',$profile_id,$group_id,$client_group_id,'$sdate','$stime','$stoptime',$header_id,$trace_header_id,$footer_id,$dup_client_group_id,'$dupes_flag','$utype','$log_camp','$prepull','$use_master','$savepaste','$randomize','$surl',$article_id,'$zip','$mail_from','$ConvertSubject','$ConvertFrom','$useRdns','$newMailing','$return_path',$groupSuppListID,$jlogProfileID,'$deployFileName',$deployPoolID,$deployPoolChunkID,$adknowledgeDeploy,$injectorID,'$use_mail_from',$mailingHeaderID,'$randomType','$CutMail')";
	}
	elsif ($submit eq "send a test") 
	{
		$sql="insert into unique_campaign(userID, campaign_type,status,campaign_id,email_addr,nl_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mta_id,include_name,random_subdomain,profile_id,group_id,client_group_id,header_id,trace_header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,log_campaign,prepull,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,ConvertSubject,ConvertFrom,useRdns,newMailing,return_path,groupSuppListID,jlogProfileID,deployFileName,deployPoolID,deployPoolChunkID,adknowledgeDeploy,injectorID,use_mail_from,mailingHeaderID,RandomType,CutMail) values($user_id, '$camp_type','START',0,'$email',$nl_id,'$domain[0]','$ip','$cname',$adv_id,$creative[0],$csubject[0],$cfrom[0],$template_id,'$wiki',curdate(),$mta_id,'$include_name','$subdomain',$profile_id,$group_id,$client_group_id,$header_id,$trace_header_id,$footer_id,$dup_client_group_id,'$dupes_flag','$utype','$log_camp','$prepull','$use_master','$savepaste','$randomize','$surl',$article_id,'$zip','$mail_from','$ConvertSubject','$ConvertFrom','$useRdns','$newMailing','$return_path',$groupSuppListID,$jlogProfileID,'$deployFileName',$deployPoolID,$deployPoolChunkID,$adknowledgeDeploy,$injectorID,'$use_mail_from',$mailingHeaderID,'$randomType','$CutMail')";
	}
	elsif ($submit eq "save it")
	{
		$sql="insert into unique_campaign(userID, campaign_type,status,campaign_id,email_addr,nl_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,mta_id,include_name,random_subdomain,profile_id,group_id,client_group_id,send_date,send_time,stop_time,header_id,trace_header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,log_campaign,prepull,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,ConvertSubject,ConvertFrom,useRdns,newMailing,return_path,groupSuppListID,jlogProfileID,deployFileName,deployPoolID,deployPoolChunkID,adknowledgeDeploy,injectorID,use_mail_from,mailingHeaderID,RandomType,CutMail) values($user_id, '$camp_type','NOT SENT',0,'$email',$nl_id,'$domain[0]','$ip','$cname',$adv_id,$creative[0],$csubject[0],$cfrom[0],$template_id,'$wiki',$mta_id,'$include_name','$subdomain',$profile_id,$group_id,$client_group_id,'$sdate','$stime','$stoptime',$header_id,$trace_header_id,$footer_id,$dup_client_group_id,'$dupes_flag','$utype','$log_camp','$prepull','$use_master','$savepaste','$randomize','$surl',$article_id,'$zip','$mail_from','$ConvertSubject','$ConvertFrom','$useRdns','$newMailing','$return_path',$groupSuppListID,$jlogProfileID,'$deployFileName',$deployPoolID,$deployPoolChunkID,$adknowledgeDeploy,$injectorID,'$use_mail_from',$mailingHeaderID,'$randomType','$CutMail')";
	}
	elsif (($submit eq "deploy it") or ($submit eq "Deploy it and continue"))
	{
		$camp_type="DEPLOYED";
		$sql="insert into unique_campaign(userID, campaign_type,status,campaign_id,email_addr,nl_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mta_id,include_name,random_subdomain,profile_id,group_id,client_group_id,send_time,stop_time,header_id,trace_header_id,footer_id,dup_client_group_id,dupes_flag,slot_type,log_campaign,prepull,use_master,pasted_domains,randomize_records,source_url,article_id,zip,mail_from,ConvertSubject,ConvertFrom,useRdns,newMailing,return_path,groupSuppListID,jlogProfileID,deployFileName,deployPoolID,deployPoolChunkID,adknowledgeDeploy,injectorID,use_mail_from,mailingHeaderID,RandomType,CutMail) values($user_id, 'DEPLOYED','NOT SENT',0,'$email',$nl_id,'$domain[0]','$ip','$cname',$adv_id,$creative[0],$csubject[0],$cfrom[0],$template_id,'$wiki','$sdate',$mta_id,'$include_name','$subdomain',$profile_id,$group_id,$client_group_id,'$stime','$stoptime',$header_id,$trace_header_id,$footer_id,$dup_client_group_id,'$dupes_flag','$utype','$log_camp','$prepull','$use_master','$savepaste','$randomize','$surl',$article_id,'$zip','$mail_from','$ConvertSubject','$ConvertFrom','$useRdns','$newMailing','$return_path',$groupSuppListID,$jlogProfileID,'$deployFileName',$deployPoolID,$deployPoolChunkID,$adknowledgeDeploy,$injectorID,'$use_mail_from',$mailingHeaderID,'$randomType','$CutMail')";
	}
}
my $rows=$dbhu->do($sql);
if ($uid == 0)
{
	$sql="select LAST_INSERT_ID()";
#	$sql="select max(unq_id) from unique_campaign where campaign_name='$cname' and campaign_type='$camp_type'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($uid)=$sth->fetchrow_array();
	$sth->finish();
	if ($sid > 0)
	{
		$sql="insert into UniqueSchedule(slot_id,unq_id) values($sid,$uid)";
		my $rows=$dbhu->do($sql);
	}
	if (($submit eq "deploy it") or ($submit eq "Deploy it and continue"))
	{
		add_campaigns($uid,$profile_id,$sdate,$stime,$client_group_id,$log_camp,$group_id);
		$sql="update unique_campaign set status='START',campaign_type='DEPLOYED' where unq_id=$uid";
		$rows=$dbhu->do($sql);
	}
}
if ($attributed_client ne "")
{
	$sql="insert into UniqueAttributedClient(unq_id,client_id) values($uid,$attributed_client)";
	my $rows=$dbhu->do($sql);
}
if ($sid > 0)
{
	$sql="insert ignore into UniqueDomain(unq_id,mailing_domain) select $uid,mailing_domain from UniqueSlotDomain where slot_id=$sid";
	$rows=$dbhu->do($sql);
	$sql="insert ignore into UniqueContentDomain(unq_id,domain_name) select $uid,domain_name from UniqueSlotContentDomain where slot_id=$sid";
	$rows=$dbhu->do($sql);
}
else
{
my $i=0;
while ($i <= $#domain)
{
	$domain[$i]=~s/,//g;
	$sql="insert ignore into UniqueDomain(unq_id,mailing_domain) values($uid,'$domain[$i]')";
	$rows=$dbhu->do($sql);
	$i++;
}
my $i=0;
while ($i <= $#cdomain)
{
	$cdomain[$i]=~s/,//g;
	$sql="insert ignore into UniqueContentDomain(unq_id,domain_name) values($uid,'$cdomain[$i]')";
	$rows=$dbhu->do($sql);
	$i++;
}
if ($csavepaste ne "")
{
	$sql="insert into UniqueContentPasted(unq_id,pasted_domains) values($uid,'$csavepaste')";
	$rows=$dbhu->do($sql);
}
}
my $i=0;
my $rowID;
while ($i <= $#creative)
{
	if ($usaType eq "Combination")
	{
		$rowID=$i+1;
	}
	else
	{
		$rowID=0;
	}
	$sql="insert into UniqueCreative(unq_id,creative_id,rowID) values($uid,$creative[$i],$rowID)";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#csubject)
{
	if ($usaType eq "Combination")
	{
		$rowID=$i+1;
	}
	else
	{
		$rowID=0;
	}
	$sql="insert into UniqueSubject(unq_id,subject_id,rowID) values($uid,$csubject[$i],$rowID)";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#cfrom)
{
	if ($usaType eq "Combination")
	{
		$rowID=$i+1;
	}
	else
	{
		$rowID=0;
	}
	$sql="insert into UniqueFrom(unq_id,from_id,rowID) values($uid,$cfrom[$i],$rowID)";
	$rows=$dbhu->do($sql);
	$i++;
}

if (($sid > 0) and ($submit ne "deploy it") and ($submit ne "Deploy it and continue"))
{
	$sql="update unique_campaign set status='START',campaign_type='DEPLOYED' where unq_id=$uid";
	$rows=$dbhu->do($sql);
	add_campaigns($uid,$profile_id,$sdate,$stime,$client_group_id,$log_camp,$group_id);
}
#
# Display the confirmation page
#
my $url;
if ($sid > 0)
{
	$url="/cgi-bin/unique_schedule.cgi";
}
else
{
	$url="/cgi-bin/unique_main.cgi?uid=$uid";
}
if ($submit eq "preview it")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/unique_preview.cgi?uid=$uid", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="$url";
</script>
end_of_html
}
elsif ($submit eq "check with spam assassin")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/unique_spam.cgi?uid=$uid", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="$url";
</script>
end_of_html
}
else
{
if ($sid > 0)
{
	$url="/cgi-bin/unique_schedule.cgi";
}
elsif ($submit eq "Deploy it and continue")
{
	$url="/cgi-bin/unique_main.cgi?uid=$uid&cflag=Y";
}
else
{
	$url="/cgi-bin/unique_list.cgi";
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<center>
end_of_html
if ($submit eq "send a test")
{
	print "<h2>Campaign <b>$cname</b> has been scheduled to be sent.</h2>\n";
}
elsif ($submit eq "save it")
{
	print "<h2>Campaign <b>$cname</b> has been added/updated.</h2>\n";
}
elsif (($submit eq "deploy it") or ($submit eq "Deploy it and continue"))
{
	print "<h2>Campaign <b>$cname</b> has been deployed.</h2>\n";
}
print "<br>";
if ($submit eq "Deploy it and continue")
{
	print "<a href=\"$url\">Continue</a>\n";
}
else
{
	print "<a href=\"$url\">Home</a>\n";
}
print<<"end_of_html";
</center>
</body></html>
end_of_html
}
$util->clean_up();
exit(0);

sub add_campaigns
{
	my ($tid,$profile_id,$sdate,$stime,$cgroupid,$log_camp,$ipgroup_id)=@_;
	my $sql;
	my $profile_name;
	my $client_id;
	my $brand_id;
	my $camp_id;
	my $third_id;
	my $cdate;
	my $added_camp;
	my $cnt;
	my $priority;

	$sql="select count(*) from IpGroup ip where $userDataRestrictionWhereClause group_id=? and (ip.goodmail_enabled='Y' or ip.group_name like 'discover%' or ip.group_name like 'credithelpadvisor%')";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($ipgroup_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		$priority=1;
	}
	else
	{
		$priority=5;
	}

	$added_camp=0;
	$sql="select curdate()"; 
	my $STHQ=$dbhq->prepare($sql);
	$STHQ->execute();
	($cdate) = $STHQ->fetchrow_array();
	$STHQ->finish();

	$sql="select client_id from ClientGroupClients where client_group_id=?";
	$STHQ=$dbhq->prepare($sql);
	$STHQ->execute($cgroupid);
	while (($client_id) = $STHQ->fetchrow_array())
	{
		$sql="select brand_id,third_party_id from client_brand_info where client_id=? and nl_id=? and status='A' and brand_type='Newsletter'";
		my $STHQ1=$dbhq->prepare($sql);
		$STHQ1->execute($client_id,$nl_id);
		if (($brand_id,$third_id) = $STHQ1->fetchrow_array())
		{	
			my $timestr=$sdate." ".$stime;
			$sql = "insert into campaign(userID, user_id,campaign_name,status,created_datetime,scheduled_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,id) values($user_id, $client_id,'$cname','C',now(),'$timestr',$adv_id,$profile_id,$brand_id,'$sdate','$stime','NEWSLETTER','$tid')";
			$rows=$dbhu->do($sql);
			$sql="select LAST_INSERT_ID()";
#			$sql = "select max(campaign_id) from campaign where campaign_name='$cname' and scheduled_date='$sdate' and id='$tid' and advertiser_id=$adv_id and profile_id=$profile_id and brand_id=$brand_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($camp_id) = $sth->fetchrow_array();
			$sth->finish();
#		   	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
#		   	$rows=$dbhu->do($sql);
			if (($sdate eq $cdate) and ($added_camp == 0))
			{
				$sql="insert into current_campaigns(userID, campaign_id,scheduled_date,scheduled_time,campaign_type) values($user_id, $camp_id,curdate(),'$stime','DEPLOYED')";
				$rows=$dbhu->do($sql);
				$added_camp=1;
				if ($adv_id > 0)
				{
					$sql="select tracking_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'"; 
					my $sth1=$dbhu->prepare($sql);
					$sth1->execute($adv_id,$client_id);
					if (($tracking_id)=$sth1->fetchrow_array())
					{
					}
					else
					{
						$util->genLinks($dbhu,$adv_id,0);
					}
					$sth1->finish();
				}
			}
		}
		$STHQ1->finish();
	}
	$STHQ->finish();
}
