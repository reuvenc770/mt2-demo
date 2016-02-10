#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser data (eg 'user' table).
# Name   : advertiser_disp.cgi (edit_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/05/04  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my $user_type;
my $images = $util->get_images_url;
my $unsub_option;
my $name;
my $sid;
my $fid;
my $aflag;
my $temp_str;
my $content_id;
my $header_id;
my $body_id;
my $style_id;
my $oflag;
my $cdate;
my $cat_id;
my $puserid; 
my $cid;
my $pmode;
my $backto;
my $pmesg;
my $replace_flag;
my $original_html;
my $host_images;
my ($create_name,$original_flag,$trigger_flag,$approved_flag,$creative_date,$inactive_date,$unsub_image,$default_subject,$default_from,$image_directory,$thumbnail,$html_code,$copywriter,$copyname); 
my $c3;
my $comm_wizard_cid;
my $comm_wizard_progid;
my $cr;
my $landing_page;

#------  connect to the util database -----------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

#--------------------------------
# get CGI Form fields
#--------------------------------
$puserid = $query->param('aid');
$cid = $query->param('cid');
$backto = $query->param('backto');
$pmesg   = $query->param('pmesg');

#------  Get the information about the user for display  --------
$sql = "select advertiser_name,date_format(curdate(),\"%m/%d/%y\"),category_id from advertiser_info where advertiser_id = $puserid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($name,$cdate,$cat_id) = $sth->fetchrow_array();
$sth->finish();
#
#	Get Creative information
#
$sql = "select creative_name,original_flag,trigger_flag,approved_flag,date_format(creative_date,\"%m/%d/%y\"),date_format(inactive_date,\"%m/%d/%y\"),unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,content_id,header_id,body_content_id,style_id,replace_flag,comm_wizard_c3,comm_wizard_cid,comm_wizard_progid,cr,landing_page,copywriter,copywriter_name,original_html,host_images from creative where creative_id=$cid";
$sth = $dbhq->prepare($sql);
$sth->execute();
($create_name,$original_flag,$trigger_flag,$approved_flag,$creative_date,$inactive_date,$unsub_image,$default_subject,$default_from,$image_directory,$thumbnail,$html_code,$content_id,$header_id,$body_id,$style_id,$replace_flag,$c3,$comm_wizard_cid,$comm_wizard_progid,$cr,$landing_page,$copywriter,$copyname,$original_html,$host_images) = $sth->fetchrow_array();
$sth->finish();
$html_code=CGI::escapeHTML($html_code);
if ($comm_wizard_cid == 0)
{
	$comm_wizard_cid='';
} 
if ($comm_wizard_progid == 0)
{
	$comm_wizard_progid='';
}

util::header("Edit Creative");
if ( $pmesg ne "" )
{
    #---------------------------------------------------------------------------
    # Display mesg (if present) from module that called this module.
    #---------------------------------------------------------------------------
    print qq{ <script language="JavaScript">  \n } ;
    print qq{   alert("$pmesg");  \n } ;
    print qq{ </script>  \n } ;
}	
print << "end_of_html";
    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans se
rif" color=#509C10
            size=3><B>Creative Information</B> </FONT></TD>
        </TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
        </TR>
        </TBODY>
        </TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#50
9C10 size=2>
end_of_html

    print qq{ To UPDATE the creative information please make  \n } ;
    print qq{ the appropriate changes and select <B>Save</B>. \n } ;

print << "end_of_html" ;
            </FONT></TD>
        </TR>
        </TBODY>
        </TABLE>
<script language=JavaScript>
        function SaveFunc(btn)
        {
            document.campform.nextfunc.value = btn;
            document.campform.submit();
        }
		function test_emailreach(url,subj) {
			header_id=document.campform.header_id.value;
			footer_id=document.campform.content_id.value;
			body_id=document.campform.body_id.value;
			subject_id=document.campform.default_subject.value;
			var count=0;
			var i;
			var selectedArray=new Array();
			for (i=0; i< document.campform.styleID.length; i++) {
				if (campform.styleID.options[i].selected) {
					selectedArray[count]=campform.styleID.options[i].value;
					count++;
				}
			}
			var styleID=selectedArray;
//			url=url+"&headID="+header_id+"&footID="+footer_id+"&bodyID="+body_id;
			if (subj) {
				url=url+"&headID="+header_id+"&footID="+footer_id+"&bodyID="+body_id+"&styleID="+styleID+"&subjID="+subject_id;
			}
			else {
				url=url+"&headID="+header_id+"&footID="+footer_id+"&bodyID="+body_id+"&styleID="+styleID;
			}
			window.open(url,'EmailReach','width=850,height=650,toolbar=no,location=no,scrollbars=yes,resizable=no');
		}
function popup_validate()
{
	url="http://validator.w3.org/#validate_by_input";
	window.open(url,'Validate','width=850,height=650,toolbar=no,location=no,scrollbars=yes,resizable=no');
}
</script>
<br>

<center><a href="Javascript:test_emailreach('/cgi-bin/emailreach_creative.cgi?aid=$puserid&cid=$cid&add_adv=Y');">EmailReach - Test Creative</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="Javascript:test_emailreach('/cgi-bin/emailreach_creative.cgi?aid=$puserid&cid=$cid&subject=Y&add_adv=Y');">EmailReach - Test Creative with All Subjects</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="Javascript:test_emailreach('/cgi-bin/emailreach_creative.cgi?aid=$puserid&cid=$cid&add_adv=N','1');">EmailReach - Test Creative with Specified Subject</a>

<br><a href="/cgi-bin/del_mon_tag.cgi?aid=$puserid&cid=$cid&ctype=D&add_adv=Y" target=_blank>Delivery Monitor - Test Creative</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/del_mon_tag.cgi?aid=$puserid&cid=$cid&subject=Y&ctype=D&add_adv=Y" target=_blank>Delivery Monitor - Test Creative with All Subjects</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/del_mon_tag.cgi?aid=$puserid&cid=$cid&add_adv=N&ctype=D" target=_blank>Delivery Monitor - Test Creative with Specified Subject</a>
<br><a href="Javascript:test_emailreach('/cgi-bin/emailreach_creative.cgi?aid=$puserid&cid=$cid&add_adv=Y&ctype=H');">Habeas - Test Creative</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="Javascript:test_emailreach('/cgi-bin/emailreach_creative.cgi?aid=$puserid&cid=$cid&subject=Y&add_adv=Y&cytpe=H');">Habeas - Test Creative with All Subjects</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="Javascript:test_emailreach('/cgi-bin/emailreach_creative.cgi?aid=$puserid&cid=$cid&add_adv=N&ctype=H','1');">Habeas - Test Creative with Specified Subject</a>
</center>
<form name=campform method=post action="/cgi-bin/upd_creative.cgi" ENCTYPE="multipart/form-data" accept-charset="UTF-8">
        <INPUT type=hidden name=nextfunc>
<input type=hidden name=aid value=$puserid>
<input type=hidden name=cid value=$cid>
<input type=hidden name=backto value=$backto>
<input type=hidden name=old_thumbnail value=$thumbnail>
<br>
Advertiser: <b>$name</b><br><br>
<b>Creative Name:</b><br>
											<input maxLength="255" size="50" name="creative_name" value="$create_name"><br><br>

<b>Original Creative </b>
end_of_html
if ($original_flag eq "Y")
{
	print "<input type=\"checkbox\" checked name=\"original_flag\" value=\"Y\">(this will checked if we designed the creative and will be noted anywhere creative can be selected)<br>\n";
}
else
{
	print "<input type=\"checkbox\" name=\"original_flag\" value=\"Y\">(this will checked if we designed the creative and will be noted anywhere creative can be selected)<br>\n";
}
print<<"end_of_html";
<br>
<b>Copywriter Creative </b>
end_of_html
if ($copywriter eq "Y")
{
	print "<input type=\"checkbox\" checked name=\"copywriter\" value=\"Y\"><br>\n";
}
else
{
	print "<input type=\"checkbox\" name=\"copywriter\" value=\"Y\"><br>\n";
	$copyname="";
}
print "<b>Copywriter Name: </b><select name=copywriter_name>";
my @CWRITER=("","ao","do","ws","jw");
my $i=0;
while ($i <= $#CWRITER)
{
	if ($CWRITER[$i] eq $copyname)
	{
		print "<option selected value=$CWRITER[$i]>$CWRITER[$i]</option>\n";
	}
	else
	{
		print "<option value=$CWRITER[$i]>$CWRITER[$i]</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select>
<br>
<br>
<b>Trigger E-mail </b>
end_of_html
if ($trigger_flag eq "Y")
{
	print "<input checked type=checkbox name=trigger_flag value=\"Y\"><br><br>\n";
}
else
{
	print "<input type=checkbox name=trigger_flag value=\"Y\"><br><br>\n";
}
print<<"end_of_html";
<b>Approved by Advertiser </b>
end_of_html
if ($approved_flag eq "Y")
{
	print "Yes<br>\n";
}
else
{
	print "No<br>\n";
}
print<<"end_of_html";
<br>
<b>Date of Creative: (default = today) </b>(&quot;Since&quot; variable on thumbnail and creative drop down)<br>
											<input maxLength="8" size="10" name="creative_date" value="$creative_date"><br><br>
<b>Inactive Date: (MM/DD/YY) </b><br>
											<input maxLength="8" size="10" name="inactive_date" value="$inactive_date"><br><br><b>
<b>
Unsubscribe Image (when turned on, this will include the unsub image from the advertiser page)</b><br>
<select name="unsub_image">
end_of_html
if ($unsub_image eq "NONE")
{
	print "<option selected value=\"NONE\">NONE</option>\n";
}
else
{
	print "<option value=\"NONE\">NONE</option>\n";
}
if ($unsub_image eq "Justify Left")
{
	print "<option selected value=\"Justify Left\">Justify Left</option>\n";
}
else
{
	print "<option value=\"Justify Left\">Justify Left</option>\n";
}
if ($unsub_image eq "Justify Center")
{
	print "<option selected value=\"Justify Center\">Justify Center</option>\n";
}
else
{
	print "<option value=\"Justify Center\">Justify Center</option>\n";
}
print<<"end_of_html";
</select><br><br>
<b>
Default Subject: (if none selected)<br>
											</b>
<select name="default_subject">
end_of_html
#
#   Get subjects for advertiser
#
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag,copywriter from advertiser_subject where advertiser_id=$puserid and status='A' order by advertiser_subject";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $csubject;
while (($sid,$csubject,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $csubject . " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
	if ($copywriter eq "Y")
	{
        $temp_str = $temp_str . "C ";
	}
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	if ($default_subject == $sid)
	{
    	print "<option selected value=\"$sid\">$temp_str</option>\n";
	}
	else
	{
    	print "<option value=\"$sid\">$temp_str</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br>
<br><b>Default From: (if none selected)</b><br>
											<select name="default_from">
end_of_html
#
#   Get from lines for advertiser
#
$sql = "select from_id,advertiser_from,approved_flag,original_flag,copywriter from advertiser_from where advertiser_id=$puserid order by advertiser_from";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $cfrom;
while (($fid,$cfrom,$aflag,$oflag,$copywriter) = $sth->fetchrow_array())
{
    $temp_str = $cfrom . " (";
    if ($oflag eq "Y")
    {
        $temp_str = $temp_str . "O ";
    }
    else
    {
        $temp_str = $temp_str . "A ";
    }
   	if ($copywriter eq "Y") 
    {
        $temp_str = $temp_str . "C ";
    }
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
	if ($default_from == $fid)
	{
    	print "<option selected value=\"$fid\">$temp_str</option>\n";
	}
	else
	{
    	print "<option value=\"$fid\">$temp_str</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br><br>
<b>Advertiser Approval E-mail: </b>(send only works once the deal is saved)<br>
<select name="from14">
end_of_html
$sql="select email_addr from advertiser_approval where advertiser_id=$puserid order by email_addr";
$sth = $dbhq->prepare($sql);
$sth->execute();
my $email_addr;
while (($email_addr) = $sth->fetchrow_array())
{
        print "<option value=$email_addr>$email_addr</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Send" name="B14"><br>
<br>
<b>Directory of images for Creative (If html file is local and the images are not stored anywhere):</b><br>
											<input type="file" maxLength="255" size="50" name="image_directory"><br><br>
										
end_of_html
if ($thumbnail ne "")
{	
	print "<b>Thumbnail for Creative:(<a href=\"http://www.affiliateimages.com/images/thumbnail/$thumbnail\" target=_blank>$thumbnail</a>)</b><br>\n";
}
else
{
	print "<b>Thumbnail for Creative:</b><br>\n";
}
print<<"end_of_html";
											<input type=file maxLength="255" size="50" name="thumbnail"><br><br>
<u><b>Footer Content:</b></u><br>
<select name=content_id><option selected value=0>None</option>
end_of_html
$sql="select footer_content.content_id,content_name,content_date from footer_content,content_category where (inactive_date > curdate() or inactive_date='0000-00-00') and category_id=$cat_id and content_category.content_id=footer_content.content_id";
my $sth1 = $dbhq->prepare($sql);
$sth1->execute();
my $temp_cid;
my $cname;
my $temp_cdate;
while (($temp_cid,$cname,$temp_cdate) = $sth1->fetchrow_array())
{
	if ($content_id == $temp_cid)
	{
		print "<option selected value=$temp_cid>$cname ($temp_cdate)</option>\n"; 
	}
	else
	{
		print "<option value=$temp_cid>$cname ($temp_cdate)</option>\n"; 
	}
}
$sth1->finish();
print qq^
</select><br><br>
<u><b>Header Content:</b></u><br>
<select name=header_id><option selected value=0>None</option>^;
my $quer=qq|SELECT id,header_name,date_add FROM header_content WHERE (inactive_date>CURDATE() OR inactive_date='0000-00-00')|;
my $sthQ=$dbhq->prepare($quer);
$sthQ->execute;
while (my $hr=$sthQ->fetchrow_hashref) {
	my $selected=$header_id==$hr->{id} ? "SELECTED" : "";
	print qq^<option value="$hr->{id}" $selected>$hr->{header_name} ($hr->{date_add})\n^;
}
$sthQ->finish;
print qq^</select><br><br>
<u><b>Body Content:</b></u><br>
<select name=body_id><option selected value=0>None</option>^;
my $quer=qq|SELECT id,body_name,date_add FROM body_content WHERE (inactive_date>CURDATE() OR inactive_date='0000-00-00')|;
my $sthQ=$dbhq->prepare($quer);
$sthQ->execute;
while (my $hr=$sthQ->fetchrow_hashref) {
    my $selected=$body_id==$hr->{id} ? "SELECTED" : "";
    print qq^<option value="$hr->{id}" $selected>$hr->{body_name} ($hr->{date_add})\n^;
}
$sthQ->finish;
print qq^</select><br><br>
<u><b>Style Content</b></u><br>
<font size=2 color=red face='arial'>List below are available style content(s). Pick the one you want to add for this creative OR hold the 'Ctrl' key and click on the available style to select multiple style content(s). Click "Save" will save for this creative or click "EmailReach Test links" at top will send a emailreach test.</font><br><br>^;
my @data;
my $qStyle=qq|SELECT id,style_name FROM style_content WHERE (inactive_date>CURDATE() OR inactive_date='0000-00-00')|;
my $sStyle=$dbhq->prepare($qStyle);
$sStyle->execute;
while (my $hr=$sStyle->fetchrow_hashref) {
	push @data, $hr;
}
$sStyle->finish;
style_options(\@data,$style_id);
print qq^<br><br>^;
print<<"end_of_html";
<b>Commission Wizard CWC3:</b><input maxLength="10" value="$c3" size="11" name="comm_wizard_c3"><br><br>
<b>Commission Wizard CWCID:</b><input maxLength="10" value="$comm_wizard_cid" size="11" name="comm_wizard_cid"><br><br>
<b>Commission Wizard CWprogID:</b><input maxLength="10" value="$comm_wizard_progid" size="11" name="comm_wizard_progid"><br><br>
<b>Creative CR:</b><input maxLength="10" value="$cr" size="11" name="cr"><br><br>
<b>Landing Page L:</b><input maxLength="2" value="$landing_page" size="3" name="landing_page"><br><br>
end_of_html

if ($replace_flag eq "N")
{
print<<"end_of_html";
<b>Do not automatically replace href's with {{URL}}</b><input type=checkbox checked name="replace_flag" value="N"><br><br>
end_of_html
}
else
{
print<<"end_of_html";
<b>Do not automatically replace href's with {{URL}}</b><input type=checkbox name="replace_flag" value="N"><br><br>
end_of_html
}
if ($original_html ne "")
{
print "<a href=view_creative.cgi?cid=$cid target=_blank>View Original HTML</a><br>\n";
}
my $chkstr="";
if ($host_images eq "Y")
{
	$chkstr="checked";
}
print<<"end_of_html";
<u><b>HTML Code:</b></u>&nbsp;&nbsp;Host Images: <input type=checkbox $chkstr value=Y name=host_images><br>
											

<textarea name="html_code" rows="15" cols="100">$html_code</textarea>
									<table cellPadding="5" width="66%" bgColor="white" id="table1">
										<tr>
											<td align="middle" width="47%">
											<a href="JavaScript:SaveFunc('home');">
											<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
											<td align="middle" width="47%">
											<a href="JavaScript:SaveFunc('save');">
											<img height="22" src="/images/save_rev.gif" width="81" border="0"></a></td>
											<td align="middle" width="50%">
											<a href="JavaScript:SaveFunc('preview');">
											<img height="22" src="/images/preview_rev.gif" width="81" border="0"></a></td>
										</tr>
									</table>
</form>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:SaveFunc('spam');">Run Spam Report</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="banned.html">Find/Replace Banned Words</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:SaveFunc('url');">Find/Replace URL</a></p>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:popup_validate();">Display Validate URL</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:SaveFunc('render');">Render HTML</a>
end_of_html

$util->footer();
$util->clean_up();

sub style_options {
	my ($data,$value)=@_;

	my $hr={};
	my @style=split(',', $value);
	foreach (@style) {
		$hr->{$_}=$_;
	}
	print qq^
	<select name="styleID" multiple size=6>
	^;
	foreach my $style (@$data) {
		my $selected=$hr->{$style->{id}} ? "SELECTED" : "";
		print qq^<option value="$style->{id}" $selected>$style->{style_name}\n^;
	}
	print qq^</select>^;
}

exit(0);
