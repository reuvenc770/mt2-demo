<md-toolbar>
    <div class="md-toolbar-tools">
        <span>Deploys </span>
        <span ng-if="deploy.searchType.length > 0">&nbsp;<md-icon md-svg-src="img/icons/ic_chevron_right_white_36px.svg"></md-icon> Search by @{{ deploy.searchType }}</span>
    </div>
</md-toolbar>
<md-table-container>
    <table md-table class="mt2-table-large" md-progress="deploy.queryPromise">
        <thead md-head>
        <tr md-row>
            <th md-column></th>
            <th md-column></th>
            <th md-column class="md-table-header-override-whitetext">Send Date</th>
            <th md-column class="md-table-header-override-whitetext" md-numeric>Deploy ID</th>
            <th md-column class="md-table-header-override-whitetext">EspAccount</th>
            <th md-column class="md-table-header-override-whitetext">List Profile</th>
            <th md-column class="md-table-header-override-whitetext">Offer</th>
            <th md-column class="md-table-header-override-whitetext">Creative</th>
            <th md-column class="md-table-header-override-whitetext">From</th>
            <th md-column class="md-table-header-override-whitetext">Subject</th>
            <th md-column class="md-table-header-override-whitetext">Template</th>
            <th md-column class="md-table-header-override-whitetext">Mailing Domain</th>
            <th md-column class="md-table-header-override-whitetext">Content Domain</th>
            <th md-column class="md-table-header-override-whitetext" md-numeric>Cake ID</th>
            <th md-column class="md-table-header-override-whitetext">Notes</th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-show="deploy.showRow">
            <td md-cell></td>
            <td md-cell>
                @if (Sentinel::hasAccess('api.deploy.update'))
                <md-button ng-click="deploy.actionLink()" class="md-raised mt2-button-xs mt2-button-success">@{{ deploy.actionText() }}</md-button>
                @endif
                <md-button ng-click="deploy.showRow = false"
                        class="md-raised mt2-button-xs md-warn md-hue-2">Cancel</md-button>

            </td>
            <td md-cell>
                <md-datepicker name="dateField" ng-model="deploy.currentDeploy.send_date"
                               required
                               md-placeholder="Enter date"></md-datepicker>
                <div class="validation-messages" ng-show="deploy.formErrors.send_date">
                    <div ng-bind="deploy.formErrors.send_date"></div>
                </div>

            </td>
            <td md-cell>@{{ deploy.deployIdDisplay }}</td>
            <td md-cell>
                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.esp_account_id }">
                    <select name="esp_account" id="esp_account"
                            ng-change="deploy.updateSelects()"
                            ng-model="deploy.currentDeploy.esp_account_id" class="form-control"
                            ng-disabled="deploy.currentlyLoading">
                        <option value="">- Please Choose an ESP Account -</option>
                        <option ng-repeat="option in deploy.espAccounts" ng-value="option.id"
                                ng-selected="option.id == deploy.currentDeploy.esp_account_id">@{{ option.account_name }}
                        </option>
                    </select>
                    <span class="help-block" ng-bind="deploy.formErrors.esp_account_id"
                          ng-show="deploy.formErrors.esp_account_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.list_profile_id }">
                    <select name="list_profile_id" id="list_profile_id"
                            ng-model="deploy.currentDeploy.list_profile_id" class="form-control"
                            ng-disabled="deploy.currentlyLoading">
                        <option value="">- Please Choose a List Profile -</option>
                        <option ng-repeat="option in deploy.listProfiles" value="@{{ option.id }}">
                            @{{ option.profile_name }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.list_profile_id"
                      ng-show="deploy.formErrors.list_profile_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.offer_id }">
                    <div angucomplete-alt
                         id="offer"
                         placeholder="Search Offers"
                         selected-object="deploy.offerWasSelected"
                         selected-object-data="deploy.currentDeploy.offer_id"
                         remote-url="/api/offer/search?searchTerm="
                         title-field="name,id"
                         text-searching="Looking for Offers..."
                         selected-object-data="offer"
                         minlength="3"
                         input-class="form-control">
                    </div>
                </div>
                <span class="help-block" ng-bind="deploy.formErrors.offer_id"
                      ng-show="deploy.formErrors.offer_id"></span>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.creative_id }">
                    <select name="creative_id" id="creative_id"
                            ng-model="deploy.currentDeploy.creative_id" class="form-control"
                            ng-disabled="deploy.offerLoading">
                        <option value="">- Please Choose a Creative -</option>
                        <option ng-repeat="option in deploy.creatives" value="@{{ option.id }}">
                            @{{ option.name }} - @{{ option.id }} - @{{ option.click_rate }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.creative_id"
                      ng-show="deploy.formErrors.creative_id"></span>
                    <a ng-show="deploy.creatives.length > 0" target="_blank" href="creatives/preview/@{{ deploy.currentDeploy.offer_id }}">Preview All Creatives</a>
                </div>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.from_id }">
                    <select name="from_id" id="from_id"
                            ng-model="deploy.currentDeploy.from_id" class="form-control"
                            ng-disabled="deploy.offerLoading">
                        <option value="">- Please Choose a From -</option>
                        <option ng-repeat="option in deploy.froms" value="@{{ option.id }}">
                            @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.from_id"
                      ng-show="deploy.formErrors.from_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.subject_id }">
                    <select name="subject_id" id="subject_id"
                            ng-model="deploy.currentDeploy.subject_id" class="form-control"
                            ng-disabled="deploy.offerLoading">
                        <option value="">- Please Choose a Subject -</option>
                        <option ng-repeat="option in deploy.subjects" value="@{{ option.id }}">
                            @{{ option.name }} - @{{ option.id }}  - @{{ option.open_rate }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.subject_id"
                      ng-show="deploy.formErrors.subject_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.template_id }">
                    <select name="template" id="template"
                            ng-model="deploy.currentDeploy.template_id" class="form-control"
                            ng-disabled="deploy.espLoaded">
                        <option value="">- Please Choose a Template -</option>
                        <option ng-repeat="option in deploy.templates" value="@{{ option.id }}">
                            @{{ option.template_name }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.mailing_domain_id"
                      ng-show="deploy.formErrors.mailing_domain_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.mailing_domain_id }">
                    <select name="mailing_domain" id="mailing_domain"
                            ng-model="deploy.currentDeploy.mailing_domain_id" class="form-control"
                            ng-disabled="deploy.espLoaded">
                        <option value="">- Please Choose a Mailing Domain -</option>
                        <option ng-repeat="option in deploy.mailingDomains track by $index" value="@{{ option.id }}">
                            @{{ option.domain_name }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.mailing_domain_id"
                      ng-show="deploy.formErrors.mailing_domain_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.content_domain_id }">
                    <select name="content_domain" id="content_domain"
                            ng-model="deploy.currentDeploy.content_domain_id" class="form-control"
                            ng-disabled="deploy.espLoaded">
                        <option value="">- Please Choose an Content Domain -</option>
                        <option ng-repeat="option in deploy.contentDomains" value="@{{ option.id }}">
                            @{{ option.domain_name }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.content_domain_id"
                      ng-show="deploy.formErrors.content_domain_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group"
                     ng-class="{ 'has-error' : deploy.formErrors.cake_affiliate_id }">
                    <select name="cake_affiliate_id" id="cake_affiliate_id"
                            ng-model="deploy.currentDeploy.cake_affiliate_id" class="form-control">
                        <option value="">- Please Choose an Cake ID -</option>
                        <option ng-repeat="option in deploy.cakeAffiliates" value="@{{ option.affiliateID }}">
                            @{{ option.affiliateID }}
                        </option>
                    </select>
                <span class="help-block" ng-bind="deploy.formErrors.cake_affiliate_id"
                      ng-show="deploy.formErrors.cake_affiliate_id"></span>
                </div>
            </td>
            <td md-cell>
                <div class="form-group" ng-class="{ 'has-error' : deploy.formErrors.notes }">
                    <div class="form-group">
                        <textarea ng-model="deploy.currentDeploy.notes" class="form-control" rows="1"
                                  id="html"></textarea>
                    </div>
                    </div>
            </td>
        </tr>

        <tr md-row ng-repeat="record in deploy.deploys track by $index" ng-class="{ 'mt2-bg-warn' : record.deployment_status == 0,
                                                                         'mt2-bg-success' : record.deployment_status ==1,
                                                                         'mt2-bg-danger' : record.deployment_status == 2
                                                                         }">
            <td md-cell>
                <md-checkbox aria-label="Select" name="selectedRows"
                             ng-click="deploy.toggleRow(record.deploy_id)"> </md-checkbox>
            </td>
            <td md-cell>
                <md-button class="md-raised" ng-click="deploy.editRow( record.deploy_id)"
                            ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }">
                    <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Edit</span>
                </md-button>
                <md-button class="md-raised md-accent" ng-click="deploy.copyRow( record.deploy_id)"
                            ng-class="{'md-icon-button mt2-icon-button-xs' : !app.mediumPageWidth() , 'mt2-button-xs' : app.mediumPageWidth() }">
                    <md-icon md-svg-icon="img/icons/ic_content_copy_white_18px.svg"></md-icon><span ng-show="app.mediumPageWidth()"> Copy</span>
                </md-button>
            </td>
            <td md-cell>@{{ record.send_date }}</td>
            <td md-cell>@{{ record.deploy_id }}</td>
            <td md-cell>@{{ record.account_name }}</td>
            <td md-cell>@{{ record.list_profile }}</td>
            <td md-cell>@{{ record.offer_name }}</td>
            <td md-cell>@{{ record.creative }}</td>
            <td md-cell>@{{ record.from }}</td>
            <td md-cell>@{{ record.subject }}</td>
            <td md-cell>@{{ record.template_name }}</td>
            <td md-cell>@{{ record.mailing_domain }}</td>
            <td md-cell>@{{ record.content_domain }}</td>
            <td md-cell>@{{ record.cake_affiliate_id }}</td>
            <td md-cell>@{{ record.notes }}</td>
        </tr>
    </tbody>
</table>
</md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="deploy.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="deploy.currentPage" md-total="@{{deploy.deployTotal}}" md-on-paginate="deploy.loadAccounts" md-page-select></md-table-pagination>
</md-content>
