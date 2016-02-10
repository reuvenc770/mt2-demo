#!/usr/bin/perl
#===============================================================================
# Purpose: Edit client demographic data (eg 'user' table).
# Name   : client_disp.cgi (edit_client_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 08/03/01  Jim Sobeck  Creation
# 08/15/01  Mike Baker  Change to allow 'Admin' User to Update other fields.
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use pma;

#------  get some objects to use later ---------
my $pms = pma->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my ($status, $max_names, $max_mailings, $username);
my $password;
my ($puserid, $pmesg);
my $company;
my $website_url;
my $company_phone;
my $user_type;
my $this_user_type;
my $images = $pms->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;
my $client_name;
my $ftp_dir;

#------  connect to the pms database -----------
$pms->db_connect();
$dbh = $pms->get_dbh;

#-----  check for login  ------
my $user_id = pma::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$pms->clean_up();
    exit(0);
}

#--------------------------------
# get CGI Form fields
#--------------------------------
my $pmode   = "A"; 
$pmode = uc($pmode);
if ( $pmode eq "A"  or  $puserid eq "" ) 
{
	# defaults for new user
	$client_name       = "" ;
	$ftp_dir = "" ;
}

# print out html page

pma::header("Edit Contact Information");
	
print << "end_of_html";
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
            size=3><B>Client Information</B> </FONT></TD>
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

if ( $pmode eq "A" )
{
	print qq{ To ADD client information please enter the appropriate fields \n } ;
	print qq{ and select <B>Add</B>. As a  \n } ;
	print qq{ reminder, we closely protect and safeguard your personal  \n } ;
	print qq{ information. See our website's  \n } ;
}
else
{
	print qq{ To UPDATE the client information please make  \n } ;
	print qq{ the appropriate changes and select <B>Save</B>. As a  \n } ;
	print qq{ reminder, we closely protect and safeguard your personal  \n } ;
	print qq{ information. See our website's  \n } ;
}

print << "end_of_html" ;
			<a href="privacy_policy.cgi">Privacy Policy</a> for details.<BR></FONT></TD>
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
		document.edit_client.pmode.value = Mode ;
        document.edit_client.submit();
        return true;
    }

    function check_mandatory_fields()
    {
        if (document.edit_client.client_name.value == "")
        {
            alert("You MUST enter a value for the Client Name field."); 
			document.edit_client.client_name.focus();
            return false;
        }
        if (document.edit_client.ftp_dir.value == "")
        {
            alert("You MUST enter a value for the Ftp Dir field."); 
			document.edit_client.ftp_dir.focus();
            return false;
        }
    }
    </script>

        <FORM name=edit_client action="client_upd.cgi" method=post>

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
						<B>Contact Information</B></FONT></TD>
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
                    <TR> <!-- -------- Contact First Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Client Name: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=20 maxlength=20 value="$client_name" name=client_name>
end_of_html
	
	#-----------------------------------------------------
	# Only Admin users (eg user_type = 'A') view Status
	#-----------------------------------------------------
	if ( $this_user_type eq "A" ) 
	{
		print qq{ &nbsp;&nbsp;&nbsp;Status: };
		#------ Toggle CHECKED based on Status value --------------
		if ( $status eq "A" )
		{
			print qq{ <input type="radio" name="status" value="A" CHECKED>Active } ;
			print qq{ &nbsp;<input type="radio" name="status" value="D">Deleted } ;
		}
		else
		{
	    	print qq{ <input type="radio" name="status" value="A">Active } ;
			print qq{ &nbsp;<input type="radio" name="status" value="D" CHECKED>Deleted } ;
		}
	}
	else
	{
		print qq{<input type="hidden" value="$status" name="status"> } ;
	}
		

	print << "end_of_html" ;
						</FONT></TD>
					</TR>
                    <TR> <!-- -------- Contact Last Name -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Ftp Directory: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=40 maxlength=40 value="$ftp_dir" name=ftp_dir></FONT></TD>
					</TR>

        <TR>
        <TD>

            <TABLE cellSpacing=0 cellPadding=7 width="100%" border=0>
            <TBODY>
            <TR>
			<td align="center" width="50%">
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></TD>	
			<td align="center" width="50%">
end_of_html

	if ( $pmode eq "A" )
	{
		print qq { 	<input type="image" name="BtnAdd" src="$images/add.gif" border=0 
						onClick="return ProcessForm('A');" > };
	}
	else
	{
		print qq { <input type="image" name="BtnAdd" src="$images/save.gif" border=0 
						onClick="return ProcessForm('U');" > }; 
	}

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

$pms->footer();
$pms->clean_up();
exit(0);
