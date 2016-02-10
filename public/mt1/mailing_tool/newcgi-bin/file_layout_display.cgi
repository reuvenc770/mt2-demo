#!/usr/bin/perl
#===============================================================================
# Purpose: Displays and Add/Updates the USER_FILE_LAYOUT rec for a specific user.
# File   : fl_disp.cgi
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 9/10/01  Created.
#===============================================================================

#-----------------------
# include Perl Modules
#-----------------------
use strict;
use CGI;
use util;
#use CGI::Carp qw(fatalsToBrowser);
use MIME::Base64;

my $util = util->new;
my ($dbhq,$dbhu)=$util->get_dbh();

my $hrCookie = retrieve_cookie();
my $user_id;

if (!$hrCookie->{'clientInfo'}) {
   print "Location: http://www.zetainteractive.com/login.html\n\n";
}

else {

    my $username = decode_base64($hrCookie->{'clientInfo'});
    
	if($username =~ /bloosky/i){
		$username = 'tranzact';
	}

    my $qSel = qq|
    SELECT 
	    user_id, 
	    username
    FROM 
    	user u
    WHERE 
    	username = "$username"
    |;

    my $sth = $dbhq->prepare($qSel);
    $sth->execute();

    my $hrInfo = $sth->fetchrow_hashref;

    $user_id = $hrInfo->{'user_id'};

}

sub retrieve_cookie {
	
	use CGI::Cookie;

	my %hCookies = fetch CGI::Cookie;

	if ($hCookies{sv}) {
     	
		my %hCookie=$hCookies{sv}->value;
		
		if ($hCookie{'clientInfo'}) {
			return wantarray() ? (\%hCookie, "COOKIE") : \%hCookie;
		}
		         
		else {
			return wantarray() ? ({}, "NO ID") : {};
		}
	     
	}     
       
	else {
		return wantarray() ? ({}, "NO COOKIE") : {};
	}  

}


#--------------------------------
# get some objects to use later
#--------------------------------
my $query = CGI->new;
my ($sth, $sql, $dbh, $reccnt ) ;
my ($sth2, $sql2) ;
my $alt_light_table_bg = $util->get_alt_light_table_bg;
#my $light_table_bg = $util->get_light_table_bg;
#my $light_table_bg = '#D1E4F0';
my $light_table_bg = '#D1E4F0';

my ($text_color1, $text_color2); 
my ($cat_id, $field_position, %file_fields, $fidx ) ;
my ($category, $db_field, $html_disp_order, $nbr_cols, $checked);

my (@fl_pos, @fl_text, @fl_db_field);
my ($readonly);
my (@fl_fields, $fl_field, $max_field_cnt);

$text_color1 = "black" ;
#----- connect to the util database -----
###$util->db_connect();

###$dbh = $util->get_dbh;

#----- check for login --------
#my $user_id = util::check_security();
#if ($user_id == 0)
#{
#    print "Location: notloggedin.cgi\n\n";
#    $util->clean_up();
#    exit(0);
#}

#pass in user id
#my $user_id = $query->param('user_id');

print qq|Content-Type: text/html;charset-utf-8\n\n|;

print qq|
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Add/Update Record Processing File Layout</title>
</head>
<body link="#000000" vlink="#000000" alink="#000000">

|;

&disp_java();
&disp_file_layout();

$util->footer();
$util->clean_up();
exit(0);

#===============================================================================
# Sub: disp_file_layout
#===============================================================================
sub disp_file_layout
{
	my ($i, $fpos);

	#---------------------------------------------------------------------------
	# Get user_file_layout for user.  The ORDER of the fields in the SELECT stmt
	# matter.  They are used in an array for display on HTML Page based on 
	# their position.
	#---------------------------------------------------------------------------
	$sql = qq|
	select 
		email_addr_pos,     
		gender_pos,
		first_name_pos,     
		last_name_pos,
		birth_date_pos,     
		address_pos,         
		address2_pos,
		city_pos,           
		state_pos,           
		zip_pos,
		country_pos,        
		job_status_pos,     
		phone_pos, 
		source_url_pos, 	
		date_capture_pos,	
		member_source_pos		
	from 
		user_file_layout 
	where 
		user_id = $user_id
	|;
	
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	
	my $userData = $sth->fetchrow_hashref() ;

	$sth->finish();
	
	#&set_fl_arrays();
	
	print << "end_of_html";

	<!-- Begin FILE TO UPLOAD Tbls ------------------------------ -->
	<FORM name="sub_add_form" action=file_layout_update.cgi method=post>
	
	<input type=hidden name=field_cnt  value="0" >

	<TABLE cellSpacing=0 cellPadding=0 width=660 border=0 bgcolor='#D1E4F0' align='center'>
	<TBODY>
	<TR>
	<TD align=center>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TBODY>
			<tr>
				<td bgcolor='#1596E7'>
					<center><FONT face=Verdana,Arial,Helvetica,sans-serif color=white size=2>
	                                <B>Add/Update Record Processing File Layout</B> </FONT></center>
				</td>
			</tr>
			<TR height=15>
				<TD align=left width="100%" height=15>
				<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
				<br>
				<center>Define the 'File Layout' for batch uploads by selecting from the desired 
				fields listed below.  The 'File Layout' will be used to upload 
				email data from ascii files.</center><br>
				</FONT></TD>
			</TR>
		</TBODY>
		</TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 bgcolor='#D1E4F0'>
		<TBODY>
			<TR height=15>
				<TD valign=top align=right width="05%" height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>&nbsp;&nbsp;</FONT>
				</TD>
				<TD align=left width="95%" height=15>
					<FONT face=Verdana,Arial,Helvetica,sans-serif color="$text_color1" size=2>
						<center>Please specify the order by entering numbers into the field positions.</center>
						<br />
						<center>Note: Source URL, IP Address and Capture Date are mandatory.</center>
					</FONT>
				</TD>
			</TR>

		</TBODY>
		</TABLE>
		
		<br>
		
		<!-- Begin Tbl Defn to display ChkBox, FldPos, DemoText ---------------------- -->
		<TABLE cellSpacing=3 cellPadding=3 width="100%" border=0>
		<TBODY>

end_of_html

	#---------------------------------------------------------------------------
	my $nbr_cols = 3 ;
	my $i = 0 ; 
	my $fpos = 1;
	
	my $counter = 0;
	my $readonly = '';
	my $mappings = fieldMappings();
	
	#for($i = 0; $i <= 22 ; $i++) {
	foreach my $mapping (@$mappings){
				
		foreach my $dbField (keys %$mapping){

			if ( ($fpos % $nbr_cols) == 1  )  
			{
				print qq{<tr bgColor="#D1E4F0"> \n };
			}
			
			$checked = '';
			
			if ( $userData->{ $dbField } )    
			{
				$checked = "CHECKED" ;     # Field Position exists - Check the box
			}
	#		else
	#		{
	#			if ($i ne "0")
	#			{
	#			$checked = "" ;
	#			}
	#		}
	
			#OnChange="chk_chkbox('$counter');"
		
			print qq{
				<TD colspan=1 width="02%" align="center" valign="bottom">
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=2>
				<input $checked value="$dbField" type="hidden" name="chkbox" OnClick="set_field_pos('$counter');">
				</font>
				</td>
	
				<TD colspan=1 width="03%" align="center" valign="bottom">
				<FONT face="verdana,arial,helvetica,sans serif" color=#509C10 size=1>
				<input $readonly type="input" name="fldpos" maxlength="4" size="3" value="$userData->{ $dbField }">
				</font>
				</td>
	
				<TD colspan=1 width="20%" align="left" valign="bottom">
				<FONT face="verdana,arial,helvetica,sans serif" color="#000000" size=2>
				&nbsp;$mapping->{ $dbField }
				</font>
				</td>
				};   # end print qq statement
	
			if ( ($fpos % $nbr_cols) == 0 )
			{
				print qq{</tr> \n} ; 
			}
	
			$fpos++;
			
			$counter++;

		}
		
		
	} # end for loop -- checking fields selected 

	if ( ($fpos % $nbr_cols) != 0 )
	{
		print "</tr> \n" ;	# Print </tr> if doesnt end on the value of $nbr_cols (eg 3)
	}

	print qq{<TR bgColor="$light_table_bg">};  # blank line for spacing.....
	print qq{<TD colspan=9> <br>};
	print qq{</TD>};
	print qq{<TR>};

	print qq{<TR bgColor="$light_table_bg">};
	print qq{<TD align="center" colspan=9>};
	print qq{<input type=button name=BtnSave value="Submit Changes"     ONCLICK="return save();">&nbsp;&nbsp;};
	print qq{<input type=button name=BtnHome value="Back"         ONCLICK="history.go(-1)">&nbsp;&nbsp;};
#	print qq{<input type=button name=BtnHome value="Home"         ONCLICK="return goto_url('mainmenu.cgi');">&nbsp;&nbsp;};
#	print qq{<input type=button name=BtnSeq  value="Re-Sequence"  ONCLICK="return resequence_fldpos();">};
	print qq{</TD>};
	print qq{<TR>};

	print qq{</TBODY> </TABLE> <!-- close out table displaying chkboxes, fldpos and text ----------------- --> };

	print qq{</TD>};
	print qq{</TR>};
	print qq{</TBODY>};
	print qq{</TABLE>};
	print qq{</FORM>};

} # end sub disp_file_layout


#===============================================================================
# Sub: disp_java
#===============================================================================
sub disp_java
{
	print << "end_of_html" ;

	<script language="JavaScript">
	
	function checkUserInput(){
		
		//this is totally hacky but it works based on the way the page/code is
		var status = 1;
		
		if(document.sub_add_form.fldpos[13].value == ""){
			alert("Source URL cannot be empty");
			status = 0;
		}
	
		if(document.sub_add_form.fldpos[14].value == ""){
			alert("Capture Date cannot be empty");
			status = 0;
		}
		
		if(document.sub_add_form.fldpos[15].value == ""){
			alert("IP Address cannot be empty");
			status = 0;
		}
		
		var numberCheck = 0;
		
		var arrayLen = document.sub_add_form.fldpos.length ;
		
		for (i=0; i <= (arrayLen -1); i++)
		{
			if( document.sub_add_form.fldpos[i].value != '' && isNaN(document.sub_add_form.fldpos[i].value) ){
			
				numberCheck = 1;
				status = 0;
				
			}
		}
		
		if(numberCheck){
			alert("Please enter numbers only.");	
		}
		
		if(status == 0){
			return false;	
		}
		
		else{
			return true;	
		}
		
	}

	//--------------------------------------------------------------------------
	// Un check the chkbox field if the value has been deleted
	//--------------------------------------------------------------------------
	function save() 
	{ 
<!--		resequence_fldpos(); -->
		if(checkUserInput()){
		
			document.sub_add_form.submit();
        	return true;
        
		}
	}
	

	//--------------------------------------------------------------------------
	// Un check the chkbox field if the value has been deleted
	//--------------------------------------------------------------------------
	function chk_chkbox(fidx) 
	{ 
		if ( document.sub_add_form.fldpos[fidx].value == "" )
		{
			document.sub_add_form.chkbox[fidx].checked = false ;
		}
		else
		{
			document.sub_add_form.chkbox[fidx].checked = true ;
		}
	} 


	//--------------------------------------------------------------------------
	// Re-Sets Field Position values to begin w/1 thru N where N is the last
	// field w/a value.  Field Pos values are integers and do NOT skip a value.
	// (eg 1, 2, 3, 4 etc).
	//--------------------------------------------------------------------------
	function resequence_fldpos() 
	{ 
		var Array1, Array2, ArrayLen, fidx, fpos, npos;
		var ArrayStr, reg3, reccnt, pipe_pos;

		Array1 = new Array();
		Array2 = new Array();

		reg3 = /^0/ ;         // from 1st pipe to end of string

		//--------------------------------------------------------------------------
		// Create a string w/values separated via the PIPE char as: FldPos|HtmlIndex
		//--------------------------------------------------------------------------
		ArrayLen = document.sub_add_form.fldpos.length ;
		for (i=0; i <= (ArrayLen -1); i++)
		{
			if ( document.sub_add_form.fldpos[i].value != "" )
			{	// Zero Padd field for total length of 4 chars - so it sorts correctly
				npos = zero_pad(document.sub_add_form.fldpos[i].value, ( 4 - document.sub_add_form.fldpos[i].value.length)) ;
				ArrayStr = npos + "|" + i ;
				Array1.push(ArrayStr);
			}
		}

		Array2 = Array1.sort();          // sort Array1 to get in order of Field Position
		ArrayLen = Array2.length ;
		reccnt = 0;
		for (i=0; i <= (ArrayLen -1); i++)
		{
			reccnt++;
			ArrayStr = Array2.shift();

			//----- Find Location of PIPE char -------------------
			for(x=0; x <= (ArrayStr.length -1); x++)
			{
				if ( ArrayStr.substr(x,1) == "|" )
				{
					pipe_pos = x ;
				}
			}

			//---- Set fidx(eg indexes chkbox, fldpos), fpos(eg Filed Pos value)
   			fidx = ArrayStr.substr((pipe_pos + 1), (ArrayStr.length -1) - pipe_pos );
   			fpos = ArrayStr.substr(0,pipe_pos);      // Start at Zero for length of pipe_pos

   			fpos = fpos.replace(reg3, "");        //Remove leading Zero
   			fpos = fpos.replace(reg3, "");        //Remove leading Zero
   			fpos = fpos.replace(reg3, "");        //Remove leading Zero

			document.sub_add_form.fldpos[fidx].value = reccnt;
		}

	document.sub_add_form.field_cnt.value = reccnt;
	return 1;
	} 


	//--------------------------------------------------------------------------
	// Zero Pad (eg left) the Field Position value to get correct sort
	// (eg so 9 sorts after 10 etc.)
	//--------------------------------------------------------------------------
	function zero_pad(NbrIn, PadLen) 
	{ 
		var padstr, y;
		padstr = "";
		for(y=1; y <= PadLen; y++)
		{
			padstr = padstr + "0" ;
		}
		padstr = padstr + NbrIn ;
		return padstr ;
	}


	//--------------------------------------------------------------------------
	// Set Field Position value 
	//  1. If checked and value is null then set to last field cnt + 1
	//  2. If un-checked then set Field Position value to null
	//
	//  Note: Field Position values are re-sequenced prior to Add/Update
	//--------------------------------------------------------------------------
	function set_field_pos(fidx) 
	{ 
		var FieldPos;
		var ChkBox;
	
		//alert(fidx);

 		if ( document.sub_add_form.chkbox[fidx].checked == true)
  		{
			if ( document.sub_add_form.fldpos[fidx].value  ==  "" )
			{
   				FieldPos = document.sub_add_form.field_cnt.value ;
   				FieldPos++;
   				//alert(FieldPos);
   				document.sub_add_form.fldpos[fidx].value = FieldPos ;
   				document.sub_add_form.field_cnt.value = FieldPos ;
			}
  		}
  		else
  		{
			document.sub_add_form.fldpos[fidx].value = "" ;
  		}

		return 1 ;  // TRUE
	}
	

	function goto_url(h) 
	{ 
		top.location.href = h; 
		return 0;  
	} 

	</script>

end_of_html

} # end sub disp_java

sub fieldMappings {
	
	my $mappings = [
		{'email_addr_pos' 	  => 'Email Address'},      
		{'first_name_pos' 	  => 'First Name'},     
		{'last_name_pos' 	  => 'Last Name'},    
		{'address_pos' 		  => 'Address 1'},         
		{'address2_pos' 	  => 'Address 2'},
		{'city_pos' 		  => 'City'},           
		{'state_pos' 		  => 'State'},           
		{'zip_pos' 			  => 'Zip'},
		{'country_pos' 		  => 'Country'},  
		{'phone_pos' 		  => 'Phone 1'}, 
		{'gender_pos' 		  => 'Gender'},
		{'birth_date_pos' 	  => 'Birth Date'},       
		{'job_status_pos' 	  => 'Job Status'},     
		{'source_url_pos' 	  => 'Source URL'}, 	
		{'date_capture_pos'   => 'Capture Date'},	
		{'member_source_pos'  => 'IP Address'},	
	];
	
	return($mappings);
}

##===============================================================================
## Sub: set_fl_arrays 
##===============================================================================
#sub set_fl_arrays
#{
#	my ($i);
#
#	#---- set fl_pos to Field Value or Null ------------
#	$max_field_cnt = 0 ;
#	$i = 0 ;
#	foreach $fl_field (@fl_fields)
#	{
#		if ($fl_field ne "")
#		{
#			$fl_pos[$i] = $fl_field ;      # Order of fields selected matters!!!!
#			
#			if ( $fl_field > $max_field_cnt )
#			{
#				$max_field_cnt = $fl_field;
#			}
#		}
#		else
#		{
#			$fl_pos[$i] = "";
#		}
#		
#		$i++;
#	}
#
#	$fl_pos[0] = '1' ;             # 1st pos is always Email Address
#	if ( $max_field_cnt == 0 )
#	{
#		$max_field_cnt = 1 ;       # no user_file_layout rec exists -- cnt for Email Addr as 1st field
#	}
#
#	#----- Set HTML Text fields to values to Display ----- Order matters!!!! (DOES IT MIKE?!)
#	$fl_text[0] = 'Email Address' ;
#	$fl_text[1] = 'Email Type' ;
#	$fl_text[2] = 'Gender' ;
#	$fl_text[3] = 'First Name' ;
#	$fl_text[4] = 'Middle Name' ;
#	$fl_text[5] = 'Last Name' ;
#	$fl_text[6] = 'Birth Day' ;
#	$fl_text[7] = 'Address Line 1' ;
#	$fl_text[8] = 'Address Line 2' ;
#	$fl_text[9] = 'City' ;
#	$fl_text[10] = 'State' ;
#	$fl_text[11] = 'Zip' ;
#	$fl_text[12] = 'Country' ;
#	$fl_text[13] = 'Marital Status' ;
#	$fl_text[14] = 'Occupation' ;
#	$fl_text[15] = 'Job Status' ;
#	$fl_text[16] = 'Household Income' ;
#	$fl_text[17] = 'Education Level' ;
#	$fl_text[2]  = 'Phone' ;
#	$fl_text[22] = 'Phone 2' ;
#	$fl_text[21] = 'Source URL' ;
#	$fl_text[18] = 'Date Captured' ;
#	$fl_text[19] = 'IP' ;
#
#
#	#----- Set DB Field Names ----- Order matters (eg must match select stmt order) -----
#	$fl_db_field[0] = 'email_addr_pos' ;
#	$fl_db_field[1] = 'email_type_pos' ;
#	$fl_db_field[2] = 'gender_pos' ;
#	$fl_db_field[3] = 'first_name_pos' ;
#	$fl_db_field[4] = 'middle_name_pos' ;
#	$fl_db_field[5] = 'last_name_pos' ;
#	$fl_db_field[6] = 'birth_date_pos' ;
#	$fl_db_field[7] = 'address_pos' ;
#	$fl_db_field[8] = 'address2_pos' ;
#	$fl_db_field[9] = 'city_pos' ;
#	$fl_db_field[10] = 'state_pos' ;
#	$fl_db_field[11] = 'zip_pos' ;
#	$fl_db_field[12] = 'country_pos' ;
#	$fl_db_field[13] = 'marital_status_pos' ;
#	$fl_db_field[14] = 'occupation_pos' ;
#	$fl_db_field[15] = 'job_status_pos' ;
#	$fl_db_field[16] = 'income_pos' ;
#	$fl_db_field[17] = 'education_pos' ;
#	$fl_db_field[18] = 'date_capture_pos' ;
#	$fl_db_field[19] = 'member_source_pos' ;
#	$fl_db_field[20] = 'phone_pos' ;
#	$fl_db_field[21] = 'source_url_pos' ;
#	$fl_db_field[22] = 'phone2_pos' ;
#
#} # end sub set_fl_arrays



