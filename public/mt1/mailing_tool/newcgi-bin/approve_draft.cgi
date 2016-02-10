#!/usr/bin/perl
#===============================================================================
# Purpose: Approve draft and move to creative table 
# Name   : approve_draft.cgi 
#
#--Change Control---------------------------------------------------------------
# 05/01/06  Jim Sobeck  Creation
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;
use App::WebAutomation::ImageHoster;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my ($pmesg, $old_email_addr) ;
my $images = $util->get_images_url;
my $creative_name ;
my $BASE_DIR;
my $sth1;
my $rows;
my $original_flag ;
my $replace_flag;
my $content_id;
my $cid;
my ($scheme, $auth, $path, $frag);
my $name;
my $new_name;
my $suffix;
my $img_added;
my $img_dir;
my $backto;
my $trigger_flag ;
my $creative_date;
my $inactive_date ;
my $unsub_image ;
my $default_subject ;
my $default_from ;
my $image_directory ;
my $thumbnail ;
my $old_thumbnail;
my $html_code ;
my $puserid;
my $pmode;
my $upload_dir = "/var/www/util/tmp";
my $global_text;
my $img_cnt;
my @var=("{{NAME}}","{{LOC}}","{{EMAIL_USER_ID}}","{{EMAIL_ADDR}}","{{URL}}","{{IMG_DOMAIN}}","{{DOMAIN}}","{{CID}}","{{FID}}","{{CRID}}","{{FOOTER_SUBDOMAIN}}","{{FOOTER_DOMAIN}}","{{FROMADDR}}","{{MAILDATE}}","{{FOOTER_TEXT}}","{{DATE}}");
$img_cnt = 0;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;
my $aid;
my $cname;
my $html_code;
my $inactive_date;
my $thumbnail;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my $data={};
$data->{'imageCollectionID'}="000000000000001";
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.key";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);

my $cid = $query->param('cid');

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();

$sql="select advertiser_id,creative_name,html_code,inactive_date,thumbnail from draft_creative where creative_id=?";
$sth=$dbhq->prepare($sql);
$sth->execute($cid);
if (($aid,$cname,$html_code,$inactive_date,$thumbnail) = $sth->fetchrow_array())
{
	$img_added = 0;
    $html_code =~ s/src = "/src="/g;
    $html_code =~ s/src= "/src="/g;
    $html_code =~ s/src ="/src="/g;
 	$global_text = $html_code;
	$img_dir = util::get_name() . "_" . $cid;
   	my $p = HTML::LinkExtor->new(\&cb);
   	$p->parse($html_code);
   	$html_code = $global_text;
	$html_code =~ s//'/g;
    $html_code =~ s/\xc2//g;
    $html_code =~ s/\xa0//g;
    $html_code =~ s/\xb7//g;
    $html_code =~ s/\x85//g;
    $html_code =~ s/\x95//g;
    $html_code =~ s/\xae//g;
    $html_code =~ s/\x99//g;
    $html_code =~ s/\xa9//g;
    $html_code =~ s/\x92//g;
    $html_code =~ s/\x93//g;
    $html_code =~ s/\x94//g;
    $html_code =~ s/\x95//g;
    $html_code =~ s/\x96//g;
    $html_code =~ s/\x97//g;
    $html_code =~ s/\x82//g;
    $html_code =~ s/\x85//g;
	$html_code =~ s/'/''/g;
#    if ($img_added == 1)
#    {
#		$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
#		$sth1 = $dbhq->prepare($sql);
#		$sth1->execute();
#		($BASE_DIR) = $sth1->fetchrow_array();
#		$sth1->finish;
#        my @args = ("${BASE_DIR}newcgi-bin/cp_img.sh $img_dir");
#        system(@args) == 0 or die "system @args failed: $?";
#    }

	$html_code =~ s/\&sub=/\&XXX=/g;
 	$global_text = $html_code;
   	my $p = HTML::LinkExtor->new(\&cb1);
   	$p->parse($html_code);
   	$html_code = $global_text;
	$sql="insert into creative(advertiser_id,status,creative_name,original_flag,creative_date,inactive_date,thumbnail,html_code) values($aid,'A','$cname','Y',curdate(),'$inactive_date','$thumbnail','$html_code')";
	$rows=$dbhu->do($sql);
	$sql="update draft_creative set status='C' where creative_id=$cid";
	$rows=$dbhu->do($sql);
}
$sth->finish();
print "Content-Type: text/plain\n\n";
print<<"end_of_html";
<html>
<head>
<body>
<script language="JavaScript">
document.location="/draft.html";
</script>
end_of_html


sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background") or (($tag eq "input") and ($url1 eq "src")))
     {
        $_ = $url2;
        if ((/DOMAIN/) || (/IMG_DOMAIN/))
        {
            my $nomove= 1;
        }
        else
        {
            #
            # Get directory and filename
            #
            ($scheme, $auth, $path, $query1, $frag) = uri_split($url2);
            ($name,$frag,$suffix) = fileparse($path);
            my $repl_url = $scheme . "://" . $auth . $frag;
            $img_cnt++;
			my $time_str = time();
            if ($query1 ne "")
            {
                $repl_url = $repl_url . $name . "?" . $query1;
            }
			my $params={};
			$params->{'image'}=$url2;
			my ($imageHostingErrors, $newImageName, $imageExtension, $allImageProperties) = $imageHoster->setUpImageHosting($params);
			$new_name=$newImageName;
            $repl_url =~ s/\?/\\?/g;
            $repl_url =~ s/\&/\\&/g;
            $repl_url =~ s/\(/\\(/g;
            $repl_url =~ s/\)/\\)/g;
			if ($query1 eq "")
			{
            	$global_text =~ s/$repl_url${name}/http:\/\/{{IMG_DOMAIN}}\/images\/${img_dir}\/$new_name/gi;
			}
			else
			{
            	$global_text =~ s/$repl_url/http:\/\/{{IMG_DOMAIN}}\/images\/${img_dir}\/$new_name/gi;
			}
			$img_added = 1;
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
		if (/{{URL}}/)
		{
			$temp_id = 0;
		}
		else
		{
			$url2 =~ s/\?/\\?/g;
			$url2=~ s/\[/\\[/g;
            $global_text =~ s/"$url2"/"{{URL}}" target=_blank/gi;
            $global_text =~ s/$url2/"{{URL}}" target=_blank/gi;
		}
	 }
}
