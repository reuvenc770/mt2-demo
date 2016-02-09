#!/usr/bin/perl -C
#===============================================================================
# Purpose: Update creative info - (eg table 'creative' data).
# Name   : upd_creative.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/12/05  Jim Sobeck  Creation
#===============================================================================

# include Perl Modules
use strict;
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
my $tmesg="";
my $images = $util->get_images_url;
my $creative_name ;
my $BASE_DIR;
my $sth1;
my $original_flag ;
my $copywriter;
my $copywriter_name;
my $catcnt;
my $replace_flag;
my $content_id;
my $header_id;
my $body_id;
my @style_id;
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
my $upload_dir = "/var/www/util/creative";
my $global_text;
my $img_cnt;
my $comm_wizard_c3;
my $comm_wizard_cid;
my $comm_wizard_progid;
my $cr;
my $landing_page;
$img_cnt = 0;

$pmesg="";
srand();
my $rid=rand();
my $cstatus;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my $data={};
$data->{'imageCollectionID'}="000000000000001";
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.sav";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);

my $mode = $query->param('nextfunc');

my $host_images=$query->param('host_images');
if ($host_images eq "")
{
	$host_images='N';
}
    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $creative_name = $query->param('creative_name');
    $content_id = $query->param('content_id');
    $header_id = $query->param('header_id');
    $body_id = $query->param('body_id');
	@style_id=$query->param('styleID');
    $original_flag = $query->param('original_flag');
    $copywriter_name= $query->param('copywriter_name');
    $copywriter= $query->param('copywriter');
	my $sub=$query->param('default_subject');
	my $from=$query->param('default_from');
	if ($original_flag eq "")
	{
		$original_flag = "N";
	}
	if ($copywriter eq "")
	{
		$copywriter = "N";
	}
	if ($copywriter eq "N")
	{
		$copywriter_name = "";
	}
    $comm_wizard_c3=$query->param('comm_wizard_c3');
    $comm_wizard_cid=$query->param('comm_wizard_cid');
    $comm_wizard_progid=$query->param('comm_wizard_progid');
    $cr=$query->param('cr');
    $landing_page=$query->param('landing_page');
    $replace_flag= $query->param('replace_flag');
	if ($replace_flag eq "")
	{
		$replace_flag = "Y";
	}
    $trigger_flag = $query->param('trigger_flag');
	if ($trigger_flag eq "")
	{
		$trigger_flag = "N";
	}
    $creative_date= $query->param('creative_date');
    my $temp_str = $creative_date;
    $creative_date = "20" . substr($temp_str,6,2) . "-" . substr($temp_str,0,2) . "-" . substr($temp_str,3,2);
    $inactive_date = $query->param('inactive_date');
	if ($inactive_date ne "" && $inactive_date ne '00/00/00')
	{
		my $temp_str;
		$temp_str = $inactive_date;
		$inactive_date = "20" . substr($temp_str,6,2) . "-" . substr($temp_str,0,2) . "-" . substr($temp_str,3,2);
	}
    $unsub_image = $query->param('unsub_image');
    $default_subject = $query->param('default_subject');
	if ($default_subject eq "")
	{
		$default_subject=0;
	}
    $default_from = $query->param('default_from');
	if ($default_from eq "")
	{
		$default_from=0;
	}
    $image_directory = $query->param('image_directory');
    $thumbnail = $query->param('thumbnail');
    $old_thumbnail = $query->param('old_thumbnail');
 	$thumbnail =~ s/.*[\/\\](.*)/$1/;
    $cid = $query->param('cid');
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
	if ($thumbnail ne "")
	{
  		my $upload_filehandle = $query->upload("thumbnail");
		$thumbnail =~ s/ /_/g; 
		$thumbnail = util::get_name(). "_" . $cid . "_" . $thumbnail;
		my $t1=substr($thumbnail,0,1);
		my $t2=substr($thumbnail,1,1);
		my $t3=substr($thumbnail,2,1);
		my $t4=$upload_dir."/".$t1;
		mkdir $t4;
		$t4=$upload_dir."/".$t1."/".$t2;
		mkdir $t4;
		$t4=$upload_dir."/".$t1."/".$t2."/".$t3;
		mkdir $t4;
		$thumbnail=$t1."/".$t2."/".$t3."/".$thumbnail;
		open UPLOADFILE, ">$upload_dir/$thumbnail";  
		while ( <$upload_filehandle> )  
		{    
			print UPLOADFILE;  
		}  
		close UPLOADFILE;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
	}
	else
	{
		$thumbnail = $old_thumbnail;
	}
    $html_code = $query->param('html_code');
    $puserid = $query->param('aid');
    $backto = $query->param('backto');

	# get the link for tonic if cwc3 set
	if ($comm_wizard_c3 ne "")
	{
		my $advurl;
		$sql="select url from advertiser_tracking where advertiser_id=? and daily_deal='N' and client_id=1";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($puserid);
		($advurl)=$sth1->fetchrow_array();
		$sth1->finish();
		$_=$advurl;
#		if (/\&c3={{EMAIL_ADDR}}/)
#		{
#			my $aname;
#			$sql="select advertiser_name from advertiser_info where advertiser_id=?"; 
#			my $sth1=$dbhq->prepare($sql);
#			$sth1->execute($puserid);
#			($aname)=$sth1->fetchrow_array();
#			$sth1->finish();
#			$tmesg="The track link for $aname ($puserid) needs to have c3 removed.";
#        	open (MAIL,"| /usr/sbin/sendmail -t");
#        	my $from_addr = "C3 in Link <info\@zetainteractive.com>";
#        	print MAIL "From: $from_addr\n";
#        	print MAIL "To: setup\@zetainteractive.com\n";
#        	print MAIL "Subject: $aname ($puserid) has c3 in link\n";
#        	my $date_str = $util->date(6,6);
#        	print MAIL "Date: $date_str\n";
#        	print MAIL "X-Priority: 1\n";
#        	print MAIL "X-MSMail-Priority: High\n";
#        	print MAIL "$tmesg\n";
#        	close MAIL;
#		}
	}
	
	$sql="select count(*) from advertiser_info ai, category_info ci where advertiser_id=? and ai.category_id=ci.category_id and ci.category_name='FR'";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($puserid);
	($catcnt)=$sth1->fetchrow_array();
	$sth1->finish();
if ($creative_name ne "")
{
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
if ($mode) {
	&update_creative();
}
my @args = ("${BASE_DIR}newcgi-bin/get_camp_new.pl","$cid");
system(@args) == 0 or die "system @args failed: $?";
#my @args = ("${BASE_DIR}newcgi-bin/get_camp_3rdparty.pl","$cid");
#system(@args) == 0 or die "system @args failed: $?";
}

# go to next screen

$util->clean_up();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
end_of_html
if ($mode eq "preview")
{
	if ($tmesg ne "")
	{
		$pmesg=$tmesg;
	}
	$pmesg=~s///g;
	$pmesg=~s/\n//g;
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/camp_preview.cgi?campaign_id=$cid&format=H", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_creative.cgi?aid=$puserid&cid=$cid&rid=$rid&backto=$backto&pmesg=$pmesg";
</script>
end_of_html
}
elsif ($mode eq "spam")
{
	if ($tmesg ne "")
	{
		$pmesg=$tmesg;
	}
	$pmesg=~s///g;
	$pmesg=~s/\n//g;
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/camp_www_spam.cgi?cid=$cid&aid=$puserid&subject=$sub&from=$from&format=H&rid=$rid", "Spam", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
	$pmesg="Spam Report generated!";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_creative.cgi?aid=$puserid&cid=$cid&rid=$rid&backto=$backto&pmesg=$pmesg";
</script>
end_of_html
}
elsif ($mode eq "render")
{
	if ($tmesg ne "")
	{
		$pmesg=$tmesg;
	}
	$pmesg=~s///g;
	$pmesg=~s/\n//g;
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/camp_render.cgi?campaign_id=$cid&format=H", "Render", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
	$pmesg="";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_creative.cgi?aid=$puserid&cid=$cid&rid=$rid&backto=$backto&pmesg=$pmesg";
</script>
end_of_html
}
elsif ($mode eq "home")
{
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/mainmenu.cgi?rid=$rid";
</script>
end_of_html
}
else
{
if ($backto eq "creative")
{
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/creative_list.cgi?rid=$rid";
</script>
end_of_html
}
elsif ($backto eq "home")
{
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/mainmenu.cgi?rid=$rid";
</script>
end_of_html
}
else
{
	if ($tmesg ne "")
	{
		$pmesg=$tmesg;
	}
	$pmesg=~s///g;
	$pmesg=~s/\n//g;
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$puserid&pmesg=$pmesg&rid=$rid";
</script>
end_of_html
}
}
print<<"end_of_html";
</body>
</html>
end_of_html
exit(0);

sub update_creative
{
	my $rows;

	$img_added = 0;
    $html_code =~ s/src = "/src="/g;
    $html_code =~ s/src= "/src="/g;
    $html_code =~ s/src ="/src="/g;
 	$global_text = $html_code;
	if ($host_images eq "Y")
	{
   		my $p = HTML::LinkExtor->new(\&cb);
   		$p->parse($html_code);
	}
   	$html_code = $global_text;
	$html_code =~ s//'/g;
	$html_code =~ s/\x60/\x27/g;
	$html_code =~ s/'/''/g;
#    if ($img_added == 1)
#    {
#        my @args = ("${BASE_DIR}newcgi-bin/cp_img.sh $img_dir");
#        system(@args) == 0 or die "system @args failed: $?";
#    }

	if (($replace_flag eq "Y") or ($mode eq "url"))
	{
		$html_code =~ s/\&sub=/\&XXX=/g;
		$html_code =~ s/\&amp;/\&/g;
 		$global_text = $html_code;
    	my $p = HTML::LinkExtor->new(\&cb1);
    	$p->parse($html_code);
    	$html_code = $global_text;
	}
    $creative_name =~ s/'/''/g;
my $first = index($html_code, "{{");
my $end;
my $i;
my $tstr;
my $notfound;
while ($first >= 0)
{
	$end=index($html_code,"}}",$first+1);
	if ($end >= 0)
	{
		$tstr=substr($html_code,$first,$end-$first+2);
		if (!util::CheckTokens($tstr))
		{
			$pmesg="One or more bad Variables specified - please fix - $tstr";
		}
		$first = index($html_code,"{{",$end+1);
	}
	else
	{
		$tstr=substr($html_code,$first);
		$pmesg="One or more bad Variables specified - please fix";
		$first=index($html_code,"{{",$first+1);
	}
}

my $bad_chars=0;
if ($pmesg eq "")
{
	if ($catcnt == 0)
	{
	if(util::isValidChars($html_code))
	{
	}
	else
	{
		$bad_chars=1;
		$pmesg="One or more invalid characters in creative has been replaced with X";	
		$html_code=util::replaceBadChars($html_code);
	}
	}
}

if (($pmesg eq "") or ($bad_chars == 1))
{
    my $temp_str=$html_code;
    $temp_str=~s/{{NAME}}//g;
    $temp_str=~tr/A-Z/a-z/;
    $_=$temp_str;
#    if (/ name /)
#    {
#        $pmesg="The keyword 'name' was found in creative - please fix";
#    }
}
if (($pmesg eq "") or ($bad_chars == 1))
{
	my $style_str="";
	if ($style_id[1]) {
		foreach (@style_id) {
			$style_str.="$_,";
		}
	}
	else { $style_str=$style_id[0]; }
	$style_str=~s/,$//;
	#
	# log all changes
	$sql="insert into creativeChangeLog select curdate(),$user_id,curtime(),c.* from creative c where creative_id=$cid";
	my $rows=$dbhu->do($sql);
	$sql = "update creative set creative_name='$creative_name',original_flag='$original_flag',trigger_flag='$trigger_flag',creative_date='$creative_date',inactive_date='$inactive_date',unsub_image='$unsub_image',default_subject=$default_subject,default_from=$default_from,image_directory='$image_directory',thumbnail='$thumbnail',html_code='$html_code',content_id=$content_id,header_id=$header_id,body_content_id=$body_id,style_id='$style_str',replace_flag='$replace_flag',comm_wizard_c3='$comm_wizard_c3',comm_wizard_cid='$comm_wizard_cid',comm_wizard_progid='$comm_wizard_progid',cr='$cr',landing_page='$landing_page',copywriter='$copywriter',copywriter_name='$copywriter_name',host_images='$host_images' where creative_id=$cid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
	    $pmesg = "Error - Updating creative record: $errmsg";
	}
	else
	{
		if ($inactive_date ne "" && $inactive_date ne '00/00/00')
		{
			my $dcnt;
			$sql="select status,datediff(curdate(),'$inactive_date') from creative where creative_id=?";
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute($cid);
			($cstatus,$dcnt) = $sth1->fetchrow_array();
			$sth1->finish;
			if (($cstatus eq "A") and ($dcnt > 0))
			{
				$sql="update creative set status='I' where creative_id=$cid";
				$sth = $dbhu->do($sql);
			}
		}
		if ($pmesg eq "")
		{
	    	$pmesg = "Successful UPDATE of Creative Info!" ;
		}
	}
}
#if (($mode eq "preview") or ($mode eq "spam"))
#{
#	$pmesg="";
#}
$_ = $html_code;
if (((/\.com/) || (/\.COM/)) && ($pmesg eq ""))
{
    $pmesg = "Error - .com still in creative";
}
	if ((/affiliateimages/) && ($pmesg eq ""))
	{
	    $pmesg = "Error - affiliateimages still in creative";
	}
	$_ = $html_code;
	if (/{{URL}}/) 
	{
	}
	else
	{
		if ($replace_flag eq "Y")
		{
	    	$pmesg = $pmesg." Error - no {{URL}} in creative";
		}
	}

	$pmode = "U" ;

}  # end sub - update_creative


sub cb 
{
     my($tag, $url1, $url2, %links) = @_;
	my $query1;
	my $temp_id;
	my $sql;
	my $sth1;
	my $link_id;
	my $ext;
	my $cdir;

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
           		$global_text =~ s/$repl_url${name}/http:\/\/{{IMG_DOMAIN}}\/$new_name/gi;
			}
			else
			{
           		$global_text =~ s/$repl_url/http:\/\/{{IMG_DOMAIN}}\/$new_name/gi;
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
		if ((/{{URL}}/) or (/{{ADV_UNSUB_URL}}/))
		{
			$temp_id = 0;
		}
        elsif ($url2 eq "")
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

sub log_img
{
    my ($url2,$new_name,$img_dir)=@_;
    my $filename="/tmp/img_upload.log";
    open(LOG,">>$filename");
    print LOG "Uploading <$url2> to <$new_name> directory <$img_dir>\n";
    close(LOG);
}

