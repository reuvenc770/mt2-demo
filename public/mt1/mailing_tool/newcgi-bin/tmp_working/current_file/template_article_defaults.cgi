#!/usr/bin/perl

# *****************************************************************************************
# template_article_defaults.cgi
#
# this page is to edit the template article defaults. The are stored in
# template_news_article
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
my $errmsg;
my $template_id = $query->param('template_id');
my $user_id;
my $template_name;
my $images = $util->get_images_url;
my ($article_title,$article_text,$article_link_name,$article_image_url);
my $article_link_id;
my $num_articles;
my $reccnt = 0;
my $light_table_bg = $util->get_light_table_bg;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $bgcolor;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

$sql = "select template_name,num_articles from template where template_id = $template_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($template_name,$num_articles) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("TEMPLATE ARTICLE DEFAULTS");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

	<TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>$template_name</B></FONT> </TD>
		</TR>
		<TR>
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Use this screen to edit the Article Defaults for $template_name 
			template.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="template_article_defaults_save.cgi" method="post">
		<input type="hidden" name="template_id" value="$template_id">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<!-- Begin main body area -->

			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
			<TBODY>
			<TR bgColor=#509C10>
			<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
				border=0 width="7" height="7"></TD>
			<TD align=middle height=15><FONT face="verdana,arial,helvetica,sans serif" 
				color=#ffffff size=2><B>Template Defaults</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
end_of_html

my $k;

for ($k=1 ; $k<=$num_articles ; $k++)
{
	# read values from the template_news_article table

	$sql = "select article_title, article_text, article_link_name, article_image_url, 
		article_link_id
		from template_news_article where template_id = $template_id and article_num = $k";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($article_title,$article_text,$article_link_name,$article_image_url,
		$article_link_id) = $sth->fetchrow_array();
	$sth->finish();

    $reccnt++;
    if ( ($reccnt % 2) == 0 )
    {
        $bgcolor = "$light_table_bg" ;
    }
    else
    {
        $bgcolor = "$alt_light_table_bg" ;
    }

	print qq {
			<!-- ----- Article Title ----------------------------------------------- -->
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Article $k Title</b></font></TD>
			</TR>
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type="text" size=60 maxlength=80 name="article${k}_title" 
				value="$article_title"></TD>
			</TR>

			<!-- ----- Article Text ----------------------------------------------- -->
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Article $k Text</b></font></TD>
			</TR>
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA rows=4 cols=58 name=article${k}_text>$article_text</textarea></TD>
			</TR>

			<!-- ----- Article Image Url ------------------------------------------ -->
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Article $k Image URL</b></font></TD>
			</TR>
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type="text" size=60 maxlength=80 name="article${k}_image_url" 
				value="$article_image_url"></TD>
			</TR>

			<!-- ----- Article Link ID ------------------------------------------ -->
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Article $k Link Identification</b></font></TD>
			</TR>
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type="text" size=40 maxlength=255 name="article${k}_link_id" 
				value="$article_link_id"></TD>
			</TR>

			<!-- ----- Article Link Name ------------------------------------------ -->
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>Article $k Link Text</b></font></TD>
			</TR>
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<input type="text" size=40 maxlength=255 name="article${k}_link_name" 
				value="$article_link_name"></TD>
			</TR>
			<TR bgcolor=$bgcolor>
			<TD colSpan=3>&nbsp;</TD>
			</TR> \n };
}

print << "end_of_html";
			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=center width="50%">
					<a href="template_edit.cgi?template_id=$template_id&mode=EDIT">
					<img src="$images/cancel.gif" border=0></a></TD>
				<TD align=middle width="50%"> 
					<INPUT TYPE=IMAGE src="$images/save.gif" border=0></TD>
				</TR>
				</TBODY>
				</TABLE>

			</TD>
			<TD>&nbsp;</TD>
			</TR>
			<TR>
			<TD vAlign=bottom align=left colSpan=2><IMG height=7 src="$images/lt_purp_bl.gif" 
				width=7 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
				width=7 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

	</TD>
	</TR>
	</TBODY>
	</TABLE>

</TD>
</TR>
<TR>
<TD noWrap align=left height=17>
end_of_html

$util->footer();

# exit function

$util->clean_up();
exit(0);
