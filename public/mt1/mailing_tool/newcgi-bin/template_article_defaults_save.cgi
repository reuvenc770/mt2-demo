#!/usr/bin/perl
# *****************************************************************************************
# template_article_defaults_save.cgi
#
# this page saves the template changes
#
# History
# Grady Nash, 9/05/01, Creation
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
my $userid;
my $rows;
my $errmsg;
my $template_id = $query->param('template_id');
my $num_articles;
my $article_title;
my $article_text;
my $article_link_name;
my $article_link_id;
my $article_image_url;
my $k;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# read number of articles for this template

$sql = "select num_articles from template where template_id = $template_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($num_articles) = $sth->fetchrow_array();
$sth->finish();

# first clean out all template_news_article records

$sql = "delete from template_news_article where template_id = $template_id";
$dbhu->do($sql);

# loop and insert each article record

for ($k=1 ; $k<=$num_articles ; $k++)
{
	# get fields from the form for one article

	$article_title = $dbhq->quote($query->param("article${k}_title"));
	$article_text = $dbhq->quote($query->param("article${k}_text"));
	$article_link_name = $dbhq->quote($query->param("article${k}_link_name"));
	$article_link_id = $dbhq->quote($query->param("article${k}_link_id"));
	$article_image_url = $dbhq->quote($query->param("article${k}_image_url"));

	# insert the article default record

	$sql = "insert into template_news_article (template_id, article_num,
		article_title, article_text, article_link_name, article_image_url, 
		article_link_id)
		values ($template_id, $k, $article_title, $article_text,
		$article_link_name, $article_image_url, $article_link_id)";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Inserting template_news_article record: $sql : $errmsg");
		exit(0);
	}
}

# go back to template_edit screen

print "Location: template_edit.cgi?template_id=$template_id&mode=EDIT\n\n";

# exit function

$util->clean_up();
exit(0);
