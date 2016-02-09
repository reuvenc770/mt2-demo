#!/usr/bin/perl
#===============================================================================
# Purpose: Displays the HTML page to add 'list_member' recs to identified lists.
# File   : sub_disp_uns.cgi
#
# Input  :
#   2. the 'list' table to display valid Lists to attach emails to.
#   3. List of Email Addrs or a FileName with ONE Email Address per line.
#
# Output :
#   1. Added recs to 'list_member' table.
#   2. Control passed to 'sub_uns.cgi' to Remove Members.
#
#--Change Control---------------------------------------------------------------
# Jim Sobeck, 03/11/02  Created.
# Jim Sobeck, 04/02/03	Changed to use new format
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use CGI::Carp;   # mebtest
use util;

#BEGIN
#{
#	use CGI::Carp qw(carpout);
#	open (LOG, ">>/tmp/meb-cgi.log")
#		or die "Unable to append to /tmp/meb-cgi.log: $! \n" ;
#	carpout(*LOG);
#}

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my ($sth, $reccnt, $sql, $dbh ) ;
my ($go_back, $go_home, $mesg, $list_id, $list_name, $status, $chkbox_name);
my $images = $util->get_images_url;
my ($nbr_cols);


#------------------------------
# connect to the util database
#------------------------------
$util->db_connect();
$dbh = $util->get_dbh;

#------------------------------
# check for login
#------------------------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

#===========================================================================
# See if any 'list' recs exist for the given user - If not disp mesg & stop
#===========================================================================
#if ($user_id == 1)
#{
#	$sth = $dbh->prepare("select count(*) from list") ;
#}
#else
#{
#	$sth = $dbh->prepare("select count(*) from list where user_id = $user_id") ;
#}
#$sth->execute();
#$reccnt = 0 ;
#( $reccnt ) = $sth->fetchrow_array() ;
#$sth->finish();
$reccnt = 1;
if ( $reccnt == 0 )
{  
	$go_back = qq{<br><a href="$ENV{'HTTP_REFERER'}">Back</a>\n };
	$go_home = qq{&nbsp;&nbsp;<a href="mainmenu.cgi?userid=$user_id">Home</a>\n };
	$mesg = qq{<br><br><font color=#509C10>No Lists exist for this user.  You may only remove subscribers from existing 'Lists' <br> } ;
	$mesg = $mesg . $go_back . $go_home ;
	util::logerror($mesg) ;
	exit(99) ;
}


util::header("REMOVE SUBSCRIBERS");    # Print HTML Header

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#ffffff colSpan=10>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>
	<B>Unsubscribe Subscribers</B> </FONT></TD>
	</TR>
	<TR>
	<TD><IMG height=3 src="$images/spacer.gif"></TD>
	</TR>
	</TBODY>
	</TABLE>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	Enter the email addresses of the subscribers you wish to remove. These email addresses 
	will be removed from your list of subscribers. To remove your email addresses quickly, 
	copy (Ctrl+C) your list and paste (Ctrl+V) it into the entry box.
	<BR></FONT></TD>
	</TR>
	<TR>
	<TD><IMG height=5 src="$images/spacer.gif"></TD>
	</TR>
	</TBODY>
	</TABLE>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD><IMG height=5 src="$images/spacer.gif"></TD>
	</TR>
	</TBODY>
	</TABLE>

	<!-- ------------------------------------------------------------------>
	<!-- ---------- Begin FORM Definition --------------------------------->
	<!-- ------------------------------------------------------------------>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align=middle>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=350 bgColor=#E3FAD1 border=0>
	<TBODY>
	<TR bgColor=#E3FAD1>
	<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	</TR>
end_of_html

	#---------------------------------------------
	# Print HTML until next eye catch
	#---------------------------------------------
	print << "end_of_html";

	<TR>
	<TD>

	<!-- -------------------------------------------------------------- -->
	<!-- BEGIN FILE to Remove Tbl Definition                            -->
	<!-- -------------------------------------------------------------- -->
	<TABLE cellSpacing=0 cellPadding=5 width=660 border=0>
	<TBODY>
	<TR>
	<TD align=middle>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#E3FAD1 border=0>
	<TBODY>
	<TR align=top bgColor=#509C10 height=18>
	<TD vAlign=top align=left height=15><IMG height=7 src="$images/blue_tl.gif" width=7 border=0></TD>
	<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
	<TD align=middle height=15>

	<!-- FILE TO REMOVE EMAIL ADDRS -------------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width=660 border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD align=middle width="100%" height=15>
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
	<B>File of Email Address to Unsubscribe</B> </FONT></TD>
	</TR>
	</TBODY>
	</TABLE>

	</TD>
	<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
	<TD vAlign=top align=right bgColor=#509C10 height=15>
	<IMG height=7 src="$images/blue_tr.gif" width=7 border=0></TD>
	</TR>
	<TR bgColor=#E3FAD1>
	<TD colSpan=5><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	</TR>
	<TR bgColor=#E3FAD1>
	<TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
	<TD align=middle>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
	</TR>
	<TR>
	<TD align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	<b>EITHER</b> Unsubscribe Email Addresses via a File <br><br>
	(note: The file is an ascii file with one email address per line.)
	</FONT></TD>
	</TR>
	<TR>
	<TD align=middle><IMG height=4 src="$images/spacer.gif"></TD>
	</TR>
	<TR>
	<TD align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	<FORM name="sub_del_form" action=sub_uns.cgi method=post encType=multipart/form-data>
	<INPUT type=file name="upload_file" size="65"></FONT><br>
	Client : <select name="client_id">
<option value=0>All</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbh->prepare($sql);
$sth->execute();
my $company;
while (($user_id,$company) = $sth->fetchrow_array())
{
	print "<option value=$user_id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
	</select><br>
	<input type=checkbox name=global value="Y">Add to Global Suppression
	<INPUT type=submit name="remove" Value="Remove"></FONT></TD>
	</form>
	</TR>
	<TR>
	<TD align=middle><IMG height=4 src="$images/spacer.gif"></TD>
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
	<TD vAlign=bottom align=left><IMG height=7 src="$images/lt_purp_bl.gif" width=7 border=0></TD>
	<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	<TD align=middle bgColor=#E3FAD1><IMG height=3 src="$images/spacer.gif" width=1 border=0>
	<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" width=7 border=0></TD>
	</TR>
	</TBODY>
	</TABLE>

	</TD>
	</TR>
	</TBODY>
	</TABLE>
	<!-- ------------------------------------------- -->
	<!--  END FILE Removal tbl(s) definition         -->
	<!-- ------------------------------------------- -->


	<!-- -------------------------------------------------------------- -->
	<!-- BEGIN Email Address List Tbl Defn ---------------------------- -->
	<!-- -------------------------------------------------------------- -->
	<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
	<TBODY>
	<TR>
	<TD align=middle>

	<!-- ---------- Begin Tbl Definition --------------------------------->
	<FORM name="sub_del_form" action=sub_uns.cgi method=get encType=multipart/form-data>
	<TABLE align=center cellSpacing=0 cellPadding=0 width=680 border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD align=middle width="100%" height=15>
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
	<B>Addresses to Unsubscribe</B> </FONT></TD>
	</TR>
	<TR bgColor=#E3FAD1>
	<TD align=left>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	<br><B>&nbsp;&nbsp;OR</B> Unsubscribe individual Email Address by entering them in the text box below.<br><br></FONT></TD>
	</TR>
	<tr><td>
	Client : <select name="client_id">
<option value=0>All</option>
end_of_html
$sql="select user_id,company from user where status='A' order by company";
$sth = $dbh->prepare($sql);
$sth->execute();
my $company;
while (($user_id,$company) = $sth->fetchrow_array())
{
	print "<option value=$user_id>$company</option>\n";
}
$sth->finish();
print<<"end_of_html";
	</select></td></tr>


	<TR bgColor=#E3FAD1>
	<TD align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> 
	&nbsp;&nbsp;(Hit ENTER after each address) Each email address must be on a separate line.</FONT> </TD>
	</TR>

	<TR bgColor=#E3FAD1>
	<TD align=left>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	&nbsp;&nbsp;<TEXTAREA name="email_list_text_area" rows=15 wrap=off cols=60></TEXTAREA>
	<br><br></FONT></TD>
	</TR>
	<tr><td align=middle>
<input type=checkbox name=global value="Y">Add to Global Suppression
	</td></tr>
	</TBODY>
	</TABLE>
	<!-- END Tbl Defn for Email Address List --------------------------------- -->


	</TD>
	</TR>
	</TBODY>
	</TABLE>

	</TD>
	</TR>

	<TR>
	<TD>

	<!-- ---------------------------------------------------------  -->
	<!-- FILE TO REMOVE EMAIL ADDRS used to begin here ............ -->


	</TD>
	</TR>
	<TR>
	<TD>&nbsp;</TD>
	</TR>
	<TR>
	<TD>

	<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
	<TBODY>
	<TR>
	<TD width="50%" align=center>
		<A HREF="mainmenu.cgi">
		<IMG src="$images/home_blkline.gif" border="0"></A></td>
	<TD width="50%" align=center>
		<input type="image" src="$images/remove.gif" border=0 onClick="return Remove();"></TD>
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
	</TBODY>
	</TABLE>

    <script language="JavaScript">
    function Remove()
    {
        var iopt;
        // validate your data first
        iopt = check_mandatory_fields();
        if (iopt == 0)
        {
            return false;
        }

		if (confirm("Are you sure you want to Unsubscribe subscribers?"))
		{
        	document.sub_del_form.submit();
        	return true;
		}
    }

    function check_mandatory_fields()
    {
		var i = 0 ;
		var ChkBoxChecked = 0 ;
		
        if (document.sub_del_form.email_list_text_area.value != ""  &&  
			document.sub_del_form.upload_file.value != "" )
        {
            alert("You may either UnSubscribe individual Emails via the Text Area or Unsubscribe Emails via a File.  But NOT both at the same time.");
			document.sub_del_form.email_list_text_area.focus();
            return false;
        }

        if (document.sub_del_form.email_list_text_area.value == ""  &&  
			document.sub_del_form.upload_file.value == "" )
        {
            alert("You MUST specify ONE of the following:  1. Individual Email(s)  OR  b. A File-Name to Upload.");
			document.sub_del_form.upload_file.focus();
            return false;
        }

        return true;
    }
    </script>

</td>
</tr>
<tr>
<td>
end_of_html

util::footer();

$util->clean_up();
exit(0);

