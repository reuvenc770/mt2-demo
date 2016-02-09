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
my $cdate;
my $puserid; 
my $pmode;
my $pmesg;
my $sid;
my $temp_str;
my $fid;
my $aflag;
my $oflag;

#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;

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
$pmesg   = $query->param('pmesg');

#------  Get the information about the user for display  --------
$sql = "select advertiser_name,date_format(curdate(),\"%m/%d/%y\") from advertiser_info where advertiser_id = $puserid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($name,$cdate) = $sth->fetchrow_array();
$sth->finish();

util::header("Add Creative");
	
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
</script>
<form name=campform method=post action="/cgi-bin/insert_creative.cgi" ENCTYPE="multipart/form-data">
        <INPUT type=hidden name=nextfunc>
<input type=hidden name=aid value=$puserid>
<br>
Advertiser: <b>$name</b><br><br>
<b>Creative Name:</b><br>
											<input maxLength="255" value="" size="50" name="creative_name"><br><br>

<b>Original Creative </b>
<input type="checkbox" name="original_flag" size="40" maxlength="90" value="Y">(this 
will checked if we designed the creative and will be noted anywhere creative can be selected)<br>
<br>
<b>Trigger E-mail </b>
<input type="checkbox" name="trigger_flag" size="40" maxlength="90" value="Y"><br><br>

<b>Date of Creative: (default = today) </b>(&quot;Since&quot; variable on thumbnail and creative drop down)<br>
											<input maxLength="8" size="10" name="creative_date" value="$cdate"><br><br>
<b>Inactive Date: (MM/DD/YY) </b><br>
											<input maxLength="8" value="" size="10" name="inactivate_date"><br><br><b>
<b>
Unsubscribe Image (when turned on, this will include the unsub image from the advertiser page)</b><br>
											<select name="unsub_image">

<option value="NONE">
NONE</option>
<option value="Justify Left">
Justify Left</option>
<option selected value="Justify Center">
Justify Center</option>
</select><br><br>
<b>
Default Subject: (if none selected)<br>
											</b>
<select name="default_subject">
end_of_html
#
#   Get subjects for advertiser
#
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag from advertiser_subject where advertiser_id=$puserid and status='A' order by advertiser_subject";
$sth = $dbh->prepare($sql);
$sth->execute();
my $csubject;
while (($sid,$csubject,$aflag,$oflag) = $sth->fetchrow_array())
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
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
    print "<option value=\"$sid\">$temp_str</option>\n";
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
$sql = "select from_id,advertiser_from,approved_flag,original_flag from advertiser_from where advertiser_id=$puserid order by advertiser_from";
$sth = $dbh->prepare($sql);
$sth->execute();
my $cfrom;
while (($fid,$cfrom,$aflag,$oflag) = $sth->fetchrow_array())
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
    if ($aflag eq "Y")
    {
        $temp_str = $temp_str . ")";
    }
    else
    {
        $temp_str = $temp_str . "- NA!)";
    }
    print "<option value=\"$fid\">$temp_str</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><br><br>
<b>Advertiser Approval E-mail: </b>(send only works once the deal is saved)<br>
<select name="from14">
end_of_html
$sql="select email_addr from advertiser_approval where advertiser_id=$puserid order by email_addr";
$sth = $dbh->prepare($sql);
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
											
<b>Thumbnail for Creative:</b><br>
											<input type=file maxLength="255" size="50" name="thumbnail"><br><br>
<u><b>HTML Code:</b></u><br>
											

<textarea name="html_code" rows="15" cols="100">
</textarea>
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
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:SaveFunc('spam');">Run Spam Report</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="banned.html">Find/Replace Banned Words</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="JavaScript:SaveFunc('url');">Find/Replace URL</a></p>
end_of_html

$util->footer();
$util->clean_up();
exit(0);
