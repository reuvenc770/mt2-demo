#!/usr/bin/perl

# *****************************************************************************************
# template_edit.cgi
#
# this page is to edit a template
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
my $mode = $query->param('mode');
my $preview = $query->param('preview');
my $user_id;
my $template_name;
my ($html_template, $text_template, $aol_template,
    $status, $show_image_url, $show_title, $show_subtitle, $show_date_str, $show_greeting,
    $show_introduction, $show_closing, $num_articles, $show_promo, $show_article_title,
    $show_article_text, $show_article_link, $show_article_image_url);
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;
my $table_text_color = $util->get_table_text_color;
my ($image_url, $title, $subtitle, $date_str, $greeting, $introduction,
    $closing, $promotion_name, $promotion_desc, $promotion_image_url,
    $promotion_link_name);

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

# read info for this template

if ($mode eq "EDIT")
{
	$sql = "select template_name, html_template, text_template, aol_template,
    	status, show_image_url, show_title, show_subtitle, show_date_str, show_greeting,
    	show_introduction, show_closing, num_articles, show_promo, show_article_title,
    	show_article_text, show_article_link, show_article_image_url
		from template where template_id = $template_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($template_name, $html_template, $text_template, $aol_template,
    	$status, $show_image_url, $show_title, $show_subtitle, $show_date_str, $show_greeting,
    	$show_introduction, $show_closing, $num_articles, $show_promo, $show_article_title,
    	$show_article_text, $show_article_link, $show_article_image_url) = $sth->fetchrow_array();
	$sth->finish();

	# read values from the template_news table

	$sql = "select image_url, title, subtitle, date_str, greeting, introduction,
    	closing, promotion_name, promotion_desc, promotion_image_url, promotion_link_name
    	from template_news where template_id = $template_id";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($image_url, $title, $subtitle, $date_str, $greeting, $introduction,
    	$closing, $promotion_name, $promotion_desc, $promotion_image_url,
    	$promotion_link_name) = $sth->fetchrow_array();
	$sth->finish();
}

# print out the html page

util::header("$mode TEMPLATE");

if ($preview == 1)
{
    print qq {
    <script language="Javascript">
    var newwin = window.open("template_preview.cgi?template_id=$template_id", "Preview", "toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=800,height=500,left=50,top=50");
    newwin.focus();
    </script> \n };
}

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
			Use this screen to edit the $template_name template.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM name="tempform" action="template_save.cgi" method="post">
		<input type="hidden" name="template_id" value="$template_id">
		<input type="hidden" name="mode" value="$mode">
		<input type="hidden" name="nextfunc">
		<script language="JavaScript">
        function SaveFunc(btn)
        {
            document.tempform.nextfunc.value = btn;
            document.tempform.submit();
        }
        </script>
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
				color=#ffffff size=2><B>Introduction</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Template Name</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<INPUT size=40 maxlength=40 name=template_name value="$template_name"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>HTML Template</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA name=html_template rows=8 cols=70>$html_template</TEXTAREA></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Text Template</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA name=text_template rows=8 cols=70>$text_template</TEXTAREA></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>AOL HTML Template</B></FONT></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<TEXTAREA name=aol_template rows=8 cols=70>$aol_template</TEXTAREA></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<td>&nbsp;</td>
			<TD><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Define which fields are used in this template 
					and what their default values should be.</b> Check the box next to the
					field name if it is used in this template.  If you check a box, you 
					must enter a default value.  The default values will be used for the
					quick popup preview of the template, and when a user creates a 
					campaign.</FONT></TD>
			<td>&nbsp;</td>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<table border=0 cellpadding=4 cellspacing=0>
				<tr>
				<td align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Field</b></font></td>
				<td><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Default Value</b></font></td>
				</tr>
				<tr>
				<td align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Image URL</b></font>
					<input type="checkbox" name="show_image_url" $checkit{$show_image_url}></td>
				<td><INPUT type="text" size=40 maxlength=255 name=image_url value="$image_url"></td>
				</tr>
				<tr>
				<td align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Title</b></font>
					<input type="checkbox" name="show_title" $checkit{$show_title}></td>
				<td><INPUT type="text" size=40 maxlength=80 name=title value="$title"></td>
				</tr>
				<tr>
				<td align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Subtitle</b></font>
					<input type="checkbox" name="show_subtitle" $checkit{$show_subtitle}></td>
				<td><INPUT type="text" size=40 maxlength=80 name=subtitle value="$subtitle"></td>
				</tr>
				<tr>
				<td align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Date</b></font>
					<input type="checkbox" name="show_date_str" $checkit{$show_date_str}></td>
				<td><INPUT type="text" size=40 maxlength=20 name=date_str value="$date_str"></td>
				</tr>
				<tr>
				<td align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Greeting</b></font>
					<input type="checkbox" name="show_greeting" $checkit{$show_greeting}></td>
				<td><INPUT type="text" size=40 maxlength=80 name=greeting value="$greeting"></td>
				</tr>
				<tr>
				<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Introduction</b></font>
					<input type="checkbox" name="show_introduction" $checkit{$show_introduction}>
					</td>
				<td><TEXTAREA rows=4 cols=58 name=introduction>$introduction</TEXTAREA></td>
				</tr>
				<tr>
				<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Closing</b></font>
					<input type="checkbox" name="show_closing" $checkit{$show_closing}>
					</td>
				<td><TEXTAREA rows=4 cols=58 name=closing>$closing</TEXTAREA></td>
				</tr>

<!--
				<tr>
				<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Promotion</b></font>
					<input type="checkbox" name="show_promo" $checkit{$show_promo}>
					</td>
				<td>
					<table border=0 cellpadding=1 cellspacing=0>
					<tr>
					<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif"
                    	color=$table_text_color size=2><B>Promotion Title</b></font></td>
					<td align=left>
						<input type="text" name="promotion_name" size=40 maxlength=80
						value="$promotion_name"></td>
					</tr>
					<tr>
					<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif"
						color=$table_text_color size=2><B>Promotion Text</b></font></td>
					<td align=left>
						<TEXTAREA name="promotion_desc" rows=4 cols=35>$promotion_desc</textarea>
						</td>
					</tr>
					<tr>
					<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif"
						color=$table_text_color size=2><B>Promotion Image URL</b></font></td>
					<td align=left>
						<input type="text" name="promotion_image_url" size=40 maxlength=80
						value="$promotion_image_url"></td>
					</tr>
					<tr>
					<td valign=top align=right><FONT face="verdana,arial,helvetica,sans serif"
						color=$table_text_color size=2><B>Promotion Link Name</b></font></td>
					<td align=left>
						<input type="text" name="promotion_link_name" size=40 maxlength=80
						value="$promotion_link_name"></td>
					</tr>
					</table>
				</td>
				</tr>
-->

				<tr>
				<td><FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>How Many Articles?</b></font></td>
				<td><input type="text" name="num_articles" value="$num_articles" size=2 
					maxlength=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="JavaScript:SaveFunc('articles');">
					<FONT face="verdana,arial,helvetica,sans serif" 
					color=$table_text_color size=2><B>Edit Article Defaults</b></font></a></td>
				</tr>
				</table>

			</TD>
			<td>&nbsp;</td>
			</TR>
			<TR>
			<TD>&nbsp;</TD>
			<TD>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=middle width="33%"> 
					<a href="template_list.cgi">
					<img src="$images/previous_arrow.gif" border=0></a></TD>
				<TD align=center width="33%">
					<a href="JavaScript:SaveFunc('preview')">
					<IMG src="$images/preview_rev.gif" border=0></a></TD>
				<TD align=center width="33%">
					<a href="JavaScript:SaveFunc('save');">
					<img src="$images/save.gif" border=0></a></TD>
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
