#################################################################
####   util.pm  - utility package for PMS					 ####
#################################################################

package util;

use strict;
use vars '$AUTOLOAD';
use CGI;
use LWP 5.64;
use LWP::UserAgent;
use MIME::Base64;
use lib "/usr/lib/perl5/site_perl/production";
use App::Mail::MtaRandomization;
use Lib::Database::Perl::Interface::Suppression;
use Lib::Database::Perl::Basic;
use Lib::Database::Perl::Interface::Administration;
use Lib::Database::Perl::Interface::Mailing::EmailEvent;

my $mtaRandom;
my $suppressionInterface;
my $interface;
# some routines for this package

sub updateUniqueEmailListUnsub
{
	my ($em) = @_;
		
    my $interface = _reportingDBInterface();
	
	my $params = {};
	
	## get current date
	my $date = `date +"%Y-%m-%d"`;
	
	push(@{$params->{'uniqueEmailListUpdates'}->{$date}}, $em);
	
    my $errors = $interface->updateLastUnsubscribeData($params);
}

sub _reportingDBInterface
{
	my $interface  = Lib::Database::Perl::Interface::Mailing::EmailEvent->new(
	(
    	'DATABASE' => 'Reporting',
    	'DATABASE_HOST' => 'reportingmasterdb.i.routename.com',
    	'DATABASE_USER' => 'db_user',
    	'DATABASE_PASSWORD' => 'sp1r3V'
	));

	return($interface);	
}

sub _suppressionDBInterface
{
	my $suppressionInterface = Lib::Database::Perl::Interface::Suppression->new(
	(
    	'MASTER_SUPPRESSION_DATABASE' => 'supp',
    	'MASTER_SUPPRESSION_DATABASE_HOST' => 'suppmasterdb.routename.com',
    	'MASTER_SUPPRESSION_DATABASE_USER' => 'db_user',
    	'MASTER_SUPPRESSION_DATABASE_PASSWORD' => 'sp1r3V'
	));

	return($suppressionInterface);
    
}

sub buildSuppressionReasonDetails
{
	my $suppressionInterface = _suppressionDBInterface();
	my $supressionDetails = $suppressionInterface->_suppressionReasonDetails();
	
	my $suppresionDetailRadios = '';
	
	foreach my $detail (sort keys %{$supressionDetails})
	{
		my $suppressionReasonCode = $supressionDetails->{$detail};
		my $checked = '';
		
		## set default checked
		if($detail eq 'Suppression via the mailing tool - DEPRECATED')
		{
			$checked = 'checked';
		}
		
		$suppresionDetailRadios .= qq|<input $checked type='radio' name='suppressionReasonCode' value="$suppressionReasonCode"> $detail <br />|;	
	}	
	
	return($suppresionDetailRadios);
}

sub addGlobal
{
	my ($params) = @_;
	
	my $suppressionInterface  = _suppressionDBInterface();
	
    my $errors = $suppressionInterface->insertSuppressionEmail( 
    { 
    	'listID' => 0, 
    	'emailAddress' => $params->{'emailAddress'}, 
    	'suppressionReasonCode' => $params->{'suppressionReasonCode'} 
    });
	my $suppressionQuery="insert ignore into suppress_list_orange(email_addr,suppressionReasonID) select '$params->{'emailAddress'}',suppressionReasonID from SuppressionReason where suppressionReasonCode='$params->{'suppressionReasonCode'}'";
	$suppressionInterface->databaseObject()->executeWrite($suppressionQuery);
    
    updateUniqueEmailListUnsub($params->{'emailAddress'});
 
}
sub addOrangeGlobal
{
	my ($params) = @_;
	
	my $suppressionInterface  = _suppressionDBInterface();
	
	my $suppressionQuery="insert ignore into suppress_list_orange(email_addr,suppressionReasonID) select '$params->{'emailAddress'}',suppressionReasonID from SuppressionReason where suppressionReasonCode='$params->{'suppressionReasonCode'}'";
	$suppressionInterface->databaseObject()->executeWrite($suppressionQuery);
 
}

sub addProadvertisers
{
	my ($em)=@_;
	return;
    if (!$suppressionInterface)
    {
		$suppressionInterface    = Lib::Database::Perl::Interface::Suppression->new(
		(
    	'MASTER_SUPPRESSION_DATABASE' => 'supp',
    	'MASTER_SUPPRESSION_DATABASE_HOST' => 'suppmasterdb.routename.com',
    	'MASTER_SUPPRESSION_DATABASE_USER' => 'db_user',
    	'MASTER_SUPPRESSION_DATABASE_PASSWORD' => 'sp1r3V'
		));
	}
    my $errors = $suppressionInterface->insertSuppressionEmail( { 'listID' => 1752, 'emailAddress' => $em } );
}

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

	$self->{'_db_user'} = "db_user";		# MySQL database username
	$self->{'_db_password'} = "sp1r3V";    	# MySQL database password
	$self->{'_db_database'} = "new_mail";		# MySQL database name
	$self->{'_spire_db_database'} = "spire_vision";		# MySQL database name
	$self->{'_db_ip'} = ":masterdb.i.routename.com";					# MySQL db ip address, blank for local machine
	$self->{'_db1_ip'} = "";					# MySQL db ip address, blank for local machine
	$self->{'_supp_db_ip'} = "";					# MySQL db ip address, blank for local machine
	$self->loadConfig();
}

sub loadConfig
{
	my ($self)=@_;
	my $config_file=$ENV{'CONFIG_FILE'} || '/var/www/util/data/config.dat';
	open(CONFIG,"<$config_file");
	while (<CONFIG>)
	{
		my ($key,$val)=split('\|',$_);
		$self->{$key}=$val;
	}
	close(CONFIG);
}

sub getConfigVal
{
	my ($self,$key)=@_;
	return($self->{$key}||0);
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

sub getUserData
{
	my ($this, $params) = @_;
	
	if(!defined($this->{'attributes'}->{'userData'}))
	{
		#$this->{'attributes'}->{'userData'};
		
		my ($errors, $results)	= $this->administrationInterface()->getUserAccounts({
			'user_id'	=> $params->{'userID'},
		});

		if(!@$errors && @$results)
		{
			$this->{'attributes'}->{'userData'} = $results->[0];
		}
		else
		{
			$this->{'attributes'}->{'userData'} = {
				'isExternal'	=> 1,  # Adding this as a default so that we are "safer" if errors occur or the user account does not exist.
			};
		}
	}
	
	return($this->{'attributes'}->{'userData'});
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

sub getPermissionsMapping
{
	my ($this) = @_;
	
	# The first two restrict things to our internal employees, who pretty much get superuser access...
	# The permissions after those are used to restrict things for outside users.
	
	$this->{'attributes'}->{'permissionsMapping'} ||= {
		'ipgroup_edit.cgi'			=> ['modifyAllMailingTool', 'viewAllMailingTool', 'mailingToolIpGroupModify'],
		'ipgroup_list.cgi'			=> ['modifyAllMailingTool', 'viewAllMailingTool', 'mailingToolIpGroupModify'],
		'ipgroup_add.cgi'			=> ['modifyAllMailingTool', 'viewAllMailingTool', 'mailingToolIpGroupModify'],
		'ipgroup_add_ips.cgi'		=> ['modifyAllMailingTool', 'viewAllMailingTool', 'mailingToolIpGroupModify'],
		'ipgroup_del.cgi'			=> ['modifyAllMailingTool', 'viewAllMailingTool', 'mailingToolIpGroupModify'],
		'ipgroup_del_multi.cgi'		=> ['modifyAllMailingTool', 'viewAllMailingTool', 'mailingToolIpGroupModify'],
		
		'login_form.cgi'			=> ['modifyAllMailingTool', 'viewAllMailingTool', 'loginMailingTool'],
		'login.cgi'					=> ['modifyAllMailingTool', 'viewAllMailingTool', 'loginMailingTool'],
		'mainmenu.cgi'				=> ['modifyAllMailingTool', 'viewAllMailingTool', 'loginMailingTool'],
		'list_category.cgi'			=> ['mailingToolListCategoryManagement'],

		'sm2_list.cgi'				=> ['mailingToolSM2'],
		'sm2_build_test_save.cgi'	=> ['mailingToolSM2'],
		'sm2_build_test.cgi'		=> ['mailingToolSM2'],
		'sm2_function.cgi'			=> ['mailingToolSM2'],
		'sm2_edit.cgi'				=> ['mailingToolSM2'],
		#'sm2_deploy_save.cgi'		=> ['mailingToolSM2'],
		#'sm2_deploy_main.cgi'		=> ['mailingToolSM2'],
		
		'sm2_send_all.cgi'			=> ['mailingToolSendAll'],
		'sm2_send_all_cancel.cgi'	=> ['mailingToolSendAll'],
		'sm2_send_all_delete.cgi'	=> ['mailingToolSendAll'],
		'sm2_send_all_disp.cgi'		=> ['mailingToolSendAll'],
		'sm2_send_all_list.cgi'		=> ['mailingToolSendAll'],
		'sm2_send_all_save.cgi'		=> ['mailingToolSendAll'],
		'sm2_build_send_all.cgi'	=> ['mailingToolSendAll'],

		'index.cgi'				=> ['mailingToolTemplatesModify'],
		'template_copy.cgi'		=> ['mailingToolTemplatesModify'],
		'template_delete.cgi'	=> ['mailingToolTemplatesModify'],
		'template_disp.cgi'		=> ['mailingToolTemplatesModify'],
		'upd_template.cgi'		=> ['mailingToolTemplatesModify'],
		'template_list.cgi'		=> ['mailingToolTemplatesModify'],
		
		'clientgroup_list.cgi'		=> ['mailingToolClientGroupModify'],
		'unique_main.cgi'			=> ['mailingToolUniqueDeployModify'],
		'unique_build.cgi'			=> ['mailingToolUniqueDeployModify'],
		'unique_list.cgi'			=> ['mailingToolUniqueDeployModify'],
		'unique_save.cgi'			=> ['mailingToolUniqueDeployModify'],
		'unique_deploy_list.cgi'	=> ['mailingToolUniqueDeployModify'],
		'unique_function.cgi'		=> ['mailingToolUniqueDeployModify'],
		'unique_resume.cgi'			=> ['mailingToolUniqueDeployModify'],
		'unique_chgtime.cgi'		=> ['mailingToolUniqueDeployModify'],
		
		'uniqueprofile_list.cgi'	=> ['mailingToolProfileModify'],
		
		
		'mta_list.cgi'				=> ['mailingToolMtaSettingsModify'],
		'view_advertiser_frame.cgi'				=> ['viewAdvertiserFrame'],
		'view_advertiser_top.cgi'				=> ['viewAdvertiserFrame'],
		'view_advertiser.cgi'				=> ['viewAdvertiserFrame'],
		'view_advertiser_save.cgi'				=> ['viewAdvertiserFrame'],

		'_default'				=> ['modifyAllMailingTool', 'viewAllMailingTool'], 
	};
	
	return($this->{'attributes'}->{'permissionsMapping'});
}

sub check_security
{
	my $this = util->new();
		
	my $status = $this->authenticateUser({});

#	my @rawCookies;
#	my %cookies;
#	my $key;
#	my $val;
#	my $login_ok;
#	
#	# see if user is currently logged in by checking for 
#	# a cookie.  cookies are seperated by a semicolon and a space, this will split
#	# them load them into a hash of cookies, then load cookies into key/value hash
#
#	@rawCookies = split (/; /,$ENV{'HTTP_COOKIE'});
#	foreach (@rawCookies)
#	{
#		($key, $val) = split (/=/,$_);
#		$cookies{$key} = $val;
#	} 
#
#	# look for util login cookie
#
#	if ($cookies{'utillogin'} ne "0")
#	{
#		# cookie is ok, the cookie is the current user_id
#		# return value is the user id if user is logged in
#
#		$login_ok = $cookies{'utillogin'};
#	}
#	else
#	{
#		# user not logged in
#		# set return value to indicate error
#
#		$login_ok = 0;
#	}
	
	return ($status);
}

sub check_external_security
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

	# look for external login cookie

	if ($cookies{'extadv'} ne "0")
	{
		# cookie is ok, the cookie is the current user_id
		# return value is the user id if user is logged in

		$login_ok = $cookies{'extadv'};
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

sub recordProcessingHeader
{
        my($title) = @_;
        my $ctitle;

        $ctitle="Record Processing";
        print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Validation</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#D1E4F0'>
<TBODY>
<TR bgcolor='#1596E7'>
<TD align='center'>
        <table border="0" cellpadding="0" cellspacing="0" width="800" align='center' border=0>
        <tr>
        <td colspan=3>
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                <td align='center'><b><font face="Arial" size="2" color='#FFFFFF'>&nbsp;$title</FONT></b></td>
                </tr>
                <tr>
                <td>
                </td>
                </tr>
                </table>
        </td>
        </tr>
        </table>
end_of_html

}


sub header 
{
	my($title) = @_;
	my $ctitle;

	$ctitle="Mailing Tool";
	print "Content-Type: text/html;charset-utf-8\n\n";
print << "end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$ctitle - Email Tool</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">
<TABLE cellSpacing=0 cellPadding=0 border=0 align='center' bgcolor='#FFFFFF'>
<TBODY>
<TR bgcolor='#FFFFFF'>
<TD align='center'>
	<table border="0" cellpadding="0" cellspacing="0" width="800" align='center' border=0>
	<tr>
	<TD width=248 bgcolor='#FFFFFF'>
		<img border="0" src="/mail-images/header.gif"></TD>
	<TD width=328 bgcolor='#FFFFFF'>&nbsp;</TD>
	</tr>
	<tr>
	<td colspan=3>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td align='center'><b><font face="Arial" size="2" color='#FFFFFF'>&nbsp;$title</FONT></b></td>
		</tr>
		<tr>
		<td>
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
	recordProcessingHeader($head_str);    # Print HTML Header
print << "end_of_html";
</TD>
</TR>
<TR bgcolor='#D1E4F0'>
<TD>

    <TABLE cellSpacing=0 cellPadding=10 border=0 align='center'>
    <TBODY>
    <TR>
    <TD vAlign=top align=left colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=460  border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#000000 
            size=3><B></B> </FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=3 src="/mail-images/spacer.gif"></TD>
		</TR>
		</TBODY>
		</TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=460 border=0>
        <TBODY>
        <TR>
        <TD colSpan=3 align=center><FONT face="verdana,arial,helvetica,sans serif" color=#000000 
            size=2>$msg</FONT></TD>
		</TR>
        <TR>
        <TD><IMG height=10 src="/mail-images/spacer.gif"></TD>
		</tr>
 		<tr><td colSpan=10 align=center></td>
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

sub record_processing_confirmation_page
{
        my ($head_str,$msg) = @_;
        recordProcessingHeader($head_str);    # Print HTML Header
print << "end_of_html";
</TD>
</TR>   
<TR bgcolor='#D1E4F0'>
<TD>

    <TABLE cellSpacing=0 cellPadding=10 border=0 align='center'>
    <TBODY>
    <TR>
    <TD vAlign=top align=left colSpan=10>

        <TABLE cellSpacing=0 cellPadding=0 width=460  border=0>
        <TBODY>
        <TR>
        <TD vAlign=center align=left><FONT face="verdana,arial,helvetica,sans serif" color=#000000
            size=3><B></B> </FONT></TD>
                </TR>
        <TR>
        <TD><IMG height=3 src="/mail-images/spacer.gif"></TD>
                </TR>
                </TBODY>
                </TABLE>

        <TABLE cellSpacing=0 cellPadding=0 width=460 border=0>
        <TBODY>
        <TR>
        <TD colSpan=3 align=center><FONT face="verdana,arial,helvetica,sans serif" color=#000000
            size=2>$msg</FONT></TD>
                </TR>
        <TR>
        <TD><IMG height=10 src="/mail-images/spacer.gif"></TD>
                </tr>
                <tr><td colSpan=10 align=center></td>
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

        $dbQuer||='masterdb.i.routename.com';
        $dbUpd||='masterdb.i.routename.com';
		my $sname=$ENV{'SERVER_NAME'};

        $dbhQ=DBI->connect("DBI:mysql:new_mail:$dbQuer", "db_user", "sp1r3V");
        if ($dbUpd ne $dbQuer) {
			$dbhU=DBI->connect("DBI:mysql:new_mail:$dbUpd", "db_user", "sp1r3V");
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

sub get_name
{
srand(rand time());
my @c1=split(/ */, "abcdefghijklmnopqrstuvwxyz");
my @c=split(/ */, "bcdfghjklmnprstvwxyz");
my @v=split(/ */, "aeiou");
my $sname;
my $i;
$sname = $c1[int(rand(26))];
$sname = $sname . $c1[int(rand(26))];
$sname = $sname . $c1[int(rand(26))];
$sname = $sname . $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
$sname = $sname . $v[int(rand(5))];
$sname = $sname . $c[int(rand(20))];
return $sname;
}

sub get_random
{
    if (!$mtaRandom)
    {
        $mtaRandom = App::Mail::MtaRandomization->new(('skipPrepopulateData'=> 1));
    }
    my $params = { 'minimum'   => 6, 'range'     => 9,'letters' =>0,'uppercase' => 0 };
    my $tstr = $mtaRandom->generateRandomString($params);
    $tstr=~tr/A-Z/a-z/;
	return($tstr);
}

sub get_gmail_url
{
	my ($ctype,$redir_domain,$eidfield,$link_id)=@_;
	my $tlink="http://$redir_domain/";
    if (!$mtaRandom)
    {
        $mtaRandom = App::Mail::MtaRandomization->new(('skipPrepopulateData'=> 1));
    }
    my $params = { 'minimum'   => 4, 'range'     => 0,'letters' =>0,'uppercase' => 0 };
    my $tstr = $mtaRandom->generateRandomString($params);
	$tstr=ucfirst($tstr);
	$tlink.=$tstr."/";
	my $redir_random_str=get_random();
	$tlink.=$redir_random_str."/".$eidfield."|";
	$redir_random_str=get_random();
	$tlink.=$redir_random_str."|".$link_id."|";
	if ($ctype eq "REDIRECT")
	{
    	$params = { 'minimum'   => 0, 'range'     => 999999 };
    	my $tnum= $mtaRandom->generateRandomNumber($params);
		$tlink.=$tnum;
	}
	else
	{
    	$params = { 'minimum'   => 4, 'range'     => 4,'letters' =>1,'uppercase' => 0 };
    	my $tnum= $mtaRandom->generateRandomString($params);
		$tlink.=$tnum;
	}	
	return($tlink);
}

sub checkLink
{
	my ($link)=@_;
	my $iret=0;

	my $ua = LWP::UserAgent->new();
	$ua->show_progress(1);
	open(STDERR,">/tmp/linkcheck_$$.txt");
	my $response=$ua->head($link);
	close(STDERR);
	open(IN,"</tmp/linkcheck_$$.txt");
	while (<IN>)
	{
		if ((/cktrk.net/) or (/ckrtk/))
		{
			$iret=1;
		}
	}
	close(IN);
	return $iret;
}

sub bld_img
{
	my ($prefix)=@_;
	my $upload_dir="/var/www/util/creative";
	my $temp_str;
	my $tdir;
	my $filename;
	my $t1;
	my $t2;
	my $t3;
	my $t4;

	if (!$mtaRandom)
	{
        $mtaRandom = App::Mail::MtaRandomization->new(('skipPrepopulateData'=> 1));
	}
	my $params = { 'minimum'   => 6, 'range'     => 9,'letters' =>0,'uppercase' => 0 };
	$prefix=~tr/A-Z/a-z/;
	if ($prefix eq "")
	{
		$prefix="jpg";
	}
	my $file_exists = 1;
	while ($file_exists == 1)
	{
		my $tstr = $mtaRandom->generateRandomString($params);
		$tstr=~tr/A-Z/a-z/;
#		$temp_str=$tstr.".".$prefix;
		$temp_str=$tstr;
    	$t1=substr($temp_str,0,1);
    	$t2=substr($temp_str,1,1);
    	$t3=substr($temp_str,2,1);
    	$t4=$upload_dir."/".$t1;
		$filename=$upload_dir."/".$t1."/".$t2."/".$t3."/".$temp_str.".".$prefix;
		if (-e $filename)
		{
		}
		else
		{
			# Check to see if on staging server
			my $url="http://staging.affiliateimages.com/".$temp_str;
			my $browser=LWP::UserAgent->new;
			my $response=$browser->get($url);
			if ($response->content_length > 43)
			{
			}
			else
			{
				$file_exists = 0;
			}
		}
	}
    mkdir $t4;
    $t4=$upload_dir."/".$t1."/".$t2;
    mkdir $t4;
    $t4=$upload_dir."/".$t1."/".$t2."/".$t3;
    mkdir $t4;
    $tdir=$t1."/".$t2."/".$t3;
	return($tdir,$temp_str);
}
sub isValidChars {
my ($str) = @_;
return 1;
#if ($str =~ /^[\w_\t\r\n\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x3A\x3B\x3C\x3D\x3E\x3F\x40\x5B\x5C\x5D\x5E\x5F\x7B\x7C\x7D\x7E]+$/) { return 1 }
#if ($str =~ /^[\w_\t\r\n\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x3A\x3B\x3C\x3D\x3E\x3F\x40\x5B\x5C\x5D\x5E\x5F\x7B\x7C\x7D\x7E\x85\x8A\x82\x88\x87\x89\x97\x8E\x90\x91\x92\x9D\x8B\x8C\x152\x153]+$/) { return 1 }
if ($str =~ /^[\w_\t\r\n\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x3A\x3B\x3C\x3D\x3E\x3F\x40\x5B\x5C\x5D\x5E\x5F\x7B\x7C\x7D\x7E\x85\x8A\x82\x88\x87\x89\x97\x8E\x90\x91\x92\x9D\x8B\x8C\x152\x153\xC1\xC9\xCD\xD3\xDA\xD1\xDC0\xE1\xE9\xED\xF3\xFA\xF1\xFC]+$/) { return 1 }
else { return 0 }
}
sub isValidFromChars {
my ($str) = @_;
return 1;
#if ($str =~ /^[\w_\t\r\n\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2D\x2E\x2F\x3C\x3D\x3E\x3F\x40\x5B\x5C\x5D\x5E\x5F\x7B\x7C\x7D\x7E]+$/) { return 1 }
if ($str =~ /^[\w_\t\r\n\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2D\x2E\x2F\x3B\x3C\x3D\x3E\x3F\x40\x5B\x5C\x5D\x5E\x5F\x7B\x7C\x7D\x7E\x85\x8A\x82\x88\x87\x89\x97\x8E\x90\x91\x92\x9D\x8B\x8C\x152\x153]+$/) { return 1 }
else { return 0 }
}
sub replaceBadChars
{
my ($str)=@_;
$str =~ tr/_0-9a-zA-Z\t\r\n\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x3A\x3B\x3C\x3D\x3E\x3F\x40\x5B\x5C\x5D\x5E\x5F\x7B\x7C\x7D\x7E\x85\x8A\x82\x88\x87\x89\x97\x8E\x90\x91\x92\x9D\x8B\x8C\x152\x153/X/c;
return $str;
}

sub buildUID
{
	my ($tid,$dbh)=@_;
	my $sql;
	my $insert_rec;
	my $sname;

	$insert_rec=0;
	$sql="delete from IOlist where date_added < date_sub(curdate(),interval 3 day)";
	my $rows=$dbh->do($sql);
	$sql="select uid from IOlist where ID=? and date_added >= date_sub(curdate(),interval 1 day)";
	my $sth=$dbh->prepare($sql);
	$sth->execute($tid);
	($sname)=$sth->fetchrow_array();
	$sth->finish();
	if ($sname eq "")
	{
    	srand(rand time());
    	my @c=split(/ */, "bcdfghjklmnprstvwxyz");
    	my @v=split(/ */, "aeiou");
    	$sname = $c[int(rand(20))];
    	$sname = $sname . int(rand(9999));
    	$sname = $sname . $v[int(rand(5))];
    	$sname = $sname . $c[int(rand(20))];
    	$sname = $sname . $v[int(rand(5))];
    	$sname = $sname . $c[int(rand(20))];
    	$sname = $sname . int(rand(999999));
		$insert_rec=1;
	}
    my $encodedValue   = encode_base64($sname, '');
    $encodedValue   =~ s/=//go;
	my $enctid=encode_base64($tid,'');
    $enctid =~ s/=//go;
	my $uid=$encodedValue."|".$enctid;

	if ($insert_rec)
	{
		$sql="insert into IOlist(ID,uid,date_added) values($tid,'$sname',curdate())";
		$rows=$dbh->do($sql);
	}
	return $uid;
}
sub validateUID
{
	my ($dbh,$aid,$uid)=@_;
	$_=$aid;
	if (/\D/)
	{
		BadPage();
	}
	#
	# Check uid for advertiser
	#
	my $temp_id="";
	if ($uid ne "")
	{
		my $sql = "select advertiser_id from approval_list where uid='$uid' and advertiser_id=$aid";
		my $sth = $dbh->prepare($sql);
		$sth->execute();
		($temp_id) = $sth->fetchrow_array();
		$sth->finish;
	}
	if ($temp_id eq "")
	{
		BadPage();
	}
	return($temp_id);
}
sub BadPage
{
	print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<center><h3>Bad Request Detected</h3></center>
</body>
</html>
end_of_html
	exit();
}

sub CheckTokens
{
	my ($tstr)=@_;
	my @var=("{{NAME}}","{{LOC}}","{{EMAIL_USER_ID}}","{{EMAIL_ADDR}}","{{URL}}","{{IMG_DOMAIN}}","{{DOMAIN}}","{{CID}}","{{FID}}","{{CRID}}","{{FOOTER_SUBDOMAIN}}","{{FOOTER_DOMAIN}}","{{FROMADDR}}","{{MAILDATE}}","{{FOOTER_TEXT}}","{{DATE}}","{{UNSUB_URL}}","{{CLIENT_BRAND}}","{{LINK_ID}}","{{ADVERTISER_NAME}}","{{SUBJECT}}","{{NID}}","{{BINDING}}","{{TID}}","{{HEADER}}","{{FOOTER}}","{{S}}","{{F}}","{{MID}}","{{CWC3}}","{{CWPROGID}}","{{ADV_UNSUB}}","{{ADV_UNSUB_URL}}","{{WORDS}}","{{YEAR}}","{{MONTH}}","{{DAY}}","{{CONFIRM}}","{{SCHOOLPROGRAM1}}","{{STERLINGUNIQUEKEY}}","{{PROGRAM}}","{{EDUCATIONLEVEL}}","{{ETHNICITY}}","{{GRADYEAR}}","{{INTERESTS}}","{{SCHOOL}}","{{SUBAFF}}","{{ADDRESS}}","{{ADDRESS2}}","{{CITY}}","{{STATE}}","{{GENDER}}","{{PHONE}}","{{BIRTH_DATE}}","{{URL1}}","{{URL2}}","{{URL3}}","{{URL4}}","{{FNAME}}","{{LNAME}}","{{FULLNAME}}","{{SUBSCRIBEIP}}","{{ZIP}}","{{URL5}}","{{URL6}}","{{URL7}}","{{URL8}}","{{URL9}}","{{URL10}}","{{SourceUrl}}","{{MSGID}}","{{RETURN_PATH}}","{{URL11}}","{{URL12}}","{{URL13}}","{{URL14}}","{{URL15}}","{{URL16}}","{{URL17}}","{{URL18}}","{{URL19}}","{{SUBSCRIBEDATE}}","{{ENC_EMAIL_USER_ID}}","{{ENC_CRID}}","{{Y_ADDADDR}}","{{DISCLAIMER}}","{{URL20}}","{{URL21}}","{{URL22}}","{{URL23}}","{{URL24}}","{{URL25}}","{{URL26}}","{{URL27}}","{{URL28}}","{{URL29}}");

	if ($tstr =~ /\{\{DATE\+(\d+)\}\}/go)
    {
    	return 1;
    }
	my $i=0;
    while ($i <= $#var) 
    {
    	if ($tstr eq $var[$i])
        {
			return 1;
        }
        $i++;
    }
    $_=$tstr;
    if (/RANDOM_STRING/)
    {
		return 1;
    }
    elsif (/RANDOM_WORD/)
    {
   		return 1; 
    }
	return 0;
}

sub getAspireURL
{
	my ($this) = @_;
	
	my $aspireurl = $ENV{'ASPIREMAIL_URL'} || "http://aspiremail.com/";
	
	return($aspireurl);
}

sub authenticateUser
{
	my ($this, $params) = @_;
	
	my $status = 0;
	
	$this->administrationInterface();
	
	# This updates the username and password values in both $params and $this.
	my $userID = $this->getUserInformation($params);

	if( $userID )
	{
		if( $this->authorizeUser($params) )
		{		
			## we need to return userID for later display purposes
  			$status = $userID;
		}
	}
	
	return($status);
}

sub userIsAuthorized
{
	my ($this, $permissionsList)	= @_;

	my $isAuthorized	= 0;
		
	my ($errors, $results)	= $this->{'attributes'}->{'administrationInterface'}->getUserPermissions({
		#'username'	=> $params->{'username'},
		'username'	=> $this->{'attributes'}->{'username'},
	});
	
	if($this->{'attributes'}->{'administrationInterface'}->hasErrors())
	{
		## TODO: Do something with
		## $administrationInterface->getErrors()
		$isAuthorized = 0;
	}
	else
	{
		if($results)
		{
			while
			(
				(! $isAuthorized) 
				&&
				(my $permissionSet = shift(@$results))
			)
			{
				# Would have liked to create a hash for something like this, but I'd be shocked to find that anyone ever
				# tests for more than two or three permissions, so creating a hash first would require more operations than just checking like this.
				
				if($permissionSet->{'adminPermissionEnabled'})
				{
					foreach my $permission (@$permissionsList)
					{
						if($permissionSet->{'adminPermissionLabel'} eq $permission)						
						{
							$isAuthorized = 1;	
						}
					}
				}
			}
		}
	}

	return($isAuthorized);	
}

sub authorizeUser
{
	my ($this, $params)	= @_;

	my $authorizedUser	= 0;

	
	my $permissionsMapping = $this->getPermissionsMapping();
	
	# $authorizedUser = $this->userIsAuthorized($params, ['modifyAllMailingTool', 'viewAllMailingTool']);
	# $authorizedUser = $this->userIsAuthorized(['modifyAllMailingTool', 'viewAllMailingTool']);

	# This is terrible, awful, dirty, etc, but it seems to make no sense to attempt to change it until we replace the entire
	# mailing tool with something better.

	my ($scriptName) = ($0 =~ /([^\/]+)$/);

	if($permissionsMapping->{$scriptName})
	{
		$authorizedUser = $this->userIsAuthorized($permissionsMapping->{$scriptName});
	}
	else
	{
		$authorizedUser = $this->userIsAuthorized($permissionsMapping->{'_default'});
	}
	
	return($authorizedUser);
}


sub getUserInformation
{
	my ($this, $params)	= @_;

	my $username = $params->{'username'} || $this->{'attributes'}->{'username'};
	my $password = $params->{'password'} || $this->{'attributes'}->{'password'};
	
	$this->{'attributes'}->{'username'} = $username;
	$this->{'attributes'}->{'password'} = $password;
	
	my $userID = 0;
	
	## Get user info from DB
	if(
		$username =~ /\w+/
		&&
		$password =~ /\w+/
	)
	{
		my ($errors, $results)	= $this->{'attributes'}->{'administrationInterface'}->getUserAccounts({
			'username'	=> $username,
			'password'	=> $password
		});
	
		if($this->{'attributes'}->{'administrationInterface'}->hasErrors())
		{
			## TODO: Do something with
			## $administrationInterface->getErrors()
		}

		$userID	= $results->[0]->{'user_id'};
		my $username = $results->[0]->{'username'};
		
		## login is OK, set the cookie
  		my $cookie = "utillogin=$username; path=/;";
  		print "Set-Cookie: $cookie\n";
  		
  		my $cookie1 = "userID=$userID; path=/;";
  		print "Set-Cookie: $cookie1\n";
	}
	
	## get user info from cookie
	else
	{
		$this->_getCookieInformation($params);
		$userID = $params->{'userID'};
	}
	
	return($userID);
}

sub _getCookieInformation
{
	my ($this, $params)	= @_;

	my @rawCookies = split (/; /,$ENV{'HTTP_COOKIE'});
	my $cookies = {};
		
	foreach (@rawCookies)
	{
		my ($key, $val) = split (/=/,$_);
		$cookies->{$key} = $val;
	} 

	# look for util login cookie
	if ($cookies->{'utillogin'} ne '')
	{
		$params->{'username'} = $cookies->{'utillogin'};
		$params->{'userID'} = $cookies->{'userID'};

		$this->{'attributes'}->{'username'} = $cookies->{'utillogin'};
		$this->{'attributes'}->{'userID'} = $cookies->{'userID'};
	}	
}

sub administrationInterface
{
	my ($this)	= @_;

	my $dbClass		= 'Lib::Database::Perl::Interface::Administration';
	
	my $databaseParameters	= {
          'DATABASE_HOST'		=> $this->{'_db_ip'},
          'DATABASE_USER'		=> $this->{'_db_user'},
          'DATABASE' 			=> $this->{'_db_database'},
          'DATABASE_PASSWORD'	=> $this->{'_db_password'},
	};
	
	$databaseParameters->{'DATABASE_HOST'}	=~ s/[^\w\.]//g;
	
	$this->{'attributes'}->{'administrationInterface'}	= Lib::Database::Perl::Interface::Administration->new(%$databaseParameters);
}

sub genLinks
{
	my ($this,$dbh,$advID,$clientID,$displayData)=@_;
	my $sql;
	my $sth;
	my $hitpath_id;
	my $url;
	my $sth2;
	my $mid;
	my $client_id;
	my $sth1;
	my $link_num;
	my $cakeID;
	my $cakeSubID;
	my $ccID;
	my $mid1;

	$sql = "select hitpath_id,cakeAffiliateID,cakeSubAffiliateID from user where user_id=1";
	$sth=$dbh->prepare($sql);
	$sth->execute();
	($hitpath_id,$cakeID,$cakeSubID) = $sth->fetchrow_array();
	$sth->finish();

	# Generate URLs for client
	#
	if ($clientID > 0)
	{
		if ($advID > 0)
		{
			# Generate links for single advertiser/client
			$sql="select url,link_num,cake_creativeID from advertiser_tracking at,advertiser_info ai where at.client_id=1 and at.advertiser_id=? and daily_deal='N' and at.advertiser_id=ai.advertiser_id";
			$sth=$dbh->prepare($sql);
			$sth->execute($advID);
			while (($url,$link_num,$ccID) = $sth->fetchrow_array())
			{
				# Remove old URLS
				$sql = "delete from advertiser_tracking where client_id =$clientID  and advertiser_id=$advID and daily_deal='N' and link_num=$link_num";
				$dbh->do($sql);
				#
				if ($ccID > 0)
				{
					$sql = "select cakeAffiliateID,cakeSubAffiliateID,user_id from user where cakeAffiliateID > 0 and cakeSubAffiliateID != '' and cakeSubAffiliateID > 0 and user_id = $clientID and status='A'";
				}
				else
				{
					$sql = "select hitpath_id,0,user_id from user where hitpath_id!= '' and user_id = $clientID and status='A'";
				}
				$sth2=$dbh->prepare($sql);
				$sth2->execute();
				while (($mid,$mid1,$client_id) = $sth2->fetchrow_array())
				{
					my $temp_url = $url;
					if ($ccID > 0)
					{
						$temp_url =~ s/a=$cakeID/a=$mid/;
						$temp_url =~ s/s1=$cakeSubID/s1=$mid1/;
					}
					else
					{
						$temp_url =~ s/$hitpath_id/$mid/;
					}
					my $lid;
					if ($displayData)
					{
						print "<br>$advID - $temp_url\n";
					}
					$sql="select max(link_id) from links where refurl=?";
					$sth1=$dbh->prepare($sql);
					$sth1->execute($temp_url);
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
					if ($lid > 0)
					{
					}
					else
					{
						$sql="insert ignore into links(refurl,date_added) values('$temp_url',now())";
						$dbh->do($sql);
						$sql="select LAST_INSERT_ID()"; 
						$sth1=$dbh->prepare($sql);
						$sth1->execute();
						($lid) = $sth1->fetchrow_array();
						$sth1->finish();
					}
					#
					# Insert record into advertiser_tracking
					#
					$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($advID,'$temp_url','$mid',curdate(),$client_id,$lid,'N',$link_num)";
					$dbh->do($sql);
				}
				$sth2->finish();
	   			$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=$advID) where advertiser_id=$advID";
				$dbh->do($sql);
			}
			$sth->finish();
		}
		else
		{
			$sql="select at.advertiser_id,url,link_num,cake_creativeID from advertiser_tracking at,advertiser_info ai where at.client_id=1 and daily_deal='N' and at.advertiser_id=ai.advertiser_id and ai.status='A'";
			$sth=$dbh->prepare($sql);
			$sth->execute();
			while (($advID,$url,$link_num,$ccID) = $sth->fetchrow_array())
			{
				# Remove old URLS
				$sql = "delete from advertiser_tracking where client_id = $clientID and advertiser_id=$advID and daily_deal='N' and link_num=$link_num";
				$dbh->do($sql);
				#
				if ($ccID > 0)
				{
					$sql = "select cakeAffiliateID,cakeSubAffiliateID,user_id from user where cakeAffiliateID > 0 and cakeSubAffiliateID != '' and cakeSubAffiliateID > 0 and user_id = $clientID  and status='A'";
				}
				else
				{
					$sql = "select hitpath_id,0,user_id from user where hitpath_id!= '' and user_id = $clientID  and status='A'";
				}
				$sth2=$dbh->prepare($sql);
				$sth2->execute();
				while (($mid,$mid1,$client_id) = $sth2->fetchrow_array())
				{
					my $temp_url = $url;
					if ($ccID > 0)
					{
						$temp_url =~ s/a=$cakeID/a=$mid/;
						$temp_url =~ s/s1=$cakeSubID/s1=$mid1/;
					}
					else
					{
						$temp_url =~ s/$hitpath_id/$mid/;
					}
					if ($displayData)
					{
						print "<br>$advID - $temp_url\n";
					}
					my $lid;
					$sql="select max(link_id) from links where refurl=?";
					$sth1=$dbh->prepare($sql);
					$sth1->execute($temp_url);
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
					if ($lid > 0)
					{
					}
					else
					{
						$sql="insert ignore into links(refurl,date_added) values('$temp_url',now())";
						$dbh->do($sql);
						$sql="select LAST_INSERT_ID()"; 
						$sth1=$dbh->prepare($sql);
						($lid) = $sth1->fetchrow_array();
						$sth1->finish();
					}
					#
					# Insert record into advertiser_tracking
					#
					$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($advID,'$temp_url','$mid',curdate(),$client_id,$lid,'N',$link_num)";
					$dbh->do($sql);
				}
				$sth2->finish();
	   			$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=$advID) where advertiser_id=$advID";
				$dbh->do($sql);
			}
			$sth->finish();
		}
	}
	#
	# Generate URLs for advertiser
	#
	else
	{
		$sql="select url,link_num,cake_creativeID from advertiser_tracking at,advertiser_info ai where at.client_id=1 and at.advertiser_id=? and daily_deal='N' and at.advertiser_id=ai.advertiser_id";
		$sth=$dbh->prepare($sql);
		$sth->execute($advID);
		while (($url,$link_num,$ccID) = $sth->fetchrow_array())
		{
			# Remove old URLS
			$sql = "delete from advertiser_tracking where client_id > 1 and advertiser_id=$advID and daily_deal='N' and link_num=$link_num";
			$dbh->do($sql);
			#
			if ($ccID > 0)
			{
				$sql = "select cakeAffiliateID,cakeSubAffiliateID,user_id from user where cakeAffiliateID > 0 and cakeSubAffiliateID != '' and cakeSubAffiliateID > 0 and user_id > 1 and status='A'";
			}
			else
			{
				$sql = "select hitpath_id,0,user_id from user where hitpath_id!= '' and user_id > 1 and status='A'";
			}
			$sth2=$dbh->prepare($sql);
			$sth2->execute();
			while (($mid,$mid1,$client_id) = $sth2->fetchrow_array())
			{
				my $temp_url = $url;
				if ($ccID > 0)
				{
					$temp_url =~ s/a=$cakeID/a=$mid/;
					$temp_url =~ s/s1=$cakeSubID/s1=$mid1/;
				}
				else
				{
					$temp_url =~ s/$hitpath_id/$mid/;
				}
				my $lid;
				$sql="select max(link_id) from links where refurl=?";
				$sth1=$dbh->prepare($sql);
				$sth1->execute($temp_url);
				($lid) = $sth1->fetchrow_array();
				$sth1->finish();
				if ($lid > 0)
				{
				}
				else
				{
					$sql="insert ignore into links(refurl,date_added) values('$temp_url',now())";
					$dbh->do($sql);
					$sql="select LAST_INSERT_ID()"; 
					$sth1=$dbh->prepare($sql);
					($lid) = $sth1->fetchrow_array();
					$sth1->finish();
				}
				#
				# Insert record into advertiser_tracking
				#
				$sql="insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,daily_deal,link_num) values($advID,'$temp_url','$mid',curdate(),$client_id,$lid,'N',$link_num)";
				$dbh->do($sql);
			}
			$sth2->finish();
   			$sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=$advID) where advertiser_id=$advID";
			$dbh->do($sql);
		}
		$sth->finish();
	}
}
sub genImage
{
	my ($name)=@_;
    if (!$mtaRandom)
    {
        $mtaRandom = App::Mail::MtaRandomization->new(('skipPrepopulateData'=> 1));
    }
    my @EXT=(".png",".bmp",".gif",".jpg");
    my @CHARS=("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    my $new_name;

    my $range=$#EXT-1;
    my $ind=int(rand($range));
    $range=$#CHARS-1;
    my $cind=int(rand($range));
    my $params = { 'minimum'   => 4, 'range'     => 8,'letters' =>0,'uppercase' => 0 };
    my $random_string= $mtaRandom->generateRandomString($params);
    $random_string=~tr/A-Z/a-z/;
    my $random_string1= $mtaRandom->generateRandomString($params);
    $random_string1=~tr/A-Z/a-z/;
    $new_name=$random_string.$CHARS[$cind].$name.$CHARS[$cind].$random_string1.$EXT[$ind];
    return $new_name;
}
1;
