<form name="listProfileForm" layout="column" novalidate>

   <md-card>
      <md-toolbar>
         <div class="md-toolbar-tools"><span>List Profile</span></div>
      </md-toolbar>
      <md-card-content>

      </md-card-content>

      <md-divider></md-divider>

      <md-card-content>
         <!-- range/action fields -->
      </md-card-content>

      <md-divider></md-divider>

      <md-card-content>
         <!-- ISP/category/offer fields -->
      </md-card-content>

      <md-toolbar>
         <div class="md-toolbar-tools"><span>Attribute Filtering</span></div>
      </md-toolbar>

      <md-card-content>

      </md-card-content>

      <md-toolbar>
         <div class="md-toolbar-tools"><span>Suppression</span></div>
      </md-toolbar>
      <md-card-content layout="column">
         <md-input-container>
            <label>Global Suppression</label>
            <md-select name="globalSupp" ng-required="true" ng-model="listProfile.current.globalSupp" multiple>
               <md-option value="Orange Global">Orange Global</md-option>
               <md-option value="Red Global">Red Global</md-option>
               <md-option value="Purple Global">Purple Global</md-option>
               <md-option value="Blue Global">Blue Global</md-option>
            </md-select>
         </md-input-container>

         <md-input-container>
            <label>List Suppression</label>
            <md-select name="listSupp" ng-model="listProfile.current.listSupp" multiple>
               <md-option value="List Option 1">List Option 1</md-option>
               <md-option value="List Option 2">List Option 2</md-option>
               <md-option value="List Option 3">List Option 3</md-option>
            </md-select>
         </md-input-container>

         <md-input-container>
            <label>Offer Suppression</label>
            <md-select name="offerSupp" ng-model="listProfile.current.offerSupp" multiple>
               <md-option value="Offer 1">Offer 1</md-option>
               <md-option value="Offer 2">Offer 2</md-option>
               <md-option value="Offer 3">Offer 3</md-option>
            </md-select>
         </md-input-container>

         <h4><strong>Attribute Suppression</strong></h4>

         <md-chips name="city" placeholder="City/Cities" secondary-placeholder="+ City"
                   ng-model="listProfile.current.cities"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

         </md-chips>

         <md-input-container>
            <label>State(s)</label>
              <md-select name="state" convert-to-number ng-model="listProfile.current.states" multiple>
              </md-select>
         </md-input-container>

         <md-chips name="zip" placeholder="Zip Code(s)" secondary-placeholder="+ Zip Code"
                   ng-model="listProfile.current.zips"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

         </md-chips>

      </md-card-content>

      <md-toolbar>
         <div class="md-toolbar-tools"><span>Hygiene</span></div>
      </md-toolbar>
      <md-card-content>

      </md-card-content>

   </md-card>

</form>