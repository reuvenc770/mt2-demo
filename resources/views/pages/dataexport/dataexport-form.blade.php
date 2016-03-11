<form id="dataExportForm" ng-init="dataExport.setupPage()">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">FTP Setup</h3>
    </div>
    <div class="panel-body">
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.fileName }">
        <input type="text" class="form-control" id="fileName" value="" placeholder="Filename (use {&zwnj;{date}} in name for unique date)" required="required" ng-model="dataExport.current.fileName" />
        <span class="help-block" ng-bind="dataExport.formErrors.fileName" ng-show="dataExport.formErrors.fileName"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpServer }">
        <input type="text" class="form-control" id="ftpServer" value="" placeholder="FTP Server" required="required" ng-model="dataExport.current.ftpServer" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpServer" ng-show="dataExport.formErrors.ftpServer"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpUser }">
        <input type="text" class="form-control" id="ftpUser" value="" placeholder="FTP username" required="required" ng-model="dataExport.current.ftpUser" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpUser" ng-show="dataExport.formErrors.ftpUser"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpPassword }">
        <input type="password" class="form-control" id="ftpPassword" value="" placeholder="FTP Password" required="required" ng-model="dataExport.current.ftpPassword" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpPassword" ng-show="dataExport.formErrors.ftpPassword"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpFolder }">
        <input type="text" class="form-control" id="ftpFolder" value="" placeholder="FTP Folder" ng-model="dataExport.current.ftpFolder" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpFolder" ng-show="dataExport.formErrors.ftpFolder"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.NumberOfFiles }">
        <input type="text" class="form-control" id="NumberOfFiles" value="" placeholder="Number of files" required="required" ng-model="dataExport.current.NumberOfFiles" />
        <span class="help-block" ng-bind="dataExport.formErrors.NumberOfFiles" ng-show="dataExport.formErrors.NumberOfFiles"></span>
      </div>
    </div>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Pull Setup</h3>
    </div>
    <div class="panel-body">
      
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.client_group_id }">
        <div layout="column" ng-cloak>
          <md-content>
            <md-autocomplete
            md-search-text="dataExport.clientGroupSearchText"
            md-items="item in dataExport.getClientGroup( dataExport.clientGroupSearchText )"
            md-item-text="item.name"
            md-selected-item-change="dataExport.setClientGroup( item )"
            md-min-length="0"
            placeholder="Choose a Client Group"
            md-selected-item="dataExport.current.client_group_id">

              <md-item-template>
                <span md-highlight-text="dataExport.clientGroupSearchText" md-highlight-flags="^i">@{{item.name}}</span>
              </md-item-template>

              <md-not-found>
              No Client Groups matching "@{{dataExport.clientGroupSearchText}}" were found.
              </md-not-found>
            </md-autocomplete>
          </md-content>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.client_group_id" ng-show="dataExport.formErrors.client_group_id"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.profile_id }">
        <div layout="column" ng-cloak>
          <md-content>
            <md-autocomplete
            md-search-text="dataExport.profileSearchText"
            md-items="item in dataExport.getProfile( dataExport.profileSearchText )"
            md-item-text="item.name"
            md-selected-item-change="dataExport.setProfile( item )"
            md-min-length="0"
            placeholder="Choose a Profile"
            md-selected-item="dataExport.current.profile_id">

              <md-item-template>
                <span md-highlight-text="dataExport.profileSearchText" md-highlight-flags="^i">@{{item.name}}</span>
              </md-item-template>

              <md-not-found>
              No Profiles matching "@{{dataExport.profileSearchText}}" were found.
              </md-not-found>
            </md-autocomplete>
          </md-content>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.profile_id" ng-show="dataExport.formErrors.profile_id"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.frequency }">
        <label>Frequency</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.current.frequency" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.frequency = 'Daily'" ng-class="{ active : dataExport.current.frequency == 'Daily' }">Daily</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.frequency = 'Weekly'" ng-class="{ active : dataExport.current.frequency == 'Weekly' }">Weekly</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.frequency = 'Bi-Weekly'" ng-class="{ active : dataExport.current.frequency == 'Bi-Weekly' }">Bi-Weekly</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.frequency = 'Monthly'" ng-class="{ active : dataExport.current.frequency == 'Monthly' }">Monthly</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.frequency" ng-show="dataExport.formErrors.frequency"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.fullPostalOnly }">
        <label>Only Include Records with Full Postal</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.current.fullPostalOnly" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.fullPostalOnly = 'Y'" ng-class="{ active : dataExport.current.fullPostalOnly == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.fullPostalOnly = 'N'" ng-class="{ active : dataExport.current.fullPostalOnly != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.fullPostalOnly" ng-show="dataExport.formErrors.fullPostalOnly"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.addressOnly }">
        <label>Only Include Records with Full Address</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.current.addressOnly" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.addressOnly = 'Y'" ng-class="{ active : dataExport.current.addressOnly == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.addressOnly = 'N'" ng-class="{ active : dataExport.current.addressOnly != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.addressOnly" ng-show="dataExport.formErrors.addressOnly"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.sendBluehornet }">
        <label>Send to BlueHornet</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.current.sendBluehornet" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.sendBluehornet = 'Y'" ng-class="{ active : dataExport.current.sendBluehornet == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.sendBluehornet = 'N'" ng-class="{ active : dataExport.current.sendBluehornet != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.sendBluehornet" ng-show="dataExport.formErrors.sendBluehornet"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.SendToImpressionwiseDays }">
        <label>Days to send to Impressionwise</label>
        <fieldset>
          <md-checkbox ng-model="dataExport.current.impMonday" aria-label="Monday">Monday</md-checkbox>
          <md-checkbox ng-model="dataExport.current.impTuesday" aria-label="Tuesday">Tuesday</md-checkbox>
          <md-checkbox ng-model="dataExport.current.impWednesday" aria-label="Wednesday">Wednesday</md-checkbox>
          <md-checkbox ng-model="dataExport.current.impThursday" aria-label="Thursday">Thursday</md-checkbox>
          <md-checkbox ng-model="dataExport.current.impFriday" aria-label="Friday">Friday</md-checkbox>
          <md-checkbox ng-model="dataExport.current.impSaturday" aria-label="Saturday">Saturday</md-checkbox>
          <md-checkbox ng-model="dataExport.current.impSunday" aria-label="Sunday">Sunday</md-checkbox>
          <span class="help-block" ng-bind="dataExport.formErrors.SendToImpressionwiseDays" ng-show="dataExport.formErrors.SendToImpressionwiseDays"></span>
        </fieldset>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.seeds }">
        <input type="text" class="form-control" id="seeds" value="" placeholder="Seeds" required="required" ng-model="dataExport.current.seeds" />
        <span class="help-block" ng-bind="dataExport.formErrors.seeds" ng-show="dataExport.formErrors.seeds"></span>
      </div>
    </div>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">File Setup</h3>
    </div>
    <div class="panel-body">
      <!--
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.outname }">
        <input type="text" class="form-control" id="outname" value="" placeholder="Output filename (use {&zwnj;{date}} in name for unique date)" required="required" ng-model="dataExport.current.outname" />
        <span class="help-block" ng-bind="dataExport.formErrors.outname" ng-show="dataExport.formErrors.outname"></span>
      </div>
      -->
      
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.includeHeaders }">
        <label>Include Headers</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.current.includeHeaders" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.includeHeaders = 'Y'" ng-class="{ active : dataExport.current.includeHeaders == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.includeHeaders = 'N'" ng-class="{ active : dataExport.current.includeHeaders != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.includeHeaders" ng-show="dataExport.formErrors.includeHeaders"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.doubleQuoteFields }">
        <label>Double-quote fields</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.current.doubleQuoteFields" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.doubleQuoteFields = 'Y'; console.log('y')" ng-class="{ active : dataExport.current.doubleQuoteFields == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.current.doubleQuoteFields = 'N'" ng-class="{ active : dataExport.current.doubleQuoteFields != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.doubleQuoteFields" ng-show="dataExport.formErrors.doubleQuoteFields"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.fields }">
        <label>Fields to include in file:</label>
        <fieldset>
          <div class="col-sm-4">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.email_addr" ng-true-value="'email_addr'">Email Address</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.eid" ng-true-value="'eid'">Email ID</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.MD5" ng-true-value="'MD5'">Email MD5</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.UMD5" ng-true-value="'UMD5'">Uppercase Email MD5</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.ISP" ng-true-value="'ISP'">ISP</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.cdate" ng-true-value="'cdate'">Capture Date</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.sdate" ng-true-value="'sdate'">Subscribe Date</md-checkbox>
          </div>

          <div class="col-sm-3">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.client_id" ng-true-value="'client_id'">Client ID</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.username" ng-true-value="'username'">Client name</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.client_network" ng-true-value="'client_network'">Client Network</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.url" ng-true-value="'url'">Source Url</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.Status" ng-true-value="'Status'">Status</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.adate" ng-true-value="'adate'">Action Date</md-checkbox>
          </div>

          <div class="col-sm-2">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.address" ng-true-value="'address'">Address</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.address2" ng-true-value="'address2'">Addr2</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.city" ng-true-value="'city'">City</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.state" ng-true-value="'state'">State</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.zip" ng-true-value="'zip'">Zip</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.country" ng-true-value="'country'">Country</md-checkbox>
          </div>

          <div class="col-sm-3">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.first_name" ng-true-value="'first_name'">First Name</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.last_name" ng-true-value="'last_name'">Last Name</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.gender" ng-true-value="'gender'">Gender</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.phone" ng-true-value="'phone'">Phone</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.dob" ng-true-value="'dob'">Birth Date</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.current.fields.ip" ng-true-value="'ip'">IP Address</md-checkbox>
          </div> 
       </fieldset>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.otherField }">
        <input type="text" class="form-control" id="otherField" value="" placeholder="Other field" required="required" ng-model="dataExport.current.otherField" />
        <span class="help-block" ng-bind="dataExport.formErrors.otherField" ng-show="dataExport.formErrors.otherField"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.otherValue }">
        <input type="text" class="form-control" id="otherValue" value="" placeholder="Other value (use {&zwnj;{date}} for current date)" required="required" ng-model="dataExport.current.otherValue" />
        <span class="help-block" ng-bind="dataExport.formErrors.otherValue" ng-show="dataExport.formErrors.otherValue"></span>
      </div>
    </div>

  </div>
</form>