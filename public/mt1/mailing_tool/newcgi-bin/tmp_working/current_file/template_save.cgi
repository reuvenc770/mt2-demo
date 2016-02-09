#!/usr/bin/perl

# *****************************************************************************************
# template_save.cgi
#
# this page saves the template changes
#
# History
# Grady Nash, 8/22/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $mode = $query->param('mode');
my $count;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get fields from the form

my $template_name = $dbh->quote($query->param('template_name'));
my $html_template = $dbh->quote($query->param('html_template'));
my $text_template = $dbh->quote($query->param('text_template'));
my $aol_template = $dbh->quote($query->param('aol_template'));
my $show_image_url = $checked{$query->param('show_image_url')};
my $show_title = $checked{$query->param('show_title')};
my $show_subtitle = $checked{$query->param('show_subtitle')};
my $show_date_str = $checked{$query->param('show_date_str')};
my $show_greeting = $checked{$query->param('show_greeting')};
my $show_introduction = $checked{$query->param('show_introduction')};
my $show_closing = $checked{$query->param('show_closing')};
my $num_articles = $query->param('num_articles');
#my $show_promo = $checked{$query->param('show_promo')};
my $show_promo = "N";
my $show_article_title = "Y";
my $show_article_text = "Y";
my $show_article_link = "Y";
my $show_article_image_url = "Y";
my $image_url = $dbh->quote($query->param('image_url'));
my $title = $dbh->quote($query->param('title'));
my $subtitle = $dbh->quote($query->param('subtitle'));
my $date_str = $dbh->quote($query->param('date_str'));
my $greeting = $dbh->quote($query->param('greeting'));
my $introduction = $dbh->quote($query->param('introduction'));
my $closing = $dbh->quote($query->param('closing'));
#my $promotion_name = $dbh->quote($query->param('promotion_name'));
#my $promotion_desc = $dbh->quote($query->param('promotion_desc'));
#my $promotion_image_url = $dbh->quote($query->param('promotion_image_url'));
#my $promotion_link_name = $dbh->quote($query->param('promotion_link_name'));
my $promotion_name = "''";
my $promotion_desc = "''";
my $promotion_image_url = "''";
my $promotion_link_name = "''";
my $template_id;

if ($mode eq "EDIT")
{
	$template_id = $query->param('template_id');

	# update the template record

	$sql = "update template set template_name = $template_name,
		html_template = $html_template,
		text_template = $text_template, 
		aol_template = $aol_template,
    	show_image_url = '$show_image_url', 
		show_title = '$show_title', 
		show_subtitle = '$show_subtitle', 
		show_date_str = '$show_date_str', 
		show_greeting = '$show_greeting',
    	show_introduction = '$show_introduction', 
		show_closing = '$show_closing', 
		num_articles = $num_articles, 
		show_promo = '$show_promo', 
		show_article_title = '$show_article_title',
    	show_article_text = '$show_article_text', 
		show_article_link = '$show_article_link', 
		show_article_image_url = '$show_article_image_url'
		where template_id = $template_id";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Updating template record: $sql : $errmsg");
		exit(0);
	}

	# look for a template_news record first

	$sql = "select count(*) from template_news where template_id = $template_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($count) = $sth->fetchrow_array();
	$sth->finish();

	if ($count == 1)
	{
		# record already exists, so update it

    	$sql = "update template_news set image_url = $image_url, 
			title = $title, 
			subtitle = $subtitle, 
			date_str = $date_str, 
			greeting = $greeting, 
			introduction = $introduction,
			closing = $closing, 
			promotion_name = $promotion_name, 
			promotion_desc = $promotion_desc, 
			promotion_image_url = $promotion_image_url, 
			promotion_link_name = $promotion_link_name
        	where template_id = $template_id";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
			$errmsg = $dbh->errstr();
			util::logerror("Updating template_news record: $sql : $errmsg");
			exit(0);
		}
	}
	else
	{
		# record does not exist, so add it now

    	$sql = "insert into template_news (template_id, image_url, title, subtitle, date_str,
			greeting, introduction, closing, promotion_name, promotion_desc,
			promotion_image_url, promotion_link_name) values ($template_id, $image_url, $title, 
			$subtitle, $date_str, $greeting, $introduction, $closing, $promotion_name, 
			$promotion_desc, $promotion_image_url, $promotion_link_name)";
		$rows = $dbh->do($sql);
		if ($dbh->err() != 0)
		{
			$errmsg = $dbh->errstr();
			util::logerror("inserting template_news record: $sql : $errmsg");
			exit(0);
		}
	}

}
elsif ($mode eq "ADD")
{
	$sql = "insert into template (status,template_name, html_template,
		text_template, aol_template, show_image_url, show_title,
		show_subtitle, show_date_str, show_greeting, show_introduction,
		show_closing, num_articles, show_promo, show_article_title,
    	show_article_text, show_article_link, show_article_image_url)
		values ('A', $template_name, $html_template, $text_template, 
		$aol_template, '$show_image_url', '$show_title', '$show_subtitle', 
		'$show_date_str', '$show_greeting','$show_introduction', '$show_closing', 
		$num_articles, '$show_promo', '$show_article_title', '$show_article_text', 
		'$show_article_link', '$show_article_image_url')";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("Inserting template record: $sql : $errmsg");
		exit(0);
	}

	# get id of template just inserted

	$sql = "select last_insert_id()";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($template_id) = $sth->fetchrow_array();
	$sth->finish();

	# insert template_news record - it holds the template field default values

    $sql = "insert into template_news (template_id, image_url, title, subtitle, date_str,
		greeting, introduction, closing, promotion_name, promotion_desc,
		promotion_image_url, promotion_link_name) values ($template_id, $image_url, $title, 
		$subtitle, $date_str, $greeting, $introduction, $closing, $promotion_name, $promotion_desc,
        $promotion_image_url, $promotion_link_name)";
	$rows = $dbh->do($sql);
	if ($dbh->err() != 0)
	{
		$errmsg = $dbh->errstr();
		util::logerror("inserting template_news record: $sql : $errmsg");
		exit(0);
	}
}

# see where to go next

my $nextfunc = $query->param('nextfunc');
if ($nextfunc eq "preview")
{
    print "Location: template_edit.cgi?template_id=$template_id&preview=1&mode=EDIT\n\n";
}
elsif ($nextfunc eq "save")
{
    print "Location: template_edit.cgi?template_id=$template_id&mode=EDIT\n\n";
}
elsif ($nextfunc eq "articles")
{
	print "Location: template_article_defaults.cgi?template_id=$template_id\n\n";
}

# exit function

$util->clean_up();
exit(0);
