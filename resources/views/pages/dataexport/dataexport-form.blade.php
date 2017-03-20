<form id="dataExportForm" ng-init="dataExport.setupPage()">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">FTP Setup</h3>
    </div>
    <div class="panel-body">
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.fileName }">
        <input type="text" class="form-control" id="fileName" value="" placeholder="Filename (use {&zwnj;{date}} in name for unique date)" required="required" ng-model="dataExport.viewed.fileName" />
        <span class="help-block" ng-bind="dataExport.formErrors.fileName" ng-show="dataExport.formErrors.fileName"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpServer }">
        <input type="text" class="form-control" id="ftpServer" value="" placeholder="FTP Server" required="required" ng-model="dataExport.viewed.ftpServer" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpServer" ng-show="dataExport.formErrors.ftpServer"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpUser }">
        <input type="text" class="form-control" id="ftpUser" value="" placeholder="FTP username" required="required" ng-model="dataExport.viewed.ftpUser" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpUser" ng-show="dataExport.formErrors.ftpUser"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpPassword }">
        <input type="password" class="form-control" id="ftpPassword" value="" placeholder="FTP Password" required="required" ng-model="dataExport.viewed.ftpPassword" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpPassword" ng-show="dataExport.formErrors.ftpPassword"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.ftpFolder }">
        <input type="text" class="form-control" id="ftpFolder" value="" placeholder="FTP Folder" ng-model="dataExport.viewed.ftpFolder" />
        <span class="help-block" ng-bind="dataExport.formErrors.ftpFolder" ng-show="dataExport.formErrors.ftpFolder"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.NumberOfFiles }">
        <input type="text" class="form-control" id="NumberOfFiles" value="" placeholder="Number of files" required="required" ng-model="dataExport.viewed.NumberOfFiles" />
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
            md-items="item in dataExport.findClientGroup( dataExport.clientGroupSearchText )"
            md-item-text="item.name"
            md-selected-item-change="dataExport.setClientGroup( item )"
            md-min-length="0"
            placeholder="Choose a Client Group"
            md-selected-item="dataExport.viewed.client_group.name">

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
            md-selected-item="dataExport.viewed.profile.name">

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
          <input type="hidden" ng-model="dataExport.viewed.frequency" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.frequency = 'Daily'" ng-class="{ active : dataExport.viewed.frequency == 'Daily' }">Daily</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.frequency = 'Weekly'" ng-class="{ active : dataExport.viewed.frequency == 'Weekly' }">Weekly</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.frequency = 'Bi-Weekly'" ng-class="{ active : dataExport.viewed.frequency == 'Bi-Weekly' }">Bi-Weekly</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.frequency = 'Monthly'" ng-class="{ active : dataExport.viewed.frequency == 'Monthly' }">Monthly</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.frequency" ng-show="dataExport.formErrors.frequency"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.fullPostalOnly }">
        <label>Only Include Records with Full Postal</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.viewed.fullPostalOnly" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.fullPostalOnly = 'Y'" ng-class="{ active : dataExport.viewed.fullPostalOnly == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.fullPostalOnly = 'N'" ng-class="{ active : dataExport.viewed.fullPostalOnly != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.fullPostalOnly" ng-show="dataExport.formErrors.fullPostalOnly"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.addressOnly }">
        <label>Only Include Records with Full Address</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.viewed.addressOnly" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.addressOnly = 'Y'" ng-class="{ active : dataExport.viewed.addressOnly == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.addressOnly = 'N'" ng-class="{ active : dataExport.viewed.addressOnly != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.addressOnly" ng-show="dataExport.formErrors.addressOnly"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.sendBluehornet }">
        <label>Send to BlueHornet</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.viewed.sendBluehornet" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.sendBluehornet = 'Y'" ng-class="{ active : dataExport.viewed.sendBluehornet == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.sendBluehornet = 'N'" ng-class="{ active : dataExport.viewed.sendBluehornet != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.sendBluehornet" ng-show="dataExport.formErrors.sendBluehornet"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.SendToImpressionwiseDays }">
        <label>Days to send to Impressionwise</label>
        <fieldset>
          <md-checkbox ng-model="dataExport.viewed.impMonday" aria-label="Monday" ng-true-value="'Y'" ng-false-value="'N'">Monday</md-checkbox>
          <md-checkbox ng-model="dataExport.viewed.impTuesday" aria-label="Tuesday" ng-true-value="'Y'" ng-false-value="'N'">Tuesday</md-checkbox>
          <md-checkbox ng-model="dataExport.viewed.impWednesday" aria-label="Wednesday" ng-true-value="'Y'" ng-false-value="'N'">Wednesday</md-checkbox>
          <md-checkbox ng-model="dataExport.viewed.impThursday" aria-label="Thursday" ng-true-value="'Y'" ng-false-value="'N'">Thursday</md-checkbox>
          <md-checkbox ng-model="dataExport.viewed.impFriday" aria-label="Friday" ng-true-value="'Y'" ng-false-value="'N'">Friday</md-checkbox>
          <md-checkbox ng-model="dataExport.viewed.impSaturday" aria-label="Saturday" ng-true-value="'Y'" ng-false-value="'N'">Saturday</md-checkbox>
          <md-checkbox ng-model="dataExport.viewed.impSunday" aria-label="Sunday" ng-true-value="'Y'" ng-false-value="'N'">Sunday</md-checkbox>
          <span class="help-block" ng-bind="dataExport.formErrors.SendToImpressionwiseDays" ng-show="dataExport.formErrors.SendToImpressionwiseDays"></span>
        </fieldset>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.seeds }">        
        <md-input-container class="md-block">
          <label>Seeds</label>
          <textarea ng-model="dataExport.viewed.seeds" md-maxlength="250" rows="10" md-select-on-focus></textarea>
        </md-input-container>
        <span class="help-block" ng-bind="dataExport.formErrors.seeds" ng-show="dataExport.formErrors.seeds"></span>
      </div>

        <membership-widget recordlist="dataExport.espList" chosenrecordlist="dataExport.selectedEsps" availablecardtitle="dataExport.availableWidgetTitle" chosenrecordtitle="dataExport.chosenWidgetTitle"  updatecallback="dataExport.espMembershipCallback()" widgetname="dataExport.widgetName"></membership-widget>
    </div>
  </div>

  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">File Setup</h3>
    </div>
    <div class="panel-body">      
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.includeHeaders }">
        <label>Include Headers</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.viewed.includeHeaders" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.includeHeaders = 'Y'" ng-class="{ active : dataExport.viewed.includeHeaders == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.includeHeaders = 'N'" ng-class="{ active : dataExport.viewed.includeHeaders != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.includeHeaders" ng-show="dataExport.formErrors.includeHeaders"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.doubleQuoteFields }">
        <label>Double-quote fields</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
          <input type="hidden" ng-model="dataExport.viewed.doubleQuoteFields" />
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.doubleQuoteFields = 'Y'; console.log('y')" ng-class="{ active : dataExport.viewed.doubleQuoteFields == 'Y' }">Yes</button>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="dataExport.viewed.doubleQuoteFields = 'N'" ng-class="{ active : dataExport.viewed.doubleQuoteFields != 'Y' }">No</button>
          </div>
        </div>
        <span class="help-block" ng-bind="dataExport.formErrors.doubleQuoteFields" ng-show="dataExport.formErrors.doubleQuoteFields"></span>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.fields }">
        <label>Fields to include in file:</label>
        <fieldset>
          <div class="col-sm-4">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.email_addr" ng-true-value="'email_addr'">Email Address</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.eid" ng-true-value="'eid'">Email ID</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.MD5" ng-true-value="'MD5'">Email MD5</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.UMD5" ng-true-value="'UMD5'">Uppercase Email MD5</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.ISP" ng-true-value="'ISP'">ISP</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.cdate" ng-true-value="'cdate'">Capture Date</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.sdate" ng-true-value="'sdate'">Registration Date</md-checkbox>
          </div>

          <div class="col-sm-3">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.client_id" ng-true-value="'client_id'">Feed ID</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.username" ng-true-value="'username'">Feed name</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.client_network" ng-true-value="'client_network'">Feed Network</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.url" ng-true-value="'url'">Source Url</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.Status" ng-true-value="'Status'">Status</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.adate" ng-true-value="'adate'">Action Date</md-checkbox>
          </div>

          <div class="col-sm-2">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.address" ng-true-value="'address'">Address</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.address2" ng-true-value="'address2'">Addr2</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.city" ng-true-value="'city'">City</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.state" ng-true-value="'state'">State</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.zip" ng-true-value="'zip'">Zip</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.country" ng-true-value="'country'">Country</md-checkbox>
          </div>

          <div class="col-sm-3">
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.first_name" ng-true-value="'first_name'">First Name</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.last_name" ng-true-value="'last_name'">Last Name</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.gender" ng-true-value="'gender'">Gender</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.phone" ng-true-value="'phone'">Phone</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.dob" ng-true-value="'dob'">Birth Date</md-checkbox>
            <md-checkbox name="fields" class="col-xs-12" ng-model="dataExport.viewed.fields.ip" ng-true-value="'ip'">IP Address</md-checkbox>
          </div> 
       </fieldset>
      </div>

      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.otherField }">
        <input type="text" class="form-control" id="otherField" value="" placeholder="Other field" required="required" ng-model="dataExport.viewed.otherField" />
        <span class="help-block" ng-bind="dataExport.formErrors.otherField" ng-show="dataExport.formErrors.otherField"></span>
      </div>
      <div class="form-group" ng-class="{ 'has-error' : dataExport.formErrors.otherValue }">
        <input type="text" class="form-control" id="otherValue" value="" placeholder="Other value (use {&zwnj;{date}} for current date)" required="required" ng-model="dataExport.viewed.otherValue" />
        <span class="help-block" ng-bind="dataExport.formErrors.otherValue" ng-show="dataExport.formErrors.otherValue"></span>
      </div>
    </div>

  </div>
</form>
