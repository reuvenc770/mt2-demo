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
		add_edit_body($args,$dbhu);
	}
    elsif ($args->{type} eq 'show') {
        my $q=qq|SELECT id AS styleID, style_name AS name, date_add, inactive_date, style_content |
			 .qq|FROM style_content WHERE id='$args->{styleID}'|;
        my $sth=$dbhu->prepare($q);
        $sth->execute;
        my $hr=$sth->fetchrow_hashref;
        $sth->finish;
		display_body();
		display_form($hr);
		display_footer();
    }	
	elsif ($args->{type} eq 'del') {
		delete_body($args,$dbhu);
	}
	elsif ($args->{type} eq 'preview') {
		preview_body($args,$dbhu);
	}
	else {
		display_body();
		display_form($args);
		display_footer();
	}
}
$util->clean_up();
exit;


sub add_edit_body {
	my $args=shift;
	my $dbh=shift;

	if ($args->{name} && $args->{style_content}) {
		my $name=$dbh->quote($args->{name});
		my $content=$dbh->quote($args->{style_content});
		my $quer;
		if ($args->{edit}) {
			$quer=qq|UPDATE style_content SET style_name=$name, style_content=$content, date_add='$args->{date_add}'|
				 .qq|, inactive_date='$args->{inactive_date}' WHERE id='$args->{styleID}'|;
		}
		else {
			$quer=qq|INSERT INTO style_content (style_name,date_add,style_content) VALUES ($name,'$args->{date_add}',$content)|;
		}
		my $rv=$dbh->do($quer);
		if ($rv) {
			my $msg=$args->{edit} ? "Style Content edited" : "New Style Content Added";
			display_confirmation('/newcgi-bin/style_content_list.cgi','2',$msg);
		}
		else {
		}
	}
	else {
		my $msg="<font color=red>Missing Style Content Name or Content.</font>";
		display_body();
		display_form($args,$msg);
		display_footer();
	}
}

sub delete_body {
	my ($args,$dbh)=@_;

	if ($args->{styleID}) {
		my $quer=qq|DELETE FROM style_content WHERE id='$args->{styleID}'|;
		my $rv=$dbh->do($quer);
		if ($rv) {
			display_confirmation('/newcgi-bin/style_content_list.cgi','2',"Style Content has been deleted!");
		}
		else {
			display_confirmation('/newcgi-bin/style_content_list.cgi','2',"Problem delete the selected Style Content, try again later!");
		}
	}
}

sub display_body {

	print "Content-type: text/html\n\n";
	print qq^
	<html>
	  <head>
		<meta http-equiv="Content-Language" content="en-us">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>Style Content</title>
	  </head>
	  <body>
	^;
}

sub display_form {
	my $args=shift;
	my $msg=shift;

	my $error=$msg ? $msg : "";
	my $hidden=$args->{type} eq 'add' ? qq^<input type=hidden name="new" value="1">^ : qq^<input type=hidden name="edit" value="1">^;
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
						Style Content</font></b></td>
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
rif" color="#509c10" size="3"><b>Style Content</b> </font></td>
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
				<form method="post" action="style_content.cgi">
					$hidden
					<input type=hidden name='styleID' value='$args->{styleID}'>
					<b>Content Name:</b><br>
					<input maxLength="255" size="50" value="$args->{name}" name="name"><br>
					<br>
					<b>Date of Content: (YYYY/MM/DD - Default = today) </b><br>
					<input size="10" value="$args->{date_add}" name="date_add"><br>
					<br>
					<b>Inactive Date: (YYYY/MM/DD) </b><br>
					<input size="10" name="inactive_date" value="$args->{inactive_date}"><br>
					<br>
					<b><u>HTML Code:</u><br>
					<font color=red size=3 face='arial'>You need to Start: &lt;STYLE&gt;, End: &lt;/STYLE&gt; around your text</font><br>
					<textarea name="style_content" rows="15" cols="100">$args->{style_content}</textarea> 
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
		  <title>Style Content Tool</title>
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

sub preview_body {
	my ($args,$dbh)=@_;

	my $quer=qq|SELECT style_content FROM style_content WHERE id='$args->{styleID}'|;
	my $sth=$dbh->prepare($quer);
	$sth->execute;
	my $content=$sth->fetchrow;
	$sth->finish;

	if ($content) {
		print "Content-type: text/html\n\n";
		print qq^<html><head><title>Style Content Preview</title></head><body>^;
		print "$content\n";
		print qq^</body></html>^;
	}
}
