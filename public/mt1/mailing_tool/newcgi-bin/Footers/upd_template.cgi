#!/usr/bin/perl

# *****************************************************************************************
# upd_tempalte.cgi
#
# this page updates information in the brand_template table
#
# History
# Jim Sobeck, 06/04/07, Creation
# *****************************************************************************************

# include Perl Modules
use lib('/var/www/html/newcgi-bin');
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
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $img_cnt=1;
my $images = $util->get_images_url;
my $camp_id;
my $pmesg;
my $rid;
my $img_dir;
my $global_text;
my ($scheme, $auth, $path, $frag);
my $name;
my $new_name;
my $suffix;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}



my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

#
# Get the information about the user from the form 
#
my $temp_id = $query->param('temp_id');
my $backto = $query->param('backto');
my $template_name = $query->param('template_name');
$template_name=~ s/'/''/g; 
my $nl_template= $query->param('nl_template');
if ($nl_template ne "")
{
}
else
{
	$nl_template='';
}
$nl_template=~ s/src = "/src="/g;
$nl_template=~ s/src= "/src="/g;
$nl_template =~ s/src ="/src="/g;
$global_text = $nl_template;
$img_dir = util::get_name() . "_" . $temp_id;
my $p = HTML::LinkExtor->new(\&cb);
$p->parse($nl_template);
$nl_template= $global_text;
$nl_template=~ s/'/''/g; 
#
#
if ($temp_id > 0)
{
	$sql = "update Footers set footer_name='$template_name',footer_code='$nl_template' where $userDataRestrictionWhereClause footer_id=$temp_id"; 
}
else
{
	$sql="insert into Footers(userID, footer_name,dateAdded,status,footer_code) values($user_id, '$template_name',curdate(),'A','$nl_template')"; 
}
$sth = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	my $errmsg = $dbhu->errstr();
   	util::logerror("Updating Footers record for $sql $user_id: $errmsg");
}
else
{
	if ($temp_id == 0)
	{
		$sql="select max(footer_id) from Footers where $userDataRestrictionWhereClause footer_name='$template_name' and status='A'";
		$sth=$dbhu->prepare($sql);
		$sth->execute();
		($temp_id)=$sth->fetchrow_array();
		$sth->finish();
	}

		print "Location: index.cgi\n\n";

}
$util->clean_up();
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
                $name = util::get_name() . "_${time_str}_${img_cnt}.gif";
                ($cdir,$new_name) = util::bld_img("gif");
				$ext="gif";
            }
            else
            {
                my $temp_str;
                ($temp_str,$ext) = split('\.',$name);
				if ($ext eq "")
				{
					$ext="jpg";
				}
                ($cdir,$new_name) = util::bld_img($ext);
            }
            my $curl = WWW::Curl::easy->new();
            $curl->setopt(CURLOPT_NOPROGRESS, 1);
#            $curl->setopt(CURLOPT_MUTE, 0);
            $curl->setopt(CURLOPT_FOLLOWLOCATION, 1);
            $curl->setopt(CURLOPT_TIMEOUT, 30);
            open HEAD, ">/tmp/head_${new_name}.out";
            $curl->setopt(CURLOPT_WRITEHEADER, *HEAD);
            open BODY, "> /var/www/util/creative/$cdir/${new_name}.$ext";
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
            my $info = $curl->getinfo(CURLINFO_HTTP_CODE);
            if ($info >= 400)
            {
                unlink("/var/www/util/creative/$cdir/${new_name}.$ext");
                $pmesg = "Error - One or more images could not be retrieved: ".$
url2;
            }
			else
			{
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
			}
        }
	}
}
