
@extends( 'layout.default' )

@section( 'title' , 'Edit Client' )

@section( 'navClientClasses' , 'active' )


@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Edit Client</h1></div>
</div>

<div ng-controller="ClientController as client" ng-init="client.loadClient()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <form>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Client Settings</h3>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label>Global Suppression</label>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default">On</button>
                                </div>

                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default">Off</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Group Restriction</label>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default">Yes</button>
                                </div>

                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default">No</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Filter By Historical OC</label>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default">Yes</button>
                                </div>

                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-default">No</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Target Record Date</label>
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group">
                                    <input type="date" class="form-control" value="" required="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Client Information</h3>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="contact" value="" placeholder="Main Contact" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="email" class="form-control" id="email" value="" placeholder="Email" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="username" value="" placeholder="Username" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control" id="password" value="" placeholder="Password" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="address" value="" placeholder="Address" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="city" value="" placeholder="City" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="state" value="" placeholder="State" maxlength="2" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="number" class="form-control" id="zip" value="" placeholder="Zip" required="required" />
                        </div>

                        <div class="form-group">
                            <select class="form-control" id="country" required="required">
                                <option value="">Country</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="tel" class="form-control" id="phone" value="" placeholder="Phone" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="network" value="" placeholder="Network" required="required" />
                        </div>

                        <div class="form-group">
                            <select class="form-control" id="type" required="required">
                                <option value="">Client Type</option>
                                <option value="AUS">AUS</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Assistance">Assistance</option>
                                <option value="Astrology">Astrology</option>
                                <option value="Auction">Auction</option>
                                <option value="Auto">Auto</option>
                                <option value="AutoResponder">AutoResponder</option>
                                <option value="B2B">B2B</option>
                                <option value="BizOp">BizOp</option>
                                <option value="Brazil">Brazil</option>
                                <option value="CPM">CPM</option>
                                <option value="Call Center">Call Center</option>
                                <option value="Canadian">Canadian</option>
                                <option value="Cash Loan">Cash Loan</option>
                                <option value="Cleaning">Cleaning</option>
                                <option value="Coupon">Coupon</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Credit Score">Credit Score</option>
                                <option value="DONOTMAIL">DONOTMAIL</option>
                                <option value="DailyDeal">DailyDeal</option>
                                <option value="Data Test">Data Test</option>
                                <option value="Dating">Dating</option>
                                <option value="ESP">ESP</option>
                                <option value="Education">Education</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Financial/Debt">Financial/Debt</option>
                                <option value="French">French</option>
                                <option value="Gambling">Gambling</option>
                                <option value="Gaming">Gaming</option>
                                <option value="German">German</option>
                                <option value="Grant">Grant</option>
                                <option value="Greek">Greek</option>
                                <option value="Health">Health</option>
                                <option value="Hispanic">Hispanic</option>
                                <option value="Home Biz">Home Biz</option>
                                <option value="Home Goods">Home Goods</option>
                                <option value="India">India</option>
                                <option value="Insurance">Insurance</option>
                                <option value="Internal Non-Mailable">Internal Non-Mailable</option>
                                <option value="Internal Sites">Internal Sites</option>
                                <option value="Italy">Italy</option>
                                <option value="Job">Job</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Misc">Misc</option>
                                <option value="Mortgage">Mortgage</option>
                                <option value="Movies">Movies</option>
                                <option value="Netherlands">Netherlands</option>
                                <option value="Offers">Offers</option>
                                <option value="Promo">Promo</option>
                                <option value="Quiz">Quiz</option>
                                <option value="Reg Path">Reg Path</option>
                                <option value="Responders">Responders</option>
                                <option value="Retail">Retail</option>
                                <option value="Senior">Senior</option>
                                <option value="Social">Social</option>
                                <option value="Spain">Spain</option>
                                <option value="Sports">Sports</option>
                                <option value="Sublists">Sublists</option>
                                <option value="Survey">Survey</option>
                                <option value="Sweeps">Sweeps</option>
                                <option value="Travel">Travel</option>
                                <option value="Turkey">Turkey</option>
                                <option value="UK">UK</option>
                                <option value="Weather">Weather</option>
                                <option value="eCards">eCards</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">FTP Information</h3>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <input type="text" class="form-control" id="ftp_url" value="" placeholder="FTP URL" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="ftp_user" value="" placeholder="FTP User" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="ftp_password" value="" placeholder="FTP Pasword" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="ftp_realtime_password" value="" placeholder="FTP Realtime Password" required="required" />
                        </div>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Tracking</h3>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <input type="number" id="subaffiliate" class="form-control" placeholder="Cake Sub Affiliate ID" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="url" class="form-control" id="source_url" value="" placeholder="Source URL" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="source_ip" value="" placeholder="Source IP" required="required" />
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="profile_id" value="" placeholder="Unique Profile ID" required="required" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
