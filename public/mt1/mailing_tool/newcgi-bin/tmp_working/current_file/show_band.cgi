#!/usr/bin/perl
#===============================================================================
# Name   : show_band.cgi 
#
#--Change Control---------------------------------------------------------------
# 05/23/05  Jim Sobeck  Creation
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
my ($birth_date,$gender,$sub_date);
my ($status, $max_names, $max_mailings, $username);
my $password;
my $internal_email_addr;
my $physical_addr;
my $cstatus;
my ($puserid, $pmesg);
my $company;
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
my $tid;
my ($fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,$list_name); 

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
$tid= $query->param('eid');
my $backto = $query->param('backto');

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
end_of_html

print << "end_of_html" ;
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
$sql = "select member_list.email_addr,member_list.first_name,member_list.last_name,member_list.address,member_list.city,member_list.state,member_list.zip,source_url,capture_date,member_source,unsubscribe_datetime,list_name,birth_date,gender,member_list.status,subscribe_datetime,company from member_list,list,user where member_list.list_id=list.list_id and email_user_id=$tid and list.user_id=user.user_id";
$sth = $dbh->prepare($sql);
$sth->execute();
if (($cemail,$fname,$lname,$addr,$city,$state,$zip,$source_url,$cdate,$cip,$unsub_date,$list_name,$birth_date,$gender,$cstatus,$sub_date,$company) = $sth->fetchrow_array())
{
	print "<tr><td><b>Email Address</b></td><td>$cemail</td>\n";
	print "<tr><td><b>Name</b></td><td>$fname $lname</td>\n";
	print "<tr><td><b>Address</b></td><td>$addr<br>$city,$state $zip</td>\n";
	print "<tr><td><b>Source</b></td><td>$source_url</td>\n";
	print "<tr><td><b>IP</b></td><td>$cip</td>\n";
	print "<tr><td><b>Date</b></td><td>$cdate</td>\n";
	print "<tr><td><b>Removal Date</b></td><td>$unsub_date</td>\n";
}
else
{
	print "<tr><td><b>Record Not Found</b></td></tr>\n";
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
end_of_html
if ($backto eq "Y")
{
	print "<td align=\"center\"><A HREF=\"/cgi-bin/find_info.cgi\">Return to Search</a></td>\n";
}
else
{
	print "<td align=\"center\"><A HREF=\"/cgi-bin/find_info_id.cgi\">Return to Search</a></td>\n";
}
print<<"end_of_html";
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
