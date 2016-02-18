<form id="clientForm" ng-init="client.loadAutoComplete()">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Client Settings</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label>Status</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.status" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.status = 'A'" ng-class="{ active : client.current.status == 'A' }">Active</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.status = 'D'" ng-class="{ active : client.current.status != 'A' }">Inactive</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Global Suppression</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.check_global_suppression" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_global_suppression = 'Y'" ng-class="{ active : client.current.check_global_suppression == 'Y' }">On</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_global_suppression = 'N'" ng-class="{ active : client.current.check_global_suppression != 'Y' }">Off</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Group Restriction</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.has_client_group_restriction" />
                    <input type="hidden" ng-model="client.current.client_has_client_group_restrictions" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.has_client_group_restriction = 1; client.current.client_has_client_group_restrictions = 1;" ng-class="{ active : client.current.has_client_group_restriction == 1 }">Yes</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.has_client_group_restriction = 0; client.current.client_has_client_group_restrictions = 0;" ng-class="{ active : client.current.has_client_group_restriction != 1 }">No</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Filter By Historical OC</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.check_previous_oc" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_previous_oc = 1" ng-class="{ active : client.current.check_previous_oc == 1 }">Yes</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_previous_oc = 0" ng-class="{ active : client.current.check_previous_oc != 1 }">No</button>
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
                <input type="text" class="form-control" id="contact" value="" placeholder="Main Contact" required="required" ng-model="client.current.client_main_name" />
            </div>

            <div class="form-group">
                <input type="email" class="form-control" id="email" value="" placeholder="Email" required="required" ng-model="client.current.email_addr" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="username" value="" placeholder="Client Name" required="required" ng-model="client.current.username" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" value="" placeholder="Password" required="required" ng-model="client.current.password" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="address" value="" placeholder="Address" required="required" ng-model="client.current.address" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="address2" value="" placeholder="Apt/Suite" ng-model="client.current.address2" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="city" value="" placeholder="City" required="required" ng-model="client.current.city" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="state" value="" placeholder="State" maxlength="2" required="required" ng-model="client.current.state" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="zip" value="" placeholder="Zip" required="required" ng-model="client.current.zip" />
            </div>

            <div class="form-group">
                <input type="tel" class="form-control" id="phone" value="" placeholder="Phone" required="required" ng-model="client.current.phone" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="network" value="" placeholder="Network" required="required" ng-model="client.current.network" />
            </div>

            <div class="form-group">
                <md-autocomplete
                    md-search-text="client.typeSearchText"
                    md-items="item in client.getClientType( client.typeSearchText )"
                    md-item-text="item.value"
                    md-selected-item-change="client.setClientType( item )"
                    placeholder="Choose a Client Type"
                    layout="column"
                    ng-model="client.current.client_type"
                    ng-cloak>

                    <md-item-template>
                        <span md-highlight-text="client.typeSearchText" md-highlight-flags="^i">@{{item.value}}</span>
                    </md-item-template>

                    <md-not-found>
                        No Client Types matching "@{{client.typeSearchText}}" were found.
                    </md-not-found>
                </md-autocomplete>

                <!-- <select class="form-control" id="type" required="required" ng-model="client.current.client_type">
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
                </select> -->
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">FTP Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <input type="text" class="form-control" id="ftp_url" value="" placeholder="FTP URL" required="required" ng-model="client.current.ftp_url" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="ftp_user" value="" placeholder="FTP User" required="required" ng-model="client.current.ftp_user" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="ftp_password" value="" placeholder="FTP Password" ng-model="client.current.ftp_pw" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="ftp_realtime_password" value="" placeholder="FTP Realtime Password" ng-model="client.current.rt_pw" />
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Tracking</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <input type="text" class="form-control" id="subaffiliate" placeholder="Cake Sub Affiliate ID" ng-model="client.current.cake_sub_id" />
            </div>

            <div class="form-group">
                <md-autocomplete
                    md-search-text="client.ownerSearchText"
                    md-items="item in client.getListOwners( client.ownerSearchText )"
                    md-item-text="item.value"
                    md-selected-item-change="client.setListOwner( item )"
                    placeholder="Choose a List Owner"
                    layout="column"
                    ng-model="client.current.list_owner"
                    ng-cloak>

                    <md-item-template>
                        <span md-highlight-text="client.ownerSearchText" md-highlight-flags="^i">@{{item.value}}</span>
                    </md-item-template>

                    <md-not-found>
                        No Client Types matching "@{{client.ownerSearchText}}" were found.
                    </md-not-found>
                </md-autocomplete>

                <!--
                <select class="form-control" id="type" required="required" ng-model="client.current.list_owner">
                    <option value="">Choose List Owner</option>
                    <option value="247lifecover">247LifeCover</option>
                    <option value="2boxmedia">2BoxMedia</option>
                    <option value="404llc">404LLC</option>
                    <option value="4usapartial">4USAPartial</option>
                    <option value="6speedmedia">6SpeedMedia</option>
                    <option value="7reach">7Reach</option>
                    <option value="800west">800west</option>
                    <option value="a&jmarketing">A&amp;JMarketing</option>
                    <option value="absoluteroi">AbsoluteROI</option>
                    <option value="accelerex">Accelerex</option>
                    <option value="accmg">ACCMG</option>
                    <option value="aclarn">Aclarn</option>
                    <option value="acquinity">Acquinity</option>
                    <option value="acquis">Acquis</option>
                    <option value="actualsales">ActualSales</option>
                    <option value="acxiom">Acxiom</option>
                    <option value="adbender">AdBender</option>
                    <option value="adbrilliant">AdBrilliant</option>
                    <option value="adchemy">Adchemy</option>
                    <option value="adgate">AdGate</option>
                    <option value="adgenics">Adgenics</option>
                    <option value="adknowledge">Adknowledge</option>
                    <option value="adlinkr">AdLinkr</option>
                    <option value="admarketers">Admarketers</option>
                    <option value="admediary">Admediary</option>
                    <option value="adone">AdOne</option>
                    <option value="adteractive">Adteractive</option>
                    <option value="adxdirect">ADXDirect</option>
                    <option value="aeg media">AEG Media</option>
                    <option value="affnet">affnet</option>
                    <option value="aluremedia">AlureMedia</option>
                    <option value="andrewswharton">AndrewsWharton</option>
                    <option value="antevenio">Antevenio</option>
                    <option value="apollo">Apollo</option>
                    <option value="aspectweb">AspectWeb</option>
                    <option value="atrinsic">Atrinsic</option>
                    <option value="avenuelink">AvenueLink</option>
                    <option value="avrick">Avrick</option>
                    <option value="barons media">barons media</option>
                    <option value="baronsmedia">BaronsMedia</option>
                    <option value="bcd">BCD</option>
                    <option value="bebo">Bebo</option>
                    <option value="bhd media">BHD Media</option>
                    <option value="bigpayout">BigPayout</option>
                    <option value="blacksmith">BlackSmith</option>
                    <option value="bloosky">Bloosky</option>
                    <option value="bluebean">Bluebean</option>
                    <option value="blueglobal">BlueGlobal</option>
                    <option value="bluekeel">BlueKeel</option>
                    <option value="bluerhino">BlueRhino</option>
                    <option value="bluesky">BlueSky</option>
                    <option value="bmmiuk">BMMIUK</option>
                    <option value="brightfire">BrightFire</option>
                    <option value="broadbase">BroadBase</option>
                    <option value="bt marketing">BT Marketing</option>
                    <option value="cabonetworks">CaboNetworks</option>
                    <option value="campusexplorer">CampusExplorer</option>
                    <option value="capdesicion">Capdesicion</option>
                    <option value="casino rewards">Casino Rewards</option>
                    <option value="castline">Castline</option>
                    <option value="channelclarity">ChannelClarity</option>
                    <option value="clashny">ClashNY</option>
                    <option value="clashuk">ClashUK</option>
                    <option value="cleervoyance">Cleervoyance</option>
                    <option value="clench media">clench media</option>
                    <option value="clicklabs">ClickLabs</option>
                    <option value="clickmedia">ClickMedia</option>
                    <option value="clickofficial">ClickOfficial</option>
                    <option value="clicktronmedia">ClicktronMedia</option>
                    <option value="cliqventures">CliqVentures</option>
                    <option value="communicationav">CommunicationAv</option>
                    <option value="consolidata">Consolidata</option>
                    <option value="consumertrack">ConsumerTrack</option>
                    <option value="consumertrak">ConsumerTrak</option>
                    <option value="converzemedia">ConverzeMedia</option>
                    <option value="coredigital">CoreDigital</option>
                    <option value="coverclicks">CoverClicks</option>
                    <option value="cpm">CPM</option>
                    <option value="credtech">CredTech</option>
                    <option value="crosspond">CrossPond</option>
                    <option value="cubed media">Cubed Media</option>
                    <option value="dam">DAM</option>
                    <option value="dashmarketing">DashMarketing</option>
                    <option value="data monetizers">Data Monetizers</option>
                    <option value="datahouseuk">DatahouseUK</option>
                    <option value="datalot">Datalot</option>
                    <option value="datastream">Datastream</option>
                    <option value="datatize">Datatize</option>
                    <option value="datatrada">DataTrada</option>
                    <option value="desktopadvertis">DesktopAdvertis</option>
                    <option value="diablomedia">DiabloMedia</option>
                    <option value="digitalboxuk">DigitalBoxUK</option>
                    <option value="digitech">Digitech</option>
                    <option value="dima">DIMA</option>
                    <option value="directmarket">DirectMarket</option>
                    <option value="diy">DIY</option>
                    <option value="dld">DLD</option>
                    <option value="dmc">DMC</option>
                    <option value="dmedia">DMedia</option>
                    <option value="dmoffers">DMOffers</option>
                    <option value="dms">DMS</option>
                    <option value="dnrmarketing">DNRMarketing</option>
                    <option value="domaindevelopme">DomainDevelopme</option>
                    <option value="dreamdirect">DreamDirect</option>
                    <option value="dreamleadintera">DreamLeadIntera</option>
                    <option value="dzmedia">DZMedia</option>
                    <option value="easyvoyage">EasyVoyage</option>
                    <option value="ecain">Ecain</option>
                    <option value="edebit">eDebit</option>
                    <option value="edemographic">Edemographic</option>
                    <option value="edemographics">eDemographics</option>
                    <option value="elevate media">Elevate Media</option>
                    <option value="elitemate">elitemate</option>
                    <option value="elitetraffic">EliteTraffic</option>
                    <option value="emailmovers">Emailmovers</option>
                    <option value="eniti media">Eniti Media</option>
                    <option value="entiremediacons">EntireMediaCons</option>
                    <option value="eserve">Eserve</option>
                    <option value="euricon">Euricon</option>
                    <option value="evaniade">EvaniaDE</option>
                    <option value="femtocore">Femtocore</option>
                    <option value="firstsource">FirstSource</option>
                    <option value="flatiron">Flatiron</option>
                    <option value="flinteractive">FLInteractive</option>
                    <option value="flt">FLT</option>
                    <option value="freemax">Freemax</option>
                    <option value="gcn">GCN</option>
                    <option value="gcpayday">GCPayday</option>
                    <option value="genesis">Genesis</option>
                    <option value="gmbdirect">gmbdirect</option>
                    <option value="gofirstmedia">GoFirstMedia</option>
                    <option value="golivemobile">GoLiveMobile</option>
                    <option value="greatamericanphoto">GreatAmericanPhoto</option>
                    <option value="groupone">GroupOne</option>
                    <option value="gvg holdings">GVG Holdings</option>
                    <option value="hb">HB</option>
                    <option value="hearst">Hearst</option>
                    <option value="helios">Helios</option>
                    <option value="helpforrenters">HelpforRenters</option>
                    <option value="hilife">Hilife</option>
                    <option value="horizonlist">horizonlist</option>
                    <option value="hylite">Hylite</option>
                    <option value="hypercross">HyperCross</option>
                    <option value="icsde">ICSDE</option>
                    <option value="idealoffers">IdealOffers</option>
                    <option value="idebt">iDebt</option>
                    <option value="idesktop">iDesktop</option>
                    <option value="ihost offers">iHost Offers</option>
                    <option value="ihost worldwide">iHost Worldwide</option>
                    <option value="ilacreations">Ilacreations</option>
                    <option value="impilo">Impilo</option>
                    <option value="indata">Indata</option>
                    <option value="intela">Intela</option>
                    <option value="interactive ms">Interactive MS</option>
                    <option value="intermedia">Intermedia</option>
                    <option value="internal">Internal</option>
                    <option value="jaak">jaak</option>
                    <option value="jbr">JBR</option>
                    <option value="jll">JLL</option>
                    <option value="joe ruiz">Joe Ruiz</option>
                    <option value="johnstaub">JohnStaub</option>
                    <option value="justus mgmt">JustUs Mgmt</option>
                    <option value="kgmdirect">kgmdirect</option>
                    <option value="kracow">Kracow</option>
                    <option value="kroll">Kroll</option>
                    <option value="lead5media">Lead5Media</option>
                    <option value="leadclick">LeadClick</option>
                    <option value="leadid">LeadID</option>
                    <option value="leadnomics">Leadnomics</option>
                    <option value="leadrevolution">LeadRevolution</option>
                    <option value="legacyrevival">LegacyRevival</option>
                    <option value="lifescript">LifeScript</option>
                    <option value="localstaffing">LocalStaffing</option>
                    <option value="londonbridge">LondonBridge</option>
                    <option value="lowdown media">LowDown Media</option>
                    <option value="lunasol">LunaSol</option>
                    <option value="malvern">Malvern</option>
                    <option value="mapnation">MapNation</option>
                    <option value="margait">Margait</option>
                    <option value="marketingpunch">MarketingPunch</option>
                    <option value="marquee">Marquee</option>
                    <option value="mccracyde">McCracyDE</option>
                    <option value="mdeg">MDEG</option>
                    <option value="mdegeurolink">MDEGEurolink</option>
                    <option value="mediabug">MediaBug</option>
                    <option value="mediabulldogs">MediaBulldogs</option>
                    <option value="mediaking">MediaKing</option>
                    <option value="miamiwebstaff">MiamiWebStaff</option>
                    <option value="milehigh">MileHigh</option>
                    <option value="mixxit">Mixxit</option>
                    <option value="monetizecam">MonetizeCAM</option>
                    <option value="monetizeit">MonetizeIt</option>
                    <option value="monetizenetwork">MonetizeNetwork</option>
                    <option value="monetizeztr">MonetizeZTR</option>
                    <option value="mundo">Mundo</option>
                    <option value="mvdatacorp2">MVDataCorp2</option>
                    <option value="myfreescorenow">MyFreeScoreNow</option>
                    <option value="netblue">netblue</option>
                    <option value="networkmedia">NetworkMedia</option>
                    <option value="neutroninteract">NeutronInteract</option>
                    <option value="newenglandmarke">NewEnglandMarke</option>
                    <option value="noc">NOC</option>
                    <option value="nocsolutions">NOCSolutions</option>
                    <option value="nuclearmarketin">NuclearMarketin</option>
                    <option value="nutraclick">NutraClick</option>
                    <option value="ocatest">OCAtest</option>
                    <option value="ondemandresearch">OnDemandResearch</option>
                    <option value="oneonone">oneonone</option>
                    <option value="onescreen">OneScreen</option>
                    <option value="opulead">OpuLead</option>
                    <option value="orange">Orange</option>
                    <option value="pars">Pars</option>
                    <option value="percipio">Percipio</option>
                    <option value="peterday">PeterDay</option>
                    <option value="planet49">Planet49</option>
                    <option value="popularllc">PopularLLC</option>
                    <option value="popularmarketing">PopularMarketing</option>
                    <option value="positiveid">PositiveID</option>
                    <option value="preciseleads">PreciseLeads</option>
                    <option value="premierbank">premierbank</option>
                    <option value="pureflow">PureFlow</option>
                    <option value="purematch">PureMatch</option>
                    <option value="puremure">PureMure</option>
                    <option value="qapply">Qapply</option>
                    <option value="qatalystmedia">QatalystMedia</option>
                    <option value="quantum">Quantum</option>
                    <option value="quotewizard">QuoteWizard</option>
                    <option value="ratemarketplace">RateMarketPlace</option>
                    <option value="reachmedia">ReachMedia</option>
                    <option value="reachmg">ReachMG</option>
                    <option value="reactivmedia">ReactivMedia</option>
                    <option value="recessnetworks">RecessNetworks</option>
                    <option value="red3i">Red3i</option>
                    <option value="reddoor7media">RedDoor7Media</option>
                    <option value="reichweite2gmbh">Reichweite2GmbH</option>
                    <option value="rel">REL</option>
                    <option value="reliantuk">ReliantUK</option>
                    <option value="renegade">Renegade</option>
                    <option value="resumebucket group llc">ResumeBucket Group LLC</option>
                    <option value="revenuegenerati">RevenueGenerati</option>
                    <option value="revenuegrp">RevenueGrp</option>
                    <option value="revimedia">ReviMedia</option>
                    <option value="scalablecommerc">ScalableCommerc</option>
                    <option value="silverback">SilverBack</option>
                    <option value="silvertap">SilverTap</option>
                    <option value="sktmarketing">SKTMarketing</option>
                    <option value="skyrocket">SkyRocket</option>
                    <option value="sma">SMA</option>
                    <option value="sobinetwork">SobiNetwork</option>
                    <option value="sovinetwork">SoviNetwork</option>
                    <option value="spark revenue llc">Spark Revenue LLC</option>
                    <option value="steficom">Steficom</option>
                    <option value="sterlinginteractive">sterlinginteractive</option>
                    <option value="stirista">Stirista</option>
                    <option value="stonelotus">StoneLotus</option>
                    <option value="subscriberbase">Subscriberbase</option>
                    <option value="summitbreakers">SummitBreakers</option>
                    <option value="surefiremg">SureFireMG</option>
                    <option value="sus">SUS</option>
                    <option value="t3media">T3Media</option>
                    <option value="tactara">Tactara</option>
                    <option value="targetedrespons">TargetedRespons</option>
                    <option value="targetrealmedia">TargetRealMedia</option>
                    <option value="tazomedia">TazoMedia</option>
                    <option value="terramatrix">TerraMatrix</option>
                    <option value="tesekkurler">Tesekkurler</option>
                    <option value="thebridgecorp">TheBridgeCorp</option>
                    <option value="thedatapartners">TheDataPartners</option>
                    <option value="themediacrew">TheMediaCrew</option>
                    <option value="thetradingfloor">TheTradingFloor</option>
                    <option value="thrive">Thrive</option>
                    <option value="tmlsolutions">TMLSolutions</option>
                    <option value="tms">TMS</option>
                    <option value="torchlight tech">Torchlight Tech</option>
                    <option value="tordyjack">TordyJack</option>
                    <option value="tpl">TPL</option>
                    <option value="tributemedia">TributeMedia</option>
                    <option value="tym">TYM</option>
                    <option value="tymax">Tymax</option>
                    <option value="tymaxresp">TymaxResp</option>
                    <option value="uniqueleads">UniqueLeads</option>
                    <option value="universalmarket">UniversalMarket</option>
                    <option value="v">V</option>
                    <option value="v12group">V12Group</option>
                    <option value="venicedata">VeniceData</option>
                    <option value="vertizinc">VertizINC</option>
                    <option value="vhmnetworks">VHMNetworks</option>
                    <option value="vinyl">Vinyl</option>
                    <option value="virtumundo">Virtumundo</option>
                    <option value="vitalintel">VitalIntel</option>
                    <option value="voltron">Voltron</option>
                    <option value="vyped">Vyped</option>
                    <option value="wage">Wage</option>
                    <option value="ward">Ward</option>
                    <option value="webclients">WebClients</option>
                    <option value="whatifholdings">WhatIfHoldings</option>
                    <option value="whitecollarmedi">WhiteCollarMedi</option>
                    <option value="ya solutions">YA Solutions</option>
                    <option value="yhmg">YHMG</option>
                    <option value="youbeauty">YouBeauty</option>
                    <option value="zayne">Zayne</option>
                    <option value="zeeto">Zeeto</option>
                    <option value="zinq media">ZinQ Media</option>
                </select>
                -->
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="client_record_source_url" value="" placeholder="Source URL" ng-model="client.current.client_record_source_url" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="source_ip" value="" placeholder="Source IP" ng-model="client.current.client_record_ip" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="record_date" value="" placeholder="Minimum Record Date" ng-model="client.current.minimum_acceptable_record_date" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="country_id" value="" placeholder="Country ID" ng-model="client.current.country_id" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="record_date" value="" placeholder="Minimum Record Date" ng-model="client.current.minimum_acceptable_record_date" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="country_id" value="" placeholder="Country ID" ng-model="client.current.country_id" />
            </div>
        </div>
    </div>
</form>
