#!/usr/bin/perl

# *****************************************************************************************
# camp_step5.cgi
#
# this page is the fifth step in the email campaign creation process
# save and review the email campaign list
#
# History
# Grady Nash, 7/30/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use util_template;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $email_addr;
my $rows;
my $errmsg;
my $campaign_id = $query->param('campaign_id');
my $user_id;
my $template_id;
my $template_name;
my $campaign_name;
my $subject;
my $subject2;
my $subject3;
my $subject4;
my $subject5;
my $from_addr;
my $tid;
my $footer_color;
my $black_flag;
my $white_flag;
my $image_url;
my $title;
my $subtitle;
my $date_str;
my $greeting;
my $introduction;
my $closing;
my $k;
my $status;
my $show_ad_top;
my $show_ad_bottom;
my $show_popup;
my $check_show_ad_top = "";
my $check_tell_a_friend = "";
my $check_show_ad_bottom = "";
my $check_show_popup = "";
my $show_image_url;
my $show_title;
my $show_subtitle;
my $show_date_str;
my $show_greeting;
my $show_introduction;
my $show_closing;
my $num_articles;
my $show_promo;
my $images = $util->get_images_url;
my $top_ad_opt;
my $top_ad_opt_w = "";
my $top_ad_opt_o = "";
my $top_ad_code;
my $bottom_ad_opt;
my $bottom_ad_opt_w = "";
my $bottom_ad_opt_o = "";
my $bottom_ad_code;
my $tell_a_friend;
my $unsub_url;
my $aid;
my $track_flag;
my $iflag;
my $adv_id;
my $aname;
my $dbh1;
my $suppression_id;
my $domain_suppression_id;
my $category_id;
my $content_id;
my $subdomain_name;
my $revenue;
my $catid;
my $category_name;
my $trigger_flag;
my $trigger_email;
my $trigger_email_campaign_id;

# setup debugging flag.  Set to 1 to turn on debug and 0 to turn it off

my $debug = 0;
if ($debug == 1)
{
    # open the logfile
    open (LOG, ">> /tmp/util.log");
	# make sure logfile is not buffered
	my $curhandle = select(LOG);
	$| = 1;
	select($curhandle);
    my $cdate = localtime();
    print LOG "camp_step5.cgi starting at $cdate\n";
}

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# read the campaigns info to fill in the form

$sql = "select campaign_name,template_id,subject,status,from_addr,footer_color,id,advertiser_id,track_internally,unsub_link,vendor_supp_list_id,vendor_domain_supp_list_id,category_id,content_id,subdomain_name,revenue,trigger_email,trigger_email_campaign_id from campaign where campaign_id = $campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($campaign_name,$template_id,$subject,$status,$from_addr,$footer_color,$tid,$aid,$track_flag,$unsub_url,$suppression_id,$domain_suppression_id,$category_id,$content_id,$subdomain_name,$revenue,$trigger_email,$trigger_email_campaign_id) = $sth->fetchrow_array();
$sth->finish();
#$subject =~ s/"/\/"/g;
$sql = "select subject2,subject3,subject4,subject5 from campaign_subject where campaign_id = $campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
if (($subject2,$subject3,$subject4,$subject5) = $sth->fetchrow_array())
{
	$sth->finish();
}
else
{
	$sth->finish();
	$subject2="";
	$subject3="";
	$subject4="";
	$subject5="";
}
if ($trigger_email eq "Y")
{
	$trigger_flag = "checked";
}
else
{
	$trigger_flag = "";
}
if ($track_flag eq "Y")
{
	$iflag = "checked";
}
else
{
	$iflag = "";
}	

if ($tell_a_friend eq "Y")
{
	$check_tell_a_friend = "checked";
}
if ($show_ad_top eq "Y")
{
	$check_show_ad_top = "checked";
}
if ($show_ad_bottom eq "Y")
{
	$check_show_ad_bottom = "checked";
}
if ($show_popup eq "Y")
{
	$check_show_popup = "checked";
}
if ($top_ad_opt eq "W")
{
	$top_ad_opt_w = "selected";
}
else
{
	$top_ad_opt_o = "selected";
}
if ($bottom_ad_opt eq "W")
{
	$bottom_ad_opt_w = "selected";
}
else
{
	$bottom_ad_opt_o = "selected";
}
if ($footer_color eq "BLACK")
{
	$black_flag = "selected";
	$white_flag = " ";
}
else
{
	$black_flag = " ";
	$white_flag = "selected";
}

# read info about this campaigns template

$sql = "select show_image_url, show_title, show_subtitle, show_date_str,
    show_greeting, show_introduction, show_closing, num_articles, show_promo, template_name
	from template
	where template_id = $template_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($show_image_url, $show_title, $show_subtitle, $show_date_str,
    $show_greeting, $show_introduction, $show_closing, $num_articles, 
	$show_promo, $template_name) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("CREATE EMAIL");

my $mode = $query->param('mode');
if ($mode eq "preview")
{
	print qq {
	<script language="Javascript">
	var newwin = window.open("camp_preview.cgi?campaign_id=$campaign_id&format=H", "Preview", "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=900,height=500,left=50,top=50");
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
			color=#509C10 size=3><B>$campaign_name</B></FONT> </TD>
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
			Use the template below to customize your <b>$campaign_name</b> email. This email was
			built using the $template_name template.  You can save and preview your work 
			at anytime in the wizard. To edit different sections of your email, use the section 
			links on the left or select <B>Previous</B> and <B>Next</B>.<BR> </FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="camp_step5_save.cgi" method="post" name="campform">
		<input type="hidden" name="campaign_id" value="$campaign_id">
		<input type="hidden" name="status" value="$status">
		<input type="hidden" name="template_id" value="$template_id">
		<input type="hidden" name="nextfunc">
		<input type="hidden" name="article">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
			<TBODY>
			<TR>
			<TD vAlign=top align=middle width=195><IMG height=7 src="$images/spacer.gif" width=190>
end_of_html

	&print_leftbar($dbhq, $campaign_id, "INTRODUCTION", 0);

print << "end_of_html";
			</TD>
			<TD vAlign=top align=middle width=465><IMG height=7 src="$images/spacer.gif" width=455>

				<!-- Begin main body area -->

				<TABLE cellSpacing=0 cellPadding=0 width=455 bgColor=#E3FAD1 border=0>
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
					color=#509C10 size=2><B>Subject(s)</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=subject value="$subject"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=subject2 value="$subject2"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=subject3 value="$subject3"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=subject4 value="$subject4"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=subject5 value="$subject5"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>From Email Address</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name="from_addr" value="$from_addr"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><B>Content</B></FONT></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <select name=content_id>
end_of_html
$sql = "select content_id,content_str from content_info order by content_str";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($catid,$category_name) = $sth->fetchrow_array())
{
    if ($catid == $content_id)
    {
        print "<option selected value=$catid>$category_name</option>\n";
    }
    else
    {
        print "<option value=$catid>$category_name</option>\n";
    }
}
$sth->finish();
print <<"end_of_html";
                </select></td>
                </tr>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><B>Sub-domain</B></FONT></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=text maxlength=10 size=11 name=subdomain value="$subdomain_name">
				</td>
				</tr>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><B>Payout Per Conversion</B></FONT></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=text maxlength=5 size=5 name=revenue value="$revenue">
				</td>
				</tr>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
                </TR>
                <TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><input type=checkbox name=trigger_email $trigger_flag value="Y"><b>&nbsp;Defined as Trigger Email</b></FONT></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
                </TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Campaign to Email On Click</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<select name=trigger_email_campaign_id>
end_of_html
$sql = "select campaign_id,campaign_name from campaign where trigger_email='Y' and campaign_id != $campaign_id order by campaign_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $temp_cid;
my $temp_name;
if ($trigger_email_campaign_id == 0)
{
	print "<option selected value=0>NONE</option>\n";
}
else
{
	print "<option value=0>NONE</option>\n";
}
while (($temp_cid,$temp_name) = $sth->fetchrow_array())
{
	if ($trigger_email_campaign_id == $temp_cid)
	{
		print "<option selected value=$temp_cid>$temp_name</option>\n";
	}
	else
	{
		print "<option value=$temp_cid>$temp_name</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select>
				</td>
				</TR>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
                </TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Footer Color</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<select name=footer_color><option $black_flag value="BLACK">Black</option><option $white_flag value="WHITE">White</option></select>
				</td>
				</TR>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><B>Category</B></FONT></TD>
                </TR>
                <TR>
                <TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <select name=catid>
end_of_html
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbhq->prepare($sql);
$sth->execute();
while (($catid,$category_name) = $sth->fetchrow_array())
{
    if ($catid == $category_id)
    {
        print "<option selected value=$catid>$category_name</option>\n";
    }
    else
    {
        print "<option value=$catid>$category_name</option>\n";
    }
}
$sth->finish();
print <<"end_of_html";
                </select></td>
                </tr>
                <TR>
                <TD colSpan=3>&nbsp;</TD>
                </TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>ID</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=30 maxlength=30 name="tid" value="$tid"> </TD>
				</TR>
				<tr>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Advertiser</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<select name=aid>
end_of_html
				$sql = "select advertiser_id,advertiser_name from advertiser_info where status='A' order by advertiser_name";
				$sth = $dbhq->prepare($sql);
				$sth->execute();
				while (($adv_id,$aname) = $sth->fetchrow_array())
				{
					if ($adv_id == $aid)
					{
						print "<option value=$adv_id selected>$aname</option>\n";
					}
					else
					{
						print "<option value=$adv_id>$aname</option>\n";
					}
				}
$sth->finish();
print <<"end_of_html";
					</select>
				</td>
				</TR>
				<tr>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><input type=checkbox name=internal_flag $iflag value="Y"><b>&nbsp;Unsubscribe Link Tracked by Us</b></FONT></TD>
				</TR>
				<tr>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Advertiser Unsubscribe URL</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 maxlength=255 name="unsub_url" value="$unsub_url"> </TD>
				</TR>
				
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Suppression List</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<select name=suppid>
end_of_html
				$sql = "select list_id,list_name from vendor_supp_list_info order by list_name";
				$sth = $dbhq->prepare($sql);
				$sth->execute();
				while (($adv_id,$aname) = $sth->fetchrow_array())
				{
					if ($adv_id == $suppression_id)
					{
						print "<option value=$adv_id selected>$aname</option>\n";
					}
					else
					{
						print "<option value=$adv_id>$aname</option>\n";
					}
				}
$sth->finish();
print <<"end_of_html";
					</select>
				</td>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Domain Suppression List</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<select name=domain_suppid>
end_of_html
				$sql = "select list_id,list_name from vendor_domain_supp_list_info order by list_name";
				$sth = $dbhq->prepare($sql);
				$sth->execute();
				while (($adv_id,$aname) = $sth->fetchrow_array())
				{
					if ($adv_id == $domain_suppression_id)
					{
						print "<option value=$adv_id selected>$aname</option>\n";
					}
					else
					{
						print "<option value=$adv_id>$aname</option>\n";
					}
				}
$sth->finish();
print <<"end_of_html";
					</select>
				</td>
				</TR>
				<tr>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR> 
end_of_html

if ($show_image_url eq "Y")
{
	print << "end_of_html";
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Logo or Image URL for Header </B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=image_url value="$image_url"></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD></TR>
				<TR>
end_of_html
}

if ($show_title eq "Y")
{
	print << "end_of_html";
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Title</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=title value="$title"></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD></TR>
				<TR>
end_of_html
}

if ($show_subtitle eq "Y")
{
	print << "end_of_html";
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Subtitle</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type=text size=50 name=subtitle value="$subtitle"></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD></TR>
				<TR>
end_of_html
}

if ($show_date_str eq "Y")
{
	print << "end_of_html";

				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2><B>Date</B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=date_str value="$date_str"></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD></TR>
				<TR>
end_of_html
}

if ($show_greeting eq "Y")
{
	print << "end_of_html";
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2> <B>Greeting </B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<INPUT type="text" size=50 name=greeting value="$greeting"> </TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
end_of_html
}

if ($show_introduction eq "Y")
{
	print << "end_of_html";
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2> <B>Introductory Paragraph </B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<TEXTAREA class=textareastyle name=introduction rows=4 cols=48>$introduction</TEXTAREA></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
end_of_html
}

if ($show_closing eq "Y")
{
	print << "end_of_html";
				<TR>
				<TD colSpan=3>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
					color=#509C10 size=2> <B>Closing Paragraph </B></FONT></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<TEXTAREA name=closing rows=4 cols=48>$closing</TEXTAREA></TD>
				</TR>
				<TR>
				<TD colSpan=3>&nbsp;</TD>
				</TR>
end_of_html
}

print << "end_of_html";
				<TR>
				<TD>&nbsp;</TD>
				<TD>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TBODY>
					<TR>
					<TD align=middle width="50%">
end_of_html

if ($status ne "C")
{
	print qq { <a href="JavaScript:SaveFunc('save');">
		<img src="$images/save_rev.gif" border=0 width="81" height="22"></a>\n };
}

print << "end_of_html";
					</TD>
					<TD align=middle width="50%">
						<a href="JavaScript:SaveFunc('preview');"><IMG 
						src="$images/preview_rev.gif" border=0 width="81" height="22"></a>
						</TD>
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

				<!-- End main body area -->

			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
			<TBODY>
			<TR>
			<TD align=right>
				<a href="JavaScript:SaveFunc('exit');">
				<IMG src="$images/exit_wizard.gif" border=0 width="90" height="22"></a>

				<IMG height=1 src="$images/spacer.gif" width=130 border=0> 

<!--				<a href="JavaScript:SaveFunc('previous');">
				<img src="$images/previous_arrow.gif" border=0 width="83" height="21"> </a>

				<a href="JavaScript:SaveFunc('next');">
				<img src="$images/next_arrow.gif" border=0 width="76" height="23"></a>-->
			</TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		<TR>
		<TD vAlign=center align=left>
			<a href="JavaScript:SaveFunc('advanced');">
			<FONT face="verdana,arial,helvetica,sans serif" color=#000000 
			size=2>Advanced Editor</FONT></a></TD>
		</TR>
		<TR>
		<TD><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
		<TR>
		<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" 
			color=#000000 size=2>Your Email Privacy Policy</FONT></TD>
		</TR>
		<TR>
		<TD><IMG height=7 src="$images/spacer.gif"></TD>
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
if ($debug == 1)
{
    print LOG "camp_step5.cgi finished\n";
    close LOG;
}
exit(0);
