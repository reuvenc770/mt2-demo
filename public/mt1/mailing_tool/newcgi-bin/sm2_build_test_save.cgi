#!/usr/bin/perl

# *****************************************************************************************
# sm2_build_test_save.cgi
#
# this page inserts records into test_campaign 
#
# History
# Jim Sobeck, 07/27/07, Creation
# *****************************************************************************************

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
my $rows;
my $errmsg;
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

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

my $emailfile = $query->param('emailfile');
my $tid = $query->param('tid');
my $ctype= $query->param('type');
my $updall = $query->param('updall');
if ($updall eq "")
{
	$updall = "N";
}
my $camp_type="TEST";
if ($ctype eq "H")
{
	$camp_type="HISTORY";
}
my $email = $query->param('email');
if ($emailfile ne "")
{
	$email="";
	my $upload_dir_unix;
	my $file_in;
	my $file_name;
	my $file_handle;
    my $em;
    my ($BytesRead, $Buffer, $Bytes ) ;
    my (@temp_file, %confirm);
    my $tmp_file;

    $sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
    my $sth1 = $dbhq->prepare($sql) ;
    $sth1->execute();
    ($upload_dir_unix) = $sth1->fetchrow_array();
    $sth1->finish();

    # deal with filename passed to this script

    if ( $emailfile =~ /([^\/\\]+)$/ )
    {
        $file_name = $1;                # set file_name to $1 var - (file-name no path)
        $file_name =~ s/^\.+//;         # say what...
        $file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
        $file_handle = $emailfile;
    }
    else
    {
        my $file_problem = $query->param('emailfile');
        &error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
        exit(0);
    }
    #---- Open file and save File to Unix box ---------------------------
    $file_in = "${upload_dir_unix}emailfile.${user_id}" ;
    open(SAVED,">$file_in") || &util::logerror("Error - could NOT open Output SAVED file: $file_in");
    binmode($file_handle);
    binmode(SAVED);
    undef $BytesRead;
    undef $Buffer;

    while ($Bytes = read($file_handle,$Buffer,1024))
    {
        $BytesRead += $Bytes;
        print SAVED $Buffer;
    }
    close SAVED;
    close($file_handle);

    $confirm{$file_handle} = $BytesRead;
    @temp_file = <CGItemp*>;
    foreach $tmp_file (@temp_file)
    {
        unlink ("$tmp_file");
    }
	system("dos2unix $file_in");
    open(SAVED,"<$file_in") || &util::logerror("Error - could NOT open Input SAVED file: $file_in");

    #----- Loop Reading the File of Email Addrs - do til EOF ------------------
    while (<SAVED>)
    {
        chomp;
        $em=$_;
        $em =~ s/^M//g;
		$em=~tr/A-Z/a-z/;
        if ($em=~ /[^a-z0-9\@\_\.\-]/)
        {
            next;
        }
		$email.=$em.",";
    }
    close(SAVED);
	chop($email);
}
my $copies = $query->param('copies');
my $article_id = $query->param('article_id');
if ($article_id eq "")
{
	$article_id=0;
}
my $client = $query->param('clientid') || 64;
my $injectorID= $query->param('injectorID');
if ($injectorID eq "")
{
	$injectorID=0;
}
my $use_test = $query->param('use_test');
if ($use_test eq "")
{
	$use_test="Y";
}
my $continuous_flag = $query->param('continuous_flag');
if ($continuous_flag eq "")
{
	$continuous_flag="N";
}
my $brandid= $query->param('brandid') || 4243;
my $domain= $query->param('domainid');
my $useRdns= $query->param('useRdns');
if ($useRdns eq "")
{
    $useRdns="N";
}
my $ipgroup_id = $query->param('ipgroup_id');
my $mdomain=$query->param('mdomain');
if ($useRdns eq "Y")
{
	$mdomain="";
}
if ($mdomain ne "")
{
	$domain="MANUAL";
}
my $mip=$query->param('mip');
my $ip;
my @ips;
my $cname= $query->param('cname');
my $adv_id = $query->param('adv_id');
my $cusa = $query->param('cusa');
if ($cusa eq "")
{
	$cusa=0;
}
my $creative;
my @creatives;
my $usaType;
my @csubject;
my @cfrom;
if ($camp_type eq "FREESTYLE")
{
}
else
{
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
			$creatives[$i]=$t1;
			$i++;
		}
		$sth->finish();
		if (($#creatives < 0) or ($#cfrom < 0) or ($#csubject < 0))
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
		@creatives = $query->param('creative');
	}
}
if ($camp_type eq "FREESTYLE")
{
	$ip = $query->param('ipid');
}
else
{
	@ips= $query->param('ipid');
}
if ($cusa == 0)
{
	@csubject= $query->param('csubject');
	@cfrom = $query->param('cfrom');
}
my $wiki = $query->param('wiki');
my $include_open = $query->param('include_open');
if ($include_open eq "")
{
	$include_open="Y";
}
my $encrypt_link= $query->param('encrypt_link');
if ($encrypt_link eq "")
{
	$encrypt_link="Y";
}
my $newMailing= $query->param('newMailing');
if ($newMailing eq "")
{
	$newMailing="N";
}
my $include_mailto = $query->param('include_mailto');
if ($include_mailto eq "")
{
	$include_mailto="Y";
}
my @template_id= $query->param('template_id');
my $header_id = $query->param('header_id');
if ($header_id eq "")
{
	$header_id=0;
}
my $footer_id = $query->param('footer_id');
my $trace_header_id= $query->param('trace_header_id');
my $mail_from= $query->param('mail_from');
my $use_mail_from= $query->param('use_mail_from');
my $proxyGroupID= $query->param('proxyGroupID');
if ($proxyGroupID eq "")
{
	$proxyGroupID=0;
}
if ($use_mail_from eq "")
{
	$use_mail_from="N";
}
my @mfrom;
if ($mail_from ne "")
{
	$mail_from =~ s/[ \n\r\f\t]/\|/g ;
        $mail_from =~ s/\|{2,999}/\|/g ;
        @mfrom= split '\|', $mail_from;
}
my $base64EncodeSubject = $query->param('base64EncodeSubject');
if ($base64EncodeSubject eq "")
{
	$base64EncodeSubject="N";
}
my $base64EncodeFrom = $query->param('base64EncodeFrom');
if ($base64EncodeFrom eq "")
{
	$base64EncodeFrom="N";
}
my $subjectEncoding= $query->param('subjectEncoding');
my $fromEncoding= $query->param('fromEncoding');
my $content_domain= $query->param('content_domain');
my $return_path = $query->param('return_path');
my $mail_from2= $query->param('mail_from2');
my $CutMail= $query->param('CutMail');
if ($CutMail eq "")
{
	$CutMail="N";
}
my $submit= $query->param('submit');
my @mailingHeaderID = $query->param('mailingHeaderID');
my $wikiTemplateID = $query->param('wikiTemplateID');
my $subject=$query->param('subject');
my $fromline=$query->param('fromline');
my $mcreative = $query->param('mcreative');
if ($mcreative ne "")
{
	$camp_type="FREESTYLE";
	$creative=$mcreative;
}
my $split_emails = $query->param('split_emails');
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
if ($submit eq "save as new")
{
	$tid=0;
}
if ($tid > 0)
{
	if (($submit eq "send it") or ($submit eq "send it and keep testing"))
	{
		if ($camp_type eq "HISTORY")
		{
        	$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,now(),'START')";
        	my $rows=$dbhu->do($sql);
		}
		if ($camp_type eq "FREESTYLE")
		{
			$creative=~s/'/''/g;
			$subject=~s/'/''/g;
			$fromline=~s/'/''/g;
			$sql="update test_campaign set campaign_type='$camp_type',email_addr='$email',copies_to_send=$copies,client_id=$client,brand_id=$brandid,mailing_domain='$domain',mailing_ip='$ip',campaign_name='$cname',advertiser_id=0,creative_id=0,subject_id=0,from_id=0,mailing_template=$template_id[0],include_wiki='$wiki',status='START',send_date=curdate(),mailingHeaderID=$mailingHeaderID[0],wikiTemplateID=$wikiTemplateID,freestyle_code='$creative',subject='$subject',fromline='$fromline',header_id=$header_id,footer_id=$footer_id,include_open='$include_open',use_test='$use_test',continuous_flag='$continuous_flag',article_id=$article_id,encrypt_link='$encrypt_link',trace_header_id=$trace_header_id,mail_from='$mfrom[0]',base64EncodeSubject='$base64EncodeSubject',base64EncodeFrom='$base64EncodeFrom',content_domain='$content_domain',return_path='$return_path',split_emails='$split_emails',batchSize=$batchSize,waitTime=$waitTime,newMailing='$newMailing',subjectEncoding='$subjectEncoding',fromEncoding='$fromEncoding',include_mailto='$include_mailto',useRdns='$useRdns',group_id=$ipgroup_id,injectorID=$injectorID,use_mail_from='$use_mail_from',proxyGroupID=$proxyGroupID,mail_from2='$mail_from2',CutMail='$CutMail' where $userDataRestrictionWhereClause test_id=$tid";
		}
		else
		{
			$sql="update test_campaign set campaign_type='$camp_type',email_addr='$email',copies_to_send=$copies,client_id=$client,brand_id=$brandid,mailing_domain='$domain',mailing_ip='$ips[0]',campaign_name='$cname',advertiser_id=$adv_id,creative_id=$creatives[0],subject_id=$csubject[0],from_id=$cfrom[0],mailing_template=$template_id[0],include_wiki='$wiki',status='START',send_date=curdate(),mailingHeaderID=$mailingHeaderID[0],wikiTemplateID=$wikiTemplateID,header_id=$header_id,footer_id=$footer_id,include_open='$include_open',use_test='$use_test',continuous_flag='$continuous_flag',article_id=$article_id,encrypt_link='$encrypt_link',trace_header_id=$trace_header_id,mail_from='$mfrom[0]',base64EncodeSubject='$base64EncodeSubject',base64EncodeFrom='$base64EncodeFrom',content_domain='$content_domain',return_path='$return_path',split_emails='$split_emails',batchSize=$batchSize,waitTime=$waitTime,newMailing='$newMailing',subjectEncoding='$subjectEncoding',fromEncoding='$fromEncoding',include_mailto='$include_mailto',useRdns='$useRdns',group_id=$ipgroup_id,injectorID=$injectorID,use_mail_from='$use_mail_from',proxyGroupID=$proxyGroupID,freestyle_code='',subject='',fromline='',mail_from2='$mail_from2',CutMail='$CutMail' where $userDataRestrictionWhereClause test_id=$tid";
		}
	}
	else
	{
		if ($camp_type eq "FREESTYLE")
		{
			$creative=~s/'/''/g;
			$subject=~s/'/''/g;
			$fromline=~s/'/''/g;
			$sql="update test_campaign set campaign_type='$camp_type',email_addr='$email',copies_to_send=$copies,client_id=$client,brand_id=$brandid,mailing_domain='$domain',mailing_ip='$ip',campaign_name='$cname',advertiser_id=0,creative_id=0,subject_id=0,from_id=0,mailing_template=$template_id[0],include_wiki='$wiki',mailingHeaderID=$mailingHeaderID[0],wikiTemplateID=$wikiTemplateID,freestyle_code='$creative',subject='$subject',fromline='$fromline',header_id=$header_id,footer_id=$footer_id,include_open='$include_open',use_test='$use_test',continuous_flag='$continuous_flag',article_id=$article_id,encrypt_link='$encrypt_link',trace_header_id=$trace_header_id,mail_from='$mfrom[0]',base64EncodeSubject='$base64EncodeSubject',base64EncodeFrom='$base64EncodeFrom',content_domain='$content_domain',return_path='$return_path',split_emails='$split_emails',batchSize=$batchSize,waitTime=$waitTime,newMailing='$newMailing',subjectEncoding='$subjectEncoding',fromEncoding='$fromEncoding',include_mailto='$include_mailto',useRdns='$useRdns',group_id=$ipgroup_id,injectorID=$injectorID,use_mail_from='$use_mail_from',proxyGroupID=$proxyGroupID,mail_from2='$mail_from2',CutMail='$CutMail' where $userDataRestrictionWhereClause test_id=$tid";
		}
		else
		{
			$sql="update test_campaign set campaign_type='$camp_type',email_addr='$email',copies_to_send=$copies,client_id=$client,brand_id=$brandid,mailing_domain='$domain',mailing_ip='$ips[0]',campaign_name='$cname',advertiser_id=$adv_id,creative_id=$creatives[0],subject_id=$csubject[0],from_id=$cfrom[0],mailing_template=$template_id[0],include_wiki='$wiki',mailingHeaderID=$mailingHeaderID[0],wikiTemplateID=$wikiTemplateID,header_id=$header_id,footer_id=$footer_id,include_open='$include_open',use_test='$use_test',continuous_flag='$continuous_flag',article_id=$article_id,encrypt_link='$encrypt_link',trace_header_id=$trace_header_id,mail_from='$mfrom[0]',base64EncodeSubject='$base64EncodeSubject',base64EncodeFrom='$base64EncodeFrom',content_domain='$content_domain',return_path='$return_path',split_emails='$split_emails',batchSize=$batchSize,waitTime=$waitTime,newMailing='$newMailing',subjectEncoding='$subjectEncoding',fromEncoding='$fromEncoding',include_mailto='$include_mailto',useRdns='$useRdns',group_id=$ipgroup_id,injectorID=$injectorID,use_mail_from='$use_mail_from',proxyGroupID=$proxyGroupID,freestyle_code='',subject='',fromline='',mail_from2='$mail_from2',CutMail='$CutMail' where $userDataRestrictionWhereClause test_id=$tid";
		}
	}
}
else
{
	if (($submit eq "send it") or ($submit eq "send it and keep testing"))
	{
		if ($camp_type eq "FREESTYLE")
		{
			$creative=~s/'/''/g;
			$subject=~s/'/''/g;
			$fromline=~s/'/''/g;
			$sql="insert into test_campaign(userID, campaign_type,status,campaign_id,email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID,wikiTemplateID,freestyle_code,subject,fromline,header_id,footer_id,include_open,use_test,continuous_flag,article_id,encrypt_link,trace_header_id,mail_from,content_domain,return_path,split_emails,batchSize,waitTime,newMailing,include_mailto,useRdns,group_id,injectorID,use_mail_from,proxyGroupID,base64EncodeSubject,base64EncodeFrom,subjectEncoding,fromEncoding,mail_from2,CutMail) values($user_id, '$camp_type','START',0,'$email',$copies,$client,$brandid,'$domain','$ip','$cname',0,0,0,0,$template_id[0],'$wiki',curdate(),$mailingHeaderID[0],$wikiTemplateID,'$creative','$subject','$fromline',$header_id,$footer_id,'$include_open','$use_test','$continuous_flag',$article_id,'$encrypt_link',$trace_header_id,'$mfrom[0]','$content_domain','$return_path','$split_emails',$batchSize,$waitTime,'$newMailing','$include_mailto','$useRdns',$ipgroup_id,$injectorID,'$use_mail_from',$proxyGroupID,'$base64EncodeSubject','$base64EncodeFrom','$subjectEncoding','$fromEncoding','$mail_from2','$CutMail')";
		}
		else
		{
			$sql="insert into test_campaign(userID, campaign_type,status,campaign_id,email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,send_date,mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,use_test,continuous_flag,article_id,encrypt_link,trace_header_id,mail_from,content_domain,return_path,split_emails,batchSize,waitTime,newMailing,include_mailto,useRdns,group_id,base64EncodeSubject,base64EncodeFrom,subjectEncoding,fromEncoding,mail_from2,CutMail) values($user_id, '$camp_type','START',0,'$email',$copies,$client,$brandid,'$domain','$ips[0]','$cname',$adv_id,$creatives[0],$csubject[0],$cfrom[0],$template_id[0],'$wiki',curdate(),$mailingHeaderID[0],$wikiTemplateID,$header_id,$footer_id,'$include_open','$use_test','$continuous_flag',$article_id,'$encrypt_link',$trace_header_id,'$mfrom[0]','$content_domain','$return_path','$split_emails',$batchSize,$waitTime,'$newMailing','$include_mailto','$useRdns',$ipgroup_id,'$base64EncodeSubject','$base64EncodeFrom','$subjectEncoding','$fromEncoding','$mail_from2','$CutMail')";
		}
	}
	else
	{
		if ($camp_type eq "FREESTYLE")
		{
			$creative=~s/'/''/g;
			$subject=~s/'/''/g;
			$fromline=~s/'/''/g;
			$sql="insert into test_campaign(userID, campaign_type,status,campaign_id,email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,mailingHeaderID,wikiTemplateID,freestyle_code,subject,fromline,header_id,footer_id,include_open,use_test,continuous_flag,article_id,encrypt_link,trace_header_id,mail_from,content_domain,return_path,split_emails,batchSize,waitTime,newMailing,include_mailto,useRdns,group_id,injectorID,use_mail_from,proxyGroupID,base64EncodeSubject,base64EncodeFrom,subjectEncoding,fromEncoding,mail_from2,CutMail) values('$camp_type','NOT SENT',0,'$email',$copies,$client,$brandid,'$domain','$ip','$cname',0,0,0,0,$template_id[0],'$wiki',$mailingHeaderID[0],$wikiTemplateID,'$creative','$subject','$fromline',$header_id,$footer_id,'$include_open','$use_test','$continuous_flag',$article_id,'$encrypt_link',$trace_header_id,'$mfrom[0]','$content_domain','$return_path','$split_emails',$batchSize,$waitTime,'$newMailing','$include_mailto','$useRdns',$ipgroup_id,$injectorID,'$use_mail_from',$proxyGroupID,'$base64EncodeSubject','$base64EncodeFrom','$subjectEncoding','$fromEncoding','$mail_from2','$CutMail')";
		}
		else
		{
			$sql="insert into test_campaign(userID, campaign_type,status,campaign_id,email_addr,copies_to_send,client_id,brand_id,mailing_domain,mailing_ip,campaign_name,advertiser_id,creative_id,subject_id,from_id,mailing_template,include_wiki,mailingHeaderID,wikiTemplateID,header_id,footer_id,include_open,use_test,continuous_flag,article_id,encrypt_link,trace_header_id,mail_from,content_domain,return_path,split_emails,batchSize,waitTime,newMailing,include_mailto,useRdns,group_id,injectorID,use_mail_from,proxyGroupID,base64EncodeSubject,base64EncodeFrom,subjectEncoding,fromEncoding,mail_from2,CutMail) values($user_id, '$camp_type','NOT SENT',0,'$email',$copies,$client,$brandid,'$domain','$ips[0]','$cname',$adv_id,$creatives[0],$csubject[0],$cfrom[0],$template_id[0],'$wiki',$mailingHeaderID[0],$wikiTemplateID,$header_id,$footer_id,'$include_open','$use_test','$continuous_flag',$article_id,'$encrypt_link',$trace_header_id,'$mfrom[0]','$content_domain','$return_path','$split_emails',$batchSize,$waitTime,'$newMailing','$include_mailto','$useRdns',$ipgroup_id,$injectorID,'$use_mail_from',$proxyGroupID,'$base64EncodeSubject','$base64EncodeFrom','$subjectEncoding','$fromEncoding','$mail_from2','$CutMail')";
		}
	}
}
open(LOG,">>/tmp.sm2.log");
print LOG "$sql\n";
close(LOG);
my $rows=$dbhu->do($sql);
if ($tid == 0)
{
	$sql="select max(test_id) from test_campaign where $userDataRestrictionWhereClause campaign_name='$cname' and campaign_type='$camp_type'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($tid)=$sth->fetchrow_array();
	$sth->finish();
	if (($camp_type eq "HISTORY") and ($submit eq "send it"))
	{
       	$sql="insert into test_schedule(test_id,schedule_date,status) values($tid,now(),'START')";
       	my $rows=$dbhu->do($sql);
	}
}
$sql="delete from test_campaign_templates where test_id=$tid";
my $rows=$dbhu->do($sql);
$sql="delete from test_campaign_mailfrom where test_id=$tid";
$rows=$dbhu->do($sql);
foreach my $m (@mfrom)
{
	$sql="insert into test_campaign_mailfrom(test_id,mail_from) values($tid,'$m')";
	$rows=$dbhu->do($sql);
}
$sql="delete from test_campaign_creatives where test_id=$tid";
$rows=$dbhu->do($sql);
$sql="delete from test_campaign_subjects where test_id=$tid";
$rows=$dbhu->do($sql);
$sql="delete from test_campaign_ip where test_id=$tid";
$rows=$dbhu->do($sql);
$sql="delete from test_campaign_froms where test_id=$tid";
$rows=$dbhu->do($sql);
$sql="delete from test_campaign_headers where test_id=$tid";
$rows=$dbhu->do($sql);
my $i=0;
while ($i <= $#template_id)
{
	$sql="insert into test_campaign_templates(test_id,mailing_template) values($tid,$template_id[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
my $i=0;
while ($i <= $#mailingHeaderID)
{
	$sql="insert into test_campaign_headers(test_id,mailingHeaderID) values($tid,$mailingHeaderID[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#creatives)
{
	$sql="insert into test_campaign_creatives(test_id,creative_id) values($tid,$creatives[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
while ($i <= $#csubject)
{
	$sql="insert into test_campaign_subjects(test_id,subject_id) values($tid,$csubject[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}
$i=0;
$sql="delete from SendAllTestDomain where test_id=$tid";
my $rows=$dbhu->do($sql);
#
if ($mdomain ne '')
{
   	$mdomain=~ s/[ \n\r\f\t]/\|/g ;
   	$mdomain=~ s/\|{2,999}/\|/g ;
   	my @domain= split '\|', $mdomain;
	my $i=0;
	while ($i <= $#domain)
	{
		$sql="insert into SendAllTestDomain(test_id,mailing_domain) values($tid,'$domain[$i]')";
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
		$sql="insert into test_campaign_ip(test_id,mailing_ip) values($tid,'$ip[$i]')";
		my $rows=$dbhu->do($sql);
		$i++;
	}
}
elsif (($mip eq '') and ($ipgroup_id == 0))
{
	$i=0;
	while ($i <= $#ips)
	{
		$sql="insert into test_campaign_ip(test_id,mailing_ip) values($tid,'$ips[$i]')";
		$rows=$dbhu->do($sql);
		$i++;
	}
}
$i=0;
while ($i <= $#cfrom)
{
	$sql="insert into test_campaign_froms(test_id,from_id) values($tid,$cfrom[$i])";
	$rows=$dbhu->do($sql);
	$i++;
}

if ($updall eq "Y")
{
	$sql="update test_campaign set email_addr='$email' where campaign_type='$camp_type'";
    my $rows=$dbhu->do($sql);
}
#
# Display the confirmation page
#
if (($submit eq "save as new") or ($submit eq "send it and keep testing"))
{
	my $pmesg="";
	if ($submit eq "send it and keep testing")
	{
		$pmesg="Campaign $cname has been scheduled to be sent.";
	}
    print "Location: sm2_function.cgi?tid=$tid&submit=edit&pmesg=$pmesg\n\n";
	exit
}
if ($submit eq "preview it")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/sm2_preview.cgi?tid=$tid", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/sm2_edit.cgi?tid=$tid";
</script>
end_of_html
}
elsif ($submit eq "render html")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/sm2_render.cgi?tid=$tid", "Render", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/sm2_edit.cgi?tid=$tid";
</script>
end_of_html
}
else
{
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<center>
end_of_html
if ($submit eq "send it")
{
	print "<h2>Campaign <b>$cname</b> has been scheduled to be sent.</h2>\n";
}
else
{
	print "<h2>Campaign <b>$cname</b> has been added/updated.</h2>\n";
}
print "<br>";
if ($ctype eq "H")
{
	print "<a href=\"/sm2_build_history.html\">Add Another History Campaign</a>&nbsp;&nbsp;&nbsp;<a href=\"sm2_list.cgi?type=H\">Home</a>\n";
}
else
{
	print "<a href=\"/sm2_build_test.html\">Add Another Strongmail Test</a>&nbsp;&nbsp;&nbsp;<a href=\"sm2_list.cgi\">Home</a>\n";
}
print<<"end_of_html";
</center>
</body></html>
end_of_html
}
$util->clean_up();
exit(0);
