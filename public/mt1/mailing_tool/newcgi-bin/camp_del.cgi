#!/usr/bin/perl
# *****************************************************************************************
# camp_del.cgi
#
# this page confirms deletion of a campaign
#
# History
# Grady Nash, 8/14/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $count;
my $sth;
my $sql;
my $dbh;
my $template_name;
my $campaign_id = $query->param('campaign_id');
my $campaign_name;
my $images = $util->get_images_url;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}

# get campaign name

$sql = "select campaign_name from campaign where campaign_id=$campaign_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($campaign_name) = $sth->fetchrow_array();
$sth->finish();

# print html page out

util::header("Delete Campaign");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=3><B>Confirm Remove or Reschedule</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>You have chosen to remove the following campaign from the display. Are you 
            sure you want to do this?<BR></FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action=camp_del_save.cgi method=post>
		<INPUT type=hidden value=$campaign_id name=campaign_id> 

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD>

			<TABLE cellSpacing=0 cellPadding=5 width="100%" border=0>
    		<TBODY>
    		<TR>
    		<TD align=middle>

        		<TABLE cellSpacing=0 cellPadding=0 width=350 bgColor=#E3FAD1 border=0>
        		<TBODY>
        		<TR align=top bgColor=#509C10 height=18>
        		<TD vAlign=top align=left height=15><IMG height=7 src="$images/blue_tl.gif" 
                    width=7 border=0></TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD align=middle height=15>

                    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR bgColor=#509C10 height=15>
                    <TD align=middle width="100%" height=15><FONT 
                        face=Verdana,Arial,Helvetica,sans-serif 
                        color=white size=2><B>Delete Campaign</B> 
                        </FONT></TD>
					</TR>
					</TBODY>
					</TABLE>

				</TD>
                <TD height=15><IMG height=1 src="$images/spacer.gif" width=3 border=0></TD>
                <TD vAlign=top align=right bgColor=#509C10 height=15><IMG height=7 
                    src="$images/blue_tr.gif" width=7 border=0></TD>
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
                    <TD><IMG height=3 src="$images/spacer.gif" width=3></TD>
					</TR>
                    <TR>
                    <TD vAlign=center align=left><font face="verdana,arial,helvetica,sans serif" 
						size="2" color="#FF0000"><b>$campaign_name</b></font></TD>
					</TR>
                    <TR>
                    <TD><IMG height=7 src="$images/spacer.gif"></TD>
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
                    width=1 border=0><IMG height=3 src="$images/spacer.gif" width=1 border=0></TD>
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
			<TD width="50%" align="center">
				<a href="mainmenu.cgi"><img src="$images/no.gif" border=0></a></td>
			<td width="50%" align="center">
				<INPUT type=image src="$images/yes.gif" border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

		</TD>
		</TR>
		</TBODY>
		</TABLE>
</form>
		<FORM action=camp_replace_save.cgi method=post>
		<INPUT type=hidden value=$campaign_id name=campaign_id> 
					<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table19">
						<tr>
							<td>
							<table cellSpacing="0" cellPadding="5" width="100%" border="0" id="table20">
								<tr>
									<td align="middle">
									<table cellSpacing="0" cellPadding="0" width="350" bgColor="#e3fad1" border="0" id="table21">
										<tr align="top" bgColor="#509c10" height="18">
											<td vAlign="top" align="left" height="15">
											<img height="7" src="/images/blue_tl.gif" width="7" border="0"></td>
											<td height="15">
											<img height="1" src="/images/spacer.gif" width="3" border="0"></td>
											<td align="middle" height="15">
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table22">
												<tr bgColor="#509c10" height="15">
													<td align="middle" width="100%" height="15">
													<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
													<b>Replace Campaign</b>
													</font></td>
												</tr>
											</table>
											</td>
											<td height="15">
											<img height="1" src="/images/spacer.gif" width="3" border="0"></td>
											<td vAlign="top" align="right" bgColor="#509c10" height="15">
											<img height="7" src="/images/blue_tr.gif" width="7" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td colSpan="5">
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
										</tr>
										<tr bgColor="#e3fad1">
											<td>
											<img height="3" src="/images/spacer.gif" width="3"></td>
											<td align="middle">
											<img height="3" src="/images/spacer.gif" width="3"></td>
											<td align="middle">
											<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table23">
												<tr>
													<td>
													<img height="3" src="/images/spacer.gif" width="3"></td>
												</tr>
												<tr>
													<td vAlign="center" align="left">
													<select name="adv_id">
end_of_html
$sql="select advertiser_id,advertiser_name from advertiser_info where status='A' and allow_strongmail='Y' order by advertiser_name";
my $sth1=$dbhq->prepare($sql);
$sth1->execute();
my $aid;
my $aname;
while (($aid,$aname)=$sth1->fetchrow_array())
{
	print "<option value=$aid>$aname</option>\n";
}
$sth1->finish();
print<<"end_of_html";
													</select></td>
												</tr>
												<tr>
													<td>
													<img height="7" src="/images/spacer.gif"></td>
												</tr>
											</table>
											</td>
											<td align="middle">
											<img height="3" src="/images/spacer.gif" width="3"></td>
											<td>
											<input type="submit" value="Submit" name="B27"><img height="3" src="/images/spacer.gif" width="3"></td>
										</tr>
<!--										<tr bgColor="#e3fad1">
											<td colSpan="5">
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
										</tr> -->
<!--										<tr bgColor="#e3fad1" height="10">
											<td vAlign="bottom" align="left">
											<img height="7" src="/images/lt_purp_bl.gif" width="7" border="0"></td>
											<td>
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
											<td align="middle" bgColor="#e3fad1">
											<img height="3" src="/images/spacer.gif" width="1" border="0"><img height="3" src="/images/spacer.gif" width="1" border="0"></td>
											<td>
											<img height="3" src="/images/spacer.gif" width="1" border="0"></td>
											<td vAlign="bottom" align="right">
											<img height="7" src="/images/lt_purp_br.gif" width="7" border="0"></td>
										</tr> -->
									</table>
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>
		</td>
	</tr>
</table>

</body>

</html>
end_of_html

$util->clean_up();
exit(0);
