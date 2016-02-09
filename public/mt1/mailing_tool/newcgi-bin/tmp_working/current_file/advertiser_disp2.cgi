#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser data (eg 'user' table).
# Name   : advertiser_disp.cgi (edit_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/05/04  Jim Sobeck  Creation
# 10/27/05  Jim Sobeck  Added advertiser rating
#==============================================================================

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
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($status, $max_names, $max_mailings, $username);
my $password;
my $internal_email_addr;
my $physical_addr;
my $cstatus;
my ($puserid, $pmesg);
my $company;
my $website_url;
my $company_phone;
my $user_type;
my $this_user_type;
my $images = $util->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;
my $prepop;
my $advertiser_rating;
my $name;
my $puserid; 
my $pmode;
my $pmesg;
my ($cname,$phone,$cemail,$company,$aim,$website,$username,$password,$notes);
my $offer_type;
my $payout;
my $advertiser_url;
my $ecpm;
my $exclude_days;
my $pixel_placed;
my $pixel_requested;
my $pixel_verified;
my $tracking_pixel;
my $vendor_suppid;
my $supp_file;
my $supp_url;
my $supp_username;
my $supp_password;
my $auto_download;
my ($track_internally,$unsub_link,$unsub_image);
my $adv_catid;

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

$sql = "select user_type from user where user_id = $user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($this_user_type) = $sth->fetchrow_array() ;
$sth->finish();

#--------------------------------
# get CGI Form fields
#--------------------------------
$puserid = $query->param('puserid');
$pmesg   = $query->param('pmesg');

#------  Get the information about the user for display  --------
$sql = "select advertiser_name,email_addr,internal_email_addr,physical_addr,status,offer_type,payout,ecpm,tracking_pixel,pixel_placed,pixel_requested,exclude_days,vendor_supp_list_id,suppression_file,suppression_url,auto_download,suppression_username,suppression_password,track_internally,unsub_link,unsub_image,category_id,advertiser_url,pixel_verified, pre_pop,advertiser_rating from advertiser_info where advertiser_id = $puserid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($name,$email_addr,$internal_email_addr,$physical_addr,$cstatus,$offer_type,$payout,$ecpm,$tracking_pixel,$pixel_placed,$pixel_requested,$exclude_days,$vendor_suppid,$supp_file,$supp_url,$auto_download,$supp_username,$supp_password,$track_internally,$unsub_link,$unsub_image,$adv_catid,$advertiser_url,$pixel_verified,$prepop,$advertiser_rating) = $sth->fetchrow_array();
$sth->finish();
#
#------  Get the information about the user for display  --------
#
$sql = "select contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes from advertiser_contact_info where advertiser_id = $puserid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($cname,$phone,$cemail,$company,$aim,$website,$username,$password,$notes) = $sth->fetchrow_array();
$sth->finish();
	
if ( $name eq "" ) 
{
	$errmsg = $dbh->errstr();
    util::logerror("<br><br>Error Getting user information for AdvertiserID: $puserid &nbsp;&nbsp;$errmsg");
	exit(99) ;
}

util::header("Edit Advertiser Information");
	
if ( $pmesg ne "" ) 
{
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
	print qq{ 	alert("$pmesg");  \n } ;
	print qq{ </script>  \n } ;
}

print << "end_of_html";
<script language="JavaScript">
function delete_tracking()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/disp_tracking.cgi?aid=$puserid&tid="+document.edit_advertiser.advertiser_url.value;
	document.edit_advertiser.submit();
	}
}
function edit_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/edit_creative.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function delete_creative()
{
	if (ProcessForm('A'))
	{
        if (confirm("Are you sure you want to delete the creative?"))
		{
			document.edit_advertiser.backto.value="/cgi-bin/delete_creative.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
			document.edit_advertiser.submit();
		}
	}
}
function delete_unsubimg()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/delete_unsubimg.cgi?aid=$puserid&cid="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/subject.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function edit_subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/edit_subject.cgi?aid=$puserid&sid="+document.edit_advertiser.csubject.value;
	document.edit_advertiser.submit();
	}
}
function delete_subject()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/del_subject.cgi?aid=$puserid&sid="+document.edit_advertiser.csubject.value;
	document.edit_advertiser.submit();
	}
}
function tracking()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/tracking.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function gen_tracking()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/gen_tracking.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function add_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/from.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function edit_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/edit_from.cgi?aid=$puserid&sid="+document.edit_advertiser.from.value;
	document.edit_advertiser.submit();
	}
}
function delete_from()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/del_from.cgi?aid=$puserid&sid="+document.edit_advertiser.from.value;
	document.edit_advertiser.submit();
	}
}
function contact()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/contact.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function approval()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/approval.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function update_seeds()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/advertiser_seedlist.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function preview_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/camp_preview.cgi?format=H&campaign_id="+document.edit_advertiser.creative.value;
	document.edit_advertiser.submit();
	}
}
function add_creative()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/add_creative.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
function update_approval()
{
	if (ProcessForm('A'))
	{
	document.edit_advertiser.backto.value="/cgi-bin/advertiser_approval.cgi?aid=$puserid";
	document.edit_advertiser.submit();
	}
}
</script>
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Advertiser Information</B> </FONT></TD>
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
end_of_html

	print qq{ To UPDATE the advertiser information please make  \n } ;
	print qq{ the appropriate changes and select <B>Save</B>. \n } ;

print << "end_of_html" ;
			</FONT></TD>
		</TR>
		</TBODY>
		</TABLE>
   	<script language="JavaScript">
   	function ProcessForm(Mode)
   	{
        var iopt;
        // validate your data first
        iopt = check_mandatory_fields();
        if (iopt == 0)
        {
            return false;
        }

        // if ok, go on to save
        return true;
    }

    function check_mandatory_fields()
    {
        if (document.edit_advertiser.name.value == "")
        {
            alert("You MUST enter a value for the Advertiser Name field."); 
			document.edit_advertiser.name.focus();
            return false;
        }
        if (document.edit_advertiser.address.value == "")
        {
            alert("You MUST enter a value for the Physical Address field."); 
			document.edit_advertiser.address.focus();
            return false;
        }
		return true;
	}
</script>
end_of_html

if ($this_user_type eq "A") 
{
print <<"end_of_html";

        <FORM name=edit_advertiser action="advertiser_upd2.cgi" method=post onsubmit="return ProcessForm('A');" ENCTYPE="multipart/form-data">
		<input type=hidden name=backto value="">
		<input type=hidden name=oldpixelverified value="$pixel_verified">
		<input type=hidden name=old_unsub value="$unsub_image">

        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#E3FAD1 border=0>
                <TBODY>
                <TR align=top bgColor=#509C10 height=18>
                <TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
					border=0 width="7" height="7"></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15><FONT 
						face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
						<B>Advertiser Information</B></FONT></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
                    src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD align=middle>
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
                    <TR> <!-- -------- Advertiser Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Advertiser Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 maxlength=50 value="$name" name=name>
						</FONT>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/replace_url.cgi?aid=$puserid">Find/Replace URLs</a>&nbsp;&nbsp;<a href="/cgi-bin/rep_adv_subject_creative.cgi?aid=$puserid" target=_blank>Subject/Creative Stats</a>&nbsp;&nbsp;<a href="/cgi-bin/replace_advertiser.cgi?aid=$puserid">Replace Advertiser</a></td>
					</TR>
                    <TR> <!-- -------- Email Addr -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Advertiser Email Addresses: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=255 value="$email_addr" name=email_addr></FONT>&nbsp;&nbsp;<a href="/cgi-bin/advertiser_setup_new.cgi?aid=$puserid">Setup Creative/Subject/From Rotation</a></TD>
					</TR>

                    <TR> <!-- -------- Internal Email Addr -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Internal Email Addresses: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=255 value="$internal_email_addr" name=internal_email_addr></FONT></TD>
					</TR>

					<TR> <!-- -------- Physical Address -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Address: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<textarea name=address rows=5 cols=80>$physical_addr</textarea></FONT></TD>
						<INPUT type="hidden" name="puserid" value="$puserid">
						<INPUT type="hidden" name="pmode" value="$pmode">
					</TR>
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
<tr><td valign=top><a href="javascript:contact();">Contact Info</a>: <br>&nbsp;</b></td>
<td>
<table border="1" width="59%" id="table1">
	<tr>
		<td width="119"><b>Contact</b></td>
		<td>$cname</td>
	</tr>
	<tr>
		<td width="119"><b>Phone</b></td>
		<td>$phone</td>
	</tr>
	<tr>
		<td width="119"><b>Email</b></td>
		<td><a href="mailto:$cemail">$cemail</a></td>
	</tr>
	<tr>
		<td width="119"><b>Company Name</b></td>
		<td>$company</td>
	</tr>
	<tr>
		<td width="119"><b>AIM</b></td>
		<td>$aim</td>
	</tr>
	<tr>
		<td width="119"><b>Reporting Website</b></td>
		<td><a href="$website" target=_blank>$website</a></td>
	</tr>
	<tr>
		<td width="119"><b>Username</b></td>
		<td>$username</td>
	</tr>
	<tr>
		<td width="119"><b>Password</b></td>
		<td>$password</td>
	</tr>
		<tr>
		<td width="119"><b>Notes</b></td>
		<td>$notes</td>
	</tr>
</table></p><p>
</td></tr>
<tr><td> <b>Status:</b><br></td>
end_of_html
	if ($cstatus eq "A")
	{
		print "<td align=left><input type=radio name=cstatus value=\"S\">Setup&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus checked value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"I\">Inactive&nbsp;&nbsp;<input type=radio name=cstatus value=\"D\">Deleted<br><br></td>\n";
	}
	elsif ($cstatus eq "S")
	{
		print "<td align=left><input type=radio name=cstatus checked value=\"S\">Setup&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"I\">Inactive&nbsp;&nbsp;<input type=radio name=cstatus value=\"D\">Deleted<br><br></td>\n";
	}
	elsif ($cstatus eq "I")
	{
		print "<td align=left><input type=radio name=cstatus value=\"S\">Setup&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio checked name=cstatus value=\"I\">Inactive&nbsp;&nbsp;<input type=radio name=cstatus value=\"D\">Deleted<br><br></td>\n";
	}
	else
	{
		print "<td align=left><input type=radio name=cstatus value=\"S\">Setup&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"I\">Inactive&nbsp;&nbsp;<input type=radio name=cstatus checked value=\"D\">Deleted<br><br></td>\n";
	}
print<<"end_of_html";
<tr><td> <b>Type:</b><br></td>
<td>
end_of_html
if ($offer_type eq "CPA")
{
	print "<input type=\"radio\" CHECKED value=\"CPA\" name=\"deal_type\"> CPA\n";
}
else
{
	print "<input type=\"radio\" value=\"CPA\" name=\"deal_type\"> CPA\n";
}
if ($offer_type eq "CPC")
{
	print "<input type=\"radio\" CHECKED value=\"CPC\" name=\"deal_type\"> CPC\n";
}
else
{
	print "<input type=\"radio\" value=\"CPC\" name=\"deal_type\"> CPC\n";
}
if ($offer_type eq "CPS")
{
	print "<input type=\"radio\" CHECKED value=\"CPS\" name=\"deal_type\"> CPS\n";
}
else
{
	print "<input type=\"radio\" value=\"CPS\" name=\"deal_type\"> CPS\n";
}
if ($offer_type eq "CPM")
{
	print "<input type=\"radio\" CHECKED value=\"CPM\" name=\"deal_type\"> CPM\n";
}
else
{
	print "<input type=\"radio\" value=\"CPM\" name=\"deal_type\"> CPM\n";
}
print<<"end_of_html";
<br><br>
</td></tr>
<tr><td> <b>Pixel Verified:</b><br></td>
<td>
end_of_html
if ($pixel_verified eq "Y")
{
	print "<input type=radio CHECKED value=Y name=pixel_verified> Y\n";
}
else
{
	print "<input type=radio value=Y name=pixel_verified> Y\n";
}
if ($pixel_verified eq "N")
{
	print "<input type=radio CHECKED value=N name=pixel_verified> N\n";
}
else
{
	print "<input type=radio value=N name=pixel_verified> N\n";
}
if ($pixel_verified eq "?")
{
	print "<input type=radio CHECKED value=? name=pixel_verified> ?\n";
}
else
{
	print "<input type=radio value=? name=pixel_verified> ?\n";
}
print<<"end_of_html";
<br><br>
</td></tr>
<tr><td> <b>Pre-pop Supported:</b><br></td>
<td>
end_of_html
my $check1=($prepop eq 'Y') ? 'CHECKED' : '';
my $check2=($prepop eq 'N') ? 'CHECKED' : '';
print qq^	<input type='radio' value='Y' name='prepop' $check1>Y&nbsp;
		<input type='radio' value='N' name='prepop' $check2>N\n^;
print<<"end_of_html";
<br><br>
</td></tr>
<tr><td valign=center> <b>Advertiser Rating:</b></td>
<td valign=center><select name="advertiser_rating">
end_of_html
my $i=0;
my $name_str;
while ($i <= 5)
{
	$name_str=$i;
	if ($i == 0)
	{
		$name_str = "None";
	}
	if ($i == $advertiser_rating)
	{
		print "<option selected value=$i>$name_str</option>\n";
	}
	else
	{
		print "<option value=$i>$name_str</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
<br><br>
<tr><td colspan=2>&nbsp;</td></tr>
<tr><td valign=top> <b>Mediactivate Tracking Pixel:</b><br></td>
<td>											<input maxLength="255" size="80" name="tracking_pixel" value='$tracking_pixel'><br><br>
<b>Pixel Requested</b>
end_of_html
if ($pixel_requested eq "Y")
{
	print "<input type=\"checkbox\" name=\"pixel_requested\" checked value=\"Y\">\n";
}
else
{
	print "<input type=\"checkbox\" name=\"pixel_requested\" value=\"Y\">\n";
}
print<<"end_of_html";
<b>Pixel Placed</b>
end_of_html
if ($pixel_placed eq "Y")
{
	print "<input type=\"checkbox\" name=\"pixel_placed\" checked value=\"Y\">\n";
}
else
{
	print "<input type=\"checkbox\" name=\"pixel_placed\" value=\"Y\">\n";
}
print<<"end_of_html";
<br>
<br>
</td></tr>
<tr><td valign=top> <b>Advertiser URL:</b><br></td>
<td><input maxLength="255" size="80" name="orig_advertiser_url" value='$advertiser_url'></td></tr>
<tr><td> <b>Payout:</b><br></td>
<td>
											<input maxLength="255" size="50" value="$payout" name="payout"><br><br>
</td></tr>
<tr><td>
<b>eCPM: </b><br></td>
<td><input maxLength="255" size="50" value="$ecpm" name="ecpm"><br><br>
</td></tr>
<tr><td valign=top> <b>Days to exclude: </b><br></td>
<td>
end_of_html
if (substr($exclude_days,0,1) eq "Y")
{
	print "<input type=checkbox checked value=1 name=ex_monday>Monday</option>\n";
}
else
{
	print "<input type=checkbox value=1 name=ex_monday>Monday</option>\n";
}
if (substr($exclude_days,1,1) eq "Y")
{
	print "<input type=checkbox checked value=2 name=ex_tuesday>Tuesday</option>\n";
}
else
{
	print "<input type=checkbox value=2 name=ex_tuesday>Tuesday</option>\n";
}
if (substr($exclude_days,2,1) eq "Y")
{
	print "<input type=checkbox checked value=3 name=ex_wednesday>Wednesday</option>\n";
}
else
{
	print "<input type=checkbox value=3 name=ex_wednesday>Wednesday</option>\n";
}
if (substr($exclude_days,3,1) eq "Y")
{
	print "<input type=checkbox checked value=4 name=ex_thursday>Thursday</option>\n";
}
else
{
	print "<input type=checkbox value=4 name=ex_thursday>Thursday</option>\n";
}
if (substr($exclude_days,4,1) eq "Y")
{
	print "<input type=checkbox checked value=5 name=ex_friday>Friday</option>\n";
}
else
{
	print "<input type=checkbox value=5 name=ex_friday>Friday</option>\n";
}
if (substr($exclude_days,5,1) eq "Y")
{
	print "<input type=checkbox checked value=6 name=ex_saturday>Saturday</option>\n";
}
else
{
	print "<input type=checkbox value=6 name=ex_saturday>Saturday</option>\n";
}
if (substr($exclude_days,6,1) eq "Y")
{
	print "<input type=checkbox checked value=7 name=ex_sunday>Sunday</option>\n";
}
else
{
	print "<input type=checkbox value=7 name=ex_sunday>Sunday</option>\n";
}
print<<"end_of_html";
</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>
<b><u>Existing Suppression Files</u>: </b>(to be used if there are multiple 
offers using the same file)<br></td>
<td><select name="suppid1">
end_of_html
$sql="select list_id,list_name from vendor_supp_list_info order by list_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $sid;
my $sname;
while (($sid,$sname) = $sth->fetchrow_array())
{
	if ($vendor_suppid == $sid)
	{
		print "<option selected value=$sid>$sname</option>\n";
	}
	else
	{
		print "<option value=$sid>$sname</option>\n";
	}
} 
$sth->finish();
print<<"end_of_html";
</select>
</td></tr>
<tr><td valign=top><b>Suppression File:</b><br></td>
<td><input type="file" maxLength="255" size="50" name="supp_file" value=$supp_file><br></td></tr>
<tr><td valign=top><b>File Date(yyyy-mm-dd):</b><br></td>
<td><input type="text" maxLength="20" size="20" name="filedate">&nbsp;&nbsp;Upload Immediately&nbsp;<input type=checkbox value="Y" name="immediate_upload"><br><br></td></tr>
<tr><td valign=top><b>Suppression URL: (just to know where to access the files for quick reference)</b><br></td>
<td><input maxLength="255" size="50" value="$supp_url" name="supp_url">
end_of_html
if ($auto_download eq "Y")
{
	print "<input type=\"checkbox\" name=\"auto_download\" checked value=\"Y\">(check box for auto download once a week)";
}
else
{
	print "<input type=\"checkbox\" name=\"auto_download\" value=\"Y\">(check box for auto download once a week)";
}
print<<"end_of_html";
<br></td></tr>
<tr><td valign=top><b>Username:<br> </b></td>
<td><input maxLength="255" size="50" name="supp_username" value="$supp_username"><br></td></tr>
<tr><td valign=top><b>Password:</b><br></td>
<td><input maxLength="255" size="50" name="supp_password" value="$supp_password"><br><br></td></tr>
<tr><td colspan=2 valign=top>
end_of_html
if ($track_internally eq "Y")
{
	print "<input type=checkbox name=track_internally value=Y checked><b>Unsubscribe Link Tracked by Us<br></b>\n";
}
else
{
	print "<input type=checkbox name=track_internally value=Y><b>Unsubscribe Link Tracked by Us<br></b>\n";
}
print<<"end_of_html";
<br></td></tr>
<tr><td valign=top><b>Advertiser Unsubscribe URL</b><br></td>
<td><input maxLength="255" size="50" name="unsub_link" value="$unsub_link"><br><br></td></tr>
end_of_html
if ($unsub_image ne "")
{
	print "<tr><td valign=top><b>Unsubscribe Image: <a href=\"http://www.affiliateimages.com/images/unsub/$unsub_image\" target=\"_blank\">$unsub_image</a></b><br></td>\n";
}
else
{
	print "<tr><td valign=top><b>Unsubscribe Image: </b><br></td>\n";
}
print<<"end_of_html";
<td><input type="file" maxLength="255" size="50" name="unsub_image" value="">&nbsp;&nbsp;<input type="button" value="Delete" name="B22" onClick="delete_unsubimg();"><br><br><br></td></tr>
<tr><td valign=top><b>Seeded E-mail Addresses:</b>(get's any e-mail that goes out for this advertiser)<br></td>
<td><select name=seeded>
end_of_html
$sql="select email_addr from advertiser_seedlist where advertiser_id=$puserid order by email_addr";
$sth = $dbh->prepare($sql);
$sth->execute();
my $email_addr; 
while (($email_addr) = $sth->fetchrow_array())
{
	print "<option value=$email_addr>$email_addr</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Update Seeds" name="B22" onClick=update_seeds();><br><br><br><br></td></tr>
<tr><td valign=top><b><a href="javascript:approval();">Advertiser Approval E-mail Addresses:</a> </b><br></td>
<td><select name=approval>
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
</select><input type="button" value="Update Approval List" name="B22" onClick=update_approval();><br><br><br><br></td></tr>
<tr><td valign=top><b><a href="Javascript:tracking();">Redirect URL</a>: (in parens = listname)</b><br></td>
<td><select name=advertiser_url>
end_of_html
$sql = "select tracking_id,url,code,date_added,date_format(date_added,\"%m/%d/%y\"),company,daily_deal from advertiser_tracking, user where advertiser_id=$puserid and client_id=user.user_id order by mediactivate_id";
$sth = $dbh->prepare($sql);
$sth->execute();
my ($url,$code,$date_added,$fdate,$company,$tracking_id,$daily_deal);
while (($tracking_id,$url,$code,$date_added,$fdate,$company,$daily_deal) = $sth->fetchrow_array())
{
	if ($daily_deal eq "Y")
	{
		print "<option value=\"$tracking_id\">$url ($code - $company - Daily) $fdate</option>\n";
	}
	elsif ($daily_deal eq "T")
	{
		print "<option value=\"$tracking_id\">$url ($code - $company - Trigger) $fdate</option>\n";
	}
	else
	{
		print "<option value=\"$tracking_id\">$url ($code - $company) $fdate</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Delete" name="B22" onClick=delete_tracking();>&nbsp;&nbsp;<input type="button" value="Gen URLs" name="B22" onClick=gen_tracking();></br></br></td></tr>
<tr><td valign=top><b>Category:</b><br></td>
<td><select name="category_id">
end_of_html
#
#	Get categories
#
$sql = "select category_id,category_name from category_info order by category_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $catid;
my $cname;
while (($catid,$cname) = $sth->fetchrow_array())
{
	if ($adv_catid == $catid)
	{
		print "<option selected value=\"$catid\">$cname</option>\n";
	}
	else
	{
		print "<option value=\"$catid\">$cname</option>\n";
	}
}
$sth->finish();
print<<"end_of_html";
</select><br><br></td></tr>
<tr><td valign=top><a href="view_thumbnails.cgi?aid=$puserid" target=_blank><b>Creative:</b></a></td>
<td><select name="creative">
end_of_html
#
#   Get creative 
#
$sql = "select creative_id, creative_name,original_flag, trigger_flag, approved_flag from creative where status='A' and advertiser_id=$puserid order by creative_name";
$sth = $dbh->prepare($sql);
$sth->execute();
my $cid;
my $cname;
my $oflag;
my $tflag;
my $aflag;
my $temp_str;

while (($cid,$cname,$oflag,$tflag,$aflag) = $sth->fetchrow_array())
{
	$temp_str = $cname . " (";
	if ($tflag eq "Y")
	{
		$temp_str = $temp_str . "TRIGGER - ";
	}
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
	print "<option value=$cid>$temp_str</option>\n";
}
$sth->finish();
print<<"end_of_html";
</select><input type="button" value="Add" name="B21" onClick="add_creative();"><input type="button" value="Edit" onClick="edit_creative();"><input type="button" value="Delete" name="B22" onClick="delete_creative();"><input type="button" value="Clear Stats" name="B28"><input type="button" value="Preview" onClick="preview_creative();"><br><br></td></tr>
<tr><td valign=top><b><a href="Javascript:subject();">Subject</a>: </b>(Select a default (ALL is for clearing stats))</td> 
<td><select name="csubject">
end_of_html
#
#	Get subjects for advertiser
#
$sql = "select subject_id,advertiser_subject,approved_flag,original_flag from advertiser_subject where advertiser_id=$puserid and status='A' order by advertiser_subject";
$sth = $dbh->prepare($sql);
$sth->execute();
my $csubject;
my $sid;
my $aflag;
my $oflag;
while (($sid,$csubject,$aflag,$oflag) = $sth->fetchrow_array())
{
    $temp_str = $csubject. " (";
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
</select><input type="button" value="Add" name="B21" onClick="subject();"><input type="button" value="Edit" onClick="edit_subject();"><input type="button" value="Delete" name="B22" onClick="delete_subject();"><input type="button" value="Clear Stats" name="B29"><br><br></td></tr> 
<tr><td valign=top><b><a href="JavaScript:add_from();">From</a>: </b>(Select a default (ALL is for clearing stats))</td>
<td><select name="from">
end_of_html
#
#	Get from lines for advertiser
#
$sql = "select from_id,advertiser_from,approved_flag,original_flag from advertiser_from where advertiser_id=$puserid order by advertiser_from";
$sth = $dbh->prepare($sql);
$sth->execute();
my $cfrom;
my $fid;
my $aflag;
my $oflag;
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
</select><input type="button" value="Add" onClick="add_from();"><input type="button" value="Edit" onClick="edit_from();"><input type="button" value="Delete" onClick="delete_from();"><input type="button" value="Clear Stats" name="B29"><br><br></td></tr> 
                    <TR>
                    <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
					</TBODY>
					</TABLE>
				</TD>
                <TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
				</TR>
                <TR bgColor=#E3FAD1>
                <TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
				</TR>
                <TR bgColor=#E3FAD1 height=10>
                <TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" 
					width=7 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" 
					width=1 border=0>
					<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
                <TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
					width=7 border=0></TD>
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
        <TD>

            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
			<td align="center" width="50%">
				<A HREF="mainmenu.cgi" target=_top>
				<IMG src="$images/home_blkline.gif" border=0></A>&nbsp;&nbsp;&nbsp;<a href="advertiser_list.cgi"><img src="$images/advertisers.gif" border=0></a>&nbsp;&nbsp;&nbsp;<a href="advertiser_list.cgi"><img src="$images/cancel_x.gif" border=0></a></TD>	
			<td align="center" width="50%">
end_of_html

		print qq { <input type="image" name="BtnAdd" src="$images/save.gif" border=0> }; 

print << "end_of_html";
			</td>
			</tr>
			</table>

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
}

$util->footer();
$util->clean_up();
exit(0);
