#!/usr/bin/perl
#===============================================================================
# File   : upload_esp_save.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use Net::FTP;
use util;
use Date::Manip;
use File::Type;
use HTML::LinkExtor;
use HTML::FormatText::WithLinks;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use App::Mail::MtaRandomization;
use App::WebAutomation::ImageHoster;
use MIME::Base64;
use URI::Escape;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $ft = File::Type->new();
my $mtaRandom = App::Mail::MtaRandomization->new();
my $dbh;
my $rows;
my $sql;
my $crid;
my $sid;
my $fid;
my $aid;
my $aname;
my $template_id;
my $cnt;
my $E;
my $espName;
my $affiliateID;
my $content_domain;
my $clientID;
my $emailfield;
my $espLabel;
my $espID;
my $eidfield;
my $global_text;
my $offer_type;
my $country;
my $newurl;
my $global_senddate;
my $global_subAffiliateID;
my $esp;
my $global_domain;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $ftpuser="mailops";
my $ftppass="zHLHCWz2JJHXE6j";
my $redir_random_str;
my $chklinkstr="";


# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $cake_domain=$util->getConfigVal("CAKE_REDIR_DOMAIN");
my $xlme_cake_domain=$util->getConfigVal("XLME_CAKE_REDIR_DOMAIN");
my $xlme_affiliate=$util->getConfigVal("XLME_AFFILIATE");
my $esp_cake_domain=$util->getConfigVal("ESP_CAKE_REDIR_DOMAIN");
my $old_esp_cake_domain=$esp_cake_domain;
my $esp_cpm_cake_domain=$util->getConfigVal("ESP_CPM_CAKE_REDIR_DOMAIN");
my $alphad_esp_cake_domain="i.soltrial.com";
my $upload_file = $query->param('upload_file');
my $test= $query->param('test');
if ($test eq "")
{
	$test=0;
}
my ($dbhq,$dbhu)=$util->get_dbh();
$sql="select espID,espName,clientID,espLabel,eidField,emailField from ESP where espStatus='A'";
my $sth=$dbhu->prepare($sql);
$sth->execute();
while (($espID,$espName,$clientID,$espLabel,$eidfield,$emailfield)=$sth->fetchrow_array())
{
	$E->{$espName}{client}=$clientID;
	$E->{$espName}{label}=$espLabel;
	$E->{$espName}{eid}=$eidfield;
	$E->{$espName}{email}=$emailfield;
	$E->{$espName}{ID}=$espID;
}
$sth->finish();

#----- Pass control to PROCESS_FILE  or  PROCESS_LIST  -------
if ( $upload_file ne "" ) 
{
	&process_file($test) ;
}
else
{
	print "Location: upload_esp.cgi\n\n";
}
exit(0) ;


sub process_file 
{
	my ($test)=@_;
	my ($file_name, $file_handle, $file_problem, $file_in, $line, @rest_of_line);
	my ($template,$send_date,$footer,@rest_of_line);
	my $one_image;
	my $linkType;
	my $cname;
	my $subject;
	my $from;
	my $sth;
	my $sth1;
	my $footer_id;
	my $upload_dir_unix;
	my $sdate;
	my $TEMPLATE;
	my $FOOT;
	my $linkstr;
	my $link_id;
	my $temp_str;
	my ($creative_name,$html_code);
	my ($temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text,$cdate);
	my $advertiser_unsub_id;
	my $oldsubAffiliateID;
	my $subAffiliateID;
	my $client_name;
	my $master_str;
	my $template_name;
	my $from_str;
	my $curtime;
	my $outfilename;
	my $sentFile;
	my $test_str;

	if ($test)
	{
		$test_str="Test";
	}
	$sentFile=0;
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html><head><title>ESP $test_str Data Upload Results</title></head>
<body>
<center>
<table>
end_of_html
	# get upload subdir
	$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
	$sth1 = $dbhq->prepare($sql) ;
	$sth1->execute();
	($upload_dir_unix) = $sth1->fetchrow_array();
	$sth1->finish();

	# deal with filename passed to this script
	if ( $upload_file =~ /([^\/\\]+)$/ ) 
	{
		$file_name = $1;                # set file_name to $1 var - (file-name no path)
		$file_name =~ s/^\.+//;         # say what...
		$file_name =~ s/\s/_/g;         # replace WhiteSpace with UnderScore global
		$file_handle = $upload_file ;
	}
	else 
	{
		$file_problem = $query->param('upfile');
		&error("Bad File Name: $file_problem, File name can't have a slash in it!\n Rename it and try again!" ) ;
		exit(0);
	}

	#---- Open file and save File to Unix box ---------------------------

	$file_in = "${upload_dir_unix}espdata.${user_id}" ;
	open(SAVED,">$file_in") || &logerror("Error - could NOT open Output SAVED file: $file_in");
	$file_handle = $upload_file ;
	print SAVED <$file_handle> ;
	close SAVED;

    my ($sec, $min, $hr, $day, $month, $year, $wkdy, $yrdy, $isDST)=localtime();
    $month+=1; $year+=1900;
	if (length($month) == 1)
	{
		$month="0".$month;
	}
	if (length($day) == 1)
	{
		$day="0".$day;
	}
	open(LOG,">>/tmp/upload_esp_$month$day$year.log");
	print LOG "$hr:$sec - $user_id\n";
	if (!$test)
	{
		$outfilename="/tmp/".$year.$month.$day."_".$hr.$min.$sec.".csv";
		open(ESPCSV,">$outfilename");
		print ESPCSV qq^"ESP","subAffiliateID","creative_name","from","subject","content_domain","send_date"\n^;
	}
	my $imageHoster;
	my $data={};
    $data->{'imageCollectionID'}="000000000000001";
    $ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.sav";
    $imageHoster = App::WebAutomation::ImageHoster->new($data);

	open(SAVED,"<$file_in") || &logerror("Error - could NOT open Input SAVED file: $file_in");
	while (<SAVED>) 
	{
		$esp_cake_domain=$old_esp_cake_domain;
		chomp;                       # remove Carriage Return (if exists)
		$line = $_;
		$line =~ s///g ;      # remove ^M from Email Addr (if exists)
		$line =~ s/\t/|/g ;
		$line =~ s/,/|/g ;
		print LOG "$hr:$sec - $user_id - <$line>\n";
		($esp,$aid,$crid,$sid,$fid,$template,$content_domain,$send_date,$footer,$affiliateID,$newurl,$one_image,@rest_of_line) = split('\|', $line) ;
		if ($esp eq "ESP")
		{
			next;
		}
		if ($newurl eq "")
		{
			$newurl="N";
		}
		if ($one_image eq "")
		{
			$one_image="N";
		}
		if ($E->{$esp}{client})
		{
			$clientID=$E->{$esp}{client};
			$espLabel=$E->{$esp}{label};
			$eidfield=$E->{$esp}{eid};
			$emailfield=$E->{$esp}{email};
			$espID=$E->{$esp}{ID};
		}
		else
		{
			print "<tr><td><font color=red>ESP $esp not processed because couldnt find ESP</font></td></tr>\n";
			next;
		}
		my $tdate=UnixDate($send_date,"%Y-%m-%d");
		if ($tdate eq "")
		{
			print "<tr><td><font color=red>ESP $esp Advertiser $aid  not processed because invalid date: $send_date</font></td></tr>\n";
			next;
		}
		else
		{
			$send_date=$tdate;
		}

		my $exflag;
		$sql="select advertiser_name,substr(exclude_days,dayofweek(date_add('$send_date',interval 6 day)),1),linkType,offer_type,countryCode from advertiser_info ai join Country c on c.countryID=ai.countryID where status='A' and advertiser_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid);
		if (($aname,$exflag,$linkType,$offer_type,$country)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>ESP $esp not processed because couldn't find Advertiser ID $aid</font></td></tr>\n";
			next;
		}
		if ($exflag eq "Y")
		{
			print "<tr><td><font color=red>ESP $esp not processed because Advertiser $aname, excluded for Date; $send_date</font></td></tr>\n";
			next;
		}
		if ($offer_type eq "CPM")
		{
			$esp_cake_domain=$esp_cpm_cake_domain;
		}

		$sql="select creative_name from creative where advertiser_id=? and status='A' and creative_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid,$crid);
		if (($cname)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>ESP $esp not processed because couldn't find Creative ID $crid - Either not Active or not for Advertiser $aname</font></td></tr>\n";
			next;
		}
		$sql="select advertiser_subject from advertiser_subject where advertiser_id=? and status='A' and subject_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid,$sid);
		if (($subject)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>ESP $esp not processed because couldn't find Subject ID $sid - Either not Active or not for Advertiser $aname</font></td></tr>\n";
			next;
		}
		$sql="select advertiser_from from advertiser_from where advertiser_id=? and status='A' and from_id=?";
		$sth=$dbhu->prepare($sql);
		$sth->execute($aid,$fid);
		if (($from)=$sth->fetchrow_array())
		{
			$sth->finish();
		}
		else
		{
			$sth->finish();
			print "<tr><td><font color=red>ESP $esp not processed because couldn't find From ID $fid - Either not Active or not for Advertiser $aname</font></td></tr>\n";
			next;
		}

		if ($TEMPLATE->{$template})
		{
			$template_id=$TEMPLATE->{$template};
		}
		else
		{
			$sql="select template_id from brand_template where template_name=?";
			$sth=$dbhu->prepare($sql);
			$sth->execute($template);
			if (($template_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$TEMPLATE->{$template}=$template_id;
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>ESP $esp  not added because couldn't find Template $template</font></td></tr>\n";
				next;
			}
		}
		if ($footer eq "None")
		{
			$footer_id=0;
		}
		elsif ($FOOT->{$footer})
		{
			$footer_id=$FOOT->{$footer};
		}
		else
		{
			$sql="select footer_id from Footers where footer_name=? and status='A'";
			$sth=$dbhu->prepare($sql);
			$sth->execute($footer);
			if (($footer_id)=$sth->fetchrow_array())
			{
				$sth->finish();
				$FOOT->{$footer}=$footer_id;
			}
			else
			{
				$sth->finish();
				print "<tr><td><font color=red>ESP $esp not added because couldn't find Footer $footer</font></td></tr>\n";
				next;
			}
		}
		if ($test)
		{
			next;
		}

		if ($affiliateID eq "")
		{
			$affiliateID=309;
		}
		#########
		#
		#	Get information about the advertiser 
		#
		$sql = " select advertiser_name,vendor_supp_list_id,unsub_link,unsub_image,advertiser_url,unsub_use,unsub_text,curdate() from advertiser_info where advertiser_id=?"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute($aid);
		($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text,$cdate)=$sth->fetchrow_array();
		$sth->finish();
		$sql = " select creative_name,html_code from creative where creative_id=?";
		$sth = $dbhq->prepare($sql);
		$sth->execute($crid);
		($creative_name,$html_code)=$sth->fetchrow_array();
		$sth->finish();

		$sql="start transaction";
		my $rows=$dbhu->prepare($sql);
		$sql="select parmval from sysparm where parmkey='ESP_CAMPAIGNID' for update";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($subAffiliateID)=$sth->fetchrow_array();
		$sth->finish();
		$subAffiliateID++;
		$sql="update sysparm set parmval='$subAffiliateID' where parmkey='ESP_CAMPAIGNID'";
		$rows=$dbhu->do($sql);
		$sql="commit";
		$rows=$dbhu->do($sql);
#
		$sql="insert ignore into EspAdvertiserJoin(subAffiliateID,advertiserID,espID,creativeID,subjectID,fromID,sendDate) values($subAffiliateID,$aid,$espID,$crid,$sid,$fid,'$send_date')";
		$rows=$dbhu->do($sql);
	    $_=$html_code;
	    if ((/redir1.cgi/) and (/&ccID=/))
	    {
	        $html_code =~ s/\&sub=/\&XXX=/g;
	        $html_code =~ s/\&amp;/\&/g;
	        $global_text = $html_code;
	        if (($newurl eq "Y") or ($newurl eq "G"))
	        {
	        	$global_senddate= $send_date;
				$global_subAffiliateID=$subAffiliateID;
	            my $p = HTML::LinkExtor->new(\&cb2);
	            $p->parse($html_code);
	        }
	        elsif (($esp eq "GotClick") or ($esp eq "ALP001") or ($esp eq "ALP002") or ($esp eq "PACK1"))
	        {
	            my $p = HTML::LinkExtor->new(\&cb1);
	            $p->parse($html_code);
	        }
	        $html_code = $global_text;
	    }
		elsif ((/redir1.cgi/) and ($esp eq "PACK1"))
		{
       		$html_code =~ s/\&sub=/\&XXX=/g;
       		$html_code =~ s/\&amp;/\&/g;
       		$global_text = $html_code;
       		my $p = HTML::LinkExtor->new(\&cb3);
       		$p->parse($html_code);
        	$html_code = $global_text;
		}
		if ($unsub_url eq "")
		{
			$advertiser_unsub_id=0;
		}
		else
		{
			$sql = "select link_id from links where refurl='$unsub_url'";
		    $sth=$dbhu->prepare($sql);
		    $sth->execute();
		    if (($advertiser_unsub_id)=$sth->fetchrow_array())
		    {
		    }
		    else
		    {
		    	$sql = "insert into links(refurl,date_added) values('$unsub_url',now())";
		        my $rows=$dbhu->do($sql);
		        $sql = "select link_id from links where refurl='$unsub_url'";
		        $sth=$dbhu->prepare($sql);
		        $sth->execute();
		        ($advertiser_unsub_id)=$sth->fetchrow_array();
			}
			my $iret=util::checkLink($unsub_url);	
			if ($iret)
			{
				$chklinkstr.="$aid,$advertiser_unsub_id,$aname,$esp,$country\n";
			}
		}
		$sql="select cakeSubAffiliateID from user where user_id=?";
		$sth = $dbhu->prepare($sql);
		$sth->execute($clientID);
		($oldsubAffiliateID)=$sth->fetchrow_array();
		$sth->finish();

		$sql = "select url from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N' and link_num=1"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute($aid,$clientID);
		($linkstr)=$sth->fetchrow_array();
		$sth->finish();
		$linkstr=~s/{{CID}}/$esp/;
		$linkstr=~s/{{FOOTER}}/{{FOOTER}}_${send_date}/;
		$linkstr=~s/s1=$oldsubAffiliateID/s1=$subAffiliateID/;
		if ($linkType eq "XLME")
		{
			if ($espLabel eq "AlphaS")
			{
				$linkstr=~s/a=$xlme_affiliate/a=15445/;
				$linkstr=~s/a=3219/a=15445/;
			}
			elsif ($espLabel eq "AlphaD")
			{
				$linkstr=~s/a=$xlme_affiliate/a=765/;
				$linkstr=~s/a=3219/a=765/;
			}
			elsif ($espLabel eq "GotClick")
			{
				$linkstr=~s/a=$xlme_affiliate/a=15448/;
				$linkstr=~s/a=3219/a=15448/;
			}
			else
			{
				$linkstr=~s/a=$xlme_affiliate/a=15480/;
				$linkstr=~s/a=3219/a=15480/;
			}
		}
		else
		{
			$linkstr=~s/a=13/a=$affiliateID/;
		}
		if ($espLabel eq "AlphaD")
		{
			$linkstr=~s/$cake_domain/$alphad_esp_cake_domain/;
		}
		else
		{
			$linkstr=~s/$cake_domain/$esp_cake_domain/;
		}
		#$linkstr=~s/$xlme_cake_domain/$esp_cake_domain/;
		#
		# Check for link
		#
		$link_id="";
		while (($link_id eq "") and ($linkstr ne ""))
		{
			$sql="select link_id from links where refurl=?";
			$sth=$dbhq->prepare($sql);
			$sth->execute($linkstr);
			if (($link_id)=$sth->fetchrow_array())
			{
			}
			else
			{
				$sql="insert ignore into links(refurl,date_added) values('$linkstr',now())";
				my $rows=$dbhu->do($sql);
			}
		}
		if ($link_id eq "")
		{
			$link_id=0;
		}
		my $iret=util::checkLink($linkstr);	
		if ($iret)
		{
			$chklinkstr.="$aid,$link_id,$aname,$esp,$country\n";
		}

		my $xlink3;
		$xlink3="http://$content_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$link_id&n=$clientID&f=$fid&s=$sid&c=$crid&tid=$template_id&footerid=$footer_id&ctype=R";
		if (($esp eq "ALP001") or ($esp eq "GotClick") or ($esp eq "ALP002"))
		{
    		my $end=index($linkstr,"&s2=");
    		$xlink3=substr($linkstr,0,$end);
		}
		elsif ($esp eq "PACK1")
		{
    		my $end=index($linkstr,"&s2=");
    		$xlink3=substr($linkstr,0,$end);
		}
		else
		{
    		if ($newurl eq "Y")
    		{
        		$redir_random_str=util::get_random();
        		$xlink3="http://$content_domain/z/$redir_random_str/$eidfield|1|$link_id|R";
			}
    		elsif ($newurl eq "G")
    		{
				$xlink3=util::get_gmail_url("REDIRECT",$content_domain,$eidfield,$link_id);
			}
    	}
		if ($espLabel eq "ZetaMail")
		{
			$xlink3.="&zetablastid=%%BLASTID%%";
		}
		my $xlink1;
    	if ($newurl eq "Y")
    	{
        	$redir_random_str=util::get_random();
        	$xlink1="http://$content_domain/z/$redir_random_str/$eidfield|1|$advertiser_unsub_id|A";
    	}
    	elsif ($newurl eq "G")
    	{
			$xlink1=util::get_gmail_url("ADVUNSUB",$content_domain,$eidfield,$advertiser_unsub_id);
    	}
    	else
    	{
			$xlink1="http://$content_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$advertiser_unsub_id&n=$clientID&f=$fid&s=$sid&c=$crid&tid=$template_id&footerid=$footer_id&ctype=A";
		}
		if ($espLabel eq "ZetaMail")
		{
			$xlink1.="&zetablastid=%%BLASTID%%";
		}
		
		
		my $random_string=util::get_random();
		my $random_string1=util::get_random();
		my $img_prefix=$content_domain."/".$random_string."/".$random_string1;
		
		$sql="select username from user where user_id=?";
		$sth = $dbhq->prepare($sql);
		$sth->execute($clientID);
		($client_name)=$sth->fetchrow_array();
		$sth->finish();
		
		$sql="select html_code,template_name from brand_template where template_id=?";
		$sth = $dbhq->prepare($sql);
		$sth->execute($template_id);
		($master_str,$template_name)=$sth->fetchrow_array();
		$sth->finish();
		
		#
		my $subject_str;
		$sql="select advertiser_subject from advertiser_subject where subject_id=$sid"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($subject_str)=$sth->fetchrow_array();
		$sth->finish();
		$sql="select advertiser_from from advertiser_from where from_id=$fid"; 
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($from_str)=$sth->fetchrow_array();
		$sth->finish();
		my $reccnt;
		#
		# Make changes to html_code
		#
		my $regExpCreativeHtml  = qr{</*(html|body)>}i;
		$html_code=~ s/$regExpCreativeHtml//g;
		
		my $t_html=$master_str;
		$t_html=~s/{{CREATIVE}}/$html_code/g;
		$html_code=$t_html;
		$html_code =~ s/{{TRACKING}}/<IMG SRC="http:\/\/$content_domain\/cgi-bin\/open.cgi?eid=$eidfield&cid=1&em=$emailfield&n=$clientID&f=$fid&s=$sid&c=$crid&did=&binding=&tid=$template_id&openflag=1&nod=1&espID=$espID&subaff=$subAffiliateID" border=0 height=1 width=1>/g;
		$html_code =~ s/{{CONTENT_HEADER}}//g;
		$html_code =~ s/{{CONTENT_HEADER_TEXT}}//g;
		$_ = $html_code;
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
		$html_code =~ s\</HTML>\</html>\;
		
		# now add end body tag to close the html email
		$temp_str="";
		if ($aid != 0)
		{
			if ($unsub_use eq "TEXT")
			{
				$temp_str=$unsub_text;
			}
			else
			{
		    	if ($unsub_img ne "")
		       	{
		        	my $regExpImage= qr{\.(jpg|jpeg|gif|bmp|png)}i;
		            $unsub_img  =~ s/$regExpImage//g;
		
		            if ( $unsub_img =~ /\// )
		            {
		            	if ($advertiser_unsub_id == 0)
		                {
		                	$temp_str = "<img src=\"http://$img_prefix/$unsub_img\" border=0><br><br>";
		                }
		                else
		                {
		                	$temp_str = "<a href=\"{{ADV_UNSUB_URL}}\"><img src=\"http://$img_prefix/$unsub_img\" border=0></a><br><br>";
		                }
					}
		            else
		            {
		            	if ($advertiser_unsub_id == 0)
		                {
		                	$temp_str = "<img src=\"http://$img_prefix/images/unsub/$unsub_img\" border=0><br><br>";
		                }
		                else
		                {
		                	$temp_str = "<a href=\"{{ADV_UNSUB_URL}}\"><img src=\"http://$img_prefix/images/unsub/$unsub_img\" border=0></a><br><br>";
		                }
					}
				}
		    }
		}
		$html_code=~ s/{{ADV_UNSUB}}/$temp_str/g;
		my $footer_str="";
		my $footer_name="";
		if ($footer_id > 0)
		{
			$sql="select footer_code,footer_name from Footer where footer_id=$footer_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute($aid,$clientID);
			($footer_str,$footer_name)=$sth->fetchrow_array();
			$sth->finish();
		}
		#
		my $i=1;
		my $link_num;
		while ($i <= 29)
		{
			$_=$html_code;
			if (/{{URL$i}}/)
			{
				my $tlink_id;
				my $xlink;
				$link_num=$i+1;
				$sql = "select url from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N' and link_num=$link_num"; 
				$sth = $dbhq->prepare($sql);
				$sth->execute($aid,$clientID);
				($linkstr)=$sth->fetchrow_array();
				$sth->finish();

				if ($esp eq "ALP001")
				{
					my $end=index($linkstr,"&s2=");
					$xlink=substr($linkstr,0,$end);
					if ($linkType eq "XLME")
					{
						$xlink=~s/a=$xlme_affiliate/a=15445/;
						$xlink=~s/a=3219/a=15445/;
					}
					else
					{
						$xlink=~s/a=13/a=$affiliateID/;
					}
				}
				elsif ($esp eq "ALP002")
				{
					my $end=index($linkstr,"&s2=");
					$xlink=substr($linkstr,0,$end);
					if ($linkType eq "XLME")
					{
						$xlink=~s/a=$xlme_affiliate/a=$affiliateID/;
						$xlink=~s/a=3219/a=$affiliateID/;
					}
					else
					{
						$xlink=~s/a=13/a=$affiliateID/;
					}
				}
				elsif ($esp eq "GotClick")
				{
					my $end=index($linkstr,"&s2=");
					$xlink=substr($linkstr,0,$end);
					if ($linkType eq "XLME")
					{
						$xlink=~s/a=$xlme_affiliate/a=15548/;
						$xlink=~s/a=3219/a=15548/;
					}
					else
					{
						$xlink=~s/a=13/a=$affiliateID/;
					}
					$xlink=~s/$cake_domain/$esp_cake_domain/;
				}
				elsif ($esp eq "PACK1")
				{
					my $end=index($linkstr,"&s2=");
					$xlink=substr($linkstr,0,$end);
					if ($linkType eq "XLME")
					{
						$xlink=~s/a=$xlme_affiliate/a=$affiliateID/;
						$xlink=~s/a=3219/a=$affiliateID/;
					}
					else
					{
						$xlink=~s/a=13/a=$affiliateID/;
					}
					$xlink=~s/$cake_domain/$esp_cake_domain/;
				}
				else
				{
					$linkstr=~s/{{CID}}/$esp/;
					$linkstr=~s/{{FOOTER}}/{{FOOTER}}_${send_date}/;
					$linkstr=~s/s1=$oldsubAffiliateID/s1=$subAffiliateID/;
					$linkstr=~s/$cake_domain/$esp_cake_domain/;
					#$linkstr=~s/$xlme_cake_domain/$esp_cake_domain/;
					if ($linkType eq "XLME")
					{
						$linkstr=~s/a=$xlme_affiliate/a=15480/;
						$linkstr=~s/a=3219/a=15480/;
					}
					else
					{
						$linkstr=~s/a=13/a=$affiliateID/;
					}
					$tlink_id="";
					while (($tlink_id eq "") and ($linkstr ne ""))
					{
						$sql="select link_id from links where refurl=?";
						$sth=$dbhq->prepare($sql);
						$sth->execute($linkstr);
						if (($tlink_id)=$sth->fetchrow_array())
						{
						}
						else
						{
							$sql="insert ignore into links(refurl,date_added) values('$linkstr',now())";
							my $rows=$dbhu->do($sql);
						}
					}
					my $iret=util::checkLink($linkstr);	
					if ($iret)
					{
						$chklinkstr.="$aid,$tlink_id,$aname,$esp,$country\n";
					}
					if ($tlink_id eq "")
					{
						$tlink_id=0;
					}
					if ($newurl eq "Y")
					{
						$redir_random_str=util::get_random();
						$xlink="http://$content_domain/z/$redir_random_str/$eidfield|1|$tlink_id|R";
					}
					elsif ($newurl eq "G")
					{
						$xlink=util::get_gmail_url("REDIRECT",$content_domain,$eidfield,$tlink_id);
					}
					else
					{
						$xlink="http://$content_domain/cgi-bin/redir1.cgi?eid=$eidfield&cid=1&em=$emailfield&id=$tlink_id&n=$clientID&f=$fid&s=$sid&c=$crid&tid=$template_id&footerid=$footer_id&ctype=R";
					}
				}
				if ($espLabel eq "AlphaD")
				{
					$linkstr=~s/$cake_domain/$alphad_esp_cake_domain/;
				}
				else
				{
					$linkstr=~s/$cake_domain/$esp_cake_domain/;
				}
				if ($espLabel eq "ZetaMail")
				{
					$xlink.="&zetablastid=%%BLASTID%%";
				}
				$html_code =~ s@{{URL$i}}@$xlink@g;
			}
			$i++;
		}
#		if ($espLabel eq "Campaigner")
#		{
#			$global_domain=$content_domain;
#		    $global_text = $html_code;
#		    my $p = HTML::LinkExtor->new(\&cb1);
#		    $p->parse($html_code);
#		    $html_code= $global_text;
#		}
		$html_code =~ s/{{HEADER_TEXT}}//g;
		$html_code =~ s/{{FOOTER_TEXT}}//g;
		$html_code =~ s/{{URL}}/$xlink3/g;
		if ($esp eq "PACK1")
		{
    		$html_code =~ s/{{ADV_UNSUB_URL}}/\$\$adv_unsub_link\$\$/g;
		}
		else
		{
    		$html_code =~ s/{{ADV_UNSUB_URL}}/$xlink1/g;
		}
		$html_code =~ s/{{FOOTER_STR}}//g;
		$html_code =~ s/{{CID}}/1/g;
		$html_code =~ s/{{CRID}}/$crid/g;
		$html_code =~ s/{{FID}}//g;
		$html_code =~ s/{{NID}}/$clientID/g;
		$html_code =~ s/{{MID}}//g;
		$html_code =~ s/{{LINK_ID}}/$link_id/g;
		$html_code =~ s/{{F}}/$fid/g;
		$html_code =~ s/{{S}}/$sid/g;
		$html_code =~ s/{{CWPROGID}}//g;
		$html_code =~ s/{{HEADER}}//g;
		$html_code =~ s/footerid={{FOOTER}}/footerid=$footer_id/g;
		$html_code =~ s/{{FOOTER}}/$footer_str/g;
		$html_code =~ s/{{BINDING}}//g;
		$html_code =~ s/{{TID}}/$template_id/g;
		$html_code =~ s/{{EMAIL_ADDR}}/$emailfield/g;
		$html_code =~ s/{{EMAIL_USER_ID}}/$eidfield/g;
		if ($esp ne "PACK1")
		{
			$creative_name=~s/ /-/g;
			$creative_name=~tr/a-zA-Z/\^/c;
			$creative_name=~s/\^//g;
		}
		
		my $file;
		my $tmp_dir = "/tmp/$crid"; 
		mkdir $tmp_dir;
		$tmp_dir = "/tmp/$crid/images"; 
		mkdir $tmp_dir;
		if ($one_image eq "Y")
		{
			my $thtml=$html_code;
		    $thtml=~s/{{DOMAIN}}/staging.affiliateimages.com/g;  
		    $thtml=~s/{{IMG_DOMAIN}}/staging.affiliateimages.com/g;  
		    $thtml=~s/^M//g;
		    $thtml=~s/\\n//g;
		    $thtml=~s/width=[\"\']*[0-9]+\%*[\"\']*//ig;
		    $thtml=~s/"/\\"/g;
		    my $cmd=`echo "$thtml" | /usr/bin/html2ps -o /tmp/$crid.ps 2>/dev/null;/usr/bin/convert -adjoin -size 600x600 /tmp/$crid.ps /tmp/$crid/images/t_$crid.jpg`;
		    my $params={};
		    my $tfile=$tmp_dir."/t_$crid.jpg";
		    if (-e $tfile)
		    {
		    }
		    else
		    {
		        my $tfile1=$tmp_dir."/t_".$crid."-0.jpg";
		        rename($tfile1,$tfile);
		    }
		    my $tfile1=$tmp_dir."/t_".$crid."-1.jpg";
			unlink($tfile1);
		    $tfile1=$tmp_dir."/t_".$crid."-2.jpg";
			unlink($tfile1);
		}
		else
		{
			$global_text=$html_code;
			my $p = HTML::LinkExtor->new(\&cb);
			$p->parse($html_code);
			$html_code=$global_text;
			$html_code =~ s/{{IMG_DOMAIN}}/$content_domain/g;
			$html_code =~ s/{{DOMAIN}}/$content_domain/g;
		}
		my $htmlfile;
		my $txtfile;
		if ($esp eq "PACK1")
		{
			$htmlfile="/tmp/$crid/".$send_date."++".$aid."++".$aname."++".$crid."++".$creative_name."++".$cdate.".html";
			$txtfile="/tmp/$crid/".$send_date."++".$aid."++".$aname."++".$crid."++".$creative_name."++".$cdate.".txt";
		}
		else
		{
			$htmlfile="/tmp/$crid/".$send_date."_".$aname."_".$creative_name."_".$crid."_".$link_id.".html";
			$txtfile="/tmp/$crid/".$send_date."_".$aname."_".$creative_name."_".$crid."_".$link_id.".txt";
		}
		open (OUT, "> $htmlfile");
		print OUT "$html_code";
		close(OUT);
		my $f = HTML::FormatText::WithLinks->new(
		        before_link => '',
		        after_link => ' [%l]',
		        unique_links => 1,
		        footnone => '',
		);
		my $string=$f->parse($html_code);
		$string=~s/\[IMAGE\]//g;
		open(OUT,"> $txtfile");
		print OUT "$string\n";
		close(OUT);
		open (OUT, "> /tmp/$crid/asset.txt");
		print OUT "FROM: $from_str\n";
		print OUT "SUBJECT: $subject_str\n";
		print OUT "TEMPLATE: $template_name ($template_id)\n";
		print OUT "CLIENT: $client_name\n";
		print OUT "FOOTER: $footer_name\n";
		close(OUT);
		if ($esp eq "PACK1")
		{
			my @args = ("/var/www/html/newcgi-bin/stuff_esp2.sh $crid $aid $send_date $cdate");
			system(@args) == 0 or die "system @args failed: $?";
			$file=$send_date."++".$aid."++".$crid."++".$cdate.".zip";
		}
		else
		{
			$aname=~s/ /-/g;
			my @args = ("/var/www/html/newcgi-bin/stuff_esp1.sh $crid $subAffiliateID $esp $aid $send_date");
			system(@args) == 0 or die "system @args failed: $?";
			$file=$subAffiliateID."_".$esp."_".$aid."_".$send_date.".zip";
		}
		sendESP($file,$esp);
		print "<tr><td>File: <b>$file</b> uploaded for ESP $esp</td></tr>\n";
		$sentFile=1;
		print ESPCSV qq^"$esp","$subAffiliateID","$creative_name","$from_str","$subject_str","$content_domain","$send_date"\n^;
		#########
	} 
	close SAVED;
	close LOG;
	if (!$test)
	{
		close ESPCSV;
		if ($sentFile)
		{
			sendESPCSV($outfilename);
		}
		unlink($outfilename);
		if ($chklinkstr ne "")
		{
        	open (MAIL,"| /usr/sbin/sendmail -t");
	        my $from_addr = "QA HHTP Redirect Alert <info\@zetainteractive.com>";
	        print MAIL "From: $from_addr\n";
	        print MAIL "To: dpezas\@zetainteractive.com,jhecht\@zetainteractive.com\n";
	        print MAIL "Subject: QA HHTP Redirect Alert\n";
	        my $date_str = $util->date(6,6);
	        print MAIL "Date: $date_str\n";
	        print MAIL "X-Priority: 1\n";
	        print MAIL "X-MSMail-Priority: High\n";
	        print MAIL "$chklinkstr\n";
	        close MAIL;
		}
	}
	unlink($file_in) || &logerror("Error - could NOT Remove file: $file_in");  # del file_in
	print<<"end_of_html";
</table>
<br><a href="upload_esp.cgi?test=$test">Back to Upload ESP</a>&nbsp;&nbsp;<a href="mainmenu.cgi" target="_top"><img src="/mail-images/home_blkline.gif" border=0>
</center>
</body>
</html>
end_of_html
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
	my $turl;

     if (($tag eq "img") or ($tag eq "background") or (($tag eq "img") and ($url1 eq "background")) or (($tag eq "input") and ($url1 eq "src")))
     {
		$turl=$url2;
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
          	$url2=~s/{{DOMAIN}}/staging.affiliateimages.com/g;  
          	$url2=~s/{{IMG_DOMAIN}}/staging.affiliateimages.com/g;  
        }
        #
        # Get directory and filename
        #
        ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
        ($name,$frag,$suffix) = fileparse($path);
		if ($name ne "open\.cgi")
		{
	        my $repl_url = $scheme . "://" . $auth . $frag;
			my $time_str = time();
	        if ($query1 ne "")
	        {
	        	$repl_url = $repl_url . $name . "?" . $query1;
	        }
	       	my $temp_str;
	        ($temp_str,$ext) = split('\.',$name);
			if ($ext eq "")
			{
				$name=$name.".jpg";
			}
	        my $curl = WWW::Curl::easy->new();
	        $curl->setopt(CURLOPT_NOPROGRESS, 1);
#	        $curl->setopt(CURLOPT_MUTE, 0);
	        $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
	        $curl->setopt(CURLOPT_TIMEOUT, 30);
	        open HEAD, ">/tmp/head.out";
	        $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
	        open BODY, "> /tmp/$crid/images/${name}";
	        $curl->setopt(CURLOPT_FILE,*BODY);
	        $curl->setopt(CURLOPT_URL, $url2);
	        my $retcode=$curl->perform();
	        if ($retcode == 0)
	        {
                my $response_code = $curl->getInfo(CURLINFO_HTTP_CODE);
				my $info = $curl->getinfo(CURLINFO_CONTENT_TYPE);
                # judge result and next action based on $response_code
	        }
	        else
	        {
	        }
	        close HEAD;
			my $tname=$name;
			my $file="/tmp/$crid/images/$name";
			my $type_from_file = $ft->checktype_filename($file);
			$_=$type_from_file;
			if (/gif/)
			{
				$tname=~s/.jpg/.gif/;
			}
			elsif (/x-png/)
			{
				$tname=~s/.jpg/.png/;
			}
			elsif (/x-bmp/)
			{
				$tname=~s/.jpg/.bmp/;
			}
			if ($name ne $tname)
			{
				my $outfile="/tmp/$crid/images/$tname";
                my @args = ("/var/www/html/newcgi-bin/rename.sh \"$file\" \"$outfile\"");
                system(@args) == 0 or die "system @args failed: $?";
			}
			if (($espLabel eq "BlueHornet-HostIMG")
				or ($espLabel eq "BlueHornet")
				or ($espLabel eq "BlueHornet2")
				or ($espLabel eq "BlueHornet3")
				or ($espLabel eq "Bluehornet4")
				or ($espLabel eq "BlueHornet5")
				or ($espLabel eq "BlueHornet6")
				or ($espLabel eq "BlueHornet7")
				or ($espLabel eq "BlueHornet8")
				or ($espLabel eq "BlueHornet9")
				or ($espLabel eq "BlueHornet10")
				or ($espLabel eq "BlueHornet11")
				or ($espLabel eq "BlueHornet12")
				or ($espLabel eq "BlueHornet13")
				or ($espLabel eq "BlueHornet14")
				or ($espLabel eq "BlueHornet15")
				or ($espLabel eq "BlueHornet16")
				or ($espLabel eq "BlueHornet17"))
			{
				$global_text=~s/$turl/http:\/\/${content_domain}\/$tname/g;
			}
			else
			{
				$global_text=~s/$turl/images\/$tname/g;
			}
		}
	}
}

sub cb1 
{
     my($tag, $url1, $url2, %links) = @_;
my ($scheme, $auth, $path, $query, $frag);
my $name;
my $suffix;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $temp_name;
	my $temp_str;
	 if ((($tag eq "a") && ($url1 eq "href")) || (($tag eq "area") && ($url1 eq "href")))
	 {
		$_ = $url2;
		if ((/{{URL}}/) or (/{{ADV_UNSUB_URL}}/))
		{
			return;
		}
		elsif (/ccID/)
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
			my $end=index($url2,"&ccID=");
			my $ccID=substr($url2,$end);
			$ccID=~s/&ccID=//;
			if ($esp eq "PACK1")
			{
            	$global_text =~ s/"$url2"/"http:\/\/$content_domain\/?a=$affiliateID&c=$ccID&s1=&s5=SSoptSS"/gi;
            	$global_text =~ s/$url2/"http:\/\/$content_domain\/?a=$affiliateID&c=$ccID&s1=&s5=SSoptSS"/gi;
			}
			else
			{
            	$global_text =~ s/"$url2"/"http:\/\/$content_domain\/?a=$affiliateID&c=$ccID&s1="/gi;
            	$global_text =~ s/$url2/"http:\/\/$content_domain\/?a=$affiliateID&c=$ccID&s1="/gi;
			}
		}
	 }
}
sub cb2
{
     my($tag, $url1, $url2, %links) = @_;
my ($scheme, $auth, $path, $query, $frag);
my $name;
my $suffix;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $temp_name;
	my $temp_str;
	 if ((($tag eq "a") && ($url1 eq "href")) || (($tag eq "area") && ($url1 eq "href")))
	 {
		$_ = $url2;
		if ((/{{URL}}/) or (/{{ADV_UNSUB_URL}}/))
		{
			return;
		}
		elsif (/ccID/)
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
			my $end=index($url2,"&ccID=");
			my $ccID=substr($url2,$end);
			$ccID=~s/&ccID=//;
			my $turl="http://$esp_cake_domain/?a=$affiliateID&c=$ccID&s1=".$global_subAffiliateID."&s2={{EMAIL_USER_ID}}_".$crid."_".$fid."_".$sid."_".$template_id."&s4=$esp&s5=0_0_0_0_".$global_senddate;
			if ($offer_type eq "CPC")
			{
				$turl.="&p=c";
			}
			my $tlink_id="";
			while (($tlink_id eq "") and ($turl ne ""))
			{
				$sql="select link_id from links where refurl=?";
				$sth=$dbhq->prepare($sql);
				$sth->execute($turl);
				if (($tlink_id)=$sth->fetchrow_array())
				{
				}
				else
				{
					$sql="insert ignore into links(refurl,date_added) values('$turl',now())";
					my $rows=$dbhu->do($sql);
				}
			}
			my $iret=util::checkLink($turl);	
			if ($iret)
			{
				$chklinkstr.="$aid,$tlink_id,$aname,$esp,$country\n";
			}
			my $newlink;
			if ($newurl eq "G")
			{
				$newlink=util::get_gmail_url("REDIRECT",$content_domain,$eidfield,$tlink_id);
			}
			else
			{
				my $redir_random_str=util::get_random();
				$newlink="http://$content_domain/z/$redir_random_str/$eidfield|1|$tlink_id|R";
			}

           	$global_text =~ s/"$url2"/"$newlink"/gi;
           	$global_text =~ s/$url2/"$newlink"/gi;
		}
	 }
}
sub genImage
{
	my ($name)=@_;
	my @EXT=(".png",".bmp",".gif",".jpg");
	my @CHARS=("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	my $new_name;

	my $range=$#EXT-1;
	my $ind=int(rand($range));
	$range=$#CHARS-1;
	my $cind=int(rand($range));
    my $params = { 'minimum'   => 4, 'range'     => 8,'letters' =>0,'uppercase' => 0 };
    my $random_string= $mtaRandom->generateRandomString($params);
	$random_string=~tr/A-Z/a-z/;
    my $random_string1= $mtaRandom->generateRandomString($params);
	$random_string1=~tr/A-Z/a-z/;
	$new_name=$random_string.$CHARS[$cind].$name.$CHARS[$cind].$random_string1.$EXT[$ind];
	return $new_name;
}

sub sendESP
{
	my ($file,$esp)=@_;
	my $tfile="/tmp/".$file;
	my $host = "ftp.aspiremail.com";
	my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
	if ($ftp)
	{
    	$ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
    	$ftp->binary();
		$ftp->mkdir($esp);
		$ftp->cwd($esp);
    	$ftp->put($tfile) or print "put failed $file", $ftp->message;
    	$ftp->quit;
	}
	unlink($tfile);
}

sub sendESPCSV
{
	my ($file)=@_;
	my $host = "ftp.aspiremail.com";
	my $ftp = Net::FTP->new("$host", Timeout => 20, Debug => 0, Passive => 1) or print "Cannot connect to $host: $@\n";
	if ($ftp)
	{
    	$ftp->login($ftpuser,$ftppass) or print "Cannot login ", $ftp->message;
    	$ftp->ascii();
    	$ftp->put($file) or print "put failed $file", $ftp->message;
    	$ftp->quit;
	}
}
sub cb3 
{
     my($tag, $url1, $url2, %links) = @_;
my ($scheme, $auth, $path, $query, $frag);
my $name;
my $suffix;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $temp_name;
	my $temp_str;
	 if ((($tag eq "a") && ($url1 eq "href")) || (($tag eq "area") && ($url1 eq "href")))
	 {
		$_ = $url2;
		if ((/{{URL}}/) or (/{{ADV_UNSUB_URL}}/))
		{
			return;
		}
		else
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
			my $end=index($url2,"&id=");
			my $ID=substr($url2,$end);
			$ID=~s/&id=//;
			my $refurl;
			my $sql="select refurl from links where link_id=?"; 
			my $sth=$dbhu->prepare($sql);
			$sth->execute($ID);
			($refurl)=$sth->fetchrow_array();
			$sth->finish();
           	$global_text =~ s/"$url2"/"$refurl"/gi;
           	$global_text =~ s/$url2/"$refurl"/gi;
		}
	 }
}
