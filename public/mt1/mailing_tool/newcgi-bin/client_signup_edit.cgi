#!/usr/bin/perl

# *****************************************************************************************
# client_signup_edit.cgi
#
# this page is to allow a client to edit their signup form
#
# History
# Grady Nash, 8/22/01, Creation
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
my $errmsg;
my $user_id;
my $list_id;
my $list_name;
my $clist_id;
my ($show_first_name, $show_last_name, $show_address, $show_city, $show_state,
    $show_zip, $show_country, $show_phone, $show_gender, $show_marital_status,
    $show_occupation, $show_income, $show_education, $show_job_status);
my %checkit = ( 'Y' => 'CHECKED', 'N' => '' );
my $images = $util->get_images_url;
my $count;
my $rows;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

$user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# find out if this user has a user_signup_form record already

$sql = "select count(*) from user_signup_form where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($count) = $sth->fetchrow_array();
$sth->finish();

if ($count == 0)
{
	# add a default record
    $sql = "insert into user_signup_form (user_id, show_first_name, show_last_name, show_zip)
        values ($user_id, 'Y', 'Y', 'Y')";
    $rows = $dbhu->do($sql);
}

# read info for this clients signup form

$sql = "select list_id, show_first_name, show_last_name, show_address, show_city, show_state,
    show_zip, show_country, show_phone, show_gender, show_marital_status,
    show_occupation, show_income, show_education, show_job_status
	from user_signup_form where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($clist_id, $show_first_name, $show_last_name, $show_address, $show_city, $show_state,
    $show_zip, $show_country, $show_phone, $show_gender, $show_marital_status,
    $show_occupation, $show_income, $show_education, $show_job_status) = $sth->fetchrow_array();
$sth->finish();

# print out the html page

util::header("EDIT SIGNUP FORM");

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
		<TD><IMG height=3 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			Pick the fields you want to capture on your signup form and click Save to 
			see a sample. The Signup form will always include email address, email type,
			and date of birth.</FONT><br></TD>
		</TR>
		<TR>
		<TD><IMG height=5 src="$images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

		<FORM action="client_signup_save.cgi" method="post" name="signupform">
		<input type="hidden" name="nextfunc">

		<TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
		<TBODY>
		<TR>
		<TD vAlign=top>

			<!-- Begin main body area -->

			<TABLE cellSpacing=0 cellPadding=0 width=100% bgColor=#E3FAD1 border=0>
			<TBODY>
			<TR bgColor=#509C10>
			<TD vAlign=top align=left height=15><IMG src="$images/blue_tl.gif" 
				border=0 width="7" height="7"></TD>
			<TD align=middle height=15><FONT face="verdana,arial,helvetica,sans serif" 
				color=#ffffff size=2><B>Signup Form</B></FONT></TD>
			<TD vAlign=top align=right bgColor=#509C10 height=15><IMG 
				src="$images/blue_tr.gif" border=0 width="7" height="7"></TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>
			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Show First Name
				<input type="checkbox" name="show_first_name" $checkit{$show_first_name}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show Last Name 
				<input type="checkbox" name="show_last_name" $checkit{$show_last_name}>
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp;<FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Show Address <input type="checkbox" name="show_address" $checkit{$show_address}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show State <input type="checkbox" name="show_state" $checkit{$show_state}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show City <input type="checkbox" name="show_city" $checkit{$show_city}>
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Show Zip
				<input type="checkbox" name="show_zip" $checkit{$show_zip}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show Country
				<input type="checkbox" name="show_country" $checkit{$show_country}>
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Show Phone
				<input type="checkbox" name="show_phone" $checkit{$show_phone}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show Gender
				<input type="checkbox" name="show_gender" $checkit{$show_gender}>
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Show Marital Status
				<input type="checkbox" name="show_marital_status" $checkit{$show_marital_status}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show Occupation
				<input type="checkbox" name="show_occupation" $checkit{$show_occupation}>
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Show Income
				<input type="checkbox" name="show_income" $checkit{$show_income}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show Education
				<input type="checkbox" name="show_education" $checkit{$show_education}>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Show Job Status
				<input type="checkbox" name="show_job_status" $checkit{$show_job_status}>
				</TD>
			</TR>
			<TR>
			<TD colSpan=3>&nbsp;</TD>
			</TR>

			<TR>
			<TD colSpan=3 align=center>&nbsp;&nbsp; <FONT face="verdana,arial,helvetica,sans serif" 
				color=#509C10 size=2><B>
				Signup For Which List
				<select name="list_id">
end_of_html

# read the lists for this user

$sth = $dbhq->prepare("select list_id, list_name from list 
	where user_id = $user_id and status='A' order by list_name") ;
$sth->execute();
while ( ($list_id, $list_name) = $sth->fetchrow_array() )
{
	if ($list_id == $clist_id)
	{
		print "<option value=$list_id selected>$list_name</option>";
	}
	else
	{
		print "<option value=$list_id>$list_name</option>";
	}
}
$sth->finish();

print << "end_of_html";
				</select>
				</TD>
			</TR>
			<TR>
			<TD colspan=3><img src="$images/spacer.gif" height=20></td>
			</tr>
			<TR>
			<TD colspan=3>

				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TBODY>
				<TR>
				<TD align=center width="50%">
					<a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border=0></a></TD>
				<TD align=center width="50%"> 
					<INPUT TYPE=IMAGE src="$images/save_rev.gif" border=0></TD>
				</TR>
				</TBODY>
				</TABLE>

			</TD>
			</TR>
			<TR>
			<TD vAlign=bottom align=left colSpan=2><IMG height=7 src="$images/lt_purp_bl.gif" 
				width=7 border=0></TD>
			<TD vAlign=bottom align=right><IMG height=7 src="$images/lt_purp_br.gif" 
				width=7 border=0></TD>
			</TR>
			</TBODY>
			</TABLE>

			<!-- End main body area -->

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

$util->footer();

# exit function

$util->clean_up();
exit(0);
