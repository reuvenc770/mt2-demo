#!/usr/bin/perl
# ******************************************************************************
# advanced_editor_variables.cgi
#
# this page displays the Advanced Editor variable documentation
#
# History
# G Nash	09/04/2001		Creation 
# ******************************************************************************

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
my $images = $util->get_images_url;
my $table_text_color = $util->get_table_text_color;
my $table_header_bg = $util->get_table_header_bg;
my $light_table_bg = $util->get_light_table_bg;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

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

# print out html page

util::header("Advanced Editor Variables");

print <<"end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#509C10 size=3><B>Advanced Editor Variables</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>The Advanced Editor allows those proficient in 
            HTML to make modifications to their campaign.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>&nbsp; <BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <HR SIZE=4> </HR></TD>
		</TR>
        <TR>
        <TD align=middle>

            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
            <TBODY>
            <TR bgcolor=$table_header_bg>
            <TD align=center colspan="3">
				<FONT face="verdana,arial,helvetica,sans serif" color=#ffffff size=2><b>
				General Campaign Fields</b></FONT></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=$table_text_color size=2>
				<b>{{TRACKING}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Adds Open Tracking to the email.  Used to generate statistics on number of 
				opened emails.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{TOP_AD}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Banner Ad at the top of the email.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{BOTTOM_AD}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Banner Ad at the bottom of the email.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{POPUP_AD}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Banner Ad at the top of the email.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{IMAGE_URL}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the campaign image for Header.  The
				HTML should look like this: &lt;img src="{{IMAGE_URL}}"&gt;
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{TITLE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Title.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{SUBTITLE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Subtitle.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{DATE_STR}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Date.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{GREETING}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Greeting.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{INTRODUCTION}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Introduction paragraph.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CLOSING}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the Closing paragraph.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
			</tbody>
			</table>
			<br>


            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
            <TBODY>
            <TR bgcolor=$table_header_bg>
            <TD align=center colspan="3">
				<FONT face="verdana,arial,helvetica,sans serif" color=#ffffff size=2><b>
				Article Field Values</b></FONT></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ARTICLEn_TITLE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Article N title text where N is the article number.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ARTICLEn_TEXT}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Place the body of Article N's text where N is the article number.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ARTICLEn_LINK}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Place the URL for the link for this article.  This is an example:
				&lt;a href="{{ARTICLE1_LINK}}"&gt;{{ARTICLE1_LINK_NAME}}&lt;/a&gt;
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ARTICLEn_LINK_NAME}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				The text that is displayed as the part of the link to click on.  
				This is an example:
				&lt;a href="{{ARTICLE1_LINK}}"&gt;{{ARTICLE1_LINK_NAME}}&lt;/a&gt;
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ARTICLEn_IMAGE_URL}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				An image that is displayed with an article.  This would contain the full
				url to the image and would typically be placed in the html template like this:
				&lt;img src="{{ARTICLE1_IMAGE_URL}}"&gt;<br>
				If you want an image that is a link, you might place this in your html template:
				&lt;a href="{{ARTICLE1_LINK}}"&gt;&lt;img src="{{ARTICLE1_IMAGE_URL}}"&gt;&lt;/a&gt;
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
			</tbody>
			</table>
			<br>


            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
            <TBODY>
            <TR bgcolor=$table_header_bg>
            <TD align=center colspan="3">
				<FONT face="verdana,arial,helvetica,sans serif" color=#ffffff size=2><b>
				Client's Contact Information</b></FONT></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CONTACT_EMAIL}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Place the clients Email address at this location. A clickable link for the
				email address would look like this:
				&lt;a href="mailto:{{CONTACT_EMAIL}}"&gt;{{CONTACT_EMAIL}}&lt;/a&gt;
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CONTACT_URL}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Place the url to the client's website.  
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CONTACT_PHONE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				The contact phone number is placed here.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CONTACT_NAME}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				The contact name field is placed here.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CONTACT_COMPANY}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				The contact company field is placed here.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
			</tbody>
			</table>
			<br>


            <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
            <TBODY>
            <TR bgcolor=$table_header_bg>
            <TD align=center colspan="3">
				<FONT face="verdana,arial,helvetica,sans serif" color=#ffffff size=2><b>
				Email Personalization</b></FONT></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{EMAIL_ADDR}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's email address.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{FIRSTNAME}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's first name.	
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{LASTNAME}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's last name.	
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ADDRESS}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's address.	
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ADDRESS2}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's second address line.	
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{CITY}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's city.	
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{STATE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's state	
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{ZIP}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's zip
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{COUNTRY}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's country name
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{PHONE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's phone.
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{BIRTH_DATE}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's date of birth.
				</FONT></TD>
			</TR>
            <TR bgcolor=$light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD vAlign=center noWrap align=right>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<b>{{GENDER}}</b>
				</FONT></TD>
			<td width=10><img src="$images/spacer.gif" width=10 height=7></td>
            <TD vAlign=center align=left>
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				Location to place the member's gender
				</FONT></TD>
			</TR>
            <TR bgcolor=$alt_light_table_bg>
            <TD colspan=3 height=3><IMG src="$images/spacer.gif" width="1" height="3"></TD>
			</TR>

			</TBODY>
			</TABLE>
			<br>

		</TD>
		</TR>
		<tr>
		<td>

            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=right> </TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>

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
$util->clean_up();
exit(0);
