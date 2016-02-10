#!/usr/bin/perl
#===============================================================================
# Purpose: Displays a List of Unique Profiles 
# File   : uniqueprofile_list.cgi
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
my $add_copy;
my $tflag_str;
my @raw_cookies;
my %cookies;
my $key;
my $val;
my ($sth, $reccnt, $sql, $dbh ) ;
my $sth1;
my $exportData;
my $changeProfile;
my $BusinessUnit;

my $images = $util->get_images_url;
my $alt_light_table_bg = $util->get_alt_light_table_bg;

my ($dbhq,$dbhu)=$util->get_dbh();

# ------- check for login - if not logged in then Exit --------------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}
my $username;
$sql = "select username, exportData,changeProfile,BusinessUnit from UserAccounts where user_id = ?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($user_id);
($username, $exportData,$changeProfile,$BusinessUnit) = $sth->fetchrow_array();
$sth->finish();

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
	my $ctitle="Mailing System";
	print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$ctitle EMail System</title>
<style type="text/css">

body {
	background: top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
	text-align: center;
  }

h1 {
	text-align: center;
	font-weight: normal;
	font-size: 1.5em;
  }

h2 {
	text-align: center;
	font-weight: normal;
	font-size: 1em;
  }

h4 {
	font-weight: normal;
	font-size: .8em;
	padding-top: 1em;
	margin: 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

#container {
	width: 80%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	width: 100%;
	text-align: left;
  }

#form table {
	border: 1px solid #aaa;
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

tr.inactive {
	color: #aaa;
}

td.label {
	width: 40%;
	text-align: right;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	margin-top: 1em;
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }
</style>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 align=left border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>
	<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
	<TD width=248 rowSpan=2>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 >&nbsp;</TD>
	</tr>
	<tr>
	<td width="468">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left"><b><font face="Arial" size="2">&nbsp;$heading_text</FONT></b></td>
		</tr>
		<tr>
		<td align="right">
    		<b><a style="TEXT-DECORATION: none" href="logout.cgi">
    		<font face=Arial size=2 color="#509C10">Logout</font></a>&nbsp;&nbsp;&nbsp;
    		<a href="wss_support_form.cgi" style="text-decoration: none">
    		<font face=Arial size=2 color="#509C10">Customer Assistance</font></a></b>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
	</TD>
	</TR>
	<TR>
	<TD vAlign=top align=left>
	<TABLE cellSpacing=0 cellPadding=0 border=0>
	<TBODY>
	<TR>
	<TD vAlign=top align=left colSpan=10>

	<TABLE cellSpacing=0 cellPadding=0 width=1100 border=0>
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
	my $rstart;
	my $rend;
	my ($ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor);
	my ($ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1);
	my ($ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2);
	my ($ostart_date,$oend_date,$cstart_date,$cend_date,$dstart_date,$dend_date);
	my ($convert_start,$convert_end,$convert_start_date,$convert_end_date,$convert_start1,$convert_end1,$convert_start2,$convert_end2);
	my $ProfileForClient;

	print << "end_of_html" ;
	<center><a href="/cgi-bin/uniqueprofile_edit.cgi?pid=0"><img src="/mail-images/add.gif" border=0></a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_edit.cgi?pid=0&uid=931">Add ODR Profile</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_edit.cgi?pid=0&uid=316">Add ODR Profile(Client 316)</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_edit.cgi?pid=0&uid=1458">Add ODR Profile(Client 1458)</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_check.cgi">Calc Profile</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_check.cgi?clientid=931">ODR Calc Profile</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_check.cgi?clientid=316">ODR Calc Profile(316)</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_checkv2.cgi">Calc Profile v2</a>&nbsp;&nbsp;<a href="/cgi-bin/uniqueprofile_checkv3.cgi">Calc Profile v3</a>
end_of_html
	if ($exportData eq "Y")
	{
		print "&nbsp;&nbsp;<a href=\"/cgi-bin/uniqueprofile_check.cgi?export=1\">Export Data</a>&nbsp;&nbsp;<a href=\"/cgi-bin/uniqueprofile_check.cgi?export=2\">Export Suppression</a>&nbsp;&nbsp;<br><a href=\"/cgi-bin/uniqueprofile_check.cgi?clientid=316&export=1\">Export Data(Client 316)</a>&nbsp;&nbsp;<a href=\"/cgi-bin/espSuppression.cgi?esp=NA\">Setup NA Advertisers for Suppression</a>";
	}
	print << "end_of_html";
	<br><br><TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TBODY>
end_of_html
if ($changeProfile eq "Y")
{
print<<"end_of_html";
        <tr><td colspan=4><form method="post" action="default_uniqueprofile.cgi">
Default Mailing Profile: <select name=profile_id>
end_of_html
my $current_pid;
my $tpid;
my $tpname;
$sql="select parmval from sysparm where parmkey='DEFAULT_MAILING_PROFILE'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($current_pid)=$sth->fetchrow_array();
$sth->finish();
#
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($tpid,$tpname)=$sth->fetchrow_array())
{
	if ($current_pid == $tpid)
	{
		print "<option selected value=$tpid>$tpname</option>";
	}
	else
	{
		print "<option value=$tpid>$tpname</option>";
	}
}
$sth->finish();
print<<"end_of_html";
</select><input type=submit value=Update></form></td></tr>
end_of_html
}
print<<"end_of_html";
        <tr>
        <tr>
        <td colspan=4><form method="post" action="upload_uniqueprofile.cgi" encType=multipart/form-data>
Unique Profile File: <input type=file name=upload_file><input type=submit value=Load></form></td></tr>
        <tr>
	<TR bgColor="#509C10" height=15>
		<TD colspan="13" align=center height=15>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="3">
		<b>List of Current Unique Profiles</font>
		<font face="Verdana,Arial,Helvetica,sans-serif" color="white" size="2">
		(click name to edit the profile)</b></font></TD>
	</TR>
	<TR> 
	<TD bgcolor="#EBFAD1" align="left" width="02%">&nbsp;</td>
	<TD bgcolor="#EBFAD1" align="left" width="10%">
		<FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2> 
		<b>Name</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Openers<br>Range</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Clickers<br>Range</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Deliverables<br>Range</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Convert<br>Range</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Deliverables<br>Factor</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2><b>Range</b></font></td>
	<TD bgcolor="#EBFAD1" align="left" width="10%"><FONT face="verdana,arial,helvetica,sans serif" color="#509C10" size=2></font></td>
	</TR> 
end_of_html

	#===========================================================================
	# Loop - Get ALL Lists that belong to the User
	#===========================================================================
	$sql = "select profile_id,profile_name,opener_start,opener_end,clicker_start,clicker_end,deliverable_start,deliverable_end,deliverable_factor,opener_start_date,opener_end_date,clicker_start_date,clicker_end_date,deliverable_start_date,deliverable_end_date,start_record,end_record,opener_start1,opener_end1,clicker_start1,clicker_end1,deliverable_start1,deliverable_end1,opener_start2,opener_end2,clicker_start2,clicker_end2,deliverable_start2,deliverable_end2,convert_start,convert_end,convert_start_date,convert_end_date,convert_start1,convert_end1,convert_start2,convert_end2,ProfileForClient from UniqueProfile where status='A' and BusinessUnit='$BusinessUnit' order by profile_name";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	$reccnt = 0 ;
	while (($pid, $pname, $ostart,$oend,$cstart,$cend,$dstart,$dend,$dfactor,$ostart_date,$oend_date,$cstart_date,$cend_date,$dstart_date,$dend_date,$rstart,$rend,$ostart1,$oend1,$cstart1,$cend1,$dstart1,$dend1,$ostart2,$oend2,$cstart2,$cend2,$dstart2,$dend2,$convert_start,$convert_end,$convert_start_date,$convert_end_date,$convert_start1,$convert_end1,$convert_start2,$convert_end2,$ProfileForClient) = $sth->fetchrow_array())
	{
		$reccnt++;
		if ( ($reccnt % 2) == 0 ) 
		{
			$bgcolor = "#EBFAD1" ;     # Light Green
		}
		else 
		{
			$bgcolor = "$alt_light_table_bg" ;     # Light Yellow
		}
		if (($ostart_date ne '') and ($ostart_date ne '0000-00-00'))
		{
			$ostart=$ostart_date;
			if ($oend_date eq '0000-00-00')
			{
				$oend='';
			}
			else
			{
				$oend=$oend_date;
			}
			$ostart1=0;
			$ostart2=0;
			$oend1=0;
			$oend2=0;
		}
		if (($cstart_date ne '') and ($cstart_date ne '0000-00-00'))
		{
			$cstart=$cstart_date;
			if ($cend_date eq '0000-00-00')
			{
				$cend='';
			}
			else
			{
				$cend=$cend_date;
			}
			$cstart1=0;
			$cstart2=0;
			$cend1=0;
			$cend2=0;
		}
		if (($dstart_date ne '') and ($dstart_date ne '0000-00-00'))
		{
			$dstart=$dstart_date;
			if ($dend_date eq '0000-00-00')
			{
				$dend='';
			}
			else
			{
				$dend=$dend_date;
			}
			$dstart1=0;
			$dstart2=0;
			$dend1=0;
			$dend2=0;
		}
		if (($convert_start_date ne '') and ($convert_start_date ne '0000-00-00'))
		{
			$convert_start=$convert_start_date;
			if ($convert_end_date eq '0000-00-00')
			{
				$convert_end='';
			}
			else
			{
				$convert_end=$convert_end_date;
			}
			$convert_start1=0;
			$convert_start2=0;
			$convert_end1=0;
			$convert_end2=0;
		}

		my $ostr=$ostart." to ".$oend;;
		my $cstr=$cstart." to ".$cend;;
		my $dstr=$dstart." to ".$dend;;
		my $confirmstr=$convert_start." to ".$convert_end;;
		if ($ostart1 > 0)
		{
			$ostr=$ostr." or ".$ostart1." to ".$oend1;
		}
		if ($ostart2 > 0)
		{
			$ostr=$ostr." or ".$ostart2." to ".$oend2;
		}
		if ($cstart1 > 0)
		{
			$cstr=$cstr." or ".$cstart1." to ".$cend1;
		}
		if ($cstart2 > 0)
		{
			$cstr=$cstr." or ".$cstart2." to ".$cend2;
		}
		if ($dstart1 > 0)
		{
			$dstr=$dstr." or ".$dstart1." to ".$dend1;
		}
		if ($dstart2 > 0)
		{
			$dstr=$dstr." or ".$dstart2." to ".$dend2;
		}
		if ($convert_start1 > 0)
		{
			$confirmstr=$confirmstr." or ".$convert_start1." to ".$convert_end1;
		}
		if ($convert_start2 > 0)
		{
			$confirmstr=$confirmstr." or ".$convert_start2." to ".$convert_end2;
		}
		if ($ProfileForClient > 0)
		{
			my $uname;
			$sql="select username from user where user_id=?";
			my $sth1=$dbhu->prepare($sql);
			$sth1->execute($ProfileForClient);
			($uname)=$sth1->fetchrow_array();
			$sth1->finish();
			$pname.=" ($uname)";
		}
		print qq{<TR bgColor=$bgcolor> \n} ;
		print qq{	<TD align=left>&nbsp;</td> \n} ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"> \n } ;
		print qq{		<A HREF="uniqueprofile_edit.cgi?pid=$pid">$pname</a></font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$ostr</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$cstr</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$dstr</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$confirmstr</font></TD> \n } ;
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$dfactor</font></TD> \n } ;
		if (($rstart ne '') or ($rend ne ''))
		{
        	print qq{	<TD align=left><font color="#509C10" face="Arial" size="2">$rstart to $rend</font></TD> \n } ;
		}
		else
		{
			print "<td></td>";
		}
			
        print qq{	<TD align=left><font color="#509C10" face="Arial" size="2"><a href="uniqueprofile_copy.cgi?pid=$pid">Copy</a>&nbsp;&nbsp;<a href="uniqueprofile_del.cgi?pid=$pid">Delete</a></font></TD> \n } ;
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


