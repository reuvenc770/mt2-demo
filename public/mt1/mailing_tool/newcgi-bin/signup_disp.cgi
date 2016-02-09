#!/usr/bin/perl
#===============================================================================
# Purpose: Build an EXAMPLE 'Signup Template' 
# File   : signup_disp.cgi
#
# Input  :
#
# Output :
#
#--Change Control---------------------------------------------------------------
# Mike Baker, 8/17/01  Created.
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
	my $username = $query->param('username');
	my $password = $query->param('password');
	my ($sth, $reccnt, $sql, $dbh ) ;
	my ($bgcolor, $list_id, $list_name, $status, $chkbox_name);
	my ($text_color) ;
	my ($state, $state_name) ;
	my ($default_flag, $country_code, $country_name) ;
	my ($email_code, $email_type) ;
	my ($marital_code, $marital_status) ;
	my ($occupation_code, $occupation) ;
	my ($job_code, $job_status) ;
	my ($income_code, $income) ;
	my ($education_code, $education) ;
	my ($vlink_color, $alink_color, $link_color, $bg_color, $text_color );
	my ($email_list) ;
my $images = $util->get_images_url;

	$text_color = "#000000" ;        # color = Black
	$bg_color = "#ffffff" ;          # color = White
	
	$email_list = "I need to set this from the DBMS....." ;

	#------------------------------
	# connect to the util database
	#------------------------------
###	$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
	$dbh = $util->get_dbh;

	#------------------------------
	# check for login
	#------------------------------
	my $user_id = util::check_security();
	if ($user_id == 0)
	{
    print "Location: notloggedin.cgi\n\n";
	    $util->clean_up();
	    exit(0);
	}

	print "Content-Type: text/html\n\n";
	#-----------------------------------------
	print << "end_of_html" ;

<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> -->
<HTML>
<HEAD>
<TITLE>Registration Form</TITLE>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
<STYLE type=text/css>
	TD { FONT-SIZE: 8pt; COLOR: $text_color; FONT-FAMILY: Arial }
	H1 { FONT-WEIGHT: bold; FONT-SIZE: 18pt; COLOR: $text_color; FONT-FAMILY: Arial }
</STYLE>

<!-- Begin JAVA SCRIPT -------------------------------------- -->
<SCRIPT language=JAVASCRIPT>
function checkForm(form)
{
//        // first name
//        if ( (form.USR_FNAME) && (form.USR_FNAME.value.length == 0) )
//        {
//                alert("Please enter your first name.");
//                form.USR_FNAME.focus();
//                return false;
//        }

//        // last name
//        if ( (form.USR_LNAME) && (form.USR_LNAME.value.length == 0) )
//        {
//                alert("Please enter your last name.");
//                form.USR_LNAME.focus();
//                return false;
//        }

//        // address
//        if ( (form.USR_ADRS1) && (form.USR_ADRS1.value.length == 0) )
//        {
//                alert("Please enter your address.");
//                form.USR_ADRS1.focus();
//                return false;
//        }

//        // city
//        if ( (form.USR_CITY) && (form.USR_CITY.value.length == 0) )
//        {
//                alert("Please enter your city.");
//                form.USR_CITY.focus();
//                return false;
//        }

//        // state
//        if ( (form.USR_STATE) &&
//                (form.USR_STATE.options[form.USR_STATE.options.selectedIndex].value == "") )
//        {
//                alert("Select your State.");
//                return false;
//        }

//        // zip
//        if (form.USR_ZIP)
//        {
//                // usa zip code format: ##### or #####-####
//                var filter = /^[0-9]{5}(-[0-9]{4})?\$/ ;

//                // canadian zip code format: X#X #X# or X#X#X#
//                var filter2 = /^[A-z][0-9][A-z] ?[0-9][A-z][0-9]\$/ ;

//                if(  ( !filter.test( form.USR_ZIP.value ) )  &&  ( !filter2.test( form.USR_ZIP.value ) ) )
//                {
//                        alert( "Please enter a valid zip code." )
//                        form.USR_ZIP.focus();
//                        return false;
//                }
//        }

		//----------------------------------------------------------------------
        // Check EMail
		//----------------------------------------------------------------------
        // first check for basic format of email with an @ and split the email up
        var emailPat = /^(.+)@(.+)\$/ ;
        var emailStr = form.USR_EMAIL.value;
        var matchArray = emailStr.match(emailPat);
        if (matchArray == null)
        {
                alert("Email address seems incorrect (check @ and .'s)");
                return false;
        }

        // Now check the username and the domain
        // check that only basic ASCII characters are in the strings (0-127).
        var user=matchArray[1];
        var domain=matchArray[2];
        for (i=0; i<user.length; i++)
        {
                if (user.charCodeAt(i) > 127)
                {
                        alert("Ths username contains invalid characters.");
                        return false;
                }
        }
        for (i=0; i<domain.length; i++)
        {
                if (domain.charCodeAt(i) > 127)
                {
                        alert("Ths domain name contains invalid characters.");
                        return false;
                }
        }
        // now check the domain for a . and invalid characters in either part
        var atomPat=new RegExp("^\.@,;:\$");
        var domArr=domain.split(".");
        var len=domArr.length;
        for (i=0;i<len;i++)
        {
                if (atomPat.test(domArr[i]))
                {
                        alert("The domain name does not seem to be valid. ");
                        return false;
                }
        }

		//----------------------------------------------------------------------
        // Validate birthday
		//----------------------------------------------------------------------
        if (    (form.B_Month) &&
                        ( form.B_Month.options[form.B_Month.options.selectedIndex].value == "**" ||
                        form.B_Day.options[form.B_Day.options.selectedIndex].value == "**" ||
                        form.B_Decade.options[form.B_Decade.options.selectedIndex].value == "**" ||
                        form.B_Year.options[form.B_Year.options.selectedIndex].value == "**") )
        {
                alert("Select your Birth Date.");
                return false;
        }

        // check to make sure person didn't enter "31" for days with 30
        if ((form.B_Month) &&  (form.B_Decade) && (form.B_Year))
        {
                var filter = /^Feb|Apr|Jun|Sep|Nov\$/;

                // must have beautiful output
                var month = "";
                if (form.B_Month.options[form.B_Month.options.selectedIndex].value == "Apr")
                        { month = "April" }
                else if (form.B_Month.options[form.B_Month.options.selectedIndex].value == "Jun")
                        { month = "June" }
                else if (form.B_Month.options[form.B_Month.options.selectedIndex].value == "Sep")
                        { month = "September" }
                else if (form.B_Month.options[form.B_Month.options.selectedIndex].value == "Nov")
                        { month = "November" }

                var day = form.B_Day.options[form.B_Day.options.selectedIndex].value;
                var year = ("19" + form.B_Decade.options[form.B_Decade.options.selectedIndex].value + form.B_Year.options[form.B_Year.options.selectedIndex].value);

                // determine if year input is a leap year
                var leapyear = 0;
                if ( year % 100 == 0 )
                        { if (year % 400 == 0)  leapyear = 1;  }
                else
                        {if (year % 4 == 0)  leapyear = 1;  }

                // if leap year and user input in day greater than 28          "February " +
                if( ( day > 29) && ( leapyear ) &&
                        ( form.B_Month.options[form.B_Month.options.selectedIndex].value == "Feb" ) )
                {
                        alert( year + " may be a leap year, but it still doesn't have more than 29 days.");
                        return false;
                }
                // if not a leap year and user input in day greater than 28
                else if ( ( day > 28) && ( !leapyear ) &&
                        ( form.B_Month.options[form.B_Month.options.selectedIndex].value == "Feb" ) )
                {  // "That year (" +
                        alert(year + " is not a leap year.");
                        return false;
                }

                // if user chose any month with 30 days and input day greater than 30
                if ( (day > 30) &&
                        filter.test(form.B_Month.options[form.B_Month.options.selectedIndex].value) )
                {
                        alert( month + " has only 30 days.");
                        return false;
                }

                // check if person younger than 13
                var d = new Date();
                // 3.0 compliant:
                var thisYear = d.getYear();
                var m = d.getMonth()
                m++;
                var t = d.getDate();
                var thisOld = 13;
                // accounts for netscape bug about reading the date
                if (thisYear < 1900) { thisYear = thisYear + 1900 }
                // check age if at least 13
                var error = false;
                if ( ( (thisYear - year == thisOld) && (form.B_Month.selectedIndex == m) && (day > t) ) ||
                        ( (thisYear - year == thisOld) && (form.B_Month.selectedIndex > m ) ) ||
                        (thisYear - year < thisOld) )
                {
                        error=true;
                }
                if (error)
                {
                        alert("Sorry, you must be at least " + thisOld +" years or older to register; but please check out other cool stuff on our site.");
                        //document.location="http://www.promocruises.com";
                        return false;
                }
        }

//		//----------------------------------------------------------------------
//        // UserName
//		//----------------------------------------------------------------------
//        if ( (form.USR_USERNAME) && (form.USR_USERNAME.value.length < 5 ) )
//        {
//                alert("Please enter your SignIn Name.  It MUST be at least 5 characters in length.");
//                form.USR_USERNAME.focus();
//                return false;
//        }

//		//----------------------------------------------------------------------
//        // Password
//		//----------------------------------------------------------------------
//        if ( (form.USR_PASSWORD) && (form.USR_PASSWORD.value.length < 5 ) )
//        {
//                alert("Please enter your Password.  It MUST be at least 5 characters in length.  It MUST also be equal to the password entered in the Re-Enter Password field.");
//                form.USR_PASSWORD.focus();
//                return false;
//        }

//        if ( ( form.USR_PASSWORD.value != form.USR_PASSWORD_VERIFY.value ) )
//        {
//			alert("The Password fields are NOT the same.  Please re-enter both Password fields.");
//			form.USR_PASSWORD.focus();
//			return false;
//        }

//		var myChkPassword = new RegExp ( form.USR_USERNAME.value ) ;
//		if ( myChkPassword.test( form.USR_PASSWORD.value ) )
//        {
//			alert("The Password may NOT contain the Sign-In Name.  Please re-enter both Passwords.");
//			form.USR_PASSWORD.focus();
//			form.USR_PASSWORD.value = "" ;
//			form.USR_PASSWORD_VERIFY.value = "" ;
//			return false;
//        }

		// alert("form.EMAIL_LIST_ID.value = " + form.EMAIL_LIST_ID.value ) ;
		// return false;
		//-----------------------------------
        // Validate List Name
		//-----------------------------------
        if ( form.EMAIL_LIST_ID.value == "x" )
        {
			alert("Please select an Email List to Sign-Up for.");
			form.EMAIL_LIST_ID.focus();
			return false;
        }


        // if you get here, then all fields filled out.
        return true;
}

</SCRIPT>
<!-- End JAVA SCRIPT -------------------------------------- -->
</head>

<!-- #   $bgcolor = "#EBFAD1" ;     # Light Green    -->
<!-- #   $bgcolor = "$alt_light_table_bg" ;     # Light Yellow   -->
<!-- #	 $bgcolor = "$text_color" ;     # Dark Green     -->

<!-- <BODY vLink="#FF8000" aLink="#FF8040" link="#FF8040" bgColor="#3498F6" text="White"> -->
<BODY vLink="#FF8000" aLink="#FF8040" link="#FF8040" bgColor="$bg_color" text="$text_color">

<FORM onsubmit="return checkForm(this);" action="signup_add.cgi" method="post">

<!-- Begin TBL Definition ----------------------------------------- -->
<TABLE width="80%" border="0" cellspacing="3" cellpadding="0">

<tr bgcolor="#dbeaf5" > 
	<td colspan="2" width="80%">
	<font face="Arial" size="4"><font color="$text_color">
	<center><b>SignUp EXAMPLE!</b></center></font><br>
	<font face="Arial" color="$text_color" size="2">
	This is an Example SignUp Form <br><br> </td>
</tr>

<tr>
	<td colspan="2" width="80%"><FONT face="Helvetica, Arial" color=$text_color size=2>( </FONT>
	<FONT color=#ff0000>*</FONT>
	<FONT face="Helvetica, Arial" color=$text_color size=2> ) <I>Indicates required items</I></FONT>
	<br><br>
	</td>
</tr>


<TR bgcolor="#eff7ff">
 	<TD colspan="2" align="left" width="20%">
 	<b><FONT face=Arial color="$text_color" size=2>User Demographic Data</FONT></b> </TD>
</tr>

<tr> <!-- Field: USR_NAME  ------------------------------ -->
	<td align="right" width="20%"><FONT color=#ff0000>&nbsp; </FONT>
	<FONT face=Arial, color=$text_color size=2 Serif San Helvetica,>First Name:</FONT></td>
	<td width="80%">
	<FONT face=Arial, size=2 Serif San Helvetica,>
	<INPUT maxLength=40 name=USR_FNAME></FONT></td>
</tr>

<tr> <!-- Field: USR_MI  -------------------------------- -->
	<td align="right" width="20%"><FONT color=$text_color>&nbsp; </FONT>
	<FONT face=Arial, color=$text_color size=2 Serif San Helvetica,> Middle Initial:</FONT></td>
	<td width="80%">
	<FONT face=Arial, size=2 Serif San Helvetica,>
	<INPUT maxLength=1 size=2 name=USR_MI></FONT> </td>
</tr>

<tr> <!-- Field: USR_LNAME  ----------------------------- -->
	<td align="right" width="20%"><FONT color=#ff0000>&nbsp; </FONT>
	<FONT face=Arial, color=$text_color size=2 Serif San Helvetica,>Last Name:</FONT></td>
	<td width="80%">
	<FONT face=Arial, size=2 Serif San Helvetica,><INPUT maxLength=40 name=USR_LNAME></FONT> </td>
</tr>

<tr> <!-- Field: USR_ADRS1  ----------------------------- -->
	<td align="right" width="20%"><FONT color=#ff0000>&nbsp; </FONT>
	<FONT face=Arial, color=$text_color size=2 Serif San Helvetica,>Home Address:</FONT></td>
	<TD width="80%">
	<FONT face=Arial, size=2 Serif San Helvetica,><INPUT maxLength=50 name=USR_ADRS1></FONT></TD>
</tr>

<TR> <!-- Field: USR_APT  ----------------------------- -->
	<TD align="right" width="20%"><FONT color=$text_color>&nbsp; </FONT>
	<FONT face=Arial, color=$text_color size=2 Serif San Helvetica,>Apt/Suite:</FONT></TD>
	<TD width="80%">
	<FONT face=Arial, size=2 Serif San Helvetica,><INPUT maxLength=50 size=7 name=USR_APT></FONT> </TD>
</TR>

<TR> <!-- Field: USR_CITY  ----------------------------- -->
	<TD align="right" width="20%"><FONT color=#ff0000>&nbsp; </FONT>
	<FONT face=Arial, color=$text_color size=2 Serif San Helvetica,>City:</FONT></TD>
	<TD width="80%">
	<FONT face=Arial, size=2 Serif San Helvetica,><INPUT maxLength=50 name=USR_CITY></FONT> </TD>
</TR>

end_of_html

	#===========================================================================
	# Get STATE from DBMS
	#===========================================================================
	print qq{<TR> <!-- Field: USR_STATE  ------------------------------- --> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	print qq{	<FONT color=#ff0000>&nbsp; </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>State/Province:</FONT></TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_STATE> \n } ;

	$sql = qq{select state, state_name from state_ref order by state } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="" SELECTED>Select a State/Province \n};
	while ( ($state, $state_name) = $sth->fetchrow_array() )
	{
		print qq{ <OPTION value="$state">$state_name \n };
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	print qq{<TR> <!-- Field: USR_ZIP  ----------------------------------------- -->  \n} ;
	print qq{	<TD align="right" width="20%"><FONT color=#ff0000>&nbsp; </FONT>  \n} ;
	print qq{	<FONT face=Arial color=$text_color size=2>Zip/Postal Code:</FONT></TD>  \n} ;
	print qq{	<TD width="80%"><FONT face=Arial size=2><INPUT maxLength=10 name=USR_ZIP></FONT></TD>  \n} ;
	print qq{</TR>  \n} ;

	#===========================================================================
	# Get COUNTRY from DBMS
	#===========================================================================
	print qq{<!-- Field: COUNTRY  ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	print qq{	<FONT color=#ff0000>&nbsp; </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Country:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_COUNTRY> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Country' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	#  print qq{ <OPTION value="" selected>Select a Country \n};
	while ( ($country_code, $country_name, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$country_code" SELECTED>$country_name \n };
			# print qq{ .OPTION value="$country_code" SELECTED.$country_name./option. };
		}
		else
		{
			print qq{ <OPTION value="$country_code">$country_name \n };
			# print qq{ .OPTION value="$country_code".$country_name./option. };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#---------------------------------------------------------------------------
	print << "end_of_html" ;

<TR> <!-- Field: PHONE  ------------------------------------------ -->
	<TD align="right" width="20%"><FONT color=$text_color>&nbsp; </FONT>
	<FONT face=Arial color=$text_color size=2>Phone:</FONT> </TD>
	<TD width="80%"> <FONT face=Arial size=3 color="$text_color"><b>
	( <input type="text" name="USR_AREACODE" size=3 maxlength=3> ) -
	<input type="text" name="USR_PHONE" size=8 maxlength=8>
	</FONT>
	</TD>
</TR>

<TR> <!-- Field: USR_EMAIL  -------------------------------------- -->
	<TD align="right" width="20%"> <FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>Email:</FONT> </TD>
	<TD width="80%"> <FONT face=Arial size=2>
	<INPUT type="text" maxLength=50 name=USR_EMAIL size="36"></FONT> </TD>
</TR>

end_of_html

	#===========================================================================
	# Get EMAIL TYPE from DBMS
	#===========================================================================
	print qq{<!-- Field: USR_EMAIL_TYPE  ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>I accept email as:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_EMAIL_TYPE> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Email Type' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	#  print qq{ <OPTION value="" selected>Select a Country \n};
	while ( ($email_code, $email_type, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$email_code" SELECTED>$email_type \n };
		}
		else
		{
			print qq{ <OPTION value="$email_code">$email_type \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#---------------------------------------------------------------------------
	print << "end_of_html" ;

<TR> <!-- Field: B_Month ------------------------------------- -->
	<TD noWrap align="right" width="20%"><FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>Date of Birth:</FONT> </TD>
	<TD width="80%"><FONT face=Arial size=2><NOBR>
	<SELECT size=1 name=B_Month>
		<OPTION value=0 selected>(month)
		<OPTION value=01>Jan
		<OPTION value=02>Feb
		<OPTION value=03>Mar
		<OPTION value=04>Apr
		<OPTION value=05>May
		<OPTION value=06>Jun
		<OPTION value=07>Jul
		<OPTION value=08>Aug
		<OPTION value=09>Sep
		<OPTION value=10>Oct
		<OPTION value=11>Nov
		<OPTION value=12>Dec
	</SELECT>

	<!-- Field: B_Day ------------------------------------- -->
	<SELECT size=1 name=B_Day>
		<OPTION value=0 selected>(day)
end_of_html

	#---------------------------------------------------------------------------
	# Loop from 1-31 (eg total Possible days for each month)
	#---------------------------------------------------------------------------
	for (my $i = 1; $i < 32; $i++)
	{
		if ( $i < 10 )
		{
			print qq{ <OPTION value=0$i>$i \n} ;
		}
		else
		{
			print qq{ <OPTION value=$i>$i \n} ;
		}
	}

	print << "end_of_html" ;

	</SELECT>
	</NOBR>19</FONT>

	<!-- Field: B_Decade ------------------------------------- -->
	<FONT face=Arial size=2>
    <SELECT size=1 name=B_Decade>
    	<OPTION value=0 selected>0
    	<OPTION value=1>1
    	<OPTION value=2>2
    	<OPTION value=3>3
    	<OPTION value=4>4
    	<OPTION value=5>5
    	<OPTION value=6>6
    	<OPTION value=7>7
    	<OPTION value=8>8
    	<OPTION value=9>9
	</SELECT>

	<!-- Field: B_Year ------------------------------------- -->
	<SELECT size=1 name=B_Year>
		<OPTION value=0 selected>0
		<OPTION value=1>1
		<OPTION value=2>2
		<OPTION value=3>3
		<OPTION value=4>4
		<OPTION value=5>5
		<OPTION value=6>6
		<OPTION value=7>7
		<OPTION value=8>8
		<OPTION value=9>9
	</SELECT>
    </FONT></TD>
</TR>


<tr><td colspan="2"><br><br></td></tr>
<TR bgcolor="#eff7ff">
 	<TD colspan="2" align="left" width="20%">
 	<b><FONT face=Arial color="$text_color" size=2>Account Information</FONT></b> </TD>
</tr>

<TR> <.-- Field: USERNAME  -------------------------------------- ..>
	<TD align="right" width="20%"> <FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>Sign-In Name:</FONT> </TD>
	<TD width="80%"><FONT face=Arial size=2>
	<INPUT maxLength=15 size=20 name=USR_USERNAME></FONT></td>
	</FONT>
	</TD>
</TR>

<TR> <!-- Field: PASSWORD  -------------------------------------- ..>
	<TD align="right" width="20%"> <FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>Password:</FONT> </TD>
	<TD width="80%"><FONT face=Arial size=2>
	<INPUT maxLength=15 size=20 name=USR_PASSWORD></FONT></td>
	</FONT>
	</TD>
</TR>

<TR> <!-- Field: PASSWORD_VERIFY  -------------------------------------- ..>
	<TD align="right" width="20%"> <FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>Re-Enter Password:</FONT> </TD>
	<TD width="80%"><FONT face=Arial size=2>
	<INPUT maxLength=15 size=20 name=USR_PASSWORD_VERIFY></FONT></td>
	</FONT>
	</TD>
</TR>
-----End of Commented out html -----------------------------------    -->

<tr><td colspan="2"><br><br></td></tr>

<TR bgcolor="#eff7ff">
 	<TD colspan="2" align="left" width="20%">
 	<b><FONT face=Arial color="$text_color" size=2>Additional Demographic Data</FONT></b> </TD>
</tr>

end_of_html

	#===========================================================================
	# Get MARITAL STATUS from DBMS
	#===========================================================================
	print qq{<!-- Field: MARITAL STATUS  ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Marital Status:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_MARTIAL_STATUS> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Marital Status' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="" selected>Select your Marital Status \n };
	while ( ($marital_code, $marital_status, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$marital_code" SELECTED>$marital_status \n };
		}
		else
		{
			print qq{ <OPTION value="$marital_code">$marital_status \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#===========================================================================
	# Get OCCUPATION from DBMS
	#===========================================================================
	print qq{<!-- Field: OCCUPATION ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Occupation:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_OCCUPATION> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Occupation' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="" selected>Select your Occupation \n };
	while ( ($occupation_code, $occupation, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$occupation_code" SELECTED>$occupation \n };
		}
		else
		{
			print qq{ <OPTION value="$occupation_code">$occupation \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#===========================================================================
	# Get JOB STATUS from DBMS
	#===========================================================================
	print qq{<!-- Field: JOB STATUS ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Job Status:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_JOB_STATUS> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Job Status' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="" selected>Select your Job Status \n };
	while ( ($job_code, $job_status, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$job_code" SELECTED>$job_status \n };
		}
		else
		{
			print qq{ <OPTION value="$job_code">$job_status \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#===========================================================================
	# Get HOUSEHOLD INCOME from DBMS
	#===========================================================================
	print qq{<!-- Field: HOUSEHOLD INCOME ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Household Income:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_HOUSEHOLD_INCOME> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Household Income' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="" selected>Select your Household Income \n };
	while ( ($income_code, $income, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$income_code" SELECTED>$income \n };
		}
		else
		{
			print qq{ <OPTION value="$income_code">$income \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#===========================================================================
	# Get EDUCATION LEVEL from DBMS
	#===========================================================================
	print qq{<!-- Field: EDUCATION LEVEL ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Education Level:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=USR_EDUCATION_LEVEL> \n } ;

	$sql = qq{select dem.demo_id, dem.description, dem.default_flag  from demo_ref dem, demo_cat cat } .
		qq{ where dem.cat_id = cat.cat_id and cat.category = 'Education Level' } .
		qq{ order by dem.disp_order, dem.description } ;
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="" selected>Select your Education Level \n };
	while ( ($education_code, $education, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$education_code" SELECTED>$education \n };
		}
		else
		{
			print qq{ <OPTION value="$education_code">$education \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	#---------------------------------------------------------------------------
	print << "end_of_html" ;

<TR>
	<TD align="right" width="20%"><SPACER TYPE="block" WIDTH="1" HEIGHT="1"><br><br></TD>
	<TD width="80%"></TD>
</TR>

<!--
<TR>
	<TD colspan="2" width="564">
	<p align="center"><input type="image" src="$images/next.gif" name="I2" width="75" height="28"></TD>
</TR>
-->

</table>
end_of_html

	#===========================================================================
	# Get LISTs from DBMS
	#===========================================================================
	print qq{<TABLE width="80%" border="0" cellspacing="3" cellpadding="0"> };
	print qq{<TR> <!-- Field: EMAIL_LIST_ID  ------------------------------- --> \n } ;
	print qq{	<TD align="right" width="50%"> \n } ;
	print qq{	<FONT color=#ff0000>&nbsp; </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Yes.  Please Sign me UP for the Email List:</FONT></TD> \n } ;
	print qq{	<TD width="50%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name="EMAIL_LIST_ID"> \n } ;

	$sql = qq{select list_id, list_name from list where status = 'A' and user_id = '$user_id' };
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	print qq{ <OPTION value="x" SELECTED>Select an Email List \n};
	while ( ($list_id, $list_name) = $sth->fetchrow_array() )
	{
		print qq{ <OPTION value="$list_id">$list_name \n };
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</FONT> </TD> \n } ;
	print qq{</TR> \n} ;

	print << "end_of_html" ;

<tr><td colspan="2" align=center>
<INPUT type="submit" name="BtnSignMeUp" value="Sign Me UP">
</td>
</tr>
</table>

<TABLE cellSpacing=0 cellPadding=0 width=470 border=0>
<TR>
	<TD align=middle width=644 colSpan=3>
	<p align="center">
	<FONT face=Arial, color=$text_color size=2><br></FONT>
	</p>
	<p align="center">&nbsp;</p>
	<p align="center">&nbsp;</p>
	</TD>
</TR>
<tr>
	<td align="middle"><br><br></td>
</tr>
</TABLE>

</FORM>
</BODY>
</HTML>
end_of_html
