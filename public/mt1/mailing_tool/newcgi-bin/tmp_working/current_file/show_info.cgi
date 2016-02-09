#!/usr/bin/perl
#===============================================================================
# Purpose: Add vendor suppression list (eg 'user' table).
# Name   : supplist_add.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/26/04  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $errmsg;
my ($birth_date,$gender,$sub_date);
my ($status, $max_names, $max_mailings, $username);
my $password;
my $internal_email_addr;
my $physical_addr;
my $cstatus;
my $list_name;
my $company;
my ($puserid, $pmesg);
my $website_url;
my $company_phone;
my $images = $util->get_images_url;
my $privacy_policy_url;
my $account_type;
my $unsub_option;
my $name;
my $puserid; 
my $pmode;
my $pmesg;
my $this_user_type;
my $cemail;
my $eid;
my ($fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,$list_name); 
my $reccnt;

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
$cemail = $query->param('name');

$sql = "select user_type from user where user_id = $user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($this_user_type) = $sth->fetchrow_array() ;
$sth->finish();

util::header("User Record");
	
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

<!--        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Find User Information</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE> 

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2> -->
end_of_html

print << "end_of_html" ;
<!--			</FONT></TD>
		</TR>
		</TBODY>
		</TABLE> -->


        <TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD>
            <TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
            <TBODY>
            <TR>
            <TD align=middle>

                <TABLE cellSpacing=0 cellPadding=0 width="70%" bgColor=#E3FAD1 border=0>
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
						<B>User Information</B></FONT></TD>
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
                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
                    <TBODY>
					<tr><th>Field</th><th>Value</th></tr>
end_of_html
$sql = "select email_list.first_name,email_list.last_name,email_list.address,email_list.city,email_list.state,email_list.zip,email_list.source_url,email_list.capture_date,member_source,unsubscribe_date,list_name,dob,gender,email_list.status,subscribe_date,email_user_id,list_name,company from email_list,list,user where email_list.list_id=list.list_id and email_list.email_addr='$cemail' and list.user_id=user.user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,$list_name,$birth_date,$gender,$cstatus,$sub_date,$eid,$list_name,$company) = $sth->fetchrow_array())
{
	print "<tr><td><b>Network</b></td><td>$company</td>\n";
	print "<tr><td><b>List Name</b></td><td>$list_name</td>\n";
	print "<tr><td><b>Email Addr</b></td><td>$cemail</td>\n";
	print "<tr><td><b>Name</b></td><td>$fname $lname</td>\n";
	print "<tr><td><b>Address</b></td><td>$addr<br>$city,$state $zip</td>\n";
	print "<tr><td><b>Source</b></td><td>$source_url</td>\n";
	print "<tr><td><b>IP</b></td><td>$cip</td>\n";
	print "<tr><td><b>Date</b></td><td>$cdate</td>\n";
	print "<tr><td><b>Birth Date</b></td><td>$birth_date</td>\n";
	print "<tr><td><b>Gender</b></td><td>$gender</td>\n";
	print "<tr><td><b>List Name</b></td><td>$list_name</td>\n";
	print "<tr><td><b>Subscribe Date</b></td><td>$sub_date</td>\n";
	print "<tr><td><b>Status</b></td><td>$cstatus</td>\n";
	print "<tr><td><b>Removal Date</b></td><td>$unsub_date</td>\n";
$sql = "select count(*) from suppress_list where email_addr='$cemail'";
$sth1 = $dbh->prepare($sql);
$sth1->execute();
($reccnt) = $sth1->fetchrow_array();
$sth1->finish();
if ($reccnt > 0)
{
	print "<tr><td><b>In Global Suppression</b></td><td><b>Y</b></td>\n";
}
else
{
	print "<tr><td><b>In Global Suppression</b></td><td>N</td>\n";
}
my $caddr;
my $cdomain;
            ($caddr,$cdomain) = split("@",$cemail);
	print "<tr><td colspan=2 align=\"center\"><a href=\"/cgi-bin/remove_user.cgi?eid=$eid\">Remove User</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"/cgi-bin/remove_user.cgi?eid=$eid&global=Y\">Add to Global Suppression</a><br><a href=\"/cgi-bin/remove_user.cgi?eid=$eid&global=D\" onClick=\"return confirm('Are you sure you want to remove <$cdomain>?\\nClick OK to remove');\">Add domain to Global Suppression</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"/cgi-bin/show_band.cgi?eid=$eid&backto=Y\">Show Info. for Bandwidth Providers</a></td></tr>\n";
}
$sth->finish();
print<< "end_of_html";
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
			<td align="center">
				<A HREF="mainmenu.cgi">
				<IMG src="$images/home_blkline.gif" border=0></A></TD>	
			<td align="center">
				<a href="/cgi-bin/add_to_global.cgi?em=$cemail">Add To Global Suppression</a>&nbsp;&nbsp;&nbsp;<A HREF="/cgi-bin/find_info.cgi">Return to Search</a></td>
			</tr>
			</table>

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
end_of_html

$util->footer();
$util->clean_up();
exit(0);
