#!/usr/bin/perl

# *****************************************************************************************
# edit_client_info.cgi
#
# this page allows a client to edit their information 
#
# History
# Jim Sobeck, 8/03/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $sth;
my $sql;
my $dbh;
my $errmsg;
my ($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr);
my $images = $util->get_images_url;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
#
# Get the information about the user for display
#
$sql = "select first_name,last_name,address,address2,city,state,zip,phone,email_addr from user where user_id = $user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
if (($fname,$lname,$address,$address2,$city,$state,$zip,$phone,$email_addr) = $sth->fetchrow_array())
{
# print out html page
util::header("Edit Contact Information");
print << "end_of_html";
</TD>
        </TR>
        <TR>
          <TD vAlign=top align=left bgColor=#FFFFFF>
            <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
              <TBODY>
              <TR>
                <TD vAlign=top align=left bgColor=#ffffff colSpan=10><!-- doing ct-table-open -->
                  <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff 
                  border=0>
                    <TBODY>
                    <TR><!-- doing ct-table-cell-open -->
                      <TD vAlign=center align=left><FONT 
                        face="verdana,arial,helvetica,sans serif" color=#509C10 
                        size=3><B>Update Your Info</B> </FONT></TD></TR>
                    <TR>
                      <TD><IMG height=3 
                        src="$images/spacer.gif"></TD></TR></TBODY></TABLE><!-- doing ct-table-open -->
                  <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff 
                  border=0>
                    <TBODY>
                    <TR>
                      <TD colSpan=10><FONT 
                        face="verdana,arial,helvetica,sans serif" color=#509C10 
                        size=2>To update your contact information please make 
                        the appropriate changes and select <B>Save</B>. As a 
                        reminder, we closely protect and safeguard your personal 
                        information. See our website's <a target="_blank" href="http://">Privacy 
                        Policy</a> for details.<BR></FONT></TD></TR>
                    <TR>
                      <TD><IMG height=5 
                        src="$images/spacer.gif"></TD></TR></TBODY></TABLE>
	<!-- ------------------- JAVA SCRIPT ----------------------------------- -->
    <script language="JavaScript">
    <!--
    function Update()
    {
        var iopt;
        // validate your data first
        iopt = check_mandatory_fields();
        if (iopt == 0)
        {
            return false;
        }

        // if ok, go on to save
        document.edit_client.submit();
        return true;
    }


    function check_mandatory_fields()
    {
        if (document.edit_client.fname.value == "")
        {
            alert("You may enter a value for the Contact First Name field."); 
			document.edit_client.fname.focus();
            return false;
        }
        if (document.edit_client.lname.value == "")
        {
            alert("You may enter a value for the Contact Last Name field."); 
			document.edit_client.lname.focus();
            return false;
        }
        if (document.edit_client.address.value == "")
        {
            alert("You may enter a value for the Customer Address field."); 
			document.edit_client.address.focus();
            return false;
        }
        if (document.edit_client.city.value == "")
        {
            alert("You may enter a value for the City field."); 
			document.edit_client.city.focus();
            return false;
        }
        if (document.edit_client.state.value == "")
        {
            alert("You may enter a value for the State field."); 
			document.edit_client.state.focus();
            return false;
        }
        if (document.edit_client.zip.value == "")
        {
            alert("You may enter a value for the Zip field."); 
			document.edit_client.zip.focus();
            return false;
        }
        if (document.edit_client.email_addr.value == "")
        {
            alert("You may enter a value for the Contact Email field."); 
			document.edit_client.email_addr.focus();
            return false;
        }
        if (document.edit_client.phone.value == "")
        {
            alert("You may enter a value for the Contact Phone field."); 
			document.edit_client.phone.focus();
            return false;
        }

        return true;
    }

    -->
    </script>

                  <FORM name=edit_client action="update_client_info.cgi" 
                  method=post><!-- doing editform --><!-- doing editform default table open --><!-- doing ct-table-open -->
                  <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff 
                  border=0><!--Entered the main body of paramgroup template-->
                    <TBODY>
                    <TR>
                      <TD>
                        <TABLE cellSpacing=0 cellPadding=5 width="100%" 
border=0>
                          <TBODY>
                          <TR>
                            <TD align=middle>
                              <TABLE cellSpacing=0 cellPadding=0 width=350 
                              bgColor=#E3FAD1 border=0>
                                <TBODY>
                                <TR align=top bgColor=#509C10 height=18>
                                <TD vAlign=top align=left height=15><IMG 
                                src="$images/blue_tl.gif" border=0 width="7" height="7"></TD>
                                <TD height=15><IMG height=1 
                                src="$images/spacer.gif" 
                                width=3 border=0></TD>
                                <TD align=middle height=15>
                                <TABLE cellSpacing=0 cellPadding=0 width="100%" 
                                border=0>
                                <TBODY>
                                <TR bgColor=#509C10 height=15>
                                <TD align=middle width="100%" height=15><FONT 
                                face=Verdana,Arial,Helvetica,sans-serif 
                                color=white size=2><B>Contact Information</B> 
                                </FONT></TD></TR></TBODY></TABLE></TD>
                                <TD height=15><IMG height=1 
                                src="$images/spacer.gif" 
                                width=3 border=0></TD>
                                <TD vAlign=top align=right bgColor=#509C10 
                                height=15><IMG 
                                src="$images/blue_tr.gif" border=0 width="7" height="7"></TD></TR>
                                <TR bgColor=#E3FAD1>
                                <TD colSpan=5><IMG height=3 
                                src="$images/spacer.gif" 
                                width=1 border=0></TD></TR>
                                <TR bgColor=#E3FAD1>
                                <TD><IMG height=3 
                                src="$images/spacer.gif" 
                                width=3></TD>
                                <TD align=middle><IMG height=3 
                                src="$images/spacer.gif" 
                                width=3></TD>
                                <TD align=middle>
                                <TABLE cellSpacing=0 cellPadding=0 width="100%" 
                                border=0>
                                <TBODY>
                                <TR>
                                <TD align=middle><IMG height=3 
                                src="$images/spacer.gif" 
                                width=3></TD></TR>
                                <TR>
                                <TD vAlign=center noWrap align=right 
                                width="30%"><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>Contact First 
                                Name:&nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2><INPUT size=20 maxlength=20 value="$fname"
                                name=fname> </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>
                                <TR>
                                <TD><IMG height=7 
                                src="$images/spacer.gif"></TD></TR>
                                <TR>
                                <TD vAlign=center noWrap align=right 
                                width="30%"><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>Contact Last 
                                Name:&nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2><INPUT size=40 maxlength=40 
                                value="$lname" name=lname> 
                                </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>

								<TR>
                                <TD vAlign=center noWrap align=right 
                                width="30%"><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>Contact Address: 
                                &nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                	<TD vAlign=center align=left><FONT
                                	face="verdana,arial,helvetica,sans serif"
                                	color=#509C10 size=2><INPUT size=50 maxlength=50
                                	value="$address" name=address>
                                </FONT></TD></TR>
								<TR>
                                <TD vAlign=center noWrap align=right 
                                width="30%"><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2> 
                                &nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                	<TD vAlign=center align=left><FONT
                                	face="verdana,arial,helvetica,sans serif"
                                	color=#509C10 size=2><INPUT size=50 maxlength=50
                                	value="$address2" name=address2>
                                </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>

								<TR>
                                <TD vAlign=center noWrap align=right 
                                width="30%"><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>City: 
                                &nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                	<TD vAlign=center align=left><FONT
                                	face="verdana,arial,helvetica,sans serif"
                                	color=#509C10 size=2><INPUT size=50 maxlength=50
                                	value="$city" name=city>
                                </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>

								<TR>
                                <TD vAlign=center noWrap align=right 
                                width="30%"><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>State&nbsp;/&nbsp;Zip: 
                                &nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                	<TD vAlign=center align=left><FONT
                                	face="verdana,arial,helvetica,sans serif"
                                	color=#509C10 size=2><INPUT size=2 maxlength=2
                                	value="$state" name=state>&nbsp;/&nbsp;<input size=10 value="$zip" maxlength=10 name=zip>
                                </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>
                                <TR>
                                <TD vAlign=center noWrap align=right><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>Contact 
                                Email:&nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2><INPUT size=50 maxlength=80
                                value="$email_addr" 
                                name=email_addr> </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>
                                <TR>
                                <TD><IMG height=7 
                                src="$images/spacer.gif"></TD></TR>
                                <TR>
                                <TD vAlign=center noWrap align=right><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2>Contact 
                                Phone:&nbsp;&nbsp;&nbsp; </FONT></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#509C10 size=2><INPUT size=30 
                                value="$phone" MAXLENGTH=35 name=phone> 
                                </FONT></TD></TR>
                                <TR>
                                <TD><IMG 
                                src="$images/spacer.gif" width="1" height="1"></TD><!-- doing ct-table-cell-open -->
                                <TD vAlign=center align=left><FONT 
                                face="verdana,arial,helvetica,sans serif" 
                                color=#ff0000 size=2>&nbsp;</FONT></TD></TR>
                                <TR>
                                <TD><IMG height=7 
                                src="$images/spacer.gif"></TD></TR>
                                <TR>
                                <TD align=middle><IMG height=3 
                                src="$images/spacer.gif" 
                                width=3></TD></TR></TBODY></TABLE></TD>
                                <TD align=middle><IMG height=3 
                                src="$images/spacer.gif" 
                                width=3></TD>
                                <TD><IMG height=3 
                                src="$images/spacer.gif" 
                                width=3></TD></TR>
                                <TR bgColor=#E3FAD1>
                                <TD colSpan=5><IMG height=3 
                                src="$images/spacer.gif" 
                                width=1 border=0></TD></TR>
                                <TR bgColor=#E3FAD1 height=10>
                                <TD vAlign=bottom align=left><IMG height=7 
                                src="$images/lt_purp_bl.gif" 
                                width=7 border=0></TD>
                                <TD><IMG height=3 
                                src="$images/spacer.gif" 
                                width=1 border=0></TD>
                                <TD align=middle bgColor=#E3FAD1><IMG height=3 
                                src="$images/spacer.gif" 
                                width=1 border=0><IMG height=3 
                                src="$images/spacer.gif" 
                                width=1 border=0></TD>
                                <TD><IMG height=3 
                                src="$images/spacer.gif" 
                                width=1 border=0></TD>
                                <TD vAlign=bottom align=right><IMG height=7 
                                src="$images/lt_purp_br.gif" 
                                width=7 
                          border=0></TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></TD></TR><!-- entering default Actiongroup -->
                    <TR>
                      <TD>&nbsp;</TD></TR>
                    <TR>
                      <TD>
                        <TABLE cellSpacing=0 cellPadding=7 width="100%" 
border=0>
                          <TBODY>
                          <TR>
                            <TD align=right>
	<A HREF="mainmenu.cgi">
	<IMG name="BtnCancel" src="$images/cancel.gif" border=0 hspace=7  width="72"  height="21" ></A>
	<A HREF="#" OnClick="Update();"><IMG src="$images/save.gif" hspace=7 border=0 name=action_next width="76" height="23"></A> 
                    </TD></TR></TBODY></TABLE></TD></TR><!-- doing editform not wrap --></TBODY></TABLE></FORM></TD></TR></TBODY></TABLE></TD>
        </TR>
        <TR>
          <TD noWrap align=left height=17>
end_of_html
$util->footer();
}
else
{
	$errmsg = $dbh->errstr();
    util::logerror("Getting user information for $user_id: $errmsg");
}
$sth->finish();
$util->clean_up();
exit(0);
