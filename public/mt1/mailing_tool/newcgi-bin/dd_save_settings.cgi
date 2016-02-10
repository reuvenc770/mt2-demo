#!/usr/bin/perl

# ******************************************************************************
# dd_save_settings.cgi 
#
# this page updates information in the DailyDealSettingDetail table
#
# History
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
my $sid;
my $i;
my $class;
my $errmsg;
my $images = $util->get_images_url;
my $pmesg="";
my @url_array;
my $cnt;
my $curl;
my $temp_cnt;
my $btype;
my $nl_id;
my $customClientID;

$cnt=0;
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
#
#
my $IPG;
my $dd_id= $query->param('dd_id');
my $ctype = $query->param('ctype');
my $submit= $query->param('submit');
my $group_id = $query->param('group_id');
if ($group_id eq "")
{
	$group_id=0;
}
my $domain=$query->param('domain');
my @content_domain=$query->param('content_domain');
my $pcontent_domain=$query->param('pcontent_domain');
my $seedlist=$query->param('seedlist');
my $template_id=$query->param('template_id');
my $header_id =$query->param('header_id');
my $footer_id =$query->param('footer_id');
my $wiki_id=$query->param('wiki_id');
my $mailingHeaderID=$query->param('header_template');
my $article_id=$query->param('article_id');
my $mail_from=$query->param('mail_from');
my $hotmailDomain=$query->param('hotmailDomain');
if ($ctype ne "Trigger")
{
	$IPG->{1}= $query->param('group_id_6');
	$IPG->{2}= $query->param('group_id_1');
	$IPG->{3}= $query->param('group_id_2');
	$IPG->{4}= $query->param('group_id_3');
	$IPG->{5}= $query->param('group_id_4');
	$IPG->{6}= $query->param('group_id_5');
	$IPG->{7}= $query->param('group_id_0');
}


if ($hotmailDomain eq "")
{
	$hotmailDomain="N";
}
my $useRdns=$query->param('useRdns');
if ($useRdns eq "")
{
	$useRdns="N";
}
my $cap_volume=$query->param('cap_volume');
my $return_path=$query->param('return_path');
if ($cap_volume eq "")
{
	$cap_volume=0;
}
my $ramp_up_freq=$query->param('ramp_up_freq');
if ($ramp_up_freq eq "")
{
	$ramp_up_freq=0;
}
my $ramp_up_email_cnt=$query->param('ramp_up_email_cnt');
if ($ramp_up_email_cnt eq "")
{
	$ramp_up_email_cnt=0;
}
$sql="select customClientID from DailyDealSetting where dd_id=$dd_id";
$sth=$dbhu->prepare($sql);
$sth->execute();
($customClientID)=$sth->fetchrow_array();
$sth->finish();

my @isps= $query->param('isps');
foreach my $isp (@isps)
{
	if ($submit eq "restore defaults")
	{
		$sql="delete from DailyDealSettingDetail where dd_id=$dd_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
		$sql="delete from DailyDealSettingContentDomain where dd_id=$dd_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
		$sql="insert into DailyDealSettingDetail select $dd_id,class_id,group_id,domain,template_id,header_id,footer_id,seedlist,wikiID,mailingHeaderID,article_id,mail_from,hotmailDomain,ramp_up_freq,ramp_up_email_cnt,curdate(),0,cap_volume,dateCntUpdated,return_path,useRdns from DailyDealSettingDetail where dd_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="insert into DailyDealSettingContentDomain select $dd_id,class_id,domain_name from DailyDealSettingContentDomain where dd_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="insert into DailyDealSettingDetailIpGroup select $dd_id,class_id,weekDay,group_id from DailyDealSettingDetailIpGroup where dd_id=1 and class_id=$isp";
    	$rows=$dbhu->do($sql);
	}
	else
	{
    	$sql="update DailyDealSettingDetail set group_id=$group_id,domain='$domain',template_id=$template_id,header_id=$header_id,footer_id=$footer_id,seedlist='$seedlist',wikiID=$wiki_id,mailingHeaderID=$mailingHeaderID,article_id=$article_id,mail_from='$mail_from',hotmailDomain='$hotmailDomain',ramp_up_freq=$ramp_up_freq,ramp_up_email_cnt=$ramp_up_email_cnt,cap_volume=$cap_volume,return_path='$return_path',useRdns='$useRdns' where dd_id=$dd_id and class_id=$isp";
    	my $rows=$dbhu->do($sql);
		$sql="delete from DailyDealSettingContentDomain where dd_id=$dd_id and class_id=$isp";
    	$rows=$dbhu->do($sql);
		$sql="delete from DailyDealSettingDetailIpGroup where dd_id=$dd_id and class_id=$isp";
    	$rows=$dbhu->do($sql);
		foreach (keys %{$IPG})
		{
			my $wday=$_;
			my $gid=$IPG->{$wday};
			$sql="insert into DailyDealSettingDetailIpGroup(dd_id,class_id,weekDay,group_id) values($dd_id,$isp,$wday,$gid)";
    		$rows=$dbhu->do($sql);
		}
		$sql="delete from DailyDealSettingCustom where dd_id=$dd_id and class_id=$isp";
    	$rows=$dbhu->do($sql);
		if ($pcontent_domain ne '')
		{
			$pcontent_domain =~ s/[ \n\r\f\t]/\|/g ;    
			$pcontent_domain =~ s/\|{2,999}/\|/g ;           
			@content_domain= split '\|', $pcontent_domain;
		}
		my $i=0;
		while ($i <= $#content_domain)
		{
			$sql="insert into DailyDealSettingContentDomain(dd_id,class_id,domain_name) values($dd_id,$isp,'$content_domain[$i]')";
    		$rows=$dbhu->do($sql);
			$i++;
		}
		if ($customClientID > 0)
		{
			my @cdata= $query->param('cdata');
			foreach my $keyval (@cdata)
			{
				my ($key,$val)=split('\|',$keyval);
				$sql="insert into DailyDealSettingCustom(dd_id,class_id,clientRecordKeyID,clientRecordValueID) values($dd_id,$isp,$key,$val)";
				$rows=$dbhu->do($sql);
			}
		}

	}
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head></head>
<body>
<center>
<h3> ISP(s) $ctype Deal Settings have been updated</h3>
</center>
</body>
</html>
end_of_html
exit(0);
