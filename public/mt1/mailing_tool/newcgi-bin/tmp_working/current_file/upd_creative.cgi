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

my $mode = $query->param('nextfunc');

    #---------------------------------------------------
    # Get the information about the user from the form
    #---------------------------------------------------
    $creative_name = $query->param('creative_name');
    $content_id = $query->param('content_id');
    $original_flag = $query->param('original_flag');
	if ($original_flag eq "")
	{
		$original_flag = "N";
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
	if ($inactive_date ne "")
	{
		my $temp_str;
		$temp_str = $inactive_date;
		$inactive_date = "20" . substr($temp_str,6,2) . "-" . substr($temp_str,0,2) . "-" . substr($temp_str,2,2);
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
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;
	if ($thumbnail ne "")
	{
  		my $upload_filehandle = $query->upload("thumbnail");
		$thumbnail = $cid . "_" . $thumbnail;
		open UPLOADFILE, ">$upload_dir/$thumbnail";  
		while ( <$upload_filehandle> )  
		{    
			print UPLOADFILE;  
		}  
		close UPLOADFILE;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
        my @args = ("${BASE_DIR}newcgi-bin/cp_thumbnail.sh $thumbnail");
        system(@args) == 0 or die "system @args failed: $?";
	}
	else
	{
		$thumbnail = $old_thumbnail;
	}
    $html_code = $query->param('html_code');
    $puserid = $query->param('aid');
    $backto = $query->param('backto');

if ($creative_name ne "")
{
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
&update_creative();
my @args = ("${BASE_DIR}newcgi-bin/get_camp_new.pl","$cid");
system(@args) == 0 or die "system @args failed: $?";
my @args = ("${BASE_DIR}newcgi-bin/get_camp_3rdparty.pl","$cid");
system(@args) == 0 or die "system @args failed: $?";
}

# go to next screen

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
    var newwin = window.open("/cgi-bin/camp_preview.cgi?campaign_id=$cid&format=H", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
	$pmesg="";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_creative.cgi?aid=$puserid&cid=$cid&rid=$rid&backto=$backto";
</script>
end_of_html
}
elsif ($mode eq "spam")
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("/cgi-bin/camp_www_spam.cgi?cid=$cid&format=H&rid=$rid", "Spam", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=25,top=50");
    newwin.focus();
    </script> \n };
	$pmesg="";
print<<"end_of_html";
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/edit_creative.cgi?aid=$puserid&cid=$cid&rid=$rid&backto=$backto";
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
else
{
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
	$img_dir = get_name() . "_" . $cid;
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
    if ($img_added == 1)
    {
        my @args = ("${BASE_DIR}newcgi-bin/cp_img.sh $img_dir");
        system(@args) == 0 or die "system @args failed: $?";
    }

	if ($mode eq "url")
	{
		$html_code =~ s/\&sub=/\&XXX=/g;
 		$global_text = $html_code;
    	my $p = HTML::LinkExtor->new(\&cb1);
    	$p->parse($html_code);
    	$html_code = $global_text;
	}
    $creative_name =~ s/'/''/g;
	$sql = "update creative set creative_name='$creative_name',original_flag='$original_flag',trigger_flag='$trigger_flag',creative_date='$creative_date',inactive_date='$inactive_date',unsub_image='$unsub_image',default_subject=$default_subject,default_from=$default_from,image_directory='$image_directory',thumbnail='$thumbnail',html_code='$html_code',content_id=$content_id where creative_id=$cid";
	$sth = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
	    $pmesg = "Error - Updating creative record: $errmsg";
	}
	else
	{
	    $pmesg = "Successful UPDATE of Creative Info!" ;
	}

	$pmode = "U" ;

}  # end sub - update_creative

sub get_name
{
srand(rand time());
my @c=split(/ */, "bcdfghjklmnprstvwxyz");
my @v=split(/ */, "aeiou");
my $sname;
my $i;
$sname = $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
return $sname;
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

     if (($tag eq "img") or ($tag eq "background") or ($url1 eq "background"))
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
                $name = get_name() . "_${time_str}_${img_cnt}.gif";
            }
            else
            {
                my $temp_str;
                ($temp_str,$ext) = split('\.',$name);
                $new_name = get_name() . "_${img_cnt}.${ext}";
            }
            my $curl = WWW::Curl::easy->new();
            $curl->setopt(CURLOPT_NOPROGRESS, 1);
            $curl->setopt(CURLOPT_MUTE, 0);
            $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
            $curl->setopt(CURLOPT_TIMEOUT, 30);
            open HEAD, ">head.out";
            $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
            open BODY, "> /var/www/util/tmpimg/$new_name";
            $curl->setopt(CURLOPT_FILE,*BODY);
            $curl->setopt(CURLOPT_URL, $url2);
            my $retcode=$curl->perform();
            if ($retcode == 0)
            {
            }
            else
            {
   # We can acces the error message in $errbuf here
#    print STDERR "$retcode / ".$curl->errbuf."\n";
#    print "not ";
            }
            close HEAD;
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
