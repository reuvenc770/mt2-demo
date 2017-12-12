# Content Management Platform (CMP)
----

## <a name="user-documentation"></a> User Documenation




### <a name="page-reference"></a> Page Reference


#### <a name="clients"></a> Clients
----
Clients are the individual companies that provide user record data for mailing. You can find information like their address, current status, and contact info.

A client can have multiple feeds that provide record data from multiple sources. An example of this is a client with multiple content sites. They'll provide records from both, using seperate feeds to show the source of the record data.

There are two types of feeds a client can have: 1st party feeds and 3rd party feeds. Sometimes a client will have a mix of both types of feeds but often it is just one type.

[Client Index][ClientPage]


#### <a name="feeds"></a> Feeds
----
Feeds are a single source of record data. You can find which client the feed belongs to, any ftp information such as password, the type of feed, and the referring source URL.

If the client is using the API, then they need to use their feed password when posting the record. If the client is using a Batch File, then they need to map columns to data fields and upload the file to the FTP server. You can map the columns from the index page.

There are two types of feeds: 1st party feeds and 3rd party feeds. A first party feed can be used to send out campaigns using the client's brand. A third party feed can be used to send out campaigns for internal offers.

[Feed Index][FeedPage]


#### <a name="feed-groups"></a> Feed Groups
----
Feed Group allow you to group feeds that don't belong to the same client. This is useful for running 3rd party offers.

[Feed Group Index][FeedGroupPage]

#### <a name="attribution"></a> Attribution
----
Attribution allows you to sort feeds in order of value to the business. The higher attribution, in this case the lowest number, the higher the payout is to the client.

[Attribution Index][AttributionPage]


#### <a name="list-profiles"></a> List Profiles
----

\#TODO: Rob to review

List Profiles allow you to set conditions for producing a segment of user records. You can set the following constraints:

- Country of Origin
- List of Feeds
- List of Feed Groups
- List of Clients (all associated feeds)
- Delivered Status Date Range in Days
- Openner Status Date Range in Days
- Clicker Status Date Range in Days
- Converter Status Date Range in Days
- List of ISP Group
- List of Offer Categories/Verticals
- Offer to Check Status (delivered, openner, clicker, and converter)
- Record Attribute Filtering: Age using Date of Birth, Gender, Zip Codes, Cities, States, Device Types, Device OSs, Device Mobile Carriers
- Global Suppression
- List Suppression
- Offer Suppression
- Record Attribute Suppression: City, State, Zip Codes
- Record Fields to Output
- Build List Profile Schedule

There are two types of List Profiles: 1st Party and 3rd Party. They are to be used for specific offers or clients. Please refer to [Clients](#clients) and [Feeds](#feeds) for more information.

You also have the ability to combine List Profiles, which are called List Combines. They can be used anywhere List Profiles can be used.


[List Profile Index][ListProfilePage]


#### <a name="bulk-suppression"></a> Bulk Suppression
----
Bulk suppression allows you to flag records that should never be mailed again.

You have two options: list individual records or upload a suppression file.

Suppressions can be flagged with reasons such as bounced, complaint, Canada address, etc.

[Bulk Suppression Index][BulkSuppPage]


#### <a name="record-lookup"></a> Record Lookup
----
Records can be viewed and/or suppressed by entering an Email ID (EID) or email address. You can view the following information:

- First Name
- Last Name
- Address
- Source URL
- IP
- Registration Date
- Date of Birth
- Gender
- Feed
- Feed Name
- Current Attribution
- Action
- Action Date
- Capture Date
- Status
- Removal Date
- Suppression Status


[Record Lookup Index][RecordLookupPage]


#### <a name="eid-append"></a> EID Append
----
EIDs can be augmented with their associated data. Uploading a list of EIDs will return that same list with their email addresses. You also have the the following options: include the current attributed feed, include all data fields, and include suppressed records. If suppressed records is not selected, the EID list will be checked against global suppression and removes suppressed records from the list before outputting.

[EID Append Index][EidAppendPage]


#### <a name="source-url-search"></a> Source URL Search
----
Record counts and feed names for a source URL can be viewed by entering a source URL. You can filter the results by clients, feeds, verticals, or a registration date range. You also have the option of exporting to a file.

[Source URL Search Index][SourceUrlPage]


#### <a name="cpm-pricing"></a> CPM Pricing
----
CPM Pricing is the amount of money received for one thousand delivered records. This can be set for any offer but should only be set for offers that are CPM campaigns. You also have the ability to set an IO date range in the case that the pricing changes for the same offer.

[CPM Pricing Index][CpmPricingPage]


#### <a name="deploys"></a> Deploys
----

[Deploy Index][DeployPage]


#### <a name="affiliates"></a> Affiliates
----
An Affiliate is the entity which mailing is done under in our 3rd party tracking system called CAKE. Redirect domains are attached to an affiliate and an offer type. These domains are used when redirecting the user to offer landing pages. 

[Affiliate Index][AffiliatePage]


#### <a name="esp-accounts"></a> ESP Accounts
----

ESP Accounts are 3rd Party Services for mailing campaigns. There are many such services in the industry. This is the main mechanism for deploying email campaigns.

[Esp Account Index][EspAccountPage]

#### <a name="esp-api-accounts"></a> ESP API Accounts
----
For any given ESP, either because of usage constraints or data segregtion, there could be multiple api/system accounts. Each individual account will have an associated ESP, a unique account name, and API credentials.

You have the ability to completely activate/deactivate an account, toggle suppression data pulls, and toggle ESP action data pulls.

[Esp API Account Index][EspApiAccountPage]


#### <a name="dbas"></a> DBAs
----

Doing Business As (DBAs) describe the business entity behind the offer or service provided. You can find information about it's registrant name, address, contact info, and any P.O. Boxes associated to the DBA. A DBA can have multiple P.O. Boxes. 

[DBA Index][DbaPage]

#### <a name="proxies"></a> Proxies
----
Proxies handle routing requests from the ISP, such as assets or actions, to their actual hosted services. A proxy can be tied to ESP accounts, ISPs, DBAs, and Cake Affiliates. Each proxy can have multiple IPs.

[Proxy Index][ProxyPage]

#### <a name="registrars"></a> Registrars
----

Registrars are companies who acquire and register domains as a service. Many DBAs can be associated to a registrar. You can also find payment method information.

[Registrar Index][RegistrarPage]

##### <a name="domains"></a> Domains
----

There are two types of domains in the system, mailing and content domains. Mailing domains are needed the offer and ESP. The mailer would use this domain in the 'from' address. For a deployment mailed through any given ESP, the mailer might use different mailing domains to keep them seperated. Content domains are used for email assets. This includes any images, open pixels, and redirect links. The mailer might also use different content domains for a given ESP/deployment.

You have the following associative data available for each domain:

- Domain Type
- DBA (DoingBusinessAs/Registered Business)
- Domain Registrar
- Creation Date
- Expiration Date
- Associated Content/Product Site
- Live Status

[Domain Index][DomainPage]



#### <a name="mailing-templates"></a> Mailing Templates
----

Mailing templates are used to generate mailing packages that go into a deployment. Key common components like unsubscribe links and redirect links are populated using specific placeholders.  

[Mailing Template Index][TemplatePage]


#### <a name="isp-groups"></a> ISP Groups
----

ISP Groups are categories of domains used in email records. Any given email provider can have many different domains for seperate products, locations, or purposes. This grouping enables the team to segment records based on the email provider since each provider has their own ways of handling mail. 

[ISP Group Index][IspGroupPage]


#### <a name="isp-domains"></a> ISP Domains
----

An ISP Domain in CMP is the domain of a given email. For example, a email provided by Microsoft can have one of the following domains: outlook.com, skype.com, hotmail.com, outlook.de, etc.

We keep track of what domains are used in record data so we can group them together in ISP Groups. This enables the team to segment records based on the email provider since each provider has their own ways of handling mail. 

[ISP Domain Index][IspDomainPage]


[ClientPage]: http://cmp.mtroute.com/client
[FeedPage]: http://cmp.mtroute.com/feed
[FeedGroupPage]: http://cmp.mtroute.com/feedgroup
[AttributionPage]: http://cmp.mtroute.com/attribution
[ListProfilePage]: http://cmp.mtroute.com/listprofile
[BulkSuppPage]: http://cmp.mtroute.com/tools/bulk-suppression
[RecordLookupPage]: http://cmp.mtroute.com/tools/show-info
[EidAppendPage]: http://cmp.mtroute.com/tools/appendeid
[SourceUrlPage]: http://cmp.mtroute.com/tools/source-url-search
[CpmPricingPage]: http://cmp.mtroute.com/cpm
[DeployPage]: http://cmp.mtroute.com/deploy
[AffiliatePage]: http://cmp.mtroute.com/tools/affiliates
[EspAccountPage]: http://cmp.mtroute.com/esp
[EspApiAccountPage]: http://cmp.mtroute.com/espapi
[DbaPage]: http://cmp.mtroute.com/dbapage
[ProxyPage]: http://cmp.mtroute.com/proxy
[RegistrarPage]: http://cmp.mtroute.com/registrar
[DomainPage]: http://cmp.mtroute.com/domain
[TemplatePage]: http://cmp.mtroute.com/mailingtemplate
[IspGroupPage]: http://cmp.mtroute.com/ispgroup
[IspDomainPage]: http://cmp.mtroute.com/isp