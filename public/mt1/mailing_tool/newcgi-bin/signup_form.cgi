#!/usr/bin/perl
#===============================================================================
# File   : signup_form.cgi
#
# History
# Grady Nash, 8/22/01  Created.
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
my ($bgcolor, $list_name, $status);
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
my ($show_first_name, $show_last_name, $show_address, $show_city, $show_state,
    $show_zip, $show_country, $show_phone, $show_gender, $show_marital_status,
    $show_occupation, $show_income, $show_education, $show_job_status);
my $clist_id;
my $bin_dir_http;
my $images = $util->get_images_url;
my $company;

$text_color = "#000000" ;        # color = Black
$bg_color = "#ffffff" ;          # color = White
	
#------------------------------
# connect to the util database
#------------------------------
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

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

# get some parameters from sysparm

$sql = "select parmval from sysparm where parmkey = 'BIN_DIR_HTTP'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($bin_dir_http) = $sth->fetchrow_array();
$sth->finish();

# get this clients company name

$sql = "select company from user where user_id = $user_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($company) = $sth->fetchrow_array();
$sth->finish();

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

# get this lists name

$sql = "select list_name from list where list_id = $clist_id";
$sth = $dbhq->prepare($sql);
$sth->execute();
($list_name) = $sth->fetchrow_array();
$sth->finish();

print "Content-Type: text/html\n\n";
print << "end_of_html" ;
<HTML>
<HEAD>
<TITLE>Signup Form</TITLE>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
<SCRIPT language=JAVASCRIPT>
function checkForm(form)
{
	//----------------------------------------------------------------------
    // Check EMail
	//----------------------------------------------------------------------
    // first check for basic format of email with an @ and split the email up
    var emailPat = /^(.+)@(.+)\$/ ;
    var emailStr = form.email.value;
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
    if ((form.B_Month) &&
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
            return false;
        }
		else
		{
			// put birthday in hidden field
			form.bday.value = year + "-" + form.B_Month.selectedIndex + "-" + day;
		}
    }
    // if you get here, then all fields filled out.
    return true;
}
</SCRIPT>
</head>
<BODY vLink="#FF8000" aLink="#FF8040" link="#FF8040" bgColor="$bg_color" text="$text_color">
<FORM onSubmit="return checkForm(this);" action="${bin_dir_http}signup_add.cgi" method="post">
<input type="hidden" name="list_id" value="$clist_id">
<input type="hidden" name="bday">

<TABLE width="80%" border="0" cellspacing="3" cellpadding="0">
<tr bgcolor="#dbeaf5" > 
<td colspan="2" width="80%" align="center">
	<font face="Arial" size="4" color="$text_color"><b>
	$company<br><br>$list_name Signup Form</b></font></td>
</tr>
<tr>
<td colspan="2" width="80%"><FONT face="Helvetica, Arial" color=$text_color size=2>( </FONT>
	<FONT color=#ff0000>*</FONT>
	<FONT face="Helvetica, Arial" color=$text_color size=2> ) <I>Indicates required items</I></FONT>
	<br><br></td>
</tr>
end_of_html

if ($show_first_name eq "Y") 
{
	print qq { <tr> 
		<td align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; 
			First Name:</font></td>
		<td width="80%"><INPUT type=text size=40 maxLength=20 name=first_name></td>
		</tr> \n };
}

if ($show_last_name eq "Y")
{
	print qq { <tr> 
		<td align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; 
		Last Name:</font></td>
		<td width="80%"><INPUT type=text size=40 maxLength=40 name=last_name></td>
		</tr> \n };
}

if ($show_address eq "Y")
{
	print qq { <tr> 
		<td align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; 
		Address:</font></td>
		<TD width="80%"><INPUT size=40 maxLength=50 name=address></TD>
		</tr> \n 
		<TR> 
		<TD align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; 
		Apt/Suite:</FONT></TD>
		<TD width="80%"><INPUT type=text size=40 maxLength=50 name=address2></TD>
		</TR> \n };
}

if ($show_city eq "Y")
{
	print qq { <TR> 
		<TD align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp;
		City:</FONT></TD>
		<TD width="80%"><INPUT type=text size=40 maxLength=50 name=city></FONT> </TD>
		</TR> \n };
}

if ($show_state eq "Y")
{
	#===========================================================================
	# Get STATE from DBMS
	#===========================================================================
	print qq {<TR> \n } ;
	print qq {	<TD align="right" width="20%"> \n } ;
	print qq {	<FONT face=Arial color=$text_color size=2>&nbsp; \n } ;
	print qq {	State/Province:</FONT></TD> \n } ;
	print qq {	<TD width="80%">\n } ;
	print qq {	<SELECT name=state> \n } ;
	print qq {   <OPTION value="" SELECTED>Select a State/Province \n};
 
	$sql = "select state, state_name from state_ref order by state";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	while ( ($state, $state_name) = $sth->fetchrow_array() )
	{
		print qq { <OPTION value="$state">$state_name \n };
	}
	$sth->finish();
	print qq {</SELECT> \n } ;
	print qq {</TD> \n} ;
}

if ($show_zip eq "Y")
{
	print qq{<TR> \n} ;
	print qq{	<TD align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; \n} ;
	print qq{	Zip Code:</FONT></TD>  \n} ;
	print qq{	<TD width="80%"><INPUT type=text maxLength=10 name=zip></TD>  \n} ;
	print qq{</TR>  \n} ;
}

if ($show_country eq "Y")
{
	#===========================================================================
	# Get COUNTRY from DBMS
	#===========================================================================
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; };
	print qq{	Country:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> \n } ;
	print qq{	<SELECT name=country> \n } ;
	print qq{   <OPTION value="" selected>Select a Country \n};

	$sql = "select dem.demo_id, dem.description, dem.default_flag  
		from demo_ref dem, demo_cat cat 
		where dem.cat_id = cat.cat_id and cat.category = 'Country'
		order by dem.disp_order, dem.description";
	$sth = $dbhq->prepare($sql) ;
	$sth->execute();
	while ( ($country_code, $country_name, $default_flag) = $sth->fetchrow_array() )
	{
		if ( $default_flag eq "Y" )
		{
			print qq{ <OPTION value="$country_code" SELECTED>$country_name \n };
		}
		else
		{
			print qq{ <OPTION value="$country_code">$country_name \n };
		}
	}
	$sth->finish();
	print qq{</SELECT> \n } ;
	print qq{</TD> \n } ;
	print qq{</TR> \n} ;
}

if ($show_phone eq "Y")
{
	print qq { <TR> 
		<TD align="right" width="20%"><FONT face=Arial color=$text_color size=2>&nbsp; 
		Phone:</FONT> </TD>
		<TD width="80%"> 
		( <input type="text" name="areacode" size=3 maxlength=3> ) -
		<input type="text" name="phone" size=8 maxlength=8>
		</TD></TR> \n };
}

print << "end_of_html" ;
<TR>
<TD align="right" width="20%"> <FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>
	Email:</FONT> </TD>
<TD width="80%"> <INPUT type="text" maxLength=50 name=email size="36"></TD>
</TR>
<TR>
<TD align="right" width="20%">
	<FONT color=#ff0000>* </FONT>
	<FONT face=Arial color=$text_color size=2>I accept email as:</FONT> </TD>
	<TD width="80%">
	<SELECT name=emailtype>
		<OPTION value="H" SELECTED>HTML
		<OPTION value="T">Text
		<OPTION value="A">AOL
		<OPTION value="D">Don't Know
	</SELECT>
	</TD>
</TR>
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
end_of_html

if ($show_marital_status eq "Y")
{
	#===========================================================================
	# Get MARITAL STATUS from DBMS
	#===========================================================================
	print qq{<!-- Field: MARITAL STATUS  ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Marital Status:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=marital> \n } ;

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
}

if ($show_occupation eq "Y")
{
	#===========================================================================
	# Get OCCUPATION from DBMS
	#===========================================================================
	print qq{<!-- Field: OCCUPATION ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Occupation:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=occupation> \n } ;

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
}

if ($show_job_status eq "Y")
{
	#===========================================================================
	# Get JOB STATUS from DBMS
	#===========================================================================
	print qq{<!-- Field: JOB STATUS ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Job Status:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=job> \n } ;

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
}

if ($show_income eq "Y")
{
	#===========================================================================
	# Get HOUSEHOLD INCOME from DBMS
	#===========================================================================
	print qq{<!-- Field: HOUSEHOLD INCOME ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Household Income:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=income> \n } ;

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
}

if ($show_education eq "Y")
{
	#===========================================================================
	# Get EDUCATION LEVEL from DBMS
	#===========================================================================
	print qq{<!-- Field: EDUCATION LEVEL ------------------------------------ --> \n } ;
	print qq{<TR> \n } ;
	print qq{	<TD align="right" width="20%"> \n } ;
	# print qq{	<FONT color=#ff0000>* </FONT> \n } ;
	print qq{	<FONT face=Arial color=$text_color size=2>Education Level:</FONT> </TD> \n } ;
	print qq{	<TD width="80%"> <FONT face=Arial size=2> \n } ;
	print qq{	<SELECT name=education> \n } ;

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
}

print << "end_of_html" ;
<TR>
	<TD align="right" width="20%"><SPACER TYPE="block" WIDTH="1" HEIGHT="1"><br><br></TD>
	<TD width="80%"></TD>
</TR>
<tr>
<td colspan="2" width="80%" align="center">
<INPUT type="submit" value="Sign Me Up">
</td>
</tr>
</table>
</FORM>
</BODY>
</HTML>
end_of_html

$util->clean_up();
exit(0);

