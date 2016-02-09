#!/usr/bin/perl
#===============================================================================
# Purpose: Export list_member recs to CSV or TXT (eg text) file.
# File   : sub_exp.cgi
#
#--Change Control---------------------------------------------------------------
#  Aug 2, 2001  Mike Baker  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;

$|=1 ;   # set OUTPUT_AUTOFLUSH to true
############################################
my (@ary_chkbox_fields) ;
my (@ary_query);
my (@ary_fldpos_fields, $str_fldpos_field, @ary_fldpos_nonblank_fields) ;
my ($i);
my ($mesg, $go_back, $go_home, $go_url);
my (@fl_db_name, $fl_pos, $ary_len );
my ($tmp_str,$reccnt,@sql_results);

#---- Vars used for insert of tbl: user_file_layout ------------
my ($email_addr_pos, $email_type_pos,     $gender_pos);
my ($first_name_pos, $middle_name_pos,    $last_name_pos);
my ($birth_date_pos,  $address_pos,        $address2_pos);
my ($city_pos,       $state_pos,          $zip_pos);
my ($country_pos,    $marital_status_pos, $occupation_pos);
my ($job_status_pos, $income_pos,         $education_pos);
############################################
my $util = util->new;
my $query = CGI->new;
my ($select_list, $file_suffix, $file_type);
my ($sql, $sth, $dbh ) ;
my $upload_dir_unix;
my $upload_dir_http;
my $images = $util->get_images_url;
my ($list_id, $list_name, $email_addr, $email_type, $subscribe_datetime);
my ($outfile, $redirect_url);
my ($list_id_criteria, $status_criteria );
my $filename;
my $last_email;
my $sdate;
my $edate;

# ----- connect to the util database -------

$util->db_connect();
$dbh = $util->get_dbh;

# ----- check for login -------

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get upload subdir

$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_UNIX'";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($upload_dir_unix) = $sth->fetchrow_array();
$sth->finish();

$sql = "select parmval from sysparm where parmkey = 'UPLOAD_DIR_HTTP'";
$sth = $dbh->prepare($sql) ;
$sth->execute();
($upload_dir_http) = $sth->fetchrow_array();
$sth->finish();

#-------------------------------------------------------
# Get CGI Form Fields
#-------------------------------------------------------
$sdate = $query->param('sdate');
if ($sdate eq "")
{
	$sdate = "01-01-2002";
}
$edate = $query->param('edate');
if ($edate eq "")
{
	$edate = "12-31-2999";
}

$select_list = $query->param('select_list') ;
if ( $query->param('file_format') eq "csv" ) 
{
	$file_suffix = '.csv';
	$file_type = "<b>Comma Separated Value (CSV) file</b>";
}
else
{
	$file_suffix = '.txt';
	$file_type = "<b>Text File</b>";
}

#---------------------
# Open the OUTFILE
#---------------------

if ($select_list eq "ALL")
{
	$filename = "all_members";
}
elsif ($select_list eq "OPT-OUT") 
{
	$filename = "all_opt_outs";
}
else
{
	$sql = "select list_name from list where list_id = $select_list";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($list_name) = $sth->fetchrow_array();
	$sth->finish();
	$filename = $list_name;
	$filename =~ s/ /_/g;			# replace any space's with underscores
	$filename =~ s/'//g;			# remove any quotes
	$filename =~ s/\#//g;			# remove any #'s
}

$outfile = $upload_dir_unix . "/" . $filename . $file_suffix;
unless (open(OUTFILE, ">$outfile"))
{
	util::logerror("There was an error opening the Output File: $outfile");
	$util->clean_up();
	exit(0);
}

#----- Set user_file_layout fields to Null ----------------
	$email_addr_pos = "null";
	$email_type_pos = "null";
	$gender_pos = "null";
	$first_name_pos = "null";
	$middle_name_pos = "null";
	$last_name_pos = "null";
	$birth_date_pos = "null";
	$address_pos = "null";
	$address2_pos = "null";
	$city_pos = "null";
	$state_pos = "null";
	$zip_pos = "null";
	$country_pos = "null";
	$marital_status_pos = "null";
	$occupation_pos = "null";
	$job_status_pos = "null";
	$income_pos = "null";
	$education_pos = "null";

#------ Get Values from Array of CHKBOX fields (eg db_field|cat_id) ---------
@ary_chkbox_fields = $query->param('chkbox') ;

#------ Put values ne "" on the New Array for processing (eg skip fields = "") -----
@ary_fldpos_fields = $query->param('fldpos') ;
foreach $str_fldpos_field (@ary_fldpos_fields) 
{
 	if ( $str_fldpos_field ne "" )
 	{
 		push @ary_fldpos_nonblank_fields, $str_fldpos_field ;
	}
}

for($i = 1; $i <= 18; $i++)
{
    $ary_query[$i] = "";
}
$ary_len = (@ary_chkbox_fields);
$ary_len = $ary_len - 1;  
for($i = 0; $i <= 17; $i++)
{
	if ($ary_chkbox_fields[$i] eq "email_addr_pos")     
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "email_addr"; }
	if ($ary_chkbox_fields[$i] eq "email_type_pos")     
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "email_type"; }
	if ($ary_chkbox_fields[$i] eq "gender_pos")         
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "gender"; }
	if ($ary_chkbox_fields[$i] eq "first_name_pos")     
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "first_name"; }
	if ($ary_chkbox_fields[$i] eq "middle_name_pos")    
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "middle_name"; }
	if ($ary_chkbox_fields[$i] eq "last_name_pos")      
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "last_name"; }
	if ($ary_chkbox_fields[$i] eq "birth_date_pos")     
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "birth_date"; }
	if ($ary_chkbox_fields[$i] eq "address_pos")        
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "address"; }
	if ($ary_chkbox_fields[$i] eq "address2_pos")      
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "address2"; }
	if ($ary_chkbox_fields[$i] eq "city_pos")         
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "city"; }
	if ($ary_chkbox_fields[$i] eq "state_pos")       
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "state"; }
	if ($ary_chkbox_fields[$i] eq "zip_pos")        
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "zip"; }
	if ($ary_chkbox_fields[$i] eq "country_pos")   
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "country"; }
	if ($ary_chkbox_fields[$i] eq "marital_status_pos")
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "marital_status"; }
	if ($ary_chkbox_fields[$i] eq "occupation_pos")   
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "occupation"; }
	if ($ary_chkbox_fields[$i] eq "job_status_pos")  
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "job_status"; }
	if ($ary_chkbox_fields[$i] eq "income_pos")     
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "income"; }
	if ($ary_chkbox_fields[$i] eq "education_pos") 
	{ $ary_query[$ary_fldpos_nonblank_fields[$i]] = "education"; }
}
#print OUTFILE "<LIST_NAME>, <EMAIL_ADDR>, <EMAIL_TYPE>, <SUBSCRIBE_DATETIME>\n";
if ( $select_list eq "OPT-OUT" ) 
{
	print OUTFILE "<UNSUBSCRIBE_DATETIME>|";
}
else
{
	print OUTFILE "<LIST_NAME>|<SUBSCRIBE_DATETIME>|";
}
for($i = 1; $i <= 18; $i++)
{
    if ($ary_query[$i] ne "")
    {
	$tmp_str = $ary_query[$i];
	$tmp_str =~ tr/a-z/A-Z/;
	if ($i != 1)
	{
		print OUTFILE "|<".$tmp_str.">";
	}
	else
	{
		print OUTFILE "<".$tmp_str.">";
	}
    }
}
print OUTFILE "\n";

#------------------------------------------
# Get Active lists and list_members recs 
#------------------------------------------
if ( ( $select_list eq "ALL" ) || ( $select_list eq "OPT-OUT" ) ) 
{
	$list_id_criteria = "" ;  # Null field -> No Limits - get ALL Lists
}
else
{
	$list_id_criteria = "and list.list_id = $select_list " 
}

if ( $select_list eq "OPT-OUT" ) 
{
	$status_criteria = "and list_member.status != 'A' " ;  # Get Not Active Members (eg OptOuts)
}
else
{
	$status_criteria = "and list_member.status = 'A' " ;  # Get Active Members (eg OptIns)
}

$reccnt = 2;

if ( $select_list eq "OPT-OUT" ) 
{
	$sql = "select list.list_id, list.list_name,list_member.unsubscribe_datetime";
}
else
{
	$sql = "select list.list_id, list.list_name,list_member.subscribe_datetime";
}
for($i = 1; $i <= 18; $i++)
{
    if ($ary_query[$i] ne "")
    {
	$sql = $sql . ",email_user." . $ary_query[$i];
	$reccnt++;
    }
}

   $sql = $sql . " from   list, list_member, email_user";
   if ($user_id == 1)
   {
		if ( $select_list eq "OPT-OUT" ) 
		{
   			$sql = $sql . " where  list.list_id != 25 and list.list_id  =  list_member.list_id and list_member.email_user_id = email_user.email_user_id $list_id_criteria $status_criteria and list.status = 'A' and unsubscribe_datetime >= '$sdate' and unsubscribe_datetime < '$edate' order by email_user.email_addr,list_member.unsubscribe_datetime,list.list_name";
		}
		else
		{
   			$sql = $sql . " where  list.list_id != 25 and list.list_id  =  list_member.list_id and list_member.email_user_id = email_user.email_user_id $list_id_criteria $status_criteria and list.status = 'A' and subscribe_datetime >= '$sdate' and subscribe_datetime < '$edate' order by email_user.email_addr,list_member.subscribe_datetime,list.list_name";
		}
   }
   else
   {
   	$sql = $sql . " where  list.user_id  =  $user_id and    list.list_id  =  list_member.list_id and list_member.email_user_id = email_user.email_user_id $list_id_criteria $status_criteria and subscribe_datetime >= '$sdate' and subscribe_datetime < '$edate' and list.status = 'A' order by email_user.email_addr,list_member.subscribe_datetime,list.list_name";
   }
$sth = $dbh->prepare($sql) ;
$sth->execute();
$last_email = "";
while ((@sql_results) = $sth->fetchrow_array())
{
	$sql_results[3] =~ tr/A-Z/a-z/;
	if ($last_email ne $sql_results[3])
	{
		if ( $select_list eq "OPT-OUT" ) 
		{
			print OUTFILE "$sql_results[2]|";
		}
		else
		{
			print OUTFILE "$sql_results[1]|$sql_results[2]|";
		}
	for ($i = 3; $i <= $reccnt; $i++)
	{
		if ($i != 3)
		{
			print OUTFILE "|$sql_results[$i]";
		}
		else
		{
			print OUTFILE "$sql_results[$i]";
		}
	}
	print OUTFILE "\n";
	}
	$last_email = $sql_results[3];
}
$sth->finish();
close OUTFILE;

# show the user the link to download the file to their pc

$redirect_url = $upload_dir_http . $filename . $file_suffix;

util::header("EXPORT SUBSCRIBERS CONFIRMATION");
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
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#509C10
            size=3><B>Export Subscribers</B> </FONT></TD>
        </TR>
        <TR>
        <TD><IMG height=20 src="$images/spacer.gif"></TD>
        </TR>
        <TR>
        <TD align=center>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			<A HREF="$redirect_url">Click this to download the $file_type</a></font></td>
        </TR>
        <TR>
        <TD><IMG height=10 src="$images/spacer.gif"></TD>
        </tr>
        <TR>
        <TD align=center>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			The Filename is $filename$file_suffix</font></td>
        </TR>
        <TR>
        <TD><IMG height=20 src="$images/spacer.gif"></TD>
        </tr>
        <TR>
        <TD align=center>
			<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
			To save a copy of the file to your local hard drive, place the mouse cursor
			over the link, click the right mouse button, and select "Save Target As...".
			Pick a directory on your local hard drive where you want the file to be saved 
			and click OK.  You can enter a new name for the file if you like, but keep
			the same extension (.txt, .csv, etc.).  In Netscape Navigator select 
			"Save Link As..."</font></td>
        </TR>
        <TR>
        <TD><IMG height=10 src="$images/spacer.gif"></TD>
        </tr>
        <tr>
		<td align=center>
			<a href="mainmenu.cgi"><img src="$images/home_blkline.gif" border="0"></a></td>
        </tr>
        <TR>
        <TD><IMG height=10 src="$images/spacer.gif"></TD>
        </tr>
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

util::footer();

$util->clean_up();
exit(0);
