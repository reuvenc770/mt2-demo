#!/usr/bin/perl

use strict;
use CGI;
use CGI::Cookie;
use util;
use DBI;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use vars qw($DBHQ);
require "/var/www/html/newcgi-bin/modules/Common.pm";
my $cwc3;
my $cwcid;
my $cwprogid;
my $cr;
my $landing_page;

# get some objects to use later
my $util = util->new;

# connect to the util database
$DBHQ=DBI->connect('DBI:mysql:new_mail:updatep.routename.com','db_user','sp1r3V') or die "Can't connect to DB: $!\n";
my $args=Common::get_args();
$args->{r_type}||='t';

my $cookieID=retrieve_cookie();

if (!$cookieID) {
	print "Location: intela_login.cgi?redir=intela_deployed_view.cgi\n\n";
}
else {
	my $infoHR={};
	$infoHR->{cookie}=$cookieID;
	$infoHR->{thirdID}=11;

	my $sqlUID=qq|SELECT user_id FROM user WHERE user_id=28|;
##	my $sqlUID=qq|SELECT user_id FROM user WHERE company='Domain Lanes'|;
	my $sthUID=$DBHQ->prepare($sqlUID);
	$sthUID->execute();
	$infoHR->{uid}=$sthUID->fetchrow;
	$sthUID->finish();

	my $sqlBID=qq|SELECT brand_id FROM schedule_info WHERE slot_type='3' AND third_party_id=$infoHR->{thirdID} AND client_id=$infoHR->{uid}|;
warn "$sqlBID\n";
	my $sthDBID=$DBHQ->prepare($sqlBID);
	$sthDBID->execute();
	$infoHR->{DBID}=$sthDBID->fetchrow;
	$sthDBID->finish();

	my $qMailer=qq|SELECT mailer_name,num_subject,num_from,num_creative,name_replace,loc_replace,date_replace,email_replace,|
    	       .qq|cid_replace,emailid_replace,capture_replace,ip_replace,include_unsubscribe,suppression_path,creative_path,|
        	   .qq|include_images FROM third_party_defaults WHERE third_party_id='$infoHR->{thirdID}'|;
	my $sthMailer=$DBHQ->prepare($qMailer);
	$sthMailer->execute;
	my $hr=$sthMailer->fetchrow_hashref;
	$sthMailer->finish;

	foreach (keys %$hr) { $infoHR->{$_}=$hr->{$_}; }
	my $Ainfo={};
	if ($args->{aID}) {
		my $qAdv=qq|SELECT advertiser_name,category_id,unsub_link,unsub_image,vendor_supp_list_id FROM advertiser_info WHERE |
    		    .qq|advertiser_id='$args->{aID}'|;
		my $sthAdv=$DBHQ->prepare($qAdv);
		$sthAdv->execute;
		$Ainfo=$sthAdv->fetchrow_hashref;
		$sthAdv->finish;

        my $ULIDq=qq|SELECT link_id FROM links WHERE refurl='$Ainfo->{unsub_link}'|;
        my $sthULID=$DBHQ->prepare($ULIDq);
        $sthULID->execute;
        $Ainfo->{ulinkID}=$sthULID->fetchrow;
        $sthULID->finish;

		my $qLINKID=qq|select link_id from advertiser_tracking where advertiser_id='$args->{aID}' and client_id='$infoHR->{uid}' |
				   .qq|and daily_deal='N'|;
		my $sthLINKID=$DBHQ->prepare($qLINKID);
		$sthLINKID->execute();
		$infoHR->{DBID_linkid}=$sthLINKID->fetchrow_array();
		$sthLINKID->finish();

		my $qBINFO=qq|select brand_name,header_text,footer_text from client_brand_info where brand_id='$infoHR->{DBID}'|;
		my $sthBINFO=$DBHQ->prepare($qBINFO);
		$sthBINFO->execute();
		($infoHR->{brand_name},$infoHR->{header_text},$infoHR->{footer_text})=$sthBINFO->fetchrow_array();
		$sthBINFO->finish();

		my $qURLINFO=qq|select url,url_id from brand_url_info where brand_id='$infoHR->{DBID}' and url_type='O'|;
		my $sthURLINFO=$DBHQ->prepare($qURLINFO);
		$sthURLINFO->execute();
		($infoHR->{redir_domain},$infoHR->{url_id})=$sthURLINFO->fetchrow_array();
		$sthURLINFO->finish();

		my $qIMG=qq|select url from brand_url_info where brand_id='$infoHR->{DBID}' and url_type='OI'|;
		my $sthIMG=$DBHQ->prepare($qIMG) ;
		$sthIMG->execute();
		($infoHR->{img_domain})=$sthIMG->fetchrow_array();
		$sthIMG->finish();

		my $sqlSub=qq|select brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info where |
				  .qq|category_id='$args->{catID}' and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and |
				  .qq|category_brand_info.brand_id='$infoHR->{DBID}'|;
		my $sthSub=$DBHQ->prepare($sqlSub);
		$sthSub->execute();
		($infoHR->{sid},$infoHR->{subdomain_name}) = $sthSub->fetchrow_array();
		$sthSub->finish();
		$infoHR->{subdomain_name}=~s/{{BRAND}}/$infoHR->{brand_name}/g;
		($infoHR->{footer_subdomain},$infoHR->{rest_str})=split '\.',$infoHR->{subdomain_name};
	}
	display_header($args);
	display_available_deployed_campaign($infoHR,$args);
	if ($args->{aID}) {
		display_info($args,$infoHR,$Ainfo);
		display_subject_from_supp($args,$infoHR,$Ainfo);
		display_creative($args,$infoHR,$Ainfo);
	}
	display_footer($cookieID);
}


sub display_available_deployed_campaign {
	my $infoHR=shift;
	my $args=shift;

	my ($date_clause,$href)=build_date_clause($args,'3c.scheduled_datetime');
	my $hr={};
	my $quer=qq|SELECT 3c.id, third_party_id, 3c.client_id, a.category_id as catid, 3c.advertiser_id, 3c.deploy_name, |
			.qq|3c.campaign_id, date_format(3c.scheduled_datetime, '%m/%d/%y') as date, date(3c.scheduled_datetime) AS sch_date |
			.qq| FROM campaign c, 3rdparty_campaign 3c, advertiser_info a WHERE c.campaign_id=3c.campaign_id AND |
			.qq|c.advertiser_id=a.advertiser_id AND 3c.advertiser_id=a.advertiser_id AND 3c.client_id='$infoHR->{uid}' |
			.qq|AND 3c.third_party_id='$infoHR->{thirdID}' |
			.qq|AND 3c.brand_id='$infoHR->{DBID}' AND $date_clause ORDER BY 3c.id ASC|;
warn "$quer\n";
	my $sth=$DBHQ->prepare($quer);
	open(LOG,">/tmp/j.j");
	print LOG "<$quer>\n";
	close(LOG);
	$sth->execute;
	while (my $info=$sth->fetchrow_hashref) {
		$hr->{$info->{id}}=$info;
	}
	$sth->finish;
	print qq^
	<tr><td align=center>
	<table border=0>
	  <tr>
	    <td><font type="Arial"><b>Deployed Campaign</b></font></td>
	  </tr>
	^;
	foreach (sort {$hr->{$a}{deploy_name} cmp $hr->{$b}{deploy_name}} keys %$hr) {
		print qq^
		<tr>
		  <td><a href="intela_deployed_view.cgi?$href&id=$_&thirdID=$hr->{$_}{third_party_id}&cID=$hr->{$_}{client_id}&catID=$hr->{$_}{catid}&aID=$hr->{$_}{advertiser_id}&campID=$hr->{$_}{campaign_id}&mailer=$args->{mailer}&sch_date=$hr->{$_}{sch_date}#info">$hr->{$_}{deploy_name} ($hr->{$_}{date})</a></td>
		</tr>
		^;
	}
	print qq^</td></tr></table>^;
}

sub display_info {
	my ($args,$infoHR,$Ainfo)=@_;

    my $med_link='';
    if ($args->{thirdID}==10) {
        $med_link='N';
    }
    else
	{
        $med_link=$args->{thirdID};
    }
	print qq^
	<a name="info">
	^;
#	my $query=qq|SELECT si.client_id AS cid, bui.brand_id AS bid, company, lp.profile_name AS profile_name, brand_name, url, |
#			 .qq|url_id FROM user u, schedule_info si, list_profile lp, client_brand_info cbi, brand_url_info bui WHERE |
#			 .qq|si.client_id=u.user_id AND si.client_id=lp.client_id AND si.client_id=cbi.client_id AND |
#			 .qq|si.profile_id=lp.profile_id AND si.brand_id=cbi.brand_id AND si.brand_id=bui.brand_id AND |
#			 .qq|cbi.brand_id=bui.brand_id AND url_type='O' AND slot_type='3' AND si.third_party_id='$infoHR->{thirdID}' |
#			 .qq|AND cbi.exclude_thirdparty='N' GROUP BY company,profile_name,brand_name ORDER BY company ASC|;
	my $query=qq|SELECT 3c.client_id AS cid, 3c.brand_id AS bid, company, lp.profile_name AS profile_name, brand_name, url, |
			 .qq|url_id FROM brand_url_info bui, client_brand_info cbi, 3rdparty_campaign 3c, campaign c, list_profile lp, user u |
			 .qq|WHERE 3c.campaign_id=c.campaign_id AND c.profile_id=lp.profile_id AND cbi.brand_id=3c.brand_id AND |
			 .qq|cbi.brand_id=bui.brand_id AND cbi.client_id=3c.client_id AND cbi.client_id=u.user_id AND |
			 .qq|cbi.third_party_id='$infoHR->{thirdID}' AND 3c.third_party_id='$infoHR->{thirdID}' AND cbi.status='A' AND |
			 .qq|exclude_thirdparty='N' AND bui.url_type='O' AND 3c.advertiser_id='$args->{aID}' AND |
			 .qq|date(3c.scheduled_datetime)='$args->{sch_date}'|;
	open(LOG,">/tmp/d.d");
	print LOG "<$query>\n";
	close(LOG);
	my $sth=$DBHQ->prepare($query);
	$sth->execute;
	my $count=1;
	while (my $info=$sth->fetchrow_hashref) {
		my $domQ=qq|SELECT subdomain_name FROM category_brand_info cbi, brandsubdomain_info bi WHERE category_id='$args->{catID}' |
				.qq|AND bi.subdomain_id=cbi.subdomain_id AND cbi.brand_id='$info->{bid}'|;
		my $sthD=$DBHQ->prepare($domQ);
		$sthD->execute;
		my $sub_dom_name=$sthD->fetchrow;
		$sub_dom_name=~s/{{BRAND}}/$info->{brand_name}/g;
		my ($footer_subdomain,$rest_str) = split '\.',$sub_dom_name;
		$sthD->finish;
		my $lIDq=qq|SELECT link_id  FROM advertiser_tracking WHERE advertiser_id='$args->{aID}' AND client_id='$info->{cid}' |
				.qq|AND daily_deal='$med_link'|;
		my $sthLID=$DBHQ->prepare($lIDq);
		$sthLID->execute;
		my $linkID=$sthLID->fetchrow;
		$sthLID->finish;
		my $qCID=qq|SELECT campaign_id FROM 3rdparty_campaign WHERE client_id='$info->{cid}' AND brand_id='$info->{bid}' AND |
				.qq|third_party_id='$args->{thirdID}' AND advertiser_id='$args->{aID}' order by scheduled_datetime DESC LIMIT 1|;
		my $sthCID=$DBHQ->prepare($qCID);
		$sthCID->execute;
		my $campID=$sthCID->fetchrow;
		$sthCID->finish;
		my $adv_link=qq^http://$info->{url}/cgi-bin/redir1.cgi?eid=$infoHR->{emailid_replace}&cid=$campID&^
					.qq^id=$linkID&f=&s=&c=^;
		my $adv_unsub_link=qq^http://$footer_subdomain.$info->{url}/cgi-bin/redir1.cgi?eid=$infoHR->{emailid_replace}&cid=0&^
						  .qq^id=$Ainfo->{ulinkID}^;
		my $client_unsub_link=qq^http://${footer_subdomain}.$info->{url}/cgi-bin/redir1.cgi?eid=$infoHR->{emailid_replace}&^
							 .qq^cid=$campID&id=42&nid=$info->{cid}^;
		my $privacy_unsub_link=qq^http://${footer_subdomain}.$info->{url}/cgi-bin/redir1.cgi?eid=$infoHR->{emailid_replace}&^
							  .qq^cid=0&id=51&nid=$info->{cid}^;
		my $img_link=qq^http://$info->{url}/cgi-bin/open_email1.cgi?id=$infoHR->{emailid_replace}&cid=$campID&f=&s=&c=&^;
		my $color=($count%2==0) ? "#E9E9E9" : "#FFFFFF";
		my $cnt=exlusion_check($info->{cid},$args->{catID},$args->{aID});
		if ($cnt==0) {
		print qq^
		<table border=0 width=100%>
		  <tr>
			<td>
			  <table border=0 bgcolor=#E3FAD1 width=100%>
		  		<tr bgcolor=#509c10><td colspan=2 width=100%><font color="#FFFFFF" type="Arial" size=-1><b>Network: </b></font>$info->{company}
            	<font type="Arial" size=-1 color="#FFFFFF"><b>Brand: </b></font>$info->{brand_name}
				<font type="Arial" size=-1 color="#FFFFFF"><b>Profile: </b></font>$info->{profile_name}
				<font type="Arial" size=-1 color="#FFFFFF"><b>Advertiser: </b></font>$Ainfo->{advertiser_name}
		  		</td></tr>
		  		<tr bgcolor=$color><td width=15%><font type="Arial" size=-1><b>URL</b></font></td>
					<td width=85%><a href="$adv_link" target=_blank>$adv_link</a></td>
				</tr>
                <tr bgcolor=$color><td width=15%><font type="Arial" size=-1><b>Privacy</b></font></td>
                    <td width=85%><a href="$privacy_unsub_link" target=_blank>$privacy_unsub_link</a></td>
                </tr>
                <tr bgcolor=$color><td width=15%><font type="Arial" size=-1><b>Mailer Unsub</b></font></td>
                    <td width=85%><a href="$client_unsub_link" target=_blank>$client_unsub_link</td>
                </tr>
		  		<tr bgcolor=$color><td width=15%><font type="Arial" size=-1><b>Advertiser Unsub</b></font></td>
					<td width=85%><a href="$adv_unsub_link" target=_blank>$adv_unsub_link</a></td>
				</tr>
                <tr bgcolor=$color><td width=15%><font type="Arial" size=-1><b>TRACKING IMG</b></font></td>
                    <td width=85%><a href="$img_link" target=_blank>$img_link</a></td>
                </tr>
			  </table>
			</td>
		  </tr>
		</table>
		^;
		$count++;
		}
	}
	$sth->finish;
}

sub display_subject_from_supp {
	my ($args,$infoHR,$Ainfo)=@_;

	my @sub=();
	my @from=();
	my $sql=qq|select advertiser_subject,sum(el.open_cnt)/sum(el.sent_cnt) from advertiser_subject,email_log el, campaign c where advertiser_subject.subject_id=el.subject_id and advertiser_subject.status='A' and advertiser_subject.advertiser_id='$args->{aID}' and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$args->{aID} group by advertiser_subject order by 2 desc limit $infoHR->{num_subject}|;
	my $sth = $DBHQ->prepare($sql);
	$sth->execute();
	while (my ($subject_str,$temp_val)=$sth->fetchrow_array()) {
		$subject_str =~ s/{{NAME}}/$infoHR->{name_replace}/g;
		$subject_str =~ s/{{LOC}}/$infoHR->{loc_replace}/g;
		push @sub, $subject_str;
	}
	$sth->finish;
	my $sqlF=qq|select advertiser_from,sum(el.open_cnt)/sum(el.sent_cnt) from advertiser_from,email_log el, campaign c where advertiser_from.from_id=el.from_id and advertiser_from.advertiser_id='$args->{aID}' and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$args->{aID} group by advertiser_from order by 2 desc limit $infoHR->{num_from}|;
	my $sthF=$DBHQ->prepare($sqlF);
	$sthF->execute;
	while (my ($from_str,$temp_val)=$sthF->fetchrow_array()) {
		if ($infoHR->{cookie} ne 'intela') {
			$from_str =~ s/{{FOOTER_SUBDOMAIN}}/$infoHR->{footer_subdomain}/g;
		}
		if ($from_str!~/{{FOOTER/) {
			push @from, $from_str;
		}
	}
	$sthF->finish;

	my $sqlSL=qq|select list_name from vendor_supp_list_info where list_id='$Ainfo->{vendor_supp_list_id}'|;
	my $sthSL=$DBHQ->prepare($sqlSL);
	$sthSL->execute();
	my ($supp_name)=$sthSL->fetchrow_array();
	$sthSL->finish();
	$supp_name =~ s/ //g;
	$supp_name =~ s/\)//g;
	$supp_name =~ s/\(//g;
	$supp_name =~ s/\&//g;
	$supp_name =~ s/\://g;
	$supp_name =~ s/\$//g;
	$supp_name =~ s/\.//g;
	$supp_name =~ s/\!//g;
	my $supp_file=$supp_name.".zip";

	print qq^
	<tr><td>
	<table border=0 width=450 bgColor="#e3fad1">
	  <tr bgcolor=#509c10><td><font type="Arial" size=3><b>SUBJECT</b></font></td></tr>
	^;
	foreach (@sub) { print qq^<tr><td><font type="Arial" size=2>$_</font></td></tr>^; }
	print qq^</table></td></tr>^;
    print qq^
	<tr><td>
    <table border=0 width=450 bgColor="#e3fad1">
      <tr bgcolor=#509c10><td><font type="Arial" size=3><b>FROM</b></font></td></tr>
    ^;
    foreach (@from) { print qq^<tr><td><font type="Arial" size=2>$_</font></td></tr>^; }
    print qq^</table></td></tr>^;
	print qq^<tr><td>
	<table border=0><tr><td><font type="Arial" size=3><b>Suppression File: </b></td>
	<td><a href="/supp/$supp_file">$supp_file</td></tr></table></td></tr>^;
}

sub display_creative {
	my ($args,$infoHR,$Ainfo)=@_;

	my $sql=qq|select el.creative_id,creative.creative_name,((sum(el.click_cnt)/sum(el.sent_cnt))*100000) from email_log el, creative,campaign c where el.creative_id=creative.creative_id and trigger_flag='N' and creative.status='A' and creative.advertiser_id='$args->{aID}' and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$args->{aID} group by 1,2 order by 3 desc limit $infoHR->{num_creative}|;
	my $sth=$DBHQ->prepare($sql);
	$sth->execute;
	my $camp_cnt = 1;
	while (my ($cid,$creative_name,$temp_val)=$sth->fetchrow_array()) {
		my $querNum=qq|select (sum(el.open_cnt)/sum(el.sent_cnt)*100),(sum(el.click_cnt)/sum(el.open_cnt)*100),|
				   .qq|((sum(el.click_cnt)/sum(el.sent_cnt))*100000) from email_log el, campaign c where el.creative_id=? and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$args->{aID}|;
		my $sthNum=$DBHQ->prepare($querNum);
		$sthNum->execute($cid);
		my ($copen,$cclick,$cindex) = $sthNum->fetchrow_array();
		$sthNum->finish();

		my $sqlC=qq|select approved_flag,original_flag,html_code,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page from creative where creative_id=$cid|;
		my $sthC=$DBHQ->prepare($sqlC);
		$sthC->execute();
		my ($aflag,$oflag,$html_code,$cwc3,$cwcid,$cwprogid,$cr,$landing_page) = $sthC->fetchrow_array();
		$sthC->finish();

		if ($copen eq "") {	$copen = "0.00"; }
		if ($cclick eq "") { $cclick = "0.00"; }
		if ($cindex eq "") { $cindex = "0"; }
		my $temp_str="";
		if ($oflag eq "Y") { $temp_str = $temp_str . "O "; }
		else { $temp_str = $temp_str . "A "; }
		if ($aflag eq "Y") { $temp_str = $temp_str . ")"; }
		else { $temp_str = $temp_str . "- NA!)"; }

		print qq^<tr><td>^;
		my $c_data=qq^<b><u>Creative $camp_cnt: $creative_name<br>^;
		if ($infoHR->{cookie} ne "intela") {
			$c_data=qq^<b><u>Creative $camp_cnt: $creative_name ($copen% Open - $cclick% Click - $temp_val Index - $temp_str</u></b><br>\n^;
		}
		print $c_data;
		$html_code =~ s\<BODY\<body\;
		my $pos1 = index($html_code, "<body");
		my $pos2 = index($html_code, ">",$pos1);
		substr($html_code,$pos1,$pos2-$pos1+1) = "<body>";
		my $tracking_str = "<IMG SRC=\"http://{{DOMAIN}}/cgi-bin/open_email1.cgi?id={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;f={{FID}}&amp;s={{SID}}&amp;c={{CRID}}&amp;\" border=0 height=1 width=1 alt=\"open\">";
		$html_code =~ s\<body>\<body><center><p STYLE="font-size:10pt; font-family:arial">{{HEADER_TEXT}}</p></center><p>${tracking_str}\;
		$html_code =~ s\<HEAD\<head\gi;
		$_ = $html_code;
		if (/<head/) { my $got_head = 1; }
		else {
			$html_code=~ s/<body>/<head><meta http-equiv="Content-Type" content="text\/html; charset=windows-1252"><title>{{EMAIL_USER_ID}}<\/title><\/head><body>/;
		}
		$html_code =~ s/{{TRACKING}}//g;
		my $content_str = "";
		$html_code =~ s/{{CONTENT_HEADER}}/$content_str/g;
		$content_str = "";
		$_ = $html_code;
		$html_code =~ s/{{CONTENT_HEADER_TEXT}}/$content_str/g;
		if (/{{TIMESTAMP}}/) {
			my $timestr = util::date('',5);
			$html_code =~ s/{{TIMESTAMP}}/$timestr/g;
		}
		if (/{{REFID}}/) {
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
        if ($Ainfo->{unsub_image} ne "") {
            $_=$Ainfo->{unsub_image};
            if ( /\// )
            {
            	$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;&amp;id=$Ainfo->{ulinkID}\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/$Ainfo->{unsub_image}\" border=0 alt=\"Unsub\"></a><br><br>";
			}
			else
			{
            	$temp_str = "<a href=\"http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid=0&amp;&amp;id=$Ainfo->{ulinkID}\" target=\"_blank\"><img src=\"http://www.{{IMG_DOMAIN}}/images/unsub/$Ainfo->{unsub_image}\" border=0 alt=\"Unsub\"></a><br><br>";
			}
		}
		if ($infoHR->{include_unsubscribe} eq "Y") {
			$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;&amp;id=42&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?cid=0&amp;eid={{EMAIL_USER_ID}}&amp;id=51&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
#			$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str<a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_1.jpg" border=0 alt="end this subscription"></a><br><a href="http://{{FOOTER_SUBDOMAIN}}.{{DOMAIN}}/cgi-bin/redir1.cgi?cid=0&amp;eid={{EMAIL_USER_ID}}&amp;id=51&amp;nid={{NID}}" target=_blank><img src="http://www.{{IMG_DOMAIN}}/fimg/{{FOOTER_STR}}_2.jpg" border=0 alt="privacy policy"></a>\g;
		}
		else {
			$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str\g;
		}
##		my $footer_str = $infoHR->{DBID} . "_" . $infoHR->{sid} . "_" . $infoHR->{url_id};
		my $footer_str = $infoHR->{DBID};
		$html_code =~ s/{{HEADER_TEXT}}/$infoHR->{header_text}/g;
		$html_code =~ s/{{FOOTER_TEXT}}/$infoHR->{footer_text}/g;
		if ($cwc3 ne "")
		{
			$html_code =~ s/{{URL}}/http:\/\/$infoHR->{redir_domain}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&id=$infoHR->{DBID_linkid}&f=&s=&c=$cid&cwc3=$cwc3&cwcid=$cwcid&cwprogid=$cwprogid&cr=$cr&l=$landing_page/g;
		}
		else
		{
			$html_code =~ s/{{URL}}/http:\/\/$infoHR->{redir_domain}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&id=$infoHR->{DBID_linkid}&f=&s=&c=$cid&cwcid=$cwcid&cwprogid=$cwprogid&cr=$cr&l=$landing_page/g;
		}
#		$html_code =~ s/{{URL}}/http:\/\/$infoHR->{redir_domain}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&cid={{CID}}&em={{EMAIL_ADDR}}&id=$infoHR->{DBID_linkid}&f=&s=&c=$cid/g;
		$html_code =~ s/{{UNSUB_LINK}}/http:\/\/{{FOOTER_SUBDOMAIN}}.$infoHR->{redir_domain}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;&amp;id=42/g;
#		$html_code =~ s/{{UNSUB_LINK}}/http:\/\/{{FOOTER_SUBDOMAIN}}.$infoHR->{redir_domain}\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42/g;
		$html_code =~ s/{{FOOTER_SUBDOMAIN}}/$infoHR->{footer_subdomain}/g;
		$html_code =~ s/{{FOOTER_STR}}/$footer_str/g;
		$html_code =~ s/{{NAME}}/$infoHR->{name_replace}/g;
		$html_code =~ s/{{EMAIL_ADDR}}/$infoHR->{email_replace}/g;
#		$html_code =~ s/{{EMAIL_ADDR}}/$infoHR->{email_replace}/g;
		$html_code =~ s/{{EMAIL_USER_ID}}//g;
		$html_code =~ s/{{LOC}}/$infoHR->{loc_replace}/g;
		$html_code =~ s/{{DATE}}/$infoHR->{date_replace}/g;
		$html_code =~ s/{{CID}}/$args->{campID}/g;
		$html_code =~ s/{{CRID}}//g;
		$html_code =~ s/{{CAPTURE_DATE}}/$infoHR->{capture_replace}/g;
		$html_code =~ s/{{IP}}/$infoHR->{ip_replace}/g;
		$html_code =~ s/{{FID}}//g;
		$html_code =~ s/{{NID}}//g;
		$html_code =~ s/{{SID}}//g;
		$html_code =~ s/{{IMG_DOMAIN}}/$infoHR->{img_domain}/g;
		$html_code =~ s/{{DOMAIN}}/$infoHR->{redir_domain}/g;

		print qq^<b><textarea name=html_code$camp_cnt rows=15 cols=100>$html_code</textarea><img src="thumbnail.cgi?creativeID=$cid"><br>\n
		</td></tr>^;
	}
}

sub display_header {
	my $args=shift;

my $date_options=print_nav_form($args);

print "Content-type: text/html\n\n";
print qq^
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Deployed Creative</title>
</head>
<body>
<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
    <tr vAlign="top">
        <td noWrap align="left">
        <table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
            <tr>
                <td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
                <td width="328" bgColor="#ffffff">&nbsp;</td>
            </tr>
            <tr>
                <td width="550">
                <table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
                    <tr>
                        <td align="left"><b><font face="Arial" size="2">&nbsp;Deployed Creative View</font></b></td>
						<td align="right"><b>
							<a style="TEXT-DECORATION: none" href="intela_login.cgi?action=logout&redir=intela_deployed_view.cgi">
                        	<font face="Arial" color="#509c10" size="2">Logout</font></a>
						</td>
                    </tr>
					<tr>
						<td colspan=2>$date_options</td>
					</tr>
                </table>
                </td>
            </tr>
        </table>
		</td>
	</tr>
^;
}

sub display_footer {
	my ($cookieID)=shift;

	my $html="";

if ($cookieID ne "intela") {
	$html=qq^
    <tr>
        <td align=center>
        <table>
            <tr>
                <td>
                    <table id="table8" cellPadding="5" width="66%" bgColor="white">
                        <tr>
                            <td align="middle" width="47%">
                            <a href="/cgi-bin/mainmenu.cgi">
                            <img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
                            <td align="middle" width="47%">
                            &nbsp;</td>
                            <td align="middle" width="50%">
                            &nbsp;</td>
                        </tr>
                    </table>
                    </b>
                </td>
            </tr>
        </table>
        </td>
    </tr>^;
}

print qq^
	$html
</table>
</body>
</html>
^;
}

sub validate_data {
        my ($hrArgs)=@_;
        my $err_flag=0; my $hrInfo={};

        if (!$hrArgs->{userID}) {
                $err_flag=1;
                $hrInfo->{err}='Please enter a userID';
        }
        elsif (!$hrArgs->{passwd}) {
                $err_flag=1;
                $hrInfo->{err}='Please enter a password';
        }
        else {
#                my $dbh=DB::connect_db();
                my $qSel=qq^SELECT user_id, username, password, status FROM user WHERE username=?^;
                my $sth=$DBHQ->prepare($qSel);
                $sth->execute($hrArgs->{userID});
                my $hrData=$sth->fetchrow_hashref;
                $sth->finish;

                if (!$hrData->{user_id}) {
                        $err_flag=1;
                        $hrInfo->{err}='Sorry, that userID doesn\'t exist';
                }
                elsif (lc($hrData->{password}) ne lc($hrArgs->{passwd})) {
                        $err_flag=1;
                        $hrInfo->{err}='Sorry, bad password';
                }
                elsif ($hrData->{status} ne 'A') {
                        $err_flag=1;
                        $hrInfo->{err}='Sorry, this account is not authorized';
                }
                else {
                        foreach (keys %$hrData) { $hrInfo->{$_}=$hrData->{$_}; }
                }
        }
        return ($err_flag, $hrInfo);
}

sub display_login_form {
	my ($args,$info)=@_;

	print qq^
	<tr><td>
	<table border=0 width=450 align=center>
	<form method='post' action='intela_deployed_view.cgi'>
	  <tr>
		<td>
		  <table width='100%' align='center' bgcolor='#FFFFFF'>
			<tr>
			  <td class='err' colspan='2' align='center'>$info->{err}</td>
			</tr>
			<tr>
			  <td class='txt' align='right'>UserID:</td>
			  <td align='left'>&nbsp;<input type='text' name='userID' class='input' size='10' value='$args->{userID}'></td>
			</tr>
			<tr>
			  <td class='txt' align='right'>Password:</td>
			  <td align='left'>&nbsp;<input type='password' name='passwd' class='input' size='10' value='$args->{passwd}'></td>
			</tr>
			<tr>
			  <td class='txt'>&nbsp;</td>
			  <td class='txt'>&nbsp;<input type='submit' class='input' name='submit' value='submit'></td>
			</tr>
		  </table>
		</td>
	  </tr>
	</form>
	</table>
	</td></tr>
	^;
}

sub retrieve_cookie {

    my ($hr, %cookies,$login_ok);
    my @rawCookies = split (/; /,$ENV{'HTTP_COOKIE'});
    foreach (@rawCookies) {
        my ($key, $val) = split (/=/,$_);
        $cookies{$key} = $val;
    }

    if ($cookies{'intelalogin'} ne "0") {
        $login_ok = $cookies{'intelalogin'};
    }
    else {
        $login_ok = 0;
    }
    return ($login_ok);
}

sub print_date_drop {
  my ($name,$args)=@_;

  my %months=('01'=>"Jan",'02'=>"Feb",'03'=>"Mar",'04'=>"Apr",'05'=>"May",'06'=>"Jun",'07'=>"Jul",'08'=>"Aug",'09'=>"Sep",'10'=>"Oct",'11'=>"Nov",'12'=>"Dec");
  my @day=(01..31);
  my $YEAR=(localtime)[5]+1900;
  my $mon_name=$name."_month";
  my $day_name=$name."_day";
  my $yr_name=$name."_year";

  my ($month,$day,$yr,$select);
  foreach (sort {$a<=>$b} keys %months) {
    if ($_ eq "$args->{$mon_name}") {
      $month.=qq^<option value="$_" SELECTED>$months{$_}\n^;
    }
    else { $month.=qq^<option value="$_">$months{$_}\n^; }
  }
  foreach (@day) {
    if ($_ eq "$args->{$day_name}") {
      $day.=qq^<option value="$_" SELECTED>$_\n^;
    }
    else { $day.=qq^<option value="$_">$_\n^; }
  }
  my $last_yr=$YEAR+1;
  for (my $i=2006; $i<=$last_yr; $i++) {
    if ($i eq "$args->{$yr_name}") {
      $yr.=qq^<option value="$i" SELECTED>$i\n^;
    }
    else { $yr.=qq^<option value="$i">$i\n^; }
  }
  $select=qq^
    <select name=$mon_name>
      <option value="">Month
      $month
    </select>
    <select name=$day_name>
      <option value="">Day
      $day
    </select>
    <select name=$yr_name>
      <option value="">Year
      $yr
    </select>
  ^; 
  return $select;
}

sub print_nav_form {
  my $args=shift;

  my $from_drop=print_date_drop("from",$args);
  my $to_drop=print_date_drop("to",$args);
  my $single_drop=print_date_drop("single",$args);

  my $mailer=qq^
  <tr height=20>
    <td colspan=2><b>Mailer:</b>
    <select name=mailer>
  ^;
  my $quer=qq|select third_party_id,mailer_name from third_party_defaults where third_party_id in ('11') order by mailer_name|;
  my $sth=$DBHQ->prepare($quer);
  $sth->execute;
  while (my ($id,$name)=$sth->fetchrow_array) {
    my $selected=($id==$args->{mailer}) ? "SELECTED" : "";
    $mailer.=qq^<option value="$id" $selected>$name\n^;
  }
  $sth->finish;
  $mailer.=qq^</select></td></tr>^;

  my $r_select= my $c_select= my $l_select= my $t_select=my $s_select="";
  if ($args->{r_type} eq "r") { $r_select="CHECKED"; }
  if ($args->{r_type} eq "s") { $s_select="CHECKED"; }
  if ($args->{r_type} eq "t") { $t_select="CHECKED"; }
  if ($args->{r_type} eq "c") { $c_select="CHECKED"; }
  if ($args->{r_type} eq "l") { $l_select="CHECKED"; }
  my $html=qq^
  <table align=center border=0 width=100% bgcolor=#509c10>
    <tr height=30>
      <td align=center><font color=#000000 size=3 face=Arial><b>Options</b></font></td>
    </tr>
    <tr>
      <td align=center bgcolor=#ffffff>
        <form method=post action="intela_deployed_view.cgi">
        <table border=0 width=100%>
		  $mailer
          <tr height=20>
            <td width=5%><input type=radio name="r_type" value="r" $r_select></td>
            <td align=left><b>FROM:</b> $from_drop <b>TO:</b> $to_drop</td>
          </tr>
          <tr height=20>
            <td width=5%><input type=radio name="r_type" value="s" $s_select></td>
            <td align=left><b>Single Date</b> $single_drop</td>
          </tr>
          <tr height=20>
            <td width=5%><input type=radio name="r_type" value="t" $t_select></td>
            <td align=left><b>Today</b></td>
          </tr>
          <tr height=20>
            <td  width=5%><input type=radio name="r_type" value="c" $c_select></td>
            <td align=left><b>Current Month</b></td>
          </tr>
          <tr height=20>
            <td  width=5%><input type=radio name="r_type" value="l" $l_select></td>
            <td align=left><b>Last Month</b></td>
          </tr>
          <tr>
            <td align=center colspan=2><input type=submit name="view" value="Submit"></td>
          </tr>
        </table>
        </form>
      </td>
    </tr>
  </table>
  <br>
  ^;
  return $html;
}

sub build_date_clause {
        my $args=shift;
        my $name=shift;
        
        my $date_clause = my $href="";
        if ($args->{r_type} eq "l") {
			$date_clause=qq^month($name)=month(date_sub(now(), interval 1 month)) AND year($name)=year(date_sub(now(), interval 1 month))^;
			$href=qq^r_type=l^;
        }
        elsif ($args->{r_type} eq "c") {
			$date_clause=qq^month($name)=month(now()) AND year($name)=year(now())^;
			$href=qq^r_type=c^;
        }       
        elsif ($args->{r_type} eq "s") {
			$args->{single_day}="0".$args->{single_day} if length($args->{single_day})<=1;
			$date_clause=qq^date_format($name,'%Y-%m-%d')='$args->{single_year}-$args->{single_month}-$args->{single_day}'^;
			$href=qq^r_type=s&single_year=$args->{single_year}&single_month=$args->{single_month}&single_day=$args->{single_day}^;
        }
		elsif ($args->{r_type} eq "t") {
			$date_clause=qq^TO_DAYS($name)=TO_DAYS(now())^;
			$href=qq^r_type=c^;
		}
        else {  
			$date_clause=qq^$name>="$args->{from_year}-$args->{from_month}-$args->{from_day}" AND $name<="$args->{to_year}-$args->{to_month}-$args->{to_day}"^;
			$href=qq^r_type=r&from_year=$args->{from_year}&from_month=$args->{from_month}&from_day=$args->{from_day}&to_year=$args->{to_year}&to_month=$args->{to_month}&to_day=$args->{to_day}^;
        }
        return ($date_clause,$href);
}

sub exlusion_check {
	my ($cID,$catID,$aID)=@_;

	my $sql=qq|select count(*) from client_category_exclusion,client_advertiser_exclusion where |
		   .qq|(client_category_exclusion.client_id=? and client_category_exclusion.category_id=?) or |
		   .qq|(client_advertiser_exclusion.client_id=? and client_advertiser_exclusion.advertiser_id=?)|;
	my $sth1a=$DBHQ->prepare($sql);
	$sth1a->execute($cID,$catID,$cID,$aID);
	my ($reccnt) = $sth1a->fetchrow_array();
	$sth1a->finish();
	return $reccnt;
}
