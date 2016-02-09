#!/usr/bin/perl

use strict;
use CGI;
use util;
require "/var/www/html/newcgi-bin/modules/Common.pm";

#------  get some objects to use later ---------
my $util = util->new;
my ($dbhq,$dbhu)=$util->get_dbh();

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0) {
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
else {
	my $args=Common::get_args();
	$args=init($args,$dbhu);

	if ($args->{new} || $args->{edit}) {
		add_edit_header($args,$dbhu);
	}
    elsif ($args->{type} eq 'show') {
        my $q=qq|SELECT id AS headID, header_name AS name, date_add, inactive_date, header_content,modified_date |
			 .qq|FROM header_content WHERE id='$args->{headID}'|;
        my $sth=$dbhu->prepare($q);
        $sth->execute;
        my $hr=$sth->fetchrow_hashref;
        $sth->finish;
		display_header();
		display_form($hr);
		display_footer();
    }	
	elsif ($args->{type} eq 'del') {
		delete_header($args,$dbhu);
	}
	elsif ($args->{type} eq 'preview') {
		preview_header($args,$dbhu);
	}
	else {
		display_header();
		display_form($args);
		display_footer();
	}
}
$util->clean_up();
exit;


sub add_edit_header {
	my $args=shift;
	my $dbh=shift;

	if ($args->{name} && $args->{header_content}) {
		my $name=$dbh->quote($args->{name});
		my $content=$dbh->quote($args->{header_content});
		my $quer;
		if ($args->{edit}) {
			$quer=qq|UPDATE header_content SET header_name=$name, header_content=$content, date_add='$args->{date_add}'|
				 .qq|, inactive_date='$args->{inactive_date}', modified_date=NOW() WHERE id='$args->{headID}'|;
		}
		else {
			$quer=qq|INSERT INTO header_content (header_name,date_add,modified_date,header_content) VALUES ($name,'$args->{date_add}',NOW(),$content)|;
		}
		my $rv=$dbh->do($quer);
		if ($rv) {
			my $msg=$args->{edit} ? "Header edited" : "New Header Added";
			display_confirmation('/newcgi-bin/header_content_list.cgi','2',$msg);
		}
		else {
		}
	}
	else {
		my $msg="<font color=red>Missing Header Name or Content.</font>";
		display_header();
		display_form($args,$msg);
		display_footer();
	}
}

sub delete_header {
	my ($args,$dbh)=@_;

	if ($args->{headID}) {
		my $quer=qq|DELETE FROM header_content WHERE id='$args->{headID}'|;
		my $rv=$dbh->do($quer);
		if ($rv) {
			display_confirmation('/newcgi-bin/header_content_list.cgi','2',"Header has been deleted!");
		}
		else {
			display_confirmation('/newcgi-bin/header_content_list.cgi','2',"Problem delete the selected Header, try again later!");
		}
	}
}

sub display_header {

	print "Content-type: text/html\n\n";
	print qq^
	<html>
	  <head>
		<meta http-equiv="Content-Language" content="en-us">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Header Content</title>
	  </head>
	  <body>
	^;
}

sub display_form {
	my $args=shift;
	my $msg=shift;

	my $error=$msg ? $msg : "";
	my $hidden=$args->{type} eq 'add' ? qq^<input type=hidden name="new" value="1">^ : qq^<input type=hidden name="edit" value="1">^;
	my $modified_date=$args->{headID} ? qq^<b>Modified Date: </b>$args->{modified_date}<br><br>^ : "";
	print qq^
	<table cellSpacing="0" cellPadding="0" align="left" bgColor="#ffffff" border="0" id="table2">
	<tr vAlign="top">
		<td noWrap align="left">
		<table cellSpacing="0" cellPadding="0" width="800" border="0" id="table3">
			<tr>
				<td width="248" bgColor="#ffffff" rowSpan="2">&nbsp;</td>
				<td width="328" bgColor="#ffffff">&nbsp;</td>
			</tr>
			<tr>
				<td width="468">
				<table cellSpacing="0" cellPadding="0" width="100%" border="0" id="table4">
					<tr>
						<td align="left"><b><font face="Arial" size="2">&nbsp;Edit 
						Header Content</font></b></td>
					</tr>
					<tr>
						<td align="right"><b>
						<a style="text-decoration: none" href="http://69.45.78.226:83/cgi-bin/logout.cgi">
						<font face="Arial" color="#509c10" size="2">Logout</font></a>&nbsp;&nbsp;&nbsp;
						<a style="text-decoration: none" href="http://69.45.78.226:83/cgi-bin/wss_support_form.cgi">
						<font face="Arial" color="#509c10" size="2">Customer 
						Assistance</font></a></b> 
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<table cellSpacing="0" cellPadding="10" width="100%" bgColor="#ffffff" border="0" id="table5">
			<tr>
				<td vAlign="top" align="left" bgColor="#ffffff" colSpan="10">
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table6">
					<tr>
						<td vAlign="center" align="left">
						<font face="verdana,arial,helvetica,sans se
rif" color="#509c10" size="3"><b>Header Content</b> </font></td>
					</tr>
					<tr>
						<td>$error<img height="3" src="/images/spacer.gif"></td>
					</tr>
				</table>
				<table cellSpacing="0" cellPadding="0" width="660" bgColor="#ffffff" border="0" id="table7">
					<tr>
						<td colSpan="10">
						&nbsp;</td>
					</tr>
				</table>
				<form method="post" action="header_content.cgi">
					$hidden
					<input type=hidden name='headID' value='$args->{headID}'>
					<b>Content Name:</b><br>
					<input maxLength="255" size="50" value="$args->{name}" name="name"><br>
					<br>
					<b>Date of Content: (YYYY/MM/DD - Default = today) </b><br>
					<input size="10" value="$args->{date_add}" name="date_add"><br>
					<br>
					$modified_date
					<b>Inactive Date: (YYYY/MM/DD) </b><br>
					<input size="10" name="inactive_date" value="$args->{inactive_date}"><br>
					<br>
					<b><u>HTML Code:</u><br>
					<textarea name="header_content" rows="15" cols="100">$args->{header_content}</textarea> 
					<table id="table8" cellPadding="5" width="66%" bgColor="white">
						<tr>
							<td align="middle" width="47%">
							<a href="/cgi-bin/mainmenu.cgi">
							<img height="22" src="/images/home_blkline.gif" width="81" border="0"></a></td>
							<td align="middle" width="47%">
							<input type=image name="save" height="22" src="/images/save_rev.gif" width="81" border="0"></td>
						</tr>
					</table>
				</form>
			    </td>
			</tr>
		</table>
		</td>
	  </tr>
	</table>
	^;
}

sub display_footer {

	print qq^
	  </body>
	</html>
	^;
}

sub init {
	my $args=shift;
	my $dbh=shift;

	if ($args->{type} eq 'add') {
		my $q=qq|SELECT CURDATE()|;
		my $sth=$dbh->prepare($q);
		$sth->execute;
		$args->{date_add}=$sth->fetchrow;
		$sth->finish;
	}
	return $args;
}

sub display_confirmation {

    my ($url, $time, $msg)=@_;
    $time||=0; $msg||='';
    print "Content-type: text/html\n\n";
    print qq^<html>
		<head>
		  <title>Header Content Tool</title>
			<meta http-equiv="refresh" content="$time;URL=$url">
		</head>
		<body>
		<table width='600' align='center'>
    	  <tr>
            <td align='center' class='title'>$msg<br>You will be redirected in a few moments</td>
    	  </tr>
		</table>
		</body>
	</html>^;
}

sub preview_header {
	my ($args,$dbh)=@_;

	my $quer=qq|SELECT header_content FROM header_content WHERE id='$args->{headID}'|;
	my $sth=$dbh->prepare($quer);
	$sth->execute;
	my $content=$sth->fetchrow;
	$sth->finish;

	if ($content) {
		print "Content-type: text/html\n\n";
		print qq^<html><head><title>Header Preview</title></head><body>^;
		print "$content\n";
		print qq^</body></html>^;
	}
}
