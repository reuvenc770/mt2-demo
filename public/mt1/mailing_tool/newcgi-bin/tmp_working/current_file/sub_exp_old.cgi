#!/usr/bin/perl
#===============================================================================
# Purpose: Create export file with email addresses.  Handles csv files and plain text
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

my $util = util->new;
my $query = CGI->new;
my ($select_list, $file_type ) ;
my ($sql, $sth, $dbh ) ;
my ($list_id, $list_name, $email_addr, $email_type, $subscribe_datetime);
my ($list_id_criteria, $status_criteria );
my ($trbeg, $trend, $c1beg, $c2beg, $c3beg, $c4beg, $cend, $c2end);
my ($bbeg, $bend, $br) ;

#-------------------------------------------------------
# Get CGI Form Fields
#-------------------------------------------------------
$select_list = $query->param('select_list') ;
if ( $query->param('BtnExportCSV.x') ne "" ) 
{
	$file_type = 'CSV' ;
	$trbeg = qq{<TR>};
	$trend = qq{</TR>};
	$c1beg  = qq{<TD width=200 align=center>};
	$c2beg  = qq{<TD width=200 align=center>};
	$c3beg  = qq{<TD width=200 align=center>};
	$c4beg  = qq{<TD width=200 align=center>};
	$cend   = qq{</TD>};
	$c2end  = qq{</TD>};
	$bbeg   = qq{<b>};
	$bend   = qq{</b>};
	$br     = qq{<br>};
}
else
{
	$file_type = 'TXT' ;
	$trbeg = "";
	$trend = "";
	$c1beg  = "";
	$c2beg  = "";
	$c3beg  = "";
	$c4beg  = "";
	$cend   = ", ";
	$c2end  = "";
	$bbeg   = "";
	$bend   = "";
	$br     = "";
}

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

$sql = "select list.list_id, list.list_name, 
   email_user.email_addr, email_user.email_type, list_member.subscribe_datetime
   from   list, list_member, email_user
   where  list.user_id  =  $user_id 
   and    list.list_id  =  list_member.list_id and
	list_member.email_user_id = email_user.email_user_id 
    $list_id_criteria 
    $status_criteria 
   and list.status = 'A' 
   order by list.list_name";
$sth = $dbh->prepare($sql) ;
$sth->execute();

#-------------------------------------------------------------------------------
# Fetch 'list' and 'list_member' til no more recs
# Note: Do a CarriageReturn (eg Octal 015 ) and LineFeed (eg \n) to get proper
#   line feeds for 'NotePad'.
#-------------------------------------------------------------------------------

if ( $file_type eq "CSV" ) 
{
	print "Content-Type: application/vnd.ms-excel\n\n";
	print "<html><body><br> \n" ;
}
else
{
	print "Content-Type: text/plain\n\n";
}

if ( $file_type eq "CSV" )
{
	print qq{<TABLE border=1><TBODY>\n};
}

print "${trbeg}${c1beg}${bbeg}LIST_NAME${bend}${cend}${c2beg}${bbeg}EMAIL_ADDR${bend}${cend}${c3beg}${bbeg}EMAIL_TYPE${bend}${cend}${c4beg}${bbeg}SUBSCRIBE_DATETIME${bend}${c2end}${trend}\015${br}\n" ;


while ( ($list_id, $list_name, $email_addr, $email_type, 
	$subscribe_datetime) = $sth->fetchrow_array() )
{
	print qq{${trbeg}${c1beg}${list_name}${cend}${c2beg}${email_addr}${cend}${c3beg}${email_type}${cend}${c4beg}${subscribe_datetime}${c2end}${trend} \015${br}\n} ;
}
$sth->finish();

if ( $file_type eq 'CSV' )
{
	print qq{</TBODY></TABLE>\n};
	print qq{</body></html>} ;
}

$util->clean_up();
exit(0);
