#!/usr/bin/perl

# *****************************************************************************************
# camp_edit_lists.cgi
#
# this page is the third step in the email campaign creation process
# select list(s) for this email campaign
#
# History
# Jim Sobeck, 01/27/05, Creation 
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $name;
my $lname;
my $template_id;
my $campaign_name;
my $pcnt;
my $campaign_id = $query->param('campaign_id');
my $aid;
my $list_id;
my $list_name;
my $subscribe_cnt;
my $include_flag;
my $aol_cnt;
my $hotmail_cnt;
my $yahoo_cnt;
my $foreign_cnt;
my $msn_cnt;
my $header_text;
my $images = $util->get_images_url;
my $totalcnt;
my $tcnt;
my $aolcnt;
my $hotmailcnt;
my $yahoocnt;
my $nonaolcnt;
my $alllists;
my $aolalllists;
my $hotmailalllists;
my $yahooalllists;
my $aol_flag;
my $other_flag;
my $max_emails;
my $status;
my $yahoo_flag;
my $hotmail_flag;
my $month_flag;
my $no_flag;
my $seven_flag;
my $two_flag;
my $three_flag;
my $old_flag;
my $yes_open_flag;
my $no_open_flag;
my $aol_yes_flag;
my $general_aol_flag;
my $opener_catid;
my $nonyahoo_flag;
my $clast60;
my $openflag;
my $aolflag;
my $yes_flag;
my $last90_flag;

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

# print out html page

util::header("SELECT LIST(S)");

print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#999999>

    <TABLE cellSpacing=0 cellPadding=10 bgColor=#999999 border=0 width="100%">
    <TBODY>
    <TR>
    <TD vAlign=top align=left bgColor=#ffffff colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
		<TD><B><FONT face="Arial" color=#509C10 size=2>$header_text</FONT></B></TD>
		</TR>
        <TR>
        <TD><IMG height=9 src="$images/spacer.gif"></TD>
		</TR>
        <TR>
		<TD><B><FONT face="Arial" color=#509C10 size=3>
			Who Should Receive this Campaign?</FONT></B></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD><FONT face="Arial" color=#509C10 size=2>This email is sent 
			to the opt-in subscribers who have joined the specific lists checked 
            below. Those subscribers who are interested in more than 
            one category only receive one email.<BR></FONT></TD>
		</TR>
        <TR>
        <TD><font face="Arial"><IMG height=5 src="$images/spacer.gif"></font></TD>
		</TR>
		</TBODY>
		</TABLE>
</td></tr>
<tr bgcolor=#509C10><td>
<script language="JavaScript">

function selectall()
{
	refno=/list_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
           	document.adform.elements[x].checked = true;
        }
    }
}
function unselectall()
{
	refno=/list_/;
    for (var x=0; x < document.adform.length; x++)
    {
        if ((document.adform.elements[x].type=="checkbox") && (refno.test(document.adform.elements[x].name)))
        {
            document.adform.elements[x].checked = false;
        }
    }
}
</script>
		<FORM action="camp_edit_lists_save.cgi" method="post" name="adform">
<input type=button name="SelectAll" value="Select All" onclick="selectall()">&nbsp;&nbsp;&nbsp;<input type=button name="UnSelectAll" value="UnSelect All" onclick="unselectall()">
		<INPUT type=hidden name="campaign_id" value="$campaign_id">
<p>

					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 bgcolor=#E3FAD1>
					<TBODY>
					<TR bgColor=#509C10 height=15>
					<td width="2%">&nbsp;</td>
					<TD align=left width="30%" height=15><b><font face="Arial" color="white" size="2">List Name</font></b></TD>
					<TD align=middle width="20%" height=15><B><FONT face=Arial color=white size=2>Subscribers</FONT></B></TD>
					<TD align=middle width="15%" height=15><B><FONT face=Arial color=white size=2>AOL Cnt</FONT></B></TD>
					<TD align=middle width="15%" height=15><B><FONT face=Arial color=white size=2>Yahoo Cnt</FONT></B></TD>
					<TD align=middle width="15%" height=15><B><FONT face=Arial color=white size=2>Other Cnt</FONT></B></TD>
<td width="2%">&nbsp;</td>
					</TR>
					<TR bgcolor=#E3FAD1>
					<TD align=middle><IMG height=3 src="$images/spacer.gif" width=3 colspan=7></TD>
					</TR>
end_of_html
$sql = "select status,max_emails,last60_flag,aol_flag,open_flag,hotmail_flag,yahoo_flag,other_flag,open_category_id,advertiser_id from campaign where campaign_id=$campaign_id";
$sth = $dbh->prepare($sql);
$sth->execute();
($status,$max_emails,$clast60,$aol_flag,$openflag,$hotmail_flag,$yahoo_flag,$other_flag,$opener_catid,$aid) = $sth->fetchrow_array();
$sth->finish();
if ($clast60 eq "Y")
{
	$yes_flag="checked";
	$month_flag = "";
	$no_flag = "";
	$seven_flag = "";
	$two_flag = "";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "7")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "checked";
	$month_flag = "";
	$two_flag = "";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "M")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "";
	$two_flag = "";
	$month_flag="checked";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "";
}
elsif ($clast60 eq "9")
{
	$no_flag="";
	$yes_flag = "";
	$seven_flag = "";
	$two_flag = "";
	$month_flag="";
	$three_flag = "";
	$old_flag = "";
	$last90_flag = "checked";
}
else
{
	$no_flag="checked";
	$yes_flag = "";
	$seven_flag = "";
}
if ($openflag eq "Y")
{
	$yes_open_flag="checked";
	$no_open_flag = "";
}
else
{
	$no_open_flag="checked";
	$yes_open_flag = "";
}
if ($aol_flag eq "N")
{
	$aol_flag="";
}
else
{
	$aol_flag="checked";
}
if ($hotmail_flag eq "N")
{
	$hotmail_flag="";
}
else
{
	$hotmail_flag="checked";
}
if ($yahoo_flag eq "N")
{
	$yahoo_flag="";
}
else
{
	$yahoo_flag="checked";
}
if ($other_flag eq "N")
{
	$other_flag="";
}
else
{
	$other_flag="checked";
}
# read all lists for this user
$sql = "select list_id, first_name,last_name,list_name from list,user where list.status='A' and user.user_id=list.user_id order by username,list_name";
$sth = $dbh->prepare($sql);
$sth->execute();
$alllists = 0; 
$aolalllists = 0; 
$hotmailalllists = 0;
$yahooalllists = 0;
my $old_name="";
while (($list_id, $name,$lname,$list_name) = $sth->fetchrow_array())
{
	if ($old_name ne $name)
	{
		if ($old_name ne "")
		{
	print "<TR>\n";
	print "<TD align=middle><font face=Arial></td><td><b>Total</b></td>\n";
		print "<TD align=middle><FONT face=Arial color=black size=2>$tcnt</FONT></TD>\n";
		$pcnt = 0;
		if ($tcnt > 0)
		{
			$pcnt = ($aolcnt/$tcnt)*100;
		}
		printf "<TD align=middle><FONT face=Arial color=black size=2 align=middle>$aolcnt(%4.2f%)</font></td>\n",$pcnt;
		$pcnt = 0;
		if ($tcnt > 0)
		{
			$pcnt = ($yahoocnt/$tcnt)*100;
		}
		printf "<TD align=middle><FONT face=Arial color=black size=2 align=middle>$yahoocnt(%4.2f%)</font></td>\n",$pcnt;
		$pcnt = 0;
		if ($tcnt > 0)
		{
			$pcnt = ($totalcnt/$tcnt)*100;
		}
		printf "<TD align=middle><FONT face=Arial color=black size=2 align=middle>$totalcnt(%4.2f%)</font></td></tr>\n",$pcnt;
		}
$tcnt = 0;
$totalcnt = 0;
$aolcnt = 0;
$yahoocnt = 0;
$hotmailcnt = 0;
		$old_name=$name;
		print "<tr><td colspan=5>&nbsp;</td></tr><TR><td colspan=2><font face=Arial color=#509C10 size=2><b>$name $lname</b></td></tr>\n";
	}
	print "<TR>\n";
	print "<TD align=middle><font face=Arial>\n";
	#
	# Check to see if list already part of campaign
	#
	$sql = "select count(*) from campaign_list where campaign_id=$campaign_id and list_id=$list_id";
	my $sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($include_flag) = $sth1->fetchrow_array();
	if ($include_flag > 0)
	{
		print "<INPUT CHECKED type=checkbox name=list_$list_id></font></TD>\n";
	}
	else
	{
		print "<INPUT type=checkbox name=list_$list_id></font></TD>\n";
	}
	$sth1->finish();
	print "<TD align=left>\n";
	print "<FONT face=Arial color=#509C10 size=2>$list_name</FONT></TD>\n";
	print "<TD align=middle>\n";
	#
	# Get the number of subscribers for the list
	#
	$sql = "select member_cnt,aol_cnt,hotmail_cnt,msn_cnt,yahoo_cnt,foreign_cnt from list where list_id=$list_id and status='A'";
	my $sth1 = $dbh->prepare($sql);
	$sth1->execute();
	if (($subscribe_cnt,$aol_cnt,$hotmail_cnt,$msn_cnt,$yahoo_cnt,$foreign_cnt) = $sth1->fetchrow_array())
	{
		$hotmail_cnt = $hotmail_cnt + $msn_cnt;
		$tcnt = $tcnt + $subscribe_cnt;
		$totalcnt = $totalcnt + $subscribe_cnt - $aol_cnt - $hotmail_cnt - $yahoo_cnt - $foreign_cnt;
		$aolcnt = $aolcnt + $aol_cnt;
		$hotmailcnt = $hotmailcnt + $hotmail_cnt;
		$yahoocnt = $yahoocnt + $yahoo_cnt;
		$nonaolcnt = $subscribe_cnt - $aol_cnt - $hotmail_cnt - $yahoo_cnt - $foreign_cnt;
		$alllists = $alllists + $nonaolcnt;
		$aolalllists = $aolalllists + $aol_cnt;
		$hotmailalllists = $hotmailalllists + $hotmail_cnt;
		$yahooalllists = $yahooalllists + $yahoo_cnt;
		print "<FONT face=Arial color=#509C10 size=2>$subscribe_cnt</FONT></TD>\n";
		print "<TD align=middle><FONT face=Arial color=#509C10 size=2 align=middle>$aol_cnt</font></td>\n";
		print "<TD align=middle><FONT face=Arial color=#509C10 size=2 align=middle>$yahoo_cnt</font></td>\n";
		print "<TD align=middle><FONT face=Arial color=#509C10 size=2 align=middle>$nonaolcnt</font></td>\n";
	}
	else
	{
		print "<FONT face=Arial color=#509C10 size=2>0</FONT></TD>\n";
	}
	$sth1->finish();
	print "<TD width=35><font face=Arial>&nbsp;</font></TD>\n";
	print "</TR>\n";
}
$sth->finish();
	print "<TR>\n";
	print "<TD align=middle><font face=Arial></td><td><b>Total</b></td>\n";
		print "<TD align=middle><FONT face=Arial color=black size=2>$tcnt</FONT></TD>\n";
		$pcnt = 0;
		if ($tcnt > 0)
		{
			$pcnt = ($aolcnt/$tcnt)*100;
		}
		printf "<TD align=middle><FONT face=Arial color=black size=2 align=middle>$aolcnt(%4.2f%)</font></td>\n",$pcnt;
		$pcnt = 0;
		if ($tcnt > 0)
		{
			$pcnt = ($yahoocnt/$tcnt)*100;
		}
		printf "<TD align=middle><FONT face=Arial color=black size=2 align=middle>$yahoocnt(%4.2f%)</font></td>\n",$pcnt;
		$pcnt = 0;
		if ($tcnt > 0)
		{
			$pcnt = ($totalcnt/$tcnt)*100;
		}
		printf "<TD align=middle><FONT face=Arial color=black size=2 align=middle>$totalcnt(%4.2f%)</font></td></tr>\n",$pcnt;

print << "end_of_html";
                    <TR>
                    <TD vAlign=center align=left colSpan=4>&nbsp;</TD>
                    </TR>
						<tr>
							<td vAlign="center" align="left" colSpan="7" height="96">
							<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table9">
								<tr>
									<td vAlign="center" align="left">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Send To:
									<input type="radio" value="N" name="clast60" $no_flag>All&nbsp;&nbsp;<input type="radio" value="7" name="clast60" $seven_flag>Last 
									7 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="M" name="clast60" $month_flag>Last 
									30 Days&nbsp;&nbsp;&nbsp;<input type="radio" value="Y" name="clast60" $yes_flag>Last 
									60 Days&nbsp;&nbsp;&nbsp;<input type="radio" $last90_flag value="9" name="clast60">Last 
									90 Days</font></td>
								</tr>
								<tr>
									<td vAlign="center" align="left">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Send To:
									<input type="checkbox" value="Y" name="aolflag" $aol_flag>AOL 
									&nbsp;&nbsp;<input type="checkbox" value="Y" name="hotmailflag" $hotmail_flag>Hotmail/MSN&nbsp;&nbsp;&nbsp;<input type="checkbox" $yahoo_flag value="Y" name="yahooflag">Yahoo&nbsp;&nbsp;&nbsp;<input type="checkbox" $other_flag value="Y" name="otherflag">Other 
									Domains</font></td>
								</tr>
								<tr>
									<td vAlign="center" align="left">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Include Openers:
									<input type="radio" $no_open_flag value="N" name="copen">No&nbsp;&nbsp;<input type="radio" $yes_open_flag value="Y" name="copen">Yes</font></td>
								</tr>
								<tr>
									<td vAlign="center" align="left">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Openers Category: <select name="open_catid">
									<option value="0" selected>All</option>
									<option value="3">Animals &amp; Pets</option>
									<option value="4">Astrology &amp; Horoscope
									</option>
									<option value="5">Auctions</option>
									<option value="6">Automotive</option>
									<option value="7">Books &amp; Magazines</option>
									<option value="2">Books &amp; Mags - Health
									</option>
									<option value="56">Cash</option>
									<option value="51">CashAdTest</option>
									<option value="8">Charity</option>
									<option value="9">Clothing &amp; Apparel
									</option>
									<option value="10">Communications - Cellular
									</option>
									<option value="11">Communications - Long Dis
									</option>
									<option value="12">Computers &amp; Internet
									</option>
									<option value="13">Computers - Hardware
									</option>
									<option value="43">Computers - Software
									</option>
									<option value="14">Coupons &amp; Discount Cards
									</option>
									<option value="15">Dating</option>
									<option value="16">Education</option>
									<option value="17">Employment</option>
									<option value="18">Entertainment</option>
									<option value="40">Entertainment - Movies
									</option>
									<option value="41">Entertainment - Music
									</option>
									<option value="42">Entertainment-Video Games
									</option>
									<option value="19">Financial - Auto Loans
									</option>
									<option value="22">Financial - Bus Oppts
									</option>
									<option value="20">Financial - Credit
									</option>
									<option value="21">Financial - Debt Mgmt
									</option>
									<option value="23">Financial - Loans
									</option>
									<option value="24">Financial - Mortgage
									</option>
									<option value="25">Food</option>
									<option value="49">Free Stuff</option>
									<option value="26">Gambling</option>
									<option value="27">Health &amp; Beauty</option>
									<option value="28">Health &amp; Beauty - Dental
									</option>
									<option value="44">Health &amp; Beauty - Fitness
									</option>
									<option value="32">Health &amp; Beauty - Pharma
									</option>
									<option value="29">Health &amp; Beauty - Skin
									</option>
									<option value="30">Health &amp; Beauty - Smokers
									</option>
									<option value="31">Health &amp; Beauty - Weight
									</option>
									<option value="33">Health &amp; Beauty- Natural
									</option>
									<option value="34">Holiday</option>
									<option value="35">House &amp; Home</option>
									<option value="50">imatch</option>
									<option value="57">Insurance</option>
									<option value="55">Military</option>
									<option value="53">MLM</option>
									<option value="54">New Car</option>
									<option value="58">None</option>
									<option value="36">Real Estate</option>
									<option value="37">Small Business</option>
									<option value="48">Surveys</option>
									<option value="47">Sweepstakes</option>
									<option value="38">Toys &amp; Hobbies</option>
									<option value="46">Toys &amp; Hobbies - Gadgets
									</option>
									<option value="45">Toys - Collectibles
									</option>
									<option value="39">Travel</option>
									<option value="52">Weight Loss</option>
									</select> </font></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td vAlign="center" align="left">
									<font face="verdana,arial,helvetica,sans serif" color="#509c10" size="2">
									Max E-mails To Send (-1 means all):
									<input style="BACKGROUND-COLOR: #ffffa0" value="$max_emails" name="max_emails"></font></td>
								</tr>
							</table>
&nbsp;</td>
						</tr>
						<!--	<tr><td colspan=3>NonAOL Cnt:</td><td colspan=2>AOL Cnt: </td></tr> -->
						<tr bgolcor="#E3FAD1">
							<td colSpan="3">&nbsp; </td>
						</tr>
					</table>
&nbsp;</P>
				</td>
			</tr>
		</TD>
		</TR>
		<TR bgcolor=white>
		<TD height="65">

			<TABLE cellSpacing=0 cellPadding=7 width="100%" border=0 bgcolor=white>
			<TBODY>
			<TR>
			<TD align=middle><a href="show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U"><IMG src="$images/cancel.gif" 
				border=0> </a>
				<IMG height=1 src="$images/spacer.gif" width=40 border=0> 
end_of_html
if (($status eq "D") || ($status eq "S"))
{
	print "<INPUT type=image src=\"$images/save.gif\" border=0></TD>\n";
}
print<<"end_of_html";
			</TR>
			</TBODY>
			</TABLE>

				</form>
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

#
#	See if any lists selected
#
	$sql = "select count(*) from campaign_list where campaign_id=$campaign_id";
	my $sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($include_flag) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($include_flag == 0)
	{
print<<"end_of_html";
<SCRIPT language="javascript">
selectall();
</script>
end_of_html
	}
$util->footer();
$util->clean_up();
exit(0);
