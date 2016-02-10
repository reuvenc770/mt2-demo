#!/usr/bin/perl
#===============================================================================
# File   : remove_user_chunk.cgi
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true

my $util = util->new;
my $query = CGI->new;
my $dbh;
my $rows;
my $sql;
my $tot_good;
my $tot_bad;
my $tot_dup;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
my $images = $util->get_images_url;
my $email_user_id;
$tot_good = 0;
$tot_bad = 0;
$tot_dup = 0;
my ($BytesRead, $Buffer, $Bytes ) ;
my (@temp_file, %confirm);
my $tmp_file;


# ------- Get fields from html Form post -----------------

my $eid = $query->param('eid');
my $global = $query->param('global');

# ----- connect to the util database -------
my ($dbhq,$dbhu)=$util->get_dbh();
my $dbhu2 = DBI->connect("DBI:mysql:new_mail:update2.routename.com","db_user","sp1r3V");

# ----- check for login -------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&uns_upd_list_member($eid);

#---- Print Delete Statistics -----------------
&print_uns_summary();


$util->clean_up();
exit(0) ;

#===============================================================================
# Sub: uns_upd_list_member
#===============================================================================
sub uns_upd_list_member
{
	my ($eid) = @_ ;
	my ($email_exists, $sth1, $sth2, $status, $i) ; 
	my $email_addr;
	my $tid;
	
	# ---- See if Email Addr Already Exists for specified List -----
	$email_exists = 0 ;
	$sql = "select email_addr,email_user_id from master_email_chunk_list where email_user_id = '$eid'";
	$sth1 = $dbhu2->prepare($sql) ;
	$sth1->execute();
	($email_addr,$email_user_id) = $sth1->fetchrow_array();
	$sth1->finish;

	if ($email_addr) 
	{
		$sth1->finish();
		# Update Existing list_member rec -- Reset Status, Subscribe and UnSubscribe fields
		if (($global eq "Y") or ($global eq "D"))
		{
			$sql = "select distinct user_id from list where list_id in (select list_id from email_list where email_addr='$email_addr' and status='A')";
			$sth1 = $dbhq->prepare($sql) ;
			$sth1->execute();
			while (($tid) = $sth1->fetchrow_array())
			{
				$sql = "insert into manual_removal(email_addr,removal_date,client_id) values('$email_addr',now(),$tid)";
				$rows = $dbhu->do($sql) ;
				$sql = "insert into unsub_log(email_addr,unsub_date,client_id) values('$email_addr',now(),$tid)";
				$rows = $dbhu->do($sql) ;
			}
			$sth1->finish();
		}
		$tot_good = $tot_good + 1 ;
		if (($global eq "Y") or ($global eq "D"))
		{
			util::addGlobal($email_addr);
    		$sql = "update unique_email_list set status='U',unsubscribe_date=now(),unsubscribe_time=now() where email_addr='$email_addr' and status in ('A','P')";
    		$rows = $dbhu2->do($sql);
    		$sql="select email_user_id from master_email_chunk_list where email_addr='$email_addr'";
    		my $sth2a=$dbhu2->prepare($sql);
    		$sth2a->execute();
    		if (($eid) = $sth2a->fetchrow_array())
    		{
        		$sql="select user_id,company from user where status='A' order by user_id";
        		my $sth1a=$dbhu->prepare($sql);
        		$sth1a->execute();
				my $company;
        		while (($user_id,$company) = $sth1a->fetchrow_array())
        		{
            		$sql="update email_chunk_list_${user_id} set unsubscribe_date=curdate(),unsubscribe_time=curtime(),status='U' where email_user_id=$eid and status ='A'";
            		$rows = $dbhu2->do($sql);
        		}
        		$sth1a->finish();
    		}
    		$sth2a->finish();
		}
		if ($global eq "D")
		{
my $caddr;
my $cdomain;
        	($caddr,$cdomain) = split("@",$email_addr);
## deprecating suppress_domain in favor of email_domains - jp Thu Jan  5 10:27:14 EST 2006
## insert b4 update so we don't override a previous dateSupp setting if suppressed already = 1
			$sql=qq^INSERT IGNORE INTO email_domains (domain_id, domain_class, domain_name, suppressed, dateSupp) VALUES (NULL, '4', '$cdomain', 1, NOW())^;
			$rows=$dbhu->do($sql);
			if ($rows == 0) {
				$sql=qq^UPDATE email_domains SET suppressed='1', dateSupp=NOW() WHERE domain_name='$cdomain' AND suppressed=0 and chunked=0^;
				$rows=$dbhu->do($sql);
			}
##			$sql = "insert into suppress_domain values('$cdomain',now())";
##			$rows = $dbhu->do($sql) ;
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Domain added to Global Suppression <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: setup\@zetainteractive.com\n";
        print MAIL "Subject: Domain Added to Global Suppression\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "$cdomain added\n";
        close MAIL;
		}
	}
	else
	{
		$tot_bad = $tot_bad + 1 ;
	}

} # end of sub


#===============================================================================
# Sub: print_uns_summary
#===============================================================================
sub print_uns_summary
{

	my ($list_cnt, $color);
	my ($sth1, $list_name) ;

util::header("UNSUBSCRIBE SUBSCRIBERS SUMMARY");    # Print HTML Header
#-----------------------------------------------------------
#  BEGIN HEADER FIX 
#-----------------------------------------------------------
print << "end_of_html";
</TD>
</TR>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF>

<TABLE cellSpacing=0 cellPadding=10 bgColor=#FFFFFF border=0>
<TBODY>
<TR>
<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
<TBODY>
<TR>
<TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>
<B>Confirmation</B></FONT></TD></TR>
</TBODY>
</TABLE>
end_of_html
#-----------------------------------------------------------
#  END HEADER FIX 
#-----------------------------------------------------------


	print << "end_of_html" ;
	
	<!--  BEGIN My Tbl Definition -------------------------------------------- -->
	<!--  Form Buttons - Images ---------------------------------------- -->
	<FORM name="sub_uns_mesg_form" action=mainmenu.cgi method=post>

	<TABLE align="left" cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor=#509C10 height=15>
	<TD colspan="6" align=middle width="100%" height=15>
	<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
	<b>Summary of Subscriber Emails Unsubscribed from Lists</b></font></TD>
	</TR>
	<TR bgColor=#509C10 height=15>
	<td colspan="6" >&nbsp;</td>
	</tr>

	<!-- bgColor = "#509C10" ;     # Dark Green     -->
	<!-- bgcolor = "#EBFAD1" ;     # Light Green    -->
	<!-- bgcolor = "$alt_light_table_bg" ;     # Light Yellow   -->

	<!-- Table Headings ----------------------------------------------------- -->
	<TR> 
		<TD bgcolor="#509C10" align="left" width="05%">&nbsp;</td>
		<TD bgcolor="#509C10" align="center" width="25%">	 
		<FONT face="verdana,arial,helvetica,sans serif" color=white size=2><b>List Name</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Number<br>Unsubscribe</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Number<br>Previously<br>Unsubscribed</b></font></td>
		<TD bgcolor="#509C10" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=white size=2>
			<b>Invalid<br>Unsubscribes</b></font></td>
		<TD bgcolor="#509C10" align="left" width="05%">&nbsp;</td>
	</TR> 
end_of_html

	print << "end_of_html" ;

	<!-- Table TOTAL Line(s) -------------------------------------------------- -->
	<TR> 
		<TD bgcolor="#EBFAD1" align="left"  width="5%" >&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left"  width="25%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="right" width="15%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left"  width="5%" >&nbsp;</td>
	</TR> 
	<TR> 
		<TD bgcolor="#EBFAD1" align="left" width="5%">&nbsp;</td>
		<TD bgcolor="#EBFAD1" align="left" width="25%">	 
		<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>Totals</b></FONT></TD> 
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_good</b></td>
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_dup</b></td>
		<TD bgcolor="#EBFAD1" align="right" width="15%"><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2><b>$tot_bad</b></td>
		<TD bgcolor="#EBFAD1" align="left" width="5%">&nbsp;</td>
	</TR> 

	<tr>
	<td colspan="6" bgcolor="#EBFAD1" align="center" width="100%"><br>
	<INPUT type="image" src="$images/home_blkline.gif" hspace=7  width="72"  height="23" border=0>
	</td>
	</tr>

	</tbody>
	</table>
	</FORM> 

</td>
</tr>
<tr>
<td>
end_of_html

	util::footer();
}  
