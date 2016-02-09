#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of List Profiles 
# File   : listprofile_list.cgi
#
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

#--------------------------------
# get some objects to use later
#--------------------------------
my $util = util->new;
my $query = CGI->new;
my $mesg = $query->param('mesg');
my $tflag=$query->param('tflag');
my $add_copy;
my $tflag_str;
my @raw_cookies;
my %cookies;
my $key;
my $val;
my $master_flag;
@raw_cookies = split (/; /,$ENV{'HTTP_COOKIE'});
foreach (@raw_cookies)
{
    ($key, $val) = split (/=/,$_);
    $cookies{$key} = $val;
}
my $soption = $cookies{'soption'};
if ($soption eq "")
{
	$soption="first_name,profile_name";
}
my $client_id = $cookies{'lnetworkopt'};
if ($client_id eq "")
{
	$client_id=1;
}
my $tid = $cookies{'tid'};
if ($tid eq "")
{
	$tid=0;
}
$add_copy=0;
if ($tflag eq "")
{
	$tflag = "N";
}
if ($tflag eq "3")
{
	$add_copy=1;
	if ($tid > 0)
	{
		$tflag_str = "and third_party_id = $tid and profile_type='3RDPARTY' ";
	}
	else
	{
		$tflag_str = "and profile_type='3RDPARTY' ";
	}
}
elsif ($tflag eq "C")
{
	$tflag_str = "and profile_type='CHUNK' ";
}
elsif ($tflag eq "L")
{
	$tflag_str = "and profile_type='NEWSLETTER' ";
}
else
{
	$tflag_str = "and profile_type='NORMAL' ";
}
if ($client_id eq "")
{
	$client_id=1;
}
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my ($member_cnt, $aol_cnt, $yahoo_cnt, $hotmail_cnt,$other_cnt,$aol_cnt_p);
my $aol_flag;
my $hotmail_flag;
my $comcast_flag;
my $other_flag;
my $yahoo_flag;
my $percent_sub;

my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

# ------- connect to the util database ---------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

&disp_header();
if ( $mesg ne "" ) {
	#---------------------------------------------------------------------------
	# Display mesg (if present) from module that called this module.
	#---------------------------------------------------------------------------
	print qq{ <script language="JavaScript">  \n } ;
	# print qq{ 	alert("The specified List Records have been SUCCESSFULLY updated!");  \n } ;
	print qq{ 	alert("$mesg");  \n } ;
	print qq{ </script>  \n } ;
}
&disp_body();
&disp_footer();
#------------------------
# End Main Logic
#------------------------



#===============================================================================
# Sub: disp_header - Header for PMS System (close bogus tbls to disp correctly)
#===============================================================================
sub disp_header
{
	my ($heading_text, $username, $curdate) ;

	$curdate = $util->date(0,2) ;
	$heading_text = "User: $username &nbsp;&nbsp;&nbsp;Date: $curdate" ;

	util::header($heading_text);    # Print HTML Header
	#-----------------------------------------------------------
	#  BEGIN HEADER FIX 
	#-----------------------------------------------------------
	print << "end_of_html";
	</TD>
	</TR>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF>
	<TABLE cellSpacing=0 cellPadding=0 bgColor=#FFFFFF border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left bgColor=#FFFFFF colSpan=10>

	<TABLE cellSpacing=0 cellPadding=0 width=1100 bgColor=#ffffff border=0>
	<TBODY>
	<TR>
	<TD>&nbsp;</TD></TR>
	</TBODY>
	</TABLE>
end_of_html
	#-----------------------------------------------------------
	#  END HEADER FIX 
	#-----------------------------------------------------------

} # end sub disp_header



#===============================================================================
# Sub: disp_body
#===============================================================================
sub disp_body
{
	my ($puserid, $username, $fname, $lname, $city, $state, $zip, $status);
	my ($bgcolor) ;
	my ($pid, $pname, $company, $day_flag);
	my $send_str;
	my $last_id;
	my $tname;
	my $atid;
	my $temp_id;
	my $company;

	print << "end_of_html" ;
	<center><a href="/cgi-bin/listprofile_add.cgi?tflag=$tflag"><img src="/mail-images/add.gif" border=0></a><br><br>
	<form method=post action="/cgi-bin/listprofile_cook.cgi">
	<input type=hidden name=tflag value=$tflag>
	Sort: <select name=sort_option>
	<option value="first_name,profile_name">Username,Name</option>
	<option value="profile_name,first_name">Name,Username</option>
	</select>&nbsp;&nbsp;
	Client: <select name=client_id>
<!--	<option value=0 selected>ALL</option> -->
end_of_html
	$sql="select user_id,first_name from user where status='A' order by first_name";
	my $sth1a=$dbhq->prepare($sql);
	$sth1a->execute();
	while (($temp_id,$company)=$sth1a->fetchrow_array())
	{
		if ($temp_id == $client_id)
		{
			print "<option value=$temp_id selected>$company</option>\n";
		}
		else
		{
			print "<option value=$temp_id>$company</option>\n";
		}
	}
	$sth1a->finish();
	print "</select>&nbsp;&nbsp;";
	if ($tflag eq "3")
	{
		print "<input type=hidden name=tflag value=3>\n";
		print "Third Party Mailer: <select name=tid>\n";
		print "<option selected value=0>ALL</option>\n";
		$sql="select third_party_id,mailer_name from third_party_defaults order by mailer_name";
		$sth1 = $dbhq->prepare($sql) ;
		$sth1->execute();
		my $ttid;
		my $temp_name;
		while (($ttid,$temp_name) = $sth1->fetchrow_array())
		{
			if ($ttid == $tid)
			{
				print "<option selected value=$ttid>$temp_name</option>\n";
			}
			else
			{
				print "<option value=$ttid>$temp_name</option>\n";
			}		
		}
		$sth1->finish();
		print "</select>\n";
	}
	print << "end_of_html" ;
&nbsp;&nbsp;<input type=submit value="Go"></form><br>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
	<TR bgColor="#509C10" height=15>
		<TD colspan="13" align=center height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current List Profiles</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the profile)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="20%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2>
		<b>Client</b></font></td>
	<TD bgcolor="#EBFAD1" align="middle" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>3rd Party</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Send To</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Sub</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Sub %</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Estimted<br>Sent</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>AOL</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Yahoo</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Other</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Hotmail/Msn</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" ><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></font></td>
	</TR> 

end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================
	if ($client_id > 0)
	{
		$sql = "select profile_id,profile_name,first_name,day_flag,last_email_user_id,third_party_id,aol_flag,hotmail_flag,other_flag,yahoo_flag,percent_sub,master,comcast_flag from list_profile,user where list_profile.client_id=user.user_id and list_profile.status='A' and user.user_id=$client_id $tflag_str order by $soption"; 
	}
	else
	{
		$sql = "select profile_id,profile_name,first_name,day_flag,last_email_user_id,third_party_id,aol_flag,hotmail_flag,other_flag,yahoo_flag,percent_sub,master,comcast_flag from list_profile,user where list_profile.client_id=user.user_id and list_profile.status='A' $tflag_str order by $soption"; 
	}
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($pid, $pname, $company, $day_flag,$last_id,$atid,$aol_flag,$hotmail_flag,$other_flag,$yahoo_flag,$percent_sub,$master_flag,$comcast_flag) = $sth->fetchrow_array())
	{
		if ($master_flag eq "Y")
		{
			$pname=$pname." :M";
		}
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "#EBFAD1" ;     # Light Green
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;     # Light Yellow
		}

		if ($day_flag eq "7")
		{
			$send_str = "Last 7 Days";
		}
		elsif ($day_flag eq "T")
		{
			$send_str = "Last 3 Days";
		}
		elsif ($day_flag eq "F")
		{
			$send_str = "Last 15 Days";
		}
		elsif ($day_flag eq "N")
		{
			$send_str = "All";
		}
		elsif ($day_flag eq "M")
		{
			$send_str = "Last 30 Days";
		}
		elsif ($day_flag eq "Y")
		{
			$send_str = "Last 60 Days";
		}
		elsif ($day_flag eq "9")
		{
			$send_str = "Last 90 Days";
		}
		elsif ($day_flag eq "O")
		{
			$send_str = "Last 180 Days";
		}
		elsif ($day_flag eq "3")
		{
			$send_str = "Last 120 Days";
		}
		elsif ($day_flag eq "5")
		{
			$send_str = "Last 150 Days";
		}
		if ($tflag eq "3")
		{
			$send_str = $send_str . "<br>";
			my $s_str="";
			if ($aol_flag ne "N")
			{
				$s_str = $s_str . "AOL,";
			}
			if ($hotmail_flag ne "N")
			{
				$s_str = $s_str . "Hotmail,";
			}
			if ($yahoo_flag ne "N")
			{
				$s_str = $s_str . "Yahoo,";
			}
			if ($comcast_flag ne "N")
			{
				$s_str = $s_str . "Comcast,";
			}
			if ($other_flag ne "N")
			{
				$s_str = $s_str . "Others,";
			}
			$_ = $s_str;
			chop;
			$s_str = $_;
			$send_str = $send_str . "(" . $s_str . ")";
		}

		if ($atid > 0)
		{
			$sql="select mailer_name from third_party_defaults where third_party_id=$atid"; 
			$sth1 = $dbhq->prepare($sql) ;
			$sth1->execute();
			($tname) = $sth1->fetchrow_array();
			$sth1->finish();
		}
		else
		{
			$tname="";
		}
#		$sql = "select sum(member_cnt),sum(aol_cnt),sum(yahoo_cnt),sum(hotmail_cnt)+sum(msn_cnt),sum(member_cnt)-sum(aol_cnt)-sum(yahoo_cnt)-sum(hotmail_cnt)-sum(msn_cnt),sum(aol_cnt_p) from list,list_profile_list where list.list_id = list_profile_list.list_id and profile_id=$pid and list.status ='A'";
#		$sth1 = $dbhq->prepare($sql) ;
#		$sth1->execute();
#		($member_cnt, $aol_cnt, $yahoo_cnt, $hotmail_cnt,$other_cnt,$aol_cnt_p) = $sth1->fetchrow_array();
#		$sth1->finish();
		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=left>&nbsp;</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="listprofile_edit.cgi?pid=$pid">$pname</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$company</font></TD> \n } ;
        print qq{	<TD align=middle><font color="#509C10" face="Arial" size="2">$tname</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$send_str</font></TD> \n } ;
		$member_cnt=0;
		if ($aol_flag eq "Y")
		{
			$member_cnt=$member_cnt + $aol_cnt;
		}
		if ($hotmail_flag eq "Y")
		{
			$member_cnt=$member_cnt + $hotmail_cnt;
		}
		if ($yahoo_flag ne "N")
		{
			$member_cnt=$member_cnt + $yahoo_cnt;
		}
		if ($other_flag eq "Y")
		{
			$member_cnt=$member_cnt + $other_cnt;
		}
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$member_cnt</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$percent_sub \%</font></TD> \n } ;
		my $estimate_cnt=int(($member_cnt)* ($percent_sub/100.0));
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$estimate_cnt</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$aol_cnt - (P - $aol_cnt_p)</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$yahoo_cnt</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$other_cnt</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$hotmail_cnt</font></TD> \n } ;
		if ($add_copy == 1)
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/copy_profile.cgi?pid=$pid">Copy</a><br><a href="/cgi-bin/listprofile_del.cgi?pid=$pid">Delete</a>&nbsp;&nbsp;</font></TD> \n } ;
		}
		else
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><a href="/cgi-bin/listprofile_del.cgi?pid=$pid">Delete</a>&nbsp;&nbsp;</font></TD> \n } ;
		}
		print qq{</TR> \n} ;

	}  # end while statement

	$sth->finish();

	print << "end_of_html" ;

	<TR><td align=center colspan=5><br>
	<A HREF="mainmenu.cgi">
	<IMG name="BtnHome" src="$images/home_blkline.gif" hspace=7  border="0" width="72"  height="21" ></A>
	</td></tr>

	</tbody>
	</table>

end_of_html
} # end sub disp_body


#===============================================================================
# Sub: disp_footer
#===============================================================================
sub disp_footer
{
	print << "end_of_html";
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td>
end_of_html

	util::footer();
	$util->clean_up();
} # end sub disp_footer


