#################################################################
####   thirdparty.pm  - thirdparty package for PMS					 ####
#################################################################

package thirdparty;

use strict;
use vars '$AUTOLOAD';
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use CGI;

my $dir2;
my $sql;
# some routines for this package

sub initialize 
{
	my $self = shift;

}

sub AUTOLOAD
{
	my ($self) = @_;
	$AUTOLOAD =~ /.*::get(_\w+)/;
	exists $self->{$1};
	return $self->{$1}	# return attribute
}

sub new 
{
	my $this = shift;
	my $class = ref($this) || $this;
	my $self = {};
	bless $self, $class;
	$self->initialize();
	return $self;
}

sub deploy_it
{
my ($dbh,$id,$camp_id,$brand_id,$adv_id,$client_id) = @_;
my ($mailer_name,$num_subject,$num_from,$num_creative,$rname,$rloc,$rdate,$remail,$rcid,$remailid,$rcdate,$rip,$unsub_flag,$suppression_path,$creative_path,$ftp_ip,$ftp_username,$ftp_password,$include_images);
my $build_zip;
my ($aname,$catid,$unsub_url,$unsub_img,$vid);
my $pname;
my $supp_name;
my $pexicom_last_upload;
my $brd_last_upload;
my $temp_val;
my $brd;
my $redir_domain;
my $img_domain;
my $subject_str;
my $tracking_str;
my $subdomain_name;
my $bname;
my $network_name;
my $tid0;
my $tid1;
my $am_pm;
my $header_text;
my $footer_text;
my $url_id;
my $sid;
my $footer_subdomain;
my $rest_str;
my $curtime;
my $dir1a;
my $sth;
my $sth1;
my $PEXICOM="Pexicom";
my $BRD="Blue Rock Dove";
my $RAUT="Raut Media";
my $INTELA="Intela";
#
#	Get information about the mailer
#
$sql = " select mailer_name,num_subject,num_from,num_creative,name_replace,loc_replace,date_replace,email_replace,cid_replace,emailid_replace,capture_replace,ip_replace,include_unsubscribe,suppression_path,creative_path,mailer_ftp,ftp_username,ftp_password,include_images,build_zip from third_party_defaults where third_party_id=$id";
$sth = $dbh->prepare($sql);
$sth->execute();
($mailer_name,$num_subject,$num_from,$num_creative,$rname,$rloc,$rdate,$remail,$rcid,$remailid,$rcdate,$rip,$unsub_flag,$suppression_path,$creative_path,$ftp_ip,$ftp_username,$ftp_password,$include_images,$build_zip)=$sth->fetchrow_array();
$sth->finish();
#
$sql="select date_format(scheduled_datetime,'%m-%d-%Y'),date_format(scheduled_datetime,'%h'),date_format(scheduled_datetime,'%p'),substr(profile_name,1,8) from campaign,list_profile where campaign_id=$camp_id and campaign.profile_id=list_profile.profile_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($tid0,$tid1,$am_pm,$pname)=$sth->fetchrow_array();
$sth->finish();
open(TMP,">>/tmp/c.log");
print TMP "<$sql> <$pname>\n";
close(TMP);
$tid1=~s/^0//;
#
$sql = "select advertiser_name,category_id,unsub_link,unsub_image,vendor_supp_list_id from advertiser_info where advertiser_id=$adv_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($aname,$catid,$unsub_url,$unsub_img,$vid)=$sth->fetchrow_array();
$sth->finish();
#
$sql = "select list_name,pexicom_last_upload,brd_last_upload from vendor_supp_list_info where list_id=$vid";
$sth = $dbh->prepare($sql);
$sth->execute();
($supp_name,$pexicom_last_upload,$brd_last_upload)=$sth->fetchrow_array();
$sth->finish();
$supp_name =~ s/ //g;
$supp_name =~ s/\)//g;
$supp_name =~ s/\(//g;
$supp_name =~ s/\&//g;
$supp_name =~ s/\://g;
$supp_name =~ s/\$//g;
$supp_name =~ s/\.//g;
$supp_name =~ s/\!//g;
#
my $link_id;
$sql = "select link_id from advertiser_tracking where advertiser_id=$adv_id and client_id=$client_id and daily_deal='$id'";
$sth = $dbh->prepare($sql);
$sth->execute();
($link_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "select company from user where user_id=$client_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($network_name)=$sth->fetchrow_array();
$sth->finish();
if ($build_zip eq "Y")
{
#
# Create Directory for stuff
#
$dir1a = $mailer_name . $network_name . $pname. $aname . $tid0 . $tid1 . $am_pm;
open(TMP,">>/tmp/c.log");
print TMP "<$dir1a> <$pname> <$aname>\n";
close(TMP);
#
$dir1a =~ s/ //g;
$dir1a =~ s/\(//g;
$dir1a =~ s/\)//g;
$dir1a =~ s/\$//g;
$dir1a =~ s/\//-/g;
$dir1a =~ s/\://g;
$dir1a =~ s/\$//g;
$dir1a =~ s/\.//g;
$dir1a =~ s/\!//g;
$dir1a =~ s/\?/-/g;
$dir1a =~ s/\&/-/g;
$dir2 = "/data3/3rdparty/" . $dir1a;
mkdir $dir2;
#
$sql = "select brand_name,header_text,footer_text from client_brand_info where brand_id=$brand_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($bname,$header_text,$footer_text)=$sth->fetchrow_array();
$sth->finish();
#
	$sql="select url,url_id from brand_url_info where brand_id=$brand_id and url_type='O'"; 
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($redir_domain,$url_id) = $sth1->fetchrow_array();
	$sth1->finish();
	$sql="select url from brand_url_info where brand_id=$brand_id and url_type='OI'"; 
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($img_domain) = $sth1->fetchrow_array();
	$sth1->finish();
    $sql="select brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info where category_id=$catid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=$brand_id";
    $sth1 = $dbh->prepare($sql);
    $sth1->execute();
    ($sid,$subdomain_name) = $sth1->fetchrow_array();
    $sth1->finish();
    $subdomain_name =~ s/{{BRAND}}/$bname/g;
    ($footer_subdomain,$rest_str) = split '\.',$subdomain_name;
$sql="select advertiser_subject,sum(el.open_cnt)/sum(el.sent_cnt) from advertiser_subject,email_log el,campaign c where advertiser_subject.subject_id=el.subject_id and advertiser_subject.status='A' and advertiser_subject.advertiser_id=$adv_id and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$adv_id group by advertiser_subject order by 2 desc limit $num_subject";
$sth = $dbh->prepare($sql);
$sth->execute();
open(OUTFILE,">$dir2/subject.txt");
while (($subject_str,$temp_val)=$sth->fetchrow_array())
{
	$subject_str =~ s/{{NAME}}/$rname/g;
	$subject_str =~ s/{{LOC}}/$rloc/g;
	print OUTFILE "$subject_str\n";
}
close (OUTFILE);
$sth->finish();
open(OUTFILE,">$dir2/suppression.txt");
print OUTFILE "${supp_name}.zip\n";
close (OUTFILE);
$sql="select advertiser_from,sum(el.open_cnt)/sum(el.sent_cnt) from advertiser_from,email_log el, campaign c where advertiser_from.from_id=el.from_id and advertiser_from.advertiser_id=$adv_id and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$adv_id group by advertiser_from order by 2 desc limit $num_from";
$sth = $dbh->prepare($sql);
$sth->execute();
open(OUTFILE,">$dir2/from.txt");
while (($subject_str,$temp_val)=$sth->fetchrow_array())
{
	$subject_str =~ s/{{FOOTER_SUBDOMAIN}}/$footer_subdomain/g;
	print OUTFILE "$subject_str\n";
}
close (OUTFILE);
$sth->finish();
$sql="select el.creative_id,creative.creative_name,((sum(el.click_cnt)/sum(el.sent_cnt))*100000) from email_log el,creative,campaign c where el.creative_id=creative.creative_id and trigger_flag='N' and creative.status='A' and creative.advertiser_id=$adv_id and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$adv_id group by 1,2 order by 3 desc limit $num_creative";
$sth = $dbh->prepare($sql);
$sth->execute();
my ($copen,$cclick,$cindex);
my ($cid,$creative_name);
my $camp_cnt = 1;
while (($cid,$creative_name,$temp_val)=$sth->fetchrow_array())
{
    $sql = "select (sum(el.open_cnt)/sum(el.sent_cnt)*100),(sum(el.click_cnt)/sum(el.open_cnt)*100),((sum(el.click_cnt)/sum(el.sent_cnt))*100000) from email_log el,campaign c where el.creative_id=$cid and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$adv_id";
    $sth1 = $dbh->prepare($sql) ;
    $sth1->execute();
    ($copen,$cclick,$cindex) = $sth1->fetchrow_array();
    $sth1->finish();
#
my     ($aflag,$oflag,$html_code);
	$sql="select approved_flag,original_flag,html_code from creative where creative_id=$cid";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($aflag,$oflag,$html_code) = $sth1->fetchrow_array();
	$sth1->finish();

    if ($copen eq "")
    {
        $copen = "0.00";
    }
    if ($cclick eq "")
    {
        $cclick = "0.00";
    }
    if ($cindex eq "")
    {
        $cindex = "0";
    }
	my $temp_str="";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	#
	# Make changes to html_code
	#
    $html_code =~ s\<BODY\<body\;
    my $pos1 = index($html_code, "<body");
    my $pos2 = index($html_code, ">",$pos1);
    substr($html_code,$pos1,$pos2-$pos1+1) = "<body>";
    $tracking_str = "<IMG SRC=\"http://{{DOMAIN}}/cgi-bin/open_email1.cgi?id={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;f={{FID}}&amp;s={{SID}}&amp;c={{CRID}}&amp;\" border=0 height=1 width=1 alt=\"open\">";
    $html_code =~ s\<body>\<body><center><p STYLE="font-size:10pt; font-fami
ly:arial">{{HEADER_TEXT}}</p></center><p>${tracking_str}\;
    $html_code =~ s\<HEAD\<head\gi;
    $_ = $html_code;
    if (/<head/)
    {
        my $got_head = 1;
    }
    else
    {
        $html_code=~ s/<body>/<head><meta http-equiv="Content-Type" content="text\/html; charset=windows-1252"><title>{{EMAIL_USER_ID}}<\/title><\/head><body>/;
    }
    $html_code =~ s/{{TRACKING}}//g;
    my $content_str = "";
    $html_code =~ s/{{CONTENT_HEADER}}/$content_str/g;
    $content_str = "";
    $_ = $html_code;
    $html_code =~ s/{{CONTENT_HEADER_TEXT}}/$content_str/g;
    if (/{{TIMESTAMP}}/)
    {
        my $timestr = util::date($curtime,5);
        $html_code =~ s/{{TIMESTAMP}}/$timestr/g;
    }
    if (/{{REFID}}/)
    {
        $html_code =~ s/{{REFID}}//g;
    }
    $html_code =~ s/{{CLICK}}//g;
    # substitute end of page (closing body tag) with all the unsubscribe
    # footer stuff that must go on the bottom of every email, adding the
    # closing body tag back on
    $html_code =~ s\</BODY>\</body>\;
    $html_code =~ s\</body>\<p><HR width="90%" SIZE=1><p><center>
<table cellspacing=0 cellpadding=0 width=600 border=0>
<tr><td align=center>
<font face="Verdana,Arial" size="1">
</font>
<br>
{{UNSUBSCRIBE}}
</td></tr></table></center></p>\;

	$html_code =~ s\</HTML>\</html>\;
    $html_code =~ s\</html>\\;

	# now add end body tag to close the html email
	$html_code .= "<p STYLE=\"font-size:10pt; font-family:arial\">{{FOOTER_TEXT}}</p></body></html>";
	$temp_str="";
    if ($adv_id != 0)
    {
    	if ($unsub_img ne "")
        {
        	my $tlink_id;
            $sql = "select link_id from links where refurl='$unsub_url'";
            $sth1 = $dbh->prepare($sql);
            $sth1->execute();
            ($tlink_id) = $sth1->fetchrow_array();
            $sth1->finish();
        	$_=$unsub_img;
        	if ( /\// )
        	{
           		$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$tlink_id\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
			}
			else
			{
           		$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;em={{EMAIL_ADDR}}&amp;id=$tlink_id\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$unsub_img\" border=0 alt=\"Unsub\"></a><br><br>";
			}
         }
    }
	if ($unsub_flag eq "Y")
	{
    	$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?cid=0&amp;eid={{EMAIL_USER_ID}}&amp;id=51&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
	}
	else
	{
		$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str\g;	
	}

#
##	my $footer_str = $brand_id . "_" . $sid . "_" . $url_id;
	my $footer_str = $brand_id;
	$html_code =~ s/{{HEADER_TEXT}}/$header_text/g;
	$html_code =~ s/{{FOOTER_TEXT}}/$footer_text/g;
    $html_code =~ s/{{URL}}/http:\/\/$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&em={{EMAIL_ADDR}}&id=$link_id&f=&s=&c=$cid/g;
    $html_code =~ s/{{UNSUB_LINK}}/http:\/\/{{FOOTER_SUBDOMAIN}}.$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42/g;
	$html_code =~ s/{{FOOTER_SUBDOMAIN}}/$footer_subdomain/g;
	$html_code =~ s/{{FOOTER_STR}}/$footer_str/g;
	$html_code =~ s/{{NAME}}/$rname/g;
	$html_code =~ s/{{EMAIL_ADDR}}/$remail/g;
	$html_code =~ s/{{EMAIL_USER_ID}}/$remailid/g;
	$html_code =~ s/{{LOC}}/$rloc/g;
	$html_code =~ s/{{DATE}}/$rdate/g;
	$html_code =~ s/{{CID}}/$camp_id/g;
	$html_code =~ s/{{CRID}}//g;
	$html_code =~ s/{{CAPTURE_DATE}}/$rcdate/g;
	$html_code =~ s/{{IP}}/$rip/g;
	$html_code =~ s/{{FID}}//g;
	$html_code =~ s/{{NID}}//g;
	$html_code =~ s/{{SID}}//g;
	if ($include_images eq "Y")
	{
		my $tmp_dir = $dir2 . "/images"; 
		mkdir $tmp_dir;
    	my $p = HTML::LinkExtor->new(\&cb);
    	$p->parse($html_code);
	}
	$html_code =~ s/{{IMG_DOMAIN}}/$img_domain/g;
	$html_code =~ s/{{DOMAIN}}/$redir_domain/g;
	$creative_name=~s/ /_/g;
	$creative_name=~tr/a-zA-Z/\^/c;
	$creative_name=~s/\^//g;
	open(OUTFILE,">$dir2/creative_${camp_cnt}_${creative_name}.html");
	print OUTFILE "$html_code";
	close (OUTFILE);
	$camp_cnt++;
}
$sth->finish();
my @args = ("/var/www/html/newcgi-bin/stuff_3rdparty.sh $dir1a");
system(@args) == 0 or die "system @args failed: $?";
}
#
# Check to see if need to copy creative and advertiser suppression file
#
my $mesg;
if ($suppression_path ne "")
{
	if ($mailer_name eq $PEXICOM)
	{
		if ($pexicom_last_upload eq "0000-00-00")
		{
			my @args = ("/var/www/html/newcgi-bin/pexicom_unstuff.sh $supp_name");
#			system(@args) == 0 or die "system @args failed: $?";
			system(@args); 
			my $tfilename=${supp_name} ."UL.txt";
			my $new_name=${supp_name} . ".txt";
			my $mesg=ftp_file($ftp_ip,$ftp_username,$ftp_password,$suppression_path,"/data5/supp/temp1/",$tfilename,"A",$new_name);
			$tfilename="/data5/supp/temp1/" . $tfilename;
			unlink($tfilename);
			$sql="update vendor_supp_list_info set pexicom_last_upload=curdate() where list_id=$vid";
			my $rows=$dbh->do($sql);
		}
	}
	elsif ($mailer_name eq $BRD)
	{
		if ($brd_last_upload eq "0000-00-00")
		{
			my @args = ("/var/www/html/newcgi-bin/pexicom_unstuff.sh $supp_name");
#			system(@args) == 0 or die "system @args failed: $?";
			system(@args); 
			my $tfilename=${supp_name} ."UL.txt";
			my $new_name=${supp_name} . ".txt";
			my $mesg=ftp_file($ftp_ip,$ftp_username,$ftp_password,$suppression_path,"/data5/supp/temp1/",$tfilename,"A",$new_name);
			$tfilename="/data5/supp/temp1/" . $tfilename;
			unlink($tfilename);
			$sql="update vendor_supp_list_info set brd_last_upload=curdate() where list_id=$vid";
			my $rows=$dbh->do($sql);
		}
	}
	else
	{
		my $tfilename=${supp_name} . ".zip";
		my $mesg=ftp_file($ftp_ip,$ftp_username,$ftp_password,$suppression_path,"/data5/supp/",$tfilename,"B","");
	}
	if ($mesg ne "")
	{
		print "<script language=JavaScript>alert('$mesg');</script>\n";
	}
}
if ($creative_path ne "")
{
	my $tfilename=$dir1a . ".zip";
	my $mesg=ftp_file($ftp_ip,$ftp_username,$ftp_password,$creative_path,"/data3/3rdparty/",$tfilename,"B","");
	if ($mesg ne "")
	{
		print "<script language=JavaScript>alert('$mesg');</script>\n";
	}
}
}

sub ftp_file
{
	my ($ftp_ip,$ftp_username,$ftp_password,$remote_path,$mydir,$tfilename,$cmode,$new_name) = @_;
	my $filename;
	my $to_dir;
	my $mesg="";

	$filename=$mydir . $tfilename;
	my $ftp = Net::FTP->new("$ftp_ip", Timeout => 120, Debug => 0, Passive => 0) or $mesg="Cannot connect to $ftp_ip: $@";
	if ($ftp)
	{
	    $ftp->login($ftp_username,$ftp_password) or $mesg="Cannot login $ftp_ip"; 
	    $ftp->cwd($remote_path);
		if ($cmode eq "A")
		{
			$ftp->ascii();
		}
		else
		{
	    	$ftp->binary();
		}
	    $ftp->put($filename) or $mesg="put failed for $filename to $remote_path on $ftp_ip"; 
		if ($new_name ne "")
		{
			$ftp->rename($tfilename,$new_name);
		}
	}
	$ftp->quit;
	return $mesg;
}

sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;
	my $scheme;
	my $auth;
	my $path;
	my $query1;
	my $frag;
	my $suffix;
	my $name;

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background") or (($tag eq "input") and ($url1 eq "src")))
     {
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
          	$url2=~s/{{DOMAIN}}/iaffiliateimages.routename.com/g;  
          	$url2=~s/{{IMG_DOMAIN}}/iaffiliateimages.routename.com/g;  
        }
        #
        # Get directory and filename
        #
        ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
        ($name,$frag,$suffix) = fileparse($path);
		if ($name ne "open_email1\.cgi")
		{
	        my $repl_url = $scheme . "://" . $auth . $frag;
			my $time_str = time();
	        if ($query1 ne "")
	        {
	        	$repl_url = $repl_url . $name . "?" . $query1;
	        }
	        else
	        {
	        	my $temp_str;
	            ($temp_str,$ext) = split('\.',$name);
	        }
	        my $curl = WWW::Curl::easy->new();
	        $curl->setopt(CURLOPT_NOPROGRESS, 1);
#	        $curl->setopt(CURLOPT_MUTE, 0);
	        $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
	        $curl->setopt(CURLOPT_TIMEOUT, 30);
	        open HEAD, ">head.out";
	        $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
	        open BODY, "> $dir2/images/$name";
	        $curl->setopt(CURLOPT_FILE,*BODY);
	        $curl->setopt(CURLOPT_URL, $url2);
	        my $retcode=$curl->perform();
	        if ($retcode == 0)
	        {
	        }
	        else
	        {
	        }
	        close HEAD;
		}
	}
}
 1;
