#!/usr/bin/perl
#===============================================================================
# Purpose: Displays the HTML page to add 'list_member' recs to identified lists.
# File   : sub_disp_del.cgi
#
# Input  :
#   2. the 'list' table to display valid Lists to attach emails to.
#   3. List of Email Addrs or a FileName with ONE Email Address per line.
#
# Output :
#   1. Added recs to 'list_member' table.
#   2. Control passed to 'sub_del.cgi' to Add Members to Specified Lists.
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 8/01/01  Created.
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
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

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
if ($user_id == 1)
{
	$sth = $dbhq->prepare("select count(*) from list") ;
}
else
{
	$sth = $dbhq->prepare("select count(*) from list where user_id = $user_id") ;
}
$sth->execute();
$reccnt = 0 ;
( $reccnt ) = $sth->fetchrow_array() ;
$sth->finish();
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
	<B>Remove Subscribers</B> </FONT></TD>
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
	<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	You must remove subscribers from specific List(s). 
	<BR></FONT></TD></TR>
	<TR>
	<TD><IMG height=5 src="$images/spacer.gif"></TD>
	</TR>
	</TBODY>
	</TABLE>

	<!-- ------------------------------------------------------------------>
	<!-- ---------- Begin FORM Definition --------------------------------->
	<!-- ------------------------------------------------------------------>
	<FORM name="sub_del_form" action=sub_del.cgi method=post encType=multipart/form-data>

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
	<TR align=top bgColor=#509C10 height=18>
	<TD vAlign=top align=left height=15>
	<IMG height=7 src="$images/blue_tl.gif" width=7 border=0></TD>
	<TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
	<TD align=middle height=15>

	<!-- ---------- Lists Available Tbl Heading ------------------------------ -->
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD align=middle width="100%" height=15><font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
	<b>Lists Available</b></font></TD>
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

	<!-- ---------- Begin Lists Available CheckBox and Name Tbl Definition -------- -->
	<TABLE cellSpacing=0 cellPadding=0 width=660 border=0>
	<TBODY>
	<TR>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
	</TR>

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================
	$nbr_cols = 3 ;
	if ($user_id == 1)
	{
		$sth = $dbhq->prepare("select list_id, list_name, status from list where status = 'A' order by list_name") ;
	}
	else
	{
		$sth = $dbhq->prepare("select list_id, list_name, status from list where user_id = $user_id and status = 'A' order by list_name") ;
	}
	$sth->execute();
	$reccnt = 0 ;
	while ( ($list_id, $list_name, $status) = $sth->fetchrow_array() )
	{
		$reccnt++;
		$chkbox_name = "list_chkbox" ;
		if ( ($reccnt % $nbr_cols) == 1 ) 
		{
			print qq{ <TR> \n } ;
		}

		print qq{	<TD align=right width="02%"><INPUT type=checkbox checked name="$chkbox_name" value="$list_id"></TD> \n } ;
		print qq{	<TD align=left width="25%">	 \n } ;
		print qq{		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> \n } ;
		print qq{		$list_name</FONT></TD> \n } ;

		if ( ($reccnt % $nbr_cols) == 0 ) 
		{
			print qq{ </TR> \n } ;
		}
	}
	$sth->finish();

	if ( ($reccnt % $nbr_cols) != 0 ) 
	{
		print qq{ </TR> \n } ;
	}

	#---------------------------------------------------------------------------
	# Gen 2 Hidden fields Named: list_chkbox so JavaScript may use Array Name
	# (eg document.FormName.list_chkbox[#].value w/out blowing up.
	#---------------------------------------------------------------------------
	print qq{ <TR><TD><INPUT type=hidden name="list_chkbox" value="dummy"><INPUT type=hidden name="list_chkbox" value="dummy"></td></tr> };


	#---------------------------------------------
	# Print HTML until next eye catch
	#---------------------------------------------
	print << "end_of_html";

	<TR>
	<TD><IMG height=7 src="$images/spacer.gif"></TD>
	</TR>
	<TR>
	<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3></TD>
	</TR>
	</TBODY>
	</TABLE>
	<!-- --------- END of LISTS AVAILABLE Tbl(s) ------------ -->
	
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
	<TD align=middle bgColor=#E3FAD1>
		<IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	<TD><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
	<TD vAlign=bottom align=right>
	<IMG height=7 src="$images/lt_purp_br.gif" width=7 border=0></TD>
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
	<B>File of Email Address to Remove</B> </FONT></TD>
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
	<b>EITHER</b> Remove Email Addresses via a File <br><br>
	(note: The file is an ascii file with one email address per line.)
	</FONT></TD>
	</TR>
	<TR>
	<TD align=middle><IMG height=4 src="$images/spacer.gif"></TD>
	</TR>
	<TR>
	<TD align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	<INPUT type=file name="upload_file" size="65"></FONT></TD>
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
	<TABLE align=center cellSpacing=0 cellPadding=0 width=680 border=0>
	<TBODY>

	<TR bgColor=#509C10 height=15>
	<TD align=middle width="100%" height=15>
	<FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
	<B>Addresses to Remove</B> </FONT></TD>
	</TR>

	<TR bgColor=#E3FAD1>
	<TD align=left>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	<br><B>&nbsp;&nbsp;OR</B> Remove individual Email Address by entering them in the text box below.<br><br></FONT></TD>
	</TR>

	<TR bgColor=#E3FAD1>
	<TD align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> 
	&nbsp;&nbsp;(Hit ENTER after each address) Each email address must be on a separate line.</FONT> </TD>
	</TR>

	<TR bgColor=#E3FAD1>
	<TD align=left>
	<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
	&nbsp;&nbsp;<TEXTAREA name="email_list_text_area" rows=3 wrap=off cols=60></TEXTAREA>
	<br><br></FONT></TD>
	</TR>

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

	</TD>
	</TR>
	</TBODY>
	</TABLE>

	</FORM>
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

		if (confirm("Are you sure you want to Remove subscribers from the Selected Lists?"))
		{
        	document.sub_del_form.submit();
        	return true;
		}
    }

    function check_mandatory_fields()
    {
		var NumListChkBoxFields = document.sub_del_form.list_chkbox.length ;
		var i = 0 ;
		var ChkBoxChecked = 0 ;
		
		//---------------------------------------------------------------
		// One or More list_chkbox(s) MUST be CHECKED - Else Error Mesg
		//---------------------------------------------------------------
		while ( i <= (NumListChkBoxFields - 1) ) 
		{
			if ( document.sub_del_form.list_chkbox[i].value != "dummy" && 
				 document.sub_del_form.list_chkbox[i].checked == true ) 
			{
				ChkBoxChecked = 1 ;
			}
			i++;
		}
		
		if ( NumListChkBoxFields <= 2 || ChkBoxChecked == 0 )
		{
        	alert("Invalid - You MUST Check One or More items from the 'Lists Available'" ) ;
			document.sub_del_form.list_chkbox[0].focus();
        	return false ;
		}

        if (document.sub_del_form.email_list_text_area.value != ""  &&  
			document.sub_del_form.upload_file.value != "" )
        {
            alert("You may either Remove individual Emails via the Text Area or Remove Emails via a File.  But NOT both at the same time.");
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

