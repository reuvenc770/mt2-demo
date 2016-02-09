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
my $name;
my $puserid; 
my $pmode;
my $pmesg;

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
$pmode   = $query->param('pmode');
$pmesg   = $query->param('pmesg');
$pmode = uc($pmode);
if ( $pmode ne "A"  and  $pmode ne "U" ) 
{	#---- Invalid MODE - Mode MUST = 'A' (add)  or  'U' (update)  ---------
	util::logerror("<br><br><b>Invalid</b> Mode: <b>$pmode</b> - The Mode MUST equal 'A' or 'U'.") ;
	exit(99) ;
}

if ( $pmode eq "A"  or  $puserid eq "" ) 
{
	# defaults for new user
	$name		 = "";
	$email_addr  = "" ;
	$internal_email_addr = "";
	$physical_addr = "";
}
else
{
	#------  Get the information about the user for display  --------
	$sql = "select advertiser_name,email_addr,internal_email_addr,physical_addr,status from advertiser_info where advertiser_id = $puserid"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($name,$email_addr,$internal_email_addr,$physical_addr,$cstatus) = $sth->fetchrow_array();
	$sth->finish();
	
	if ( $name eq "" ) 
	{
		$errmsg = $dbh->errstr();
	    util::logerror("<br><br>Error Getting user information for AdvertiserID: $puserid &nbsp;&nbsp;$errmsg");
		exit(99) ;
	}
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

if ( $pmode eq "A" )
{
	print qq{ To ADD advertiser information please enter the appropriate fields \n } ;
	print qq{ and select <B>Add</B>. \n } ;
}
else
{
	print qq{ To UPDATE the advertiser information please make  \n } ;
	print qq{ the appropriate changes and select <B>Save</B>. \n } ;
}

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

        <FORM name=edit_advertiser action="advertiser_upd.cgi" method=post onsubmit="return ProcessForm('A');">

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
						</FONT></TD>
					</TR>
                    <TR> <!-- -------- Email Addr -------------- -->
                    <TD vAlign=center noWrap align=right width="20%">
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						Advertiser Email Addresses: </FONT></TD>
                    <TD vAlign=center align=left>
						<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
						<INPUT size=50 maxlength=255 value="$email_addr" name=email_addr></FONT></TD>
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
end_of_html
if ( $pmode eq "U" )
{
	print "<tr>\n";
	print "<td align=right><FONT face=\"verdana,arial,helvetica,sans serif\" color=#509C10 size=2>Status</td>\n";
	if ($cstatus eq "A")
	{
		print "<td align=left><input type=radio name=cstatus value=\"S\">&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus checked value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"D\">Deleted</td>\n";
	}
	elsif ($cstatus eq "S")
	{
		print "<td align=left><input type=radio name=cstatus checked value=\"S\">&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"D\">Deleted</td>\n";
	}
	else
	{
		print "<td align=left><input type=radio name=cstatus value=\"S\">&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus value=\"A\">Active&nbsp;&nbsp;&nbsp;<input type=radio name=cstatus checked value=\"D\">Deleted</td>\n";
	}
	print "</tr>\n";
}
print <<"end_of_html";
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
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></TD>	
			<td align="center" width="50%">
end_of_html

	if ( $pmode eq "A" )
	{
		print qq { 	<input type="image" name="BtnAdd" src="$images/add.gif" border=0> };
	}
	else
	{
		print qq { <input type="image" name="BtnAdd" src="$images/save.gif" border=0> }; 
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
}

$util->footer();
$util->clean_up();
exit(0);
