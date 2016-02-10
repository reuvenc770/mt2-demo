#!/usr/bin/perl

# *****************************************************************************************
# camp_preview.cgi
#
# this page presents the user with a preview of the campaign email
#
# History
# Grady Nash, 7/30/01, Creation
# Jim Sobeck, 04/11/06, Added logic for Creative Deploy call
# *****************************************************************************************

# include Perl Modules

use strict;
use util_mail;
use util;

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $sth;
my $sql;
my $rows;
my $errmsg;
my $email_addr;
my $email_user_id;
my $first_name;
my $last_name;
my $campaign_id = $query->param('campaign_id');
my $format = $query->param('format');
my $cdeploy = $query->param('cdeploy');
if ($cdeploy eq "")
{
	$cdeploy = "N";
}
my $images = $util->get_images_url;
my $subject;
my $body_text;
my $first_part;
my $testvar;
my $pos;
my $pos2;
my $the_rest;
my $end_pos;
my $selected_bg_color = "#509C10";
my $not_selected_bg_color = "#E3FAD1";
my $selected_tl_gif = "$images/blue_tl.gif";
my $selected_tr_gif = "$images/blue_tr.gif";
my $not_selected_tl_gif = "$images/lt_purp_tl.gif";
my $not_selected_tr_gif = "$images/lt_purp_tr.gif";
my $selected_text_color = "#FFFFFF";
my $not_selected_text_color = "#509C10";
my @bg_color;
my @tl_gif;
my @tr_gif;
my @text_color;
my $k;
my $from_addr;
my $other_addr;
my $aid;
my $footer_color;
my $internal_flag;
my $unsub_url;
my $unsub_image;
my $cunsub_image;
my $csite;
my $content_id;
my $footer_content_id;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

my $user_id;
my $cookie=retrieve_cookie();
if (!$cookie) {
	print "Location: login_advertiser_list.cgi\n\n";
}
else {
	$user_id=1;
# check for login
#my $user_id = util::check_security();
#if ($user_id == 0)
#{
#    print "Location: notloggedin.cgi\n\n";
#    $util->clean_up();
#    exit(0);
#}

# read the email address for this user

$sql = "select email_addr,first_name,last_name,website_url from user where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($email_addr,$first_name,$last_name,$csite) = $sth->fetchrow_array();
$sth->finish();
$email_addr="email\@domain.com";

# read the subject for this newsletter

$sql = "select default_subject,default_from,advertiser_id,unsub_image,content_id from creative where creative_id = $campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject,$from_addr,$aid,$cunsub_image,$footer_content_id) = $sth->fetchrow_array();
$sth->finish();
$sql = "select advertiser_subject from advertiser_subject where subject_id = $subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
($subject) = $sth->fetchrow_array();
$sth->finish();
if ($subject eq "")
{
	$subject = "No subject selected";
}
$sql = "select advertiser_from from advertiser_from where from_id = $from_addr";
$sth = $dbhq->prepare($sql);
$sth->execute();
($from_addr) = $sth->fetchrow_array();
$sth->finish();
if ($from_addr eq "")
{
	$from_addr = "{{{{FOOTER_SUBDOMAIN}}";
}
#
#	Get approval addresses
#
$sql = "select email_addr from advertiser_approval where advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $temail;
$other_addr="";
while (($temail) = $sth->fetchrow_array())
{
	$other_addr = $other_addr . $temail . ",";
}
$sth->finish();
$_ = $other_addr;
chop;
$other_addr = $_;
#
# Get unsub information and internal_flag
#
$sql = "select track_internally,unsub_link,unsub_image from advertiser_info where advertiser_id=$aid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($internal_flag,$unsub_url,$unsub_image) = $sth->fetchrow_array();
$sth->finish();

if ($cunsub_image eq "NONE")
{
	$unsub_image="";
}
$email_user_id = 0; 

# Get the email template and substitute all the field data

my $the_email = &util_mail::mail_preview($dbhq,$campaign_id,$format,$email_addr,$email_user_id,$user_id,$footer_color,$aid,$internal_flag,$unsub_url,$unsub_image,$content_id,"",$cdeploy);

# this screen presents the email template inside of a table with some other html
# "stuff" on top.  So I must first break up the html template to remove the body
# and /body tags, and put everything else in the right place in the table tag

if ($format eq "H" || $format eq "A")
{
	# find everything in the email up to the end of the body tag
	# and shove it into "first_part"

	$testvar = uc($the_email);
	$pos = index($testvar, "<BODY");
	$pos2 = index($testvar, ">", $pos);
	$first_part = substr($the_email, 0, $pos2+1);
	$first_part = "<html><head><title>HTML Preview</title></head><body>";
	
	# get the rest of the page up to the </BODY>
	
	$the_rest = substr($the_email, $pos2+1);
	$end_pos = index($the_rest, "</BODY>");
	$body_text = substr($the_rest, 0, $end_pos);
	$body_text =~ s/{{HEAD}}//;
	if ($footer_content_id > 0)
	{
		my $content_html;
		$sql="select content_html from footer_content where content_id=$footer_content_id";
		my $sth1=$dbhq->prepare($sql);
		$sth1->execute();
		($content_html) = $sth1->fetchrow_array();
		$sth1->finish();
		$body_text = $body_text . $content_html;
	}
}


# add the screen stuff to the top of the email html page

my $new_page = $first_part .  qq { 
<center>
<form> <input type="button" value="Close Window" onClick="closeme();"> </form>
<TABLE cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>

    <table border="0" cellpadding="0" cellspacing="0" width="719">
<!--    <tr>
    <TD width=248 bgColor=#FFFFFF rowSpan=2>
		<img border="0" src="$images/header.gif"></TD>
    <TD width=328 bgColor=#FFFFFF>&nbsp;</TD>
    </tr> -->
    <tr>
    <td width="468">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
        <td align="left"><b><font face="Arial" size="2">Campaign Preview</FONT></b></td>
        </tr>
        <tr>
        <td align="right">
            <b><a style="TEXT-DECORATION: none" href="logout.cgi">
            <font face=Arial size=2 color="#509C10">Logout</font></a>&nbsp;&nbsp;&nbsp;
            <a href="wss_support_form.cgi" style="text-decoration: none">
            <font face=Arial size=2 color="#509C10">Customer Assistance</font></a></b>
        </td>
        </tr>
        </table>
    </td>
    </tr>
    </table>

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
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509c10 
            size=3><B>Review Your Campaign</B></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" 
            color=#509c10 size=2>Carefully review your creative. Send yourself a test copy of the email by selecting <B>Send</B>. You should test all links from the creative to ensure they are correct.<BR></FONT></TD>
		</TR>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" 
            color=#509c10 size=2>To print the contents of this window, 
            press Control-p in your browser window.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <FORM action="camp_test_gen.cgi" method="post">
        <INPUT type="hidden" name="campaign_id" value="$campaign_id">
        <INPUT type="hidden" name="format" value="$format">
        <INPUT type="hidden" name="cdeploy" value="$cdeploy">

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD noWrap align=right width=250><FONT 
			face="verdana,arial,helvetica,sans serif" color=#509c10 
            size=2>Your Email Address:&nbsp;&nbsp;&nbsp;</FONT></TD>
        <TD align=left width=190>
            <INPUT type=text size=80 value="setup\@zetainteractive.com" name="cemail"></TD>
        <TD align=center width=220>
			<INPUT type=image height=23 width=76 src="$images/send_blkline.gif" border=0></TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>

        <FORM action="camp_test_gen.cgi" method="post">
        <INPUT type="hidden" name="campaign_id" value="$campaign_id">
        <INPUT type="hidden" name="format" value="$format">
        <INPUT type="hidden" name="cdeploy" value="$cdeploy">
        <INPUT type="hidden" name="other_send" value="Y">

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD noWrap align=right width=250><FONT 
			face="verdana,arial,helvetica,sans serif" color=#509c10 
            size=2>Other Email Addresses:&nbsp;&nbsp;&nbsp;</FONT></TD>
        <TD align=left>
            <INPUT type=text size=80 value="$other_addr" name="cemail"></TD>
        <TD align=center>
			<INPUT type=image height=23 width=76 src="$images/send_blkline.gif" border=0></TD>
		</TR>
		</TBODY>
		</TABLE>

		</FORM>
		<form method="post" action="send_spam.cgi">
        <INPUT type="hidden" name="cid" value="$campaign_id">
        <INPUT type="hidden" name="returnto" value="preview">
        <INPUT type="hidden" name="format" value="$format">
        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD noWrap align=right width=250><FONT 
			face="verdana,arial,helvetica,sans serif" color=#509c10 
            size=2>Email Address:&nbsp;&nbsp;&nbsp;</FONT></TD>
        <TD align=left width=190>
            <INPUT type=text size=50 value="setup\@zetainteractive.com" name="cemail"></TD>
        <TD align=center width=220>&nbsp;&nbsp;
			<INPUT type=submit value="Send Spam Report"></TD>
		</TR>
        <TR>
        <TD colSpan=3><IMG height=7 src="$images/spacer.gif"></TD>
		</TR>
        <TR>
        <TD colSpan=3><HR SIZE=4></TD>
		</TR>
		</TBODY>
		</TABLE>
		</form>
        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD align=middle>

            <TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
            <TBODY>
            <TR>
            <TD align=right><FONT face="verdana,arial,helvetica,sans serif" color=#509c10 
                size=2>From</FONT>:&nbsp;&nbsp;&nbsp;&nbsp;</TD>
            <TD><FONT face="verdana,arial,helvetica,sans serif" 
                color=#509c10 size=2>$first_name $last_name
                ($from_addr)</FONT></TD>
			</TR>
            <TR>
            <TD align=right width=90><FONT 
                face="verdana,arial,helvetica,sans serif" color=#509c10 
                size=2>Subject</FONT>:&nbsp;&nbsp;&nbsp;&nbsp;</TD>
            <TD><FONT face="verdana,arial,helvetica,sans serif" 
                color=#509c10 size=2>$subject</FONT></TD>
			</TR>
			<TR>
            <TD><IMG height=7 src="$images/spacer.gif" width=7 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
        <TR>
        <TD align=middle>

            <TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
            <TBODY>
            <TR>
            <TD width=7 bgColor=white><IMG height=1 src="$images/spacer.gif" width=7 border=0></TD>
            <TD bgColor=white><IMG height=1 src="$images/spacer.gif" width=350 border=0></TD>
            <TD width=7 bgColor=white><IMG height=1 src="$images/spacer.gif" width=7 border=0></TD>
			</TR>
            <TR>
            <TD vAlign=top align=left bgColor=#509c10><IMG height=7 src="$images/blue_tl.gif" 
				width=7 border=0></TD>
            <TD bgColor=#509c10><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
            <TD vAlign=top align=right bgColor=#509c10><IMG height=7 src="$images/blue_tr.gif" 
				width=7 border=0></TD>
			</TR>
            <TR>
            <TD bgColor=#509c10><IMG src="$images/spacer.gif" width=7 border=0></TD>
            <TD vAlign=top bgColor=white>

                <TABLE cellPadding=5 width="100%" bgColor=white>
                <TBODY>
                <TR>
                <TD> };

$new_page .= $body_text;

$new_page .= qq {  </TD>
				</TR>
				</TBODY>
				</TABLE>

			</TD>
            <TD bgColor=#509c10><IMG src="$images/spacer.gif" width=7 border=0></TD>
			</TR>
            <TR>
            <TD vAlign=bottom align=left bgColor=#509c10><IMG height=7 src="$images/blue_bl.gif" 
				width=7 border=0></TD>
            <TD bgColor=#509c10><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
            <TD vAlign=bottom align=right bgColor=#6699cc><IMG height=7 src="$images/blue_br.gif" 
				width=7 border=0> </TD>
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
    <br><p align="center">
    <img border="0" src="$images/footer.gif"></p>
</TD>
</TR>
</TBODY>
</TABLE>
<script language="Javascript">
function closeme ()
{
	window.close();
}
</script>
</body>
</html> };

print "Content-type: text/html\n\n";
print "$new_page\n";

$util->clean_up();
}

sub retrieve_cookie {
    
    my ($hr, %cookies,$login_ok);
    my @rawCookies = split (/; /,$ENV{'HTTP_COOKIE'});
    foreach (@rawCookies) {         
        my ($key, $val) = split (/=/,$_);
        $cookies{$key} = $val;
    }               
                                    
    if ($cookies{'listadv'} ne "0") {
        $login_ok = $cookies{'listadv'};
    }                               
    else {                          
        $login_ok = 0;              
    }                               
    return ($login_ok);             
}

exit(0);

