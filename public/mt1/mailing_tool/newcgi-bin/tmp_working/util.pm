#################################################################
####   util.pm  - utility package for PMS					 ####
#################################################################

package util;

use strict;
use vars '$AUTOLOAD';
use CGI;

# some routines for this package

sub date
{
	my ( $time, $format )= @_;
	my $retstr;
	my ( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst )= localtime( time );

	$sec = "0$sec" if ($sec < 10);
	$min = "0$min" if ($min < 10);
	$hour = "0$hour" if ($hour < 10);
	$mon = "0$mon" if ($mon < 10);
	$mday = "0$mday" if ($mday < 10);
	$year = $year + 1900;
	my $month = ($mon + 1);
	my @months = ( "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                   "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" );
	my @weekday =("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	my @weekday1 =("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");

	if ($format == 0)
	{
		# format for Screen header display
		$retstr = "$months[$mon] $mday, $year at $hour\:$min\:$sec";
	}
	elsif ($format == 1)
	{
		# cookie expiration date format
    	$retstr = "$weekday[$wday], $mday-$months[$mon]-$year $hour\:$min\:$sec GMT";
	}
	elsif ($format == 2)
	{
		# mysql date format
		$mon = $mon + 1;
    	$retstr = "$year-$mon-$mday $hour\:$min\:$sec";
	}
	elsif ($format == 3)
	{
		# date in YYYYMMDD
		$mon = $mon + 1;
    	$retstr = "$year$mon$mday";
	}
	elsif ($format == 4)
	{
		# date in MM/DD/YYYY
		$mon = $mon + 1;
    	$retstr = "$mon/$mday/$year";
	}
	elsif ($format == 5)
	{
		# date in YYYYMMDDHHMISS 
		$mon = $mon + 1;
    	$retstr = "$year$mon$mday$hour$min$sec";
	}
	elsif ($format == 6)
	{
		# date in Day, dd Mon YEAR hh:mm:ss -offset 
    	$retstr = "$weekday1[$wday], $mday $months[$mon] $year $hour\:$min\:$sec -0400";
	}

	return $retstr;
}

sub initialize 
{
	my $self = shift;

	# location for all images

	$self->{'_images_url'} = "/mail-images";

	# screen colors

	$self->{'_light_table_bg'} 	= "#E3FAD1";
	$self->{'_alt_light_table_bg'} 	= "#D6C6FF";
	$self->{'_table_header_bg'} 	= "#509C10";
	$self->{'_table_text_color'} 	= "#509C10";

	# database connection parameters

	$self->{'_db_user'} = "mailer";		# MySQL database username
	$self->{'_db_password'} = "9wEBdEfY";    	# MySQL database password
	$self->{'_db_database'} = "new_mail";		# MySQL database name
	$self->{'_spire_db_database'} = "spire_vision";		# MySQL database name
	$self->{'_db_ip'} = "";					# MySQL db ip address, blank for local machine
	$self->{'_db1_ip'} = "";					# MySQL db ip address, blank for local machine
	$self->{'_supp_db_ip'} = "";					# MySQL db ip address, blank for local machine
}

sub AUTOLOAD
{
	my ($self) = @_;
	$AUTOLOAD =~ /.*::get(_\w+)/;
	exists $self->{$1};
	return $self->{$1}	# return attribute
}

sub new 
{
	my $this = shift;
	my $class = ref($this) || $this;
	my $self = {};
	bless $self, $class;
	$self->initialize();
	return $self;
}

sub db_connect
{
	my $self = shift;
	use DBI;
	my $db_database = $self->{'_db_database'};
	my $db_ip = $self->{'_db_ip'};
	my $db_user = $self->{'_db_user'};
	my $db_password = $self->{'_db_password'};
	my $dbh = DBI->connect("DBI:mysql:$db_database$db_ip",$db_user,$db_password);
	$self->{'_dbh'} = $dbh;
	return $self;
}
sub db_connect1
{
	my $self = shift;
	use DBI;
	my $db_database = $self->{'_db_database'};
	my $db_ip = $self->{'_db1_ip'};
	my $db_user = $self->{'_db_user'};
	my $db_password = $self->{'_db_password'};
	my $dbh = DBI->connect("DBI:mysql:$db_database$db_ip",$db_user,$db_password);
	$self->{'_dbh1'} = $dbh;
	return $self;
}
sub supp_db_connect
{
	my $self = shift;
	use DBI;
	my $db_database = $self->{'_db_database'};
	my $db_ip = $self->{'_supp_db_ip'};
	my $db_user = $self->{'_db_user'};
	my $db_password = $self->{'_db_password'};
	my $dbh = DBI->connect("DBI:mysql:$db_database$db_ip",$db_user,$db_password);
	$self->{'_supp_dbh'} = $dbh;
	return $self;
}
sub spire_db_connect
{
	my $self = shift;
	use DBI;
	my $db_database = $self->{'_spire_db_database'};
	my $db_ip = $self->{'_db_ip'};
	my $db_user = $self->{'_db_user'};
	my $db_password = $self->{'_db_password'};
	my $dbh = DBI->connect("DBI:mysql:$db_database$db_ip",$db_user,$db_password);
	$self->{'_spire_dbh'} = $dbh;
	return $self;
}

sub clean_up
{
#	my $self = shift;
#	$self->{'_dbh'}->disconnect;
}

sub check_security
{
	my @rawCookies;
	my %cookies;
	my $key;
	my $val;
	my $login_ok;

	# see if user is currently logged in by checking for 
	# a cookie.  cookies are seperated by a semicolon and a space, this will split
	# them load them into a hash of cookies, then load cookies into key/value hash

	@rawCookies = split (/; /,$ENV{'HTTP_COOKIE'});
	foreach (@rawCookies)
	{
		($key, $val) = split (/=/,$_);
		$cookies{$key} = $val;
	} 

	# look for util login cookie

	if ($cookies{'utillogin'} ne "0")
	{
		# cookie is ok, the cookie is the current user_id
		# return value is the user id if user is logged in

		$login_ok = $cookies{'utillogin'};
	}
	else
	{
		# user not logged in
		# set return value to indicate error

		$login_ok = 0;
	}
	return ($login_ok);
}

sub logerror
{
	my $message = shift;

    header("Error Message");
    print "<font color=red>$message</font><br><br><br>\n";
    footer();
}

sub message
{
	my $message = shift;

	print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Message</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>
	<table border="0" cellpadding="0" cellspacing="0" width="719">
	<tr>
	<TD width=248 bgColor=#FFFFFF rowSpan=2>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgColor=#FFFFFF>&nbsp;</TD>
	</tr>
	<tr>
	<td width="468">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td><b><font face="Verdana,Arial" size="2">System Message</FONT></b></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td>
    <font face="Verdana,Arial" size="2" color="#509C10">$message</font>
	<br><br><br>
end_of_html

    footer();

}

sub header 
{
	my($title) = @_;
	my $ctitle;

	$ctitle="Mailing System";
	print "Content-Type: text/html\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$ctitle EMail System</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 align=left bgColor=#ffffff border=0>
<TBODY>
<TR vAlign=top>
<TD noWrap align=left>
	<table border="0" cellpadding="0" cellspacing="0" width="800">
	<tr>
	<TD width=248 bgColor=#FFFFFF rowSpan=2>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgColor=#FFFFFF>&nbsp;</TD>
	</tr>
	<tr>
	<td width="468">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left"><b><font face="Arial" size="2">&nbsp;$title</FONT></b></td>
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
end_of_html

}

sub footer
{
	print qq {
		<br><p align="center">
		<img border="0" src="/mail-images/footer.gif"></p></TD>
		</TR>
		</TABLE>
		</body>
		</html> \n };
}

sub confirmation_page
{
	my ($head_str,$msg) = @_;
	header($head_str);    # Print HTML Header
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
            size=3><B>Confirmation</B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="/mail-images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=660 bgColor=#ffffff border=0>
        <TBODY>
        <TR>
        <TD colSpan=10><FONT face="verdana,arial,helvetica,sans serif" color=#509C10 
            size=2>$msg</FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=10 src="/mail-images/spacer.gif"></TD>
		</tr>
 		<tr><td colSpan=10 align=center><a href="mainmenu.cgi"><font 
			face="verdana,arial,helvetica,sans serif" color=#509C10 size=3>Back To Main 
			Screen</font></a></td>
		</tr>
        <TR>
        <TD><IMG height=10 src="/mail-images/spacer.gif"></TD>
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
	footer();
}

sub errmail ()
{
	shift;
	my ($dbh,$program,$errmsg,$sql) = @_;
	my $sql;
	my $sth;
	my $from_addr;
	my $to_addr;
	my $subject = "Error from $program";
	my $cdate = localtime();

	# lookup some parameters from the sysparm table

	$sql = "select parmval from sysparm where parmkey = 'ERR_MAIL_ADDR'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($to_addr) = $sth->fetchrow_array();
	$sth->finish();

	$sql = "select parmval from sysparm where parmkey = 'SYSTEM_MGR_ADDR'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($from_addr) = $sth->fetchrow_array();
	$sth->finish();

	# send the error message in an email

	open (MAIL,"| /usr/lib/sendmail -t");
	print MAIL "Reply-To: $from_addr\n";
	print MAIL "From: $from_addr\n";
	print MAIL "To: $to_addr\n";
	print MAIL "Subject: $subject\n";
	print MAIL "Content-Type: text/plain\n\n";
	print MAIL "There was an error in $program at $cdate\n";
	print MAIL "errmsg: $errmsg\n";
	print MAIL "sql: $sql\n\n";
	close MAIL;
}

sub get_dbh 
{
        my ($dbQuer, $dbUpd);
        my ($dbhQ, $dbhU);

        open (DB, "/tmp/db_handle");
        while (<DB>) {
                chomp $_;
                ($dbQuer, $dbUpd)=split/\|/, $_;
        }
        close DB;

        $dbQuer||='localhost';
        $dbUpd||='localhost';

        $dbhQ=DBI->connect("DBI:mysql:new_mail:$dbQuer", "leaddog", "mymail");
        if ($dbUpd ne $dbQuer) {
			$dbhU=DBI->connect("DBI:mysql:new_mail:$dbUpd", "leaddog", "mymail");
        }
        else {
                $dbhU=$dbhQ;
        }
		if (!$dbhQ || !$dbhU)
		{
			print "Exit - cant connect to one of the servers <$dbhQ> <$dbhU>\n";
			exit(0);
		}
        return ($dbhQ, $dbhU);
}
1;
