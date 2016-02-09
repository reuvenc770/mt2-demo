#!/usr/bin/perl
#===============================================================================
# Purpose: Update creative info - (eg table 'creative' data).
# Name   : replace_url.cgi 
#
#--Change Control---------------------------------------------------------------
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
my $cid;
my ($scheme, $auth, $path, $frag);
my $name;
my $suffix;
my $img_added;
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

my $aid= $query->param('aid');
#------ connect to the util database ------------------
$util->db_connect();
$dbh = 0;
while (!$dbh)
{
print LOG "Connecting to db\n";
$dbh = $util->get_dbh;
}
$dbh->{mysql_auto_reconnect}=1;
$sql = "select parmval from sysparm where parmkey='BASE_DIR'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($BASE_DIR) = $sth1->fetchrow_array();
$sth1->finish;
$sql = "select creative_id,html_code from creative where advertiser_id=$aid and status='A'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
while (($cid,$html_code) = $sth1->fetchrow_array())
{
	update_creative();
	my @args = ("${BASE_DIR}newcgi-bin/get_camp_new.pl","$cid");
	system(@args) == 0 or die "system @args failed: $?";
	my @args = ("${BASE_DIR}newcgi-bin/get_camp_3rdparty.pl","$cid");
	system(@args) == 0 or die "system @args failed: $?";
}
$sth1->finish;

$util->clean_up();
print "Content-Type: text/plain\n\n";
print<<"end_of_html";
<html>
<head>
<head>
<body>
<script language="JavaScript">
document.location="/cgi-bin/advertiser_disp2.cgi?pmode=$pmode&puserid=$aid&pmesg=$pmesg&rid=$rid";
</script>
</body>
</html>
end_of_html
exit(0);

sub update_creative
{
	my $rows;

    $html_code =~ s/'/''/g;
	$html_code =~ s/\&sub=/\&XXX=/g;
 	$global_text = $html_code;
   	my $p = HTML::LinkExtor->new(\&cb1);
   	$p->parse($html_code);
   	$html_code = $global_text;
	$sql = "update creative set html_code='$html_code' where creative_id=$cid";
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
