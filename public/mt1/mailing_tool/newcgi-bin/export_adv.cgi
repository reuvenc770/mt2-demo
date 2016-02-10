#!/usr/bin/perl

# ******************************************************************************
# export_adv.cgi
#
# this page displays exports images and html for advertiser 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use Net::FTP;
use CGI;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use File::Type;
use URI::Split qw(uri_split uri_join);
use File::Basename;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $ft = File::Type->new();
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
my $IMG;

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
$aid= $query->param('aid');
my @cidarr= $query->param('cid');
my $cidstr="";
foreach my $c (@cidarr)
{
	$cidstr=$cidstr.$c.",";
}
chop($cidstr);
my $tmp_dir = "/data5/hitpath/$aid"; 
mkdir $tmp_dir;
my $auto_cake;
$sql = " select advertiser_name,vendor_supp_list_id,unsub_link,unsub_image,advertiser_url,unsub_use,unsub_text,auto_cake_creativeID from advertiser_info where advertiser_id=?";
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
($aname,$temp_id,$unsub_url,$unsub_img,$advertiser_url,$unsub_use,$unsub_text,$auto_cake)=$sth->fetchrow_array();
$sth->finish();
my @ACAKE=split(",",$auto_cake);

$sql="select creative_id,creative_name,html_code from creative where status='A' and trigger_flag='N' and advertiser_id=?";
if ($cidstr ne "")
{
	$sql=$sql." and creative_id in ($cidstr)";
}
$sth1 = $dbhq->prepare($sql) ;
$sth1->execute($aid);
while (($cid,$creative_name,$html_code)=$sth1->fetchrow_array())
{
#
# Make changes to html_code
#
	$html_code =~ s\<BODY\<body\;
	my $pos1 = index($html_code, "<body");
	my $pos2 = index($html_code, ">",$pos1);
	substr($html_code,$pos1,$pos2-$pos1+1) = "<body>";
	$tracking_str = "";
	#$html_code =~ s\<body>\<body><center><p STYLE="font-size:10pt; font-family:arial">{{HEADER_TEXT}}</p></center><p>${tracking_str}\;
	$html_code =~ s\<HEAD\<head\gi;
	$html_code =~ s\</BODY>\</body>\;
	$html_code =~ s\</body>\
{{UNSUBSCRIBE}}
{{DISCLAIMER}}\;
    $html_code =~ s\</HTML>\</html>\;
    $html_code =~ s\</html>\\;
	#$html_code .= "<p STYLE=\"font-size:10pt; font-family:arial\">{{FOOTER_TEXT}}</p></body></html>";
	$html_code .= "</body></html>";
	$temp_str="";
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
            #$temp_str = "<a href=\"xxoptoutlinkxx\" target=\"_blank\"><img src=\"$unsub_img\" border=0></a><br><br>";
            $temp_str = "<a href=\"#unsub#\" target=\"_blank\"><img src=\"http://{{IMG_DOMAIN}}/images/$unsub_img\" border=0></a><br><br>";
         }
    }
	$html_code =~ s\{{UNSUBSCRIBE}}\$temp_str\g;	
    $html_code=~s/{{ADV_UNSUB_URL}}/#unsub#/g;
	my $dstr=qq^<img src="http://{{IMG_DOMAIN}}/0jhkanbe9mxp" border="0" style="max-width:100%;max-height:100%;display:block;padding:0px;margin:0px;">^;
	$html_code =~ s\{{DISCLAIMER}}\$dstr\g;	
#
	my $footer_str = $bid;
	$html_code =~ s/{{HEADER_TEXT}}//g;
	$html_code =~ s/{{FOOTER_TEXT}}//g;
	#$html_code =~ s/{{URL}}/ttlinktt/g;
	#$html_code =~ s/{{URL}}/#url#/g;
	my $tstr="#url<c=".$ACAKE[0]."&s1=>#";
	$html_code =~ s/{{URL}}/$tstr/g;
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
    my $i=1;
    while ($i <= 29)
    {
    	$_=$html_code;
        if (/{{URL$i}}/)
        {
			my $tstr="#url<c=".$ACAKE[$i]."&s1=>#";
			$html_code =~ s/{{URL$i}}/$tstr/g;
		}
		$i++;
	}
	my $tmp_dir = "/data5/hitpath/$aid/$creative_name"; 
	mkdir $tmp_dir;
	$global_text=$html_code;
	$IMG={};
	my $p = HTML::LinkExtor->new(\&cb);
	$p->parse($html_code);
	$html_code=$global_text;
	$global_text=$html_code;
	my $p = HTML::LinkExtor->new(\&cb1);
	$p->parse($html_code);
	$html_code=$global_text;
	$html_code=~s/http:\/\/{{IMG_DOMAIN}}\///g;
	open (OUT, "> /data5/hitpath/$aid/$creative_name/${cid}.html");
	print OUT "$html_code";
	close(OUT);
}
$sth1->finish();
my $tname=$aname."-CreativePackage";
$tname=~s/ /-/g;
my @args = ("/var/www/html/newcgi-bin/stuff_adv_images.sh $aid \"$tname\"");
system(@args) == 0 or die "system @args failed: $?";
my $file=$tname.".zip";
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Export Images</title>
</head>

<body>
<b>Advertiser Name: </b>$aname<br>
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
	my $link_id;
	my $ext;
	my $scheme;
	my $auth;
	my $path;
	my $query1;
	my $frag;
	my $suffix;
	my $name;
	my $imgname;

     if (($tag eq "img") or ($tag eq "background") or (($tag eq "img") and ($url1 eq "background")) or (($tag eq "input") and ($url1 eq "src")))
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
		if (($name ne "open_email1\.cgi") and (!$IMG->{$name}))
		{
	        my $repl_url = $scheme . "://" . $auth . $frag;
			my $time_str = time();
	        if ($query1 ne "")
	        {
	        	$repl_url = $repl_url . $name . "?" . $query1;
	        }
	       	my $temp_str;
	        ($temp_str,$ext) = split('\.',$name);
			$imgname=$name;
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
	        open BODY, "> /data5/hitpath/$aid/$creative_name/${name}";
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
            my $file="/data5/hitpath/$aid/$creative_name/$name";
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
                my $outfile="/data5/hitpath/$aid/$creative_name/$tname";
				my @args = ("/var/www/html/newcgi-bin/rename.sh \"$file\" \"$outfile\"");
				system(@args) == 0 or die "system @args failed: $?";
            }
			$global_text=~s/$imgname/$tname/g;
			$IMG->{$imgname}=1;
		}
	}
}
sub cb1
{
     my($tag, $url1, $url2, %links) = @_;
     if ((($tag eq "a") && ($url1 eq "href")) || (($tag eq "area") && ($url1 eq "href")))
     {
        $_ = $url2;
        if (/ccID=/) 
        {
			my $turl=$url2;
			my @F=split("&",$url2);
			my $ind=$#F;
			my $fld=$F[$ind];
			my ($t1,$ccID)=split("=",$fld);
          	$turl="#url<c=".$ccID;  
			$turl=$turl."&s1=>#";
            $url2 =~ s/\?/\\?/g;
            $url2=~ s/\[/\\[/g;
            $global_text =~ s/$url2/$turl/gi;
        }
	}
}
