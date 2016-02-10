#!/usr/bin/perl
#===============================================================================
# Purpose: Update creative info - (eg table 'creative' data).
# Name   : upd_creative.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/12/05  Jim Sobeck  Creation
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
my $upload_dir = "/var/www/util/creative";
my $global_text;
my $img_cnt;
my @var=("{{NAME}}","{{LOC}}","{{EMAIL_USER_ID}}","{{EMAIL_ADDR}}","{{URL}}","{{IMG_DOMAIN}}","{{DOMAIN}}","{{CID}}","{{FID}}","{{CRID}}","{{FOOTER_SUBDOMAIN}}","{{FOOTER_DOMAIN}}","{{FROMADDR}}","{{MAILDATE}}","{{FOOTER_TEXT}}","{{DATE}}","{{ADVERTISER_NAME}}");
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
$ENV{'IMAGE_HOSTER_SSH_KEY'}="/var/www/.ssh/images.key";
my $imageHoster = App::WebAutomation::ImageHoster->new($data);

my $mode = $query->param('nextfunc');
my $completed= $query->param('completed');
if ($completed eq "")
{
	$completed="N";
}	

#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $creative_name = $query->param('creative_name');
    my $notes = $query->param('notes');
	$notes=$dbhq->quote($notes);
    $inactive_date = $query->param('inactive_date');
	if ($inactive_date ne "")
	{
		my $temp_str;
		$temp_str = $inactive_date;
		$inactive_date =~ s/\\/-/g; 
	}
    $thumbnail = $query->param('thumbnail');
    $old_thumbnail = $query->param('old_thumbnail');
 	$thumbnail =~ s/.*[\/\\](.*)/$1/;
    $cid = $query->param('cid');
	if ($thumbnail ne "")
	{
  		my $upload_filehandle = $query->upload("thumbnail");
		$thumbnail =~ s/ /_/g; 
		$thumbnail = util::get_name(). "_D_" . $cid . "_" . $thumbnail;
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
    $backto = $query->param('backto');

# go to next screen
&update_creative();
$util->clean_up();
print "Content-Type: text/plain\n\n";
print<<"end_of_html";
<html>
<head>
end_of_html
if ($mode eq "preview")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/camp_draft_preview.cgi?campaign_id=$cid&format=H", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_draft.cgi?cid=$cid&rid=$rid&backto=$backto&pmesg=$pmesg";
</script>
end_of_html
}
elsif ($mode eq "spam")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/camp_www_spam.cgi?cid=$cid&format=H&rid=$rid&cdraft=Y", "Spam", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_draft.cgi?cid=$cid&rid=$rid&backto=$backto&pmesg=$pmesg";
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
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/draft.html";
</script>
end_of_html
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
#	$img_dir = util::get_name() . "_" . $cid;
#   	my $p = HTML::LinkExtor->new(\&cb);
#   	$p->parse($html_code);
#   	$html_code = $global_text;
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
#        my @args = ("${BASE_DIR}newcgi-bin/cp_img.sh $img_dir");
#        system(@args) == 0 or die "system @args failed: $?";
#    }

    $creative_name =~ s/'/''/g;
my $first = index($html_code, "{");
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
		$i=0;
		$notfound=0;
		while (($i <= $#var) && ($notfound == 0))
		{
			if ($tstr eq $var[$i])
			{
				$notfound=1;
			}
			$i++;
		}
		if ($notfound == 0)
		{
			$pmesg="One or more bad Variables specified - please fix - $tstr";
		}
		$first = index($html_code,"{",$end+1);
	}
	else
	{
		$tstr=substr($html_code,$first);
		$pmesg="One or more bad Variables specified - please fix";
		$first=index($html_code,"{",$first+1);
	}
}
	if ($pmesg eq "")
	{
	$sql = "update draft_creative set creative_name='$creative_name',inactive_date='$inactive_date',thumbnail='$thumbnail',html_code='$html_code',completed='$completed',notes=$notes,updated_date=curdate() where creative_id=$cid";
	$sth = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
	    $pmesg = "Error - Updating creative record: $errmsg";
	}
	else
	{
	    $pmesg = "Successful UPDATE of Creative Info!" ;
	}
	}
	if (($mode eq "preview") or ($mode eq "spam"))
	{
		$pmesg="";
	}
	$_ = $html_code;
	if (((/\.com/) || (/\.COM/)) && ($pmesg eq ""))
	{
	    $pmesg = "Error - .com still in creative";
	}
    $_ = $html_code;
    if (/{{URL}}/)
    {
    }
    else
    {
        $pmesg = $pmesg." Error - no {{URL}} in creative";
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
            	$global_text =~ s/$repl_url${name}/http:\/\/{{IMG_DOMAIN}}\/${img_dir}\/$new_name/gi;
			}
			else
			{
           			$global_text =~ s/$repl_url/http:\/\/{{IMG_DOMAIN}}\/${img_dir}\/$new_name/gi;
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
