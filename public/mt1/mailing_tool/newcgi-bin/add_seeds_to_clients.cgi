#!/usr/bin/perl -w

###########################################
# This tool checks if entered email is valid (looks like smth@smth.smth), if it's not in db yet, 
# links it to List Owner or Advertiser depending on the type (internal/external), adds it to 
# appropriate RT24 file (if internal - to the same file where we have our smth.client@spirevision 
# seeds; if external - to a file for specified email domain), and reports any errors if they occurred. 
###########################################

use strict;
use DBI;
use CGI;
use FileHandle;
use Sys::Hostname;
use Fcntl qw (:flock);

use Data::Dumper;

use vars qw($DBH @COLS $LINKS);

use util;

## TODO: take care of ALL selection for both internal and external

my $util = util->new;

my ($dbhq,$DBH);

{
	my $thisHost = hostname();
	if ($thisHost eq 'edanilova01.routename.com')
	{
#		use Common;
#		use DB;			
		$DBH=DBI->connect("DBI:mysql:$ENV{'DATABASE'}:$ENV{'DATABASE_HOST'}", $ENV{'DATABASE_USER'}, $ENV{'DATABASE_PASSWORD'} );		
	}
	else
	{
		require "/usr/local/share/modules/Common.pm";
		require "/usr/local/share/modules/DB.pm";
		($dbhq,$DBH)=$util->get_dbh();		
	}
}

my $args=Common::get_args();

## init
if (!$args->{rt}) {
    $args->{rt}='s';
    my $yestQ=qq|SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 0 DAY), "%Y-%m-%d")|;
    my $sth=$DBH->prepare($yestQ);
    $sth->execute;
    my $date=$sth->fetchrow;
    $sth->finish;
    ($args->{single_year}, $args->{single_month},$args->{single_day})=split('-', $date);
}
if (!$args->{inex}) {
	$args->{inex}='i';
}
if (!$args->{owner_adv}) {
	$args->{owner_adv}='o';
}
if (!$args->{aol} && !$args->{hotmail} && !$args->{yahoo} && !$args->{gmail} && !$args->{comcast} && !$args->{spirevision}) {
	$args->{yahoo}='yahoo';
}

display_header($args);
show_report($args);
display_footer();
exit;

sub show_report {
	
	my ($args)=@_;
	
	my $inserted_emails = '';
	my $added_to = '';
	my $error_emails = '';
	my $inex = '';
	my $owner_adv = '';
	
	($inserted_emails, $added_to, $error_emails) = insert_emails($args);
	
	if ($args->{owner_adv} eq 'o')
	{
		$inex = qq^<tr><td bgcolor=#EBFAD1>Internal</td></tr>^;
		$owner_adv = qq^<tr><td><h4><p>Linked Seed(s) to List Owner(s) in DB:</p></h4></td></tr>^;		
	}
	else
	{
		$inex = qq^<tr><td bgcolor=#EBFAD1>External</td></tr>^;
		$owner_adv = qq^<tr><td><h4><p>Linked Seed(s) to Advertiser(s) in DB:</p></h4></td></tr>^;
	}
	
	$owner_adv .= qq^$added_to ^;		     

	print qq^
			<table align=center border=0 bgcolor=#509C10 width=100%>
			
				<tr height=30>  
					<td align=center>
						<font color=#FFFFFF size=4 face=Arial bold>Confirmation</font>
					</td>		
				</tr>	

				<tr>
					<td align=left bgcolor=#EBFAD1>
						<table border=0 width=100% bgcolor=#EBFAD1>
						
						<tr><td><br></td></tr>
		^;
	
	if ($inserted_emails)
	{
		print qq^	
				<tr><td><h4>Added Seed(s) to DB:</h4></td></tr>						
				$inserted_emails
				
				$owner_adv			
				
				<tr><td><br></td></tr>
				
				<tr><td bgcolor=#EBFAD1><h4>Type:</h4></td></tr>
				$inex
				
				<tr><td><br></td></tr>
		^;	
	}

	if ($error_emails)
	{
		print qq^	
				<tr><td><h4>Errors: </h4></td></tr>						
				$error_emails
				
				<tr><td><br></td></tr>
		^;		
	}
	
	print qq^						
					</table>
				</td>
			</tr>
		</table>
		^;	

}

sub insert_emails 
{	
	my ($args) = @_;
	
	my $query = new CGI;
	
	my @added_to;

	my $added_to_html = '';
	my $error_html = '';
	my $to_insert_emails = {};
	my $to_insert_clients = {};
	
	if ($args->{owner_adv} eq 'o') # internal
	{
		## ----- Get list of owners	

		$added_to_html .= qq|<td><table border=1 width=100%> |		
						 .qq|<tr align=center><td>Email</td><td>Owner Name</td><td>Company</td><td>Status</td></tr> |;

		@added_to = $query->param('cID');
		
		foreach my $added_to_id (@added_to)
		{
			if ($added_to_id == 0)
			{
				## TODO: create hash of all advertisers

#				$added_to_html .= qq|<tr><td colspan=3>ALL </td></tr> |;
			}
			else
			{			
				my $sql=qq|SELECT user_id, company, first_name, status FROM user WHERE user_id = $added_to_id |;
					
				my $sth=$DBH->prepare($sql);
				$sth->execute;			
				
				my ($cID, $user, $first_name, $status)=$sth->fetchrow;

				$to_insert_clients->{$cID} = [$first_name, $user, $status];
	
#				$added_to_html .= qq|<tr align=left><td>$first_name</td><td>$user &nbsp;</td><td>$status &nbsp;</td></tr> |;
			}
		}
		
#		$added_to_html .= qq|</table></td>|;
					
	}
	else # external
	{
		## ----- Get list of advertisers		

		$added_to_html .= qq|<td><table border=1 width=100%> |		
						 .qq|<tr align=center><td>Email</td><td>Advertiser Name</td><td>URL</td></tr> |;

		@added_to = $query->param('advID');	

		foreach my $added_to_id (@added_to)
		{
			if ($added_to_id == 0)
			{
				## TODO: create hash of all advertisers

#				$added_to_html .= qq|<tr><td colspan=2>ALL </td></tr>|;
			}
			else
			{
				my $sql=qq|SELECT advertiser_id, advertiser_name, advertiser_url FROM advertiser_info |
					   .qq|WHERE advertiser_id = $added_to_id |;
			
				my $sth=$DBH->prepare($sql);
				$sth->execute;	
				
				my ($advID, $advName, $advUrl)=$sth->fetchrow;
				
				$to_insert_clients->{$advID} = [$advName, $advUrl];
				
#				$added_to_html .= qq|<tr align=left><td>$advName</td><td>$advUrl &nbsp;</td></tr> |;		
			}			
		}
#		$added_to_html .= qq|</table></td>|;		
	}

	## ----- Get list of emails
	
	my $email_list_text_area = $query->param('email_list_text_area');
	
	#-----------------------------------------------------------------------------
	#   1. Remove Space, NewLine, CR, FF, Tab from text string 
	#   2. if Mult Pipes Exist together change to Single Pipe char (eg from 2-999)
	#   3. Split text line via Pipe char into Array to get individual Email Addrs
	#-----------------------------------------------------------------------------
	$email_list_text_area =~ s/[ \n\r\f\t]/\|/g ;    
	$email_list_text_area =~ s/\|{2,999}/\|/g ;           
	my @email_array = split '\|', $email_list_text_area ;	

	my $emails_html = '';

	# full email
	foreach my $email (@email_array)
	{	
		my $domain = getDomain($email);
		
		if ($domain)
		{
			my $sql=qq|SELECT count(*) FROM delivery_test_seeds |
				   .qq|WHERE email_addr = '$email' |;
		
			my $sth=$DBH->prepare($sql);
			$sth->execute;	
			
			my ($exists)=$sth->fetchrow;
			
			if ($exists)
			{
				$error_html .= qq|<tr><td>Duplicate seed: $email </td></tr>|; 
			}		
			else
			{				
				push (@{$to_insert_emails->{$domain}}, $email);
				
#				$emails_html .= qq|<tr><td>$email </td></tr>|;					
			}
		}
		else
		{
			$error_html .= qq|<tr><td>Invalid domain: $email </td></tr>|; 			
		}
	}

	insert_in_db_and_rt24($to_insert_emails, $to_insert_clients, $args, \$emails_html, \$added_to_html, \$error_html);

	$added_to_html .= qq|</table></td>|;

	return ($emails_html, $added_to_html, $error_html);
}

sub getDomain
{
	my ($email) = @_;
	
	my ($eml,$domain) = split ('\@', $email);
	
	my ($dom, $ext) = split (/\./, $domain, 2);
	
	if ($dom 
		&& $dom ne 'test'
		&& $dom ne 'yahoo'
		&& $dom ne 'hotmail'
		&& $dom ne 'gmail'
		&& $dom ne 'aim'
		&& $dom ne 'aol'
		&& $dom ne 'spirevision'		
		)
	{
		$dom = 'other';
	}
#	if ($dom eq 'aol')
#	{
#		$dom = 'aim';
#	}
	
	return ($dom);	
}

sub insert_in_db_and_rt24
{
	my ($to_insert_emails, $to_insert_clients, $args, $emails_html, $added_to_html, $error_html) = @_;
	
	my $type;
	
	if ($args->{owner_adv} eq 'o')
	{
		$type = "internal";
	}
	else
	{
		$type = "external";
	}

	while (my $domain = each %$to_insert_emails)
	{	
		# full email	
		foreach my $email (@{$to_insert_emails->{$domain}})
		{
			my $sql=qq|INSERT INTO delivery_test_seeds VALUES |
				   .qq|(DEFAULT, '$email', '$domain') |;
				   
			my $sth=$DBH->prepare($sql);
			$sth->execute;			
			
			$sql=qq|SELECT id FROM delivery_test_seeds  |
				.qq|WHERE email_addr = '$email' |;
				   
			$sth=$DBH->prepare($sql);
			$sth->execute;									   			

			my ($email_id) = $sth->fetchrow;
			
			if (!$email_id)
			{
				$$error_html .= qq|<tr><td>Couldn't add seed to DB: $email </td></tr>|; 				
			}
			else
			{
				$$emails_html .= qq|<tr><td>$email </td></tr>|;
				
				my $fOpen = 1; # assume we could open rt24 file to append
				my $lockError = 0;
				my $fileError = 0;	
				
				my $file = "seed_emails";
				
				if ($domain ne "spirevision")
				{
					$file .= "_$domain";
				}				
				
#				if ($type eq "external")
#				{
#					$file .= "_$domain";
#				}

				## For now internal and external emails of the same domain are placed in the same file.
				## Can change this logic later if necessary, but probably will query client_seeds table
				## to determine which seeds are in/ex				
				
				my $fh = new FileHandle ">> " . "/var/www/util/bin/monitor/realtime/rt/$file";

				if (! (defined $fh))
				{
					$$error_html .= qq|<tr><td>Couldn't open RT24 file '$file' to append - seed '$email' not added: $! </td></tr>|;
					$fOpen = 0;					
				}
				else
				{
					#error locking file
					if(!flock ($fh,LOCK_EX))
					{
						$$error_html .= qq|<tr><td>Couldn't lock RT24 file '$file' to append - seed '$email' not added: $! </td></tr>|; 				
						$lockError = 1;
					}					
				}				
				
				while (my $client_id = each %$to_insert_clients)
				{
					$sql=qq|INSERT INTO client_seeds VALUES |
						.qq|($client_id, $email_id, '$type') |;
						   
					my $sth=$DBH->prepare($sql);
					$sth->execute;						   

					$sql=qq|SELECT count(*) FROM client_seeds  |
						.qq|WHERE $email_id = $email_id |
						.qq|AND client_id = $client_id |
						.qq|AND type = '$type' |;
						   
					$sth=$DBH->prepare($sql);
					$sth->execute;									   			
		
					my ($inserted) = $sth->fetchrow;
					
					if (!$inserted)
					{
						$$error_html .= qq|<tr><td>Couldn't link seed $email to client $to_insert_clients->{$client_id}->[0] </td></tr>|; 				
					}
					else
					{
						$$added_to_html .= qq|<tr align=left><td>$email</td><td>$to_insert_clients->{$client_id}->[0]</td><td>$to_insert_clients->{$client_id}->[1] &nbsp;</td><td>$to_insert_clients->{$client_id}->[2]</td></tr>|;						

						if (!$lockError && $fOpen)
						{
							my ($email_name, $dom) = split ('@', $email);
							
							if ($domain eq 'other')
							{
								$email_name = $email;
							}

							# print to file
							# email before @ except if domain is 'other'
							print $fh "\n$client_id\t$email_name\t24vision";
						}
					}	   
				}
				
				if (defined $fh)
				{
					#error unlocking file
					if(!$lockError && !flock ($fh,LOCK_UN))
					{
						$$error_html .= qq|<tr><td>Couldn't unlock RT24 file '$file': $! </td></tr>|; 
						$fileError = 1;				
					}					
					
					#close file
					if(!$fh->close)
					{
						$$error_html .= qq|<tr><td>Couldn't close RT24 file '$file': $! </td></tr>|;
						$fileError = 1;
					}
					
					
					# doing this in crontab (ymx3, every hour at xx:15)
#					if (! $fileError)
#					{
#						my $status = `scp /var/www/util/bin/monitor/realtime/rt/$file root\@sv-db-2.routename.com:/var/www/util/bin/monitor/realtime/rt/`;
#						
#						if (! $status)
#						{
#							$$error_html .= qq|<tr><td>Couldn't copy RT24 file '$file' to sv-db-2 for processing: $! </td></tr>|;		
#						}
#					} 										
				}	
			}
		}
	}
}


# not being used
sub insert_in_rt24
{
	my ($to_insert_emails, $to_insert_clients, $args, $emails_html, $added_to_html, $error_html) = @_;
	
	while (my $domain = each %$to_insert_emails)
	{
		eval
		{
			# TODO: if needs to be run on something other than sv-db-2 need to append over the network
#			open (FILE, "a", "svadmin\@sv-db-2.routename.com:/var/www/util/bin/monitor/realtime/rt/seed_emails_$domain");				

			open (FILE, "a", "/var/www/util/bin/monitor/realtime/rt/seed_emails_$domain");				
		};
		if ($@)
		{
			my $file = "seed_emails_$domain";
			$error_html .= qq|<tr><td>Couldn't open RT24 file $file to append: $@ </td></tr>|;
		}
		else
		{
			print FILE "\n"; # in case cursor is not on new line 
			
			foreach my $num (@$domain)
			{
				my $client_id = $domain->[$num]->[0];
				my ($email, $dom) = split ('@', $domain->[$num]->[1]);
				
				print FILE "$client_id\t$email\t24vision";
				
				$emails_html .= qq|<tr><td>$domain->[$num]->[1] </td></tr>|;								
			}			
		}		
	}	
}

sub getTestToolResults {
		
	my $date_clause = build_date_clause($args, 'send_date');
	
	my $sm_query = qq|
	
	SELECT
		tt.test_id,
		tt.email_address,
		tc.send_date,
		tt.email_status,
		ads.advertiser_subject,
		af.advertiser_from,
		ai.advertiser_name
	FROM
		test_campaign tc,
		test_tool_email_status tt,
		advertiser_info ai,
		advertiser_subject ads,
		advertiser_from af
	WHERE
		tc.test_id = tt.test_id
	AND
		tc.advertiser_id = ai.advertiser_id
	AND
		tc.subject_id = ads.subject_id
	AND
		tc.from_id = af.from_id
	AND
		$date_clause
	
	|;
	
	my $sth = $DBH->prepare($sm_query);
	$sth->execute;
	
	my $count = 0;
	my $string = '';
	
	while (my $data = $sth->fetchrow_hashref) {
		
		my $color = $count % 2==0 ? "#FFFFF" : "#EFEFEF";
		
		$string .= qq|
		<tr bgcolor=$color>
			<td>$data->{email_address}</td>
			<td>$data->{advertiser_subject}</td>
			<td>$data->{advertiser_from}</td>
			<td>$data->{send_date}</td>			
			<td>SM2 - $data->{test_id}</td>
			<td>&nbsp;</td>
			<td>$data->{advertiser_name}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>$data->{email_status}</td>
		</tr>
		|;
		
		$count++;
		
	} #end while
	
	return($string);
	
	
}

sub build_internal_clause
{
	my (%args) = @_;
	
	my $internal_clause = '';
	
	if ($args->{inex} eq 'i' && $args->{cID} != '0')
	{
		
		my $sql=qq|SELECT cs.seed_id as seed_id, dts.email_addr as seed |
			   .qq|FROM client_seeds cs, user u, delivery_test_seeds dts |
			   .qq|WHERE cs.seed_id = dts.id |
			   .qq|AND cs.client_id = u.user_id |
			   .qq|AND cs.type = 'internal' |
			   .qq|AND cs.client_id=$args->{cID} |
			   .qq|ORDER BY seed |;
		my $sth=$DBH->prepare($sql);
		$sth->execute;
		
		my @emails;
		while (my ($sID,$seed)=$sth->fetchrow) 
		{			
			push (@emails, $seed);
		}
	
		my $emails=join("','", @emails);
				
		$internal_clause = qq|AND email_addr IN ('$emails') |;
	}	
	
	return $internal_clause;
}

sub build_external_clause
{
	my (%args) = @_;
	
	my $external_clause = '';
	
	if ($args->{inex} eq 'e' && $args->{wID} ne 'ALL')
	{
		$external_clause = qq|AND e_from like '%$args->{wID}%' |;
	}	
	
	return $external_clause;
}

sub build_date_clause {
    my ($args,$name)=@_;

    my $date_clause;
    if ($args->{rt} eq "c") {
        $date_clause=qq^month($name)=month(now()) AND year($name)=year(now())^;
    }
    elsif ($args->{rt} eq "s") {
        $date_clause=qq^DATE($name)='$args->{single_year}-$args->{single_month}-$args->{single_day}'^;
    }
    else {
        $date_clause=qq^DATE($name)>="$args->{from_year}-$args->{from_month}-$args->{from_day}" AND DATE($name)<="$args->{to_year}-$args->{to_month}-$args->{to_day}"^;
    }
    return $date_clause;
}

sub print_date_drop {
  my ($name,$args)=@_;

  my %months=('01'=>"Jan",'02'=>"Feb",'03'=>"Mar",'04'=>"Apr",'05'=>"May",'06'=>"Jun",'07'=>"Jul",'08'=>"Aug",'09'=>"Sep",'10'=>"Oct
",'11'=>"Nov",'12'=>"Dec");
  my @day=('01'..'31');
  my $YEAR=(localtime)[5]+1900;
  my $mon_name=$name."_month";
  my $day_name=$name."_day";
  my $yr_name=$name."_year";

  my ($month,$day,$yr,$select);
  foreach (sort {$a<=>$b} keys %months) {
    if ($_ eq "$args->{$mon_name}") {
      $month.=qq^<option value="$_" SELECTED>$months{$_}\n^;
    }
    else { $month.=qq^<option value="$_">$months{$_}\n^; }
  }
  foreach (@day) {
    if ($_ eq "$args->{$day_name}") {
      $day.=qq^<option value="$_" SELECTED>$_\n^;
    }
    else { $day.=qq^<option value="$_">$_\n^; }
  }
  my $last_yr=$YEAR+1;
  for (my $i=2005; $i<=$last_yr; $i++) {
    if ($i eq "$args->{$yr_name}") {
      $yr.=qq^<option value="$i" SELECTED>$i\n^;
    }
    else { $yr.=qq^<option value="$i">$i\n^; }
  }
  $select=qq^
    <select name=$mon_name>
      <option value="">Month
      $month
    </select>
    <select name=$day_name>
      <option value="">Day
      $day
    </select>
    <select name=$yr_name>
      <option value="">Year
      $yr
    </select>
  ^;
  return $select;
}

sub print_nav_form {
  my $args=shift;

  my $r_select= my $c_select= my $l_select= my $y_select=my $s_select=my $i_select=my $e_select=my $o_select=my $a_select="" ;
  if ($args->{rt} eq "r") { $r_select="CHECKED"; }
  if ($args->{rt} eq "s") { $s_select="CHECKED"; }
  if ($args->{rt} eq "c") { $c_select="CHECKED"; }
  if ($args->{rt} eq "l") { $l_select="CHECKED"; }
   
  if ($args->{owner_adv} eq "o") { $o_select="CHECKED"; }
  if ($args->{owner_adv} eq "a") { $a_select="CHECKED"; }
  
  my $client=client_dropdown($args);
  my $email_drop=email_dropdown($args);
  my $email_box=email_box($args);
  my $adv_drop=adv_dropdown($args);
    
  my $html=qq^
  <table align=center border=0 width=100% bgcolor=#509C10>
    <tr height=30>
      <td align=center><font color=#FFFFFF size=3 face=Arial><b>Add Seeds To Clients</b></font></td>
    </tr>
    <tr>
      <td align=center bgcolor=#EBFAD1>
        <form method=post action="add_seeds_to_clients.cgi">
        <table border=0 width=100%>
        
          <tr><td><p>&nbsp;</p></td></tr>
          
          <tr height=20>
            <td><b>1) <input type=radio name="owner_adv" value="o" $o_select> Internal - Select List Owner:</b> (hold Ctrl to select multiple owners)</td>
          </tr>
          <tr height=20>
            <td>&nbsp;&nbsp;&nbsp;&nbsp; $client</td>
          </tr>           
          <tr><td><p>&nbsp;</p></td></tr>
          <tr><td><p>&nbsp;&nbsp;&nbsp; <b>OR</b> </p></td></tr> 
          <tr><td><p>&nbsp;</p></td></tr> 
           <tr height=20>
            <td><b>&nbsp;&nbsp;&nbsp; <input type=radio name="owner_adv" value="a" $a_select> External - Select Advertiser:</b> (hold Ctrl to select multiple advertisers)</td>
          </tr>
          <tr>
            <td>&nbsp;&nbsp;&nbsp;&nbsp; $adv_drop</td>
          </tr>           
          <tr><td>&nbsp;&nbsp;&nbsp;&nbsp; If Advertiser is not in this list please <a href="http://mailingtool.routename.com:83/newcgi-bin/advertiser_disp.cgi?pmode=A" target="_blank">Add a New Advertiser</a> and reload this page.</td></tr>         
          
          <tr><td><p>&nbsp;</p></td></tr> 
          
          <tr height=20>
            <td><b>2) Enter Emails:</b> </td>
          </tr>                 
          <tr><td>&nbsp;&nbsp;&nbsp;  Type email addresses in the box below. Hit ENTER after each email address. Each email address must be on a separate line. </td></tr>
          <tr><td>&nbsp;&nbsp;&nbsp;  NOTE: If your emails are NOT yahoo, hotmail, gmail, aim or spirevision please email developers\@zetainteractive.com and ask to turn on the realtime monitor and reports for file "seed_emails_other"</td></tr>
          <tr><td>&nbsp;&nbsp;&nbsp;  (this applies to aol emails as well)</td></tr> 
          <tr><td>&nbsp;&nbsp;&nbsp; $email_box</td></tr>  
	^;
	
	$html.=qq^                   
          <tr>
            <td align=center colspan=2>
                <input type=submit name="view" value="Submit">
            </td>
          </tr>
        </table>
        </form>
      </td>
    </tr>
  </table>
  <br>
  ^;
  return $html;
}

sub client_dropdown {
	my $args=shift;

	my $html=qq^
	<select name='cID' multiple="yes" size="5">

	^;

	my $sql=qq|SELECT user_id, company, first_name, status FROM user WHERE user_id <> 0 |
		   .qq|AND first_name <> 'test' ORDER BY first_name, company, user_id ASC|;

	my $sth=$DBH->prepare($sql);
	$sth->execute;
	while (my ($cID,$user,$first_name, $status)=$sth->fetchrow) {
		my $select = "";
		if ($user)
		{
			$user = "[$user]";
		}
		if ($status)
		{
			$status = "($status)";
		}		
#		if ($args->{inex} eq 'i')
#		{
#			$select=$args->{cID}==$cID ? "SELECTED" : "";
#		}
		$html.=qq^<option value="$cID" $select>$first_name $user $status \n^;
	}
	$sth->finish;
	$html.=qq^</select>^;

	return $html;
}

sub email_box
{	
	my $args=shift;
	
	my $html=qq^
				<TEXTAREA name="email_list_text_area" rows=10 wrap=off cols=45></TEXTAREA>
			^;
						
	return $html;
}

sub adv_dropdown {
	my $args=shift;

	my $html=qq^
	<select name='advID' multiple="yes" size="5" style="width: 1000px">

	^;
#		<option value='0'>ALL
	my $sql=qq|SELECT advertiser_id, advertiser_name, advertiser_url FROM advertiser_info |
		   .qq|WHERE advertiser_name <> '' |
		   .qq|AND advertiser_name <> 'test' |
		   .qq|AND advertiser_name <> 'test2' |
		   .qq|AND advertiser_name <> 'test2a' |
		   .qq|AND advertiser_name <> 'testing' |
		   .qq|ORDER BY advertiser_name, advertiser_url |;

	my $sth=$DBH->prepare($sql);
	$sth->execute;
	while (my ($advID, $advName, $advUrl)=$sth->fetchrow) {
		my $select = "";
		$select=$args->{advID}==$advID ? "SELECTED" : "";

#		foreach my $arg (@{$args->{advID}})
#		{
#			$select=$arg==$advID ? "SELECTED" : "";
#		}

		if ($advUrl)
		{
			$advUrl = "[$advUrl]";
		}
				
		$html.=qq^<option value="$advID" $select>$advName $advUrl \n^;
	}
	$sth->finish;
	$html.=qq^</select>^;

	return $html;
}

sub email_dropdown {
	my $args=shift;

	my $html=qq^
	<select name='eID' multiple="yes" size="10">

	^;
#			<option value='0'>ALL

	my $sql=qq|SELECT id, email_addr FROM delivery_test_seeds |
		   .qq|ORDER BY type, email_addr |;

	my $sth=$DBH->prepare($sql);
	$sth->execute;
	while (my ($eID,$email)=$sth->fetchrow) {
		my $select = "";
#		$select=$args->{eID}==$eID ? "SELECTED" : "";
		
		$html.=qq^<option value="$eID" $select>$email\n^;
	}
	$sth->finish;
	$html.=qq^</select>^;

	return $html;
}

sub website_dropdown {
	my $args=shift;

	my $html=qq^
	<select name='wID'>

	^;
	
#			<option value="ALL">ALL
	
	my $sql=qq|SELECT DISTINCT u.user_id as user_id, u.website_url as website_url |
		   .qq|FROM user u, client_seeds cs |
		   .qq|WHERE u.user_id = cs.client_id |
		   .qq|AND cs.type = 'external' |
		   .qq|AND u.website_url <> 'http://' |
		   .qq|AND u.website_url <> '' |
		   .qq|ORDER BY u.website_url, u.user_id |;
	my $sth=$DBH->prepare($sql);
	$sth->execute;
	while (my ($wID,$url)=$sth->fetchrow) {
		my $select = "";
		$url =~ s%https*://+%%ig;
		$url =~ s%^www\.%%i; 
		$url =~ s%/.*$%%i; 
#		if ($args->{inex} eq 'e')
#		{
#	 		$select=$args->{wID} eq "$url" ? "SELECTED" : "";			
#		}
		$html.=qq^<option value="$url" $select>$url\n^;
	}
		
	$sth->finish;
	$html.=qq^</select>^;

	return $html;
}

sub display_header {
        my $args=shift;

        my $nav=print_nav_form($args);
        print "Content-type: text/html\n\n";
        print qq^
        <html>
          <head>
                <link rel="stylesheet" href="report.css" type="text/css" />
                <title>Add Seeds To Clients</title>
                <body>
                  <center>$nav</center>
        ^;
}

sub display_footer {

        print qq^<br>
                  <center><a href="mainmenu.cgi"><IMG src="images/home_blkline.gif" border=0></a></center>
                </body>
        </html>
        ^;
}

