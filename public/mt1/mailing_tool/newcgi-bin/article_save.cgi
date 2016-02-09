#!/usr/bin/perl

# *****************************************************************************************
# article_save.cgi
#
# this page inserts/updates information in the article table 
#
# History
# *****************************************************************************************

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
my $sth;
my $sql;
my $dbh;
my $sid;
my $errmsg;
my $images = $util->get_images_url;
my $year;
my $mon;
my $mday;
my $global_text;

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
my $content_id = $query->param('content_id');
my $nextfunc= $query->param('nextfunc');
my $content_name = $query->param('content_name');
$content_name=~s/'/''/g;
my $content_date= $query->param('content_date');
my $inactive_date = $query->param('inactive_date');
my $article_font = $query->param('article_font');
my $category_id= $query->param('category_id');
my $article_headline = $query->param('article_headline');
my $article_source= $query->param('article_source');
$article_headline=~s/'/''/g;
my $article_author = $query->param('article_author');
my $html_code = $query->param('html_code');
$html_code =~ s/\&sub=/\&XXX=/g;
$global_text = $html_code;
my $p = HTML::LinkExtor->new(\&cb1);
$p->parse($html_code);
$html_code = $global_text;
$html_code=~s/&/&amp;/g;
$html_code=~s/"/&quot;/g;
$html_code=~ s/s*=s*&quot;(.*?)&quot;/="$1"/gi; 
$html_code=~s/'/&#39;/g;
$html_code=~ s/'/''/g;
#
# Insert record into article 
#
my $tdate;
($mon,$mday,$year) = split('\/',$content_date);
if (length($year) == 2)
{
	$year = "20" . $year;
}
$tdate = $year . "-" . $mon . "-" . $mday;
my $idate;
if ($inactive_date eq "")
{
	$idate="0000-00-00";
}
else
{
	($mon,$mday,$year) = split('\/',$inactive_date);
	if (length($year) == 2)
	{
		$year = "20" . $year;
	}
	$idate = $year . "-" . $mon . "-" . $mday;
}
if ($content_id == 0)
{
	$sql = "insert into article(article_name,date_of_content,inactive_date,html_code,status,article_font,headline,author,datatype_id,source_id) values('$content_name','$tdate','$idate','$html_code','A','$article_font','$article_headline','$article_author',$category_id,$article_source)";
	$sth = $dbhu->do($sql);
}
else
{
	$sql = "update article set article_name='$content_name',date_of_content='$tdate',inactive_date='$idate',html_code='$html_code',article_font='$article_font',headline='$article_headline',author='$article_author',datatype_id=$category_id,source_id=$article_source where article_id=$content_id";
	$sth = $dbhu->do($sql);
}
#
# Display the confirmation page
#
if ($nextfunc eq "")
{
	print "Location: /cgi-bin/article_list.cgi\n\n";
}
else
{
	print "Location: $nextfunc\n\n";
}
$util->clean_up();
exit(0);

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
