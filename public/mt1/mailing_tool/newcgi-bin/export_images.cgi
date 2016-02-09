#!/usr/bin/perl

# ******************************************************************************
# export_images.cgi
#
# this page displays exports images for a creative 
#
# History
# Jim Sobeck, 05/25/07, Creation
# ******************************************************************************

# include Perl Modules

use strict;
use Net::FTP;
use CGI;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $temp_id;
##my $dbh;
my $url_id;
my $bid;
my $unsub_use;
my $unsub_text;
my $got_it;
my $errmsg;
my $images = $util->get_images_url;
my $curl;
my $footer_subdomain;
my $rest_str;
my $sdate1;
my $curtime;
my $sdate;
my $aname;
my $supp_name;
my $pexicom_last_upload;
my $bname;
my $header_text;
my $footer_text;
my $network_name;
my $tracking_str;
my $camp_cnt;
my $num_subject;
my $unsub_flag;
my $include_images;
my $physical_addr;
my $temp_val;
my $num_from;
my $num_creative;
my $subject_str;
my $cid;
my $dir2;
my $creative_name;
my $oflag;
my $html_code;
my $global_text;
my $aflag;
my $rname;
my $rloc;
my $rdate;
my $remail;
my $remailid;
my $rcdate;
my $rip;
my $rcid;
my $link_id;
my $redir_domain;
my $img_domain;
my $sid;
my $catid;
my $vid;
my $subdomain_name;
my $unsub_url;
my $unsub_img;
my $advertiser_url;
my $new_camp_id;
my $mon;
my $day;
my $year;
my $mailer_name;
my $temp_cid;
my $profile_id;
my $pname;
my $suppression_path;
my $creative_path;
my $ftp_ip;
my $ftp_username;
my $ftp_password;
my $mflag_str;
my $aid;
my $temp_str;

# connect to the util database
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
my $cid= $query->param('cid');
my $html= $query->param('html');
$sql="select creative_name,html_code,advertiser_id from creative where creative_id=?";
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute($cid);
($creative_name,$html_code,$aid)=$sth1->fetchrow_array();
$sth1->finish();
$sql = " select advertiser_name,vendor_supp_list_id,unsub_link,unsub_image,advertiser_url,unsub_use,unsub_text from advertiser_info where advertiser_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text)=$sth->fetchrow_array();
$sth->finish();
#
# Make changes to html_code
#
$html_code =~ s\<BODY\<body\;
my $pos1 = index($html_code, "<body");
my $pos2 = index($html_code, ">",$pos1);
substr($html_code,$pos1,$pos2-$pos1+1) = "<body>";
$tracking_str = "";
$html_code =~ s\<body>\<body><center><p STYLE="font-size:10pt; font-family:arial">{{HEADER_TEXT}}</p></center><p>${tracking_str}\;
$html_code =~ s\<HEAD\<head\gi;
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
$html_code .= "<p STYLE=\"font-size:10pt; font-family:arial\">{{FOOTER_TEXT}}</p></body></html>";
$temp_str="";
if ($aid != 0)
{
        if ($unsub_use eq "TEXT")
        {
            $temp_str=$unsub_text;
            #$temp_str=~s/{{ADV_UNSUB_URL}}/xxoptoutlinkxx/g;
            $temp_str=~s/{{ADV_UNSUB_URL}}/#unsub#/g;
        }
        else
        {
        if ($unsub_img ne "")
        {
            $_=$unsub_img;
            if ( /\// )
            {
                my $t1;
                my $t2;
                my $t3;
                my $t4;
                ($t1,$t2,$t3,$t4) = split ('\/',$unsub_img);
                if ($t4 ne "")
                {
                    $unsub_img=$t4;
                }
                else
                {
                    $unsub_img=$t2;
                }
            }
            my $tt4;
            my $ext;
            ($tt4,$ext) = split('\.',$unsub_img);
            if ($ext eq "")
            {
                $unsub_img=$unsub_img.".jpg";
            }
            #$temp_str = "<a href=\"xxoptoutlinkxx\" target=\"_blank\"><img src=\"xxipathxx/$unsub_img\" border=0></a><br><br>";
            $temp_str = "<a href=\"#unsub#\" target=\"_blank\"><img src=\"xxipathxx/$unsub_img\" border=0></a><br><br>";
         }
        }
    }
$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str\g;	
#
my $footer_str = $bid;
$html_code =~ s/{{HEADER_TEXT}}//g;
$html_code =~ s/{{FOOTER_TEXT}}//g;
#$html_code =~ s/{{URL}}/ttlinktt/g;
$html_code =~ s/{{URL}}/#url#/g;
$html_code =~ s/{{UNSUB_LINK}}/http:\/\/{{FOOTER_SUBDOMAIN}}.$redir_domain\/cgi-bin\/redir1.cgi?eid={{EMAIL_USER_ID}}&amp;cid={{CID}}&amp;em={{EMAIL_ADDR}}&amp;id=42/g;
$html_code =~ s/{{FOOTER_SUBDOMAIN}}/$footer_subdomain/g;
$html_code =~ s/{{FOOTER_STR}}/$footer_str/g;
$html_code =~ s/{{NAME}}/$rname/g;
$html_code =~ s/{{EMAIL_ADDR}}/$remail/g;
$html_code =~ s/{{EMAIL_USER_ID}}/$remailid/g;
$html_code =~ s/{{LOC}}/$rloc/g;
$html_code =~ s/{{DATE}}/$rdate/g;
$html_code =~ s/{{CID}}/$temp_cid/g;
$html_code =~ s/{{CRID}}//g;
$html_code =~ s/{{CAPTURE_DATE}}/$rcdate/g;
$html_code =~ s/{{IP}}/$rip/g;
$html_code =~ s/{{FID}}//g;
$html_code =~ s/{{NID}}//g;
$html_code =~ s/{{SID}}//g;
my $file;
if ($html)
{
	$html_code=~s/http:\/\/{{IMG_DOMAIN}}\///g;
	open (OUT, "> /data5/hitpath/${cid}.html");
	print OUT "$html_code";
	close(OUT);
	$file=$cid.".html";
}
else
{
my $tmp_dir = "/data5/hitpath/$cid"; 
mkdir $tmp_dir;
my $p = HTML::LinkExtor->new(\&cb);
$p->parse($html_code);
$html_code=$global_text;
my @args = ("/var/www/html/newcgi-bin/stuff_images.sh $cid");
system(@args) == 0 or die "system @args failed: $?";
$file=$cid.".zip";
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Export Images</title>
</head>

<body>
<b>Creative Name: </b>$creative_name<br>
<table>
<tr>
                                <td><b>Download:</b></td>
                                <td><b><a href="/hitpath/$file">$file</a></b></td>
                            </tr>
</table>
</body>
</html>
end_of_html
exit(0);

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
          	$url2=~s/{{DOMAIN}}/staging.affiliateimages.com/g;  
          	$url2=~s/{{IMG_DOMAIN}}/staging.affiliateimages.com/g;  
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
	        open BODY, "> /data5/hitpath/$cid/${name}";
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
		}
	}
}
