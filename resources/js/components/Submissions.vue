<template>
  <v-app>
    <div class="message">
      <div class="completeMessage" v-show="is_success">Status is successfully saved.</div>
      <div class="errorMessage" v-show="is_error">{{ error_message ]}</div>
    </div>
    <v-layout row wrap>
      <v-flex xs2 md2 class="pl-3">
        <v-select
          v-model="DateItemsSelected"
          color="primary"
          label="Date"
          :items="date_items"
          class="ml-5"
        >
        </v-select>
      </v-flex>
      <v-flex xs3 md3 class="mx-2" v-show="show_dates">
        <v-menu
          ref="menu"
          :close-on-content-click="false"
          v-model="menu"
          :nudge-right="40"
          :return-value="datefr_raw"
          transition="scale-transition"
          offset-y
          min-width="290px"
        >
          <template v-slot:activator="{ on }">
            <v-text-field
              v-model="datefr"
              label="Date From"
              prepend-icon="event"
              readonly
              clearable
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="datefr_raw"
          :min="date_min"
          no-title
          scrollable
          >
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="clearDateFr">Clear</v-btn>
            <v-btn text color="primary" @click="$refs.menu.save(datefr_raw)">OK</v-btn>
          </v-date-picker>
        </v-menu>
      </v-flex>
      <v-flex xs3 md3 class="mx-2" v-show="show_dates">
        <v-menu
          ref="menu2"
          :close-on-content-click="false"
          v-model="menu2"
          :nudge-right="40"
          :return-value="dateto_raw"
          transition="scale-transition"
          offset-y
          min-width="290px"
        >
          <template v-slot:activator="{ on }">
            <v-text-field
              v-model="dateto"
              label="Date To"
              readonly
              clearable
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="dateto_raw"
          :min="date_min"
          :max="date_max"
          no-title
          scrollable
          >
            <v-spacer></v-spacer>
            <v-btn text color="primary" @click="clearDateTo">Clear</v-btn>
            <v-btn text color="primary" @click="$refs.menu2.save(dateto_raw)">OK</v-btn>
          </v-date-picker>
        </v-menu>
      </v-flex>
      <v-spacer></v-spacer>
      <v-flex xs2 md2 class="text-xs-right text-md-right mr-5">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              outlined
              v-on="on"
              dark
              color="primary"
              icon
              @click="populateSubmissionList(false)">GO
            </v-btn>
          </template>
          <span>Search</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              outlined
              v-on="on"
              dark
              color="primary"
              icon
              @click="resetfilters"><v-icon>refresh</v-icon>
            </v-btn>
          </template>
          <span>Reset Filters</span>
        </v-tooltip>
        <!--<v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              outlined
              v-on="on"
              dark
              color="primary"
              @click="download"
              icon
              :disabled="disabled_download"><v-icon>cloud_download</v-icon>
            </v-btn>
          </template>
          <span>Download</span>
        </v-tooltip>//-->
      </v-flex>
    </v-layout>
<!--v-slot:header="{ props: { headers } }"    v-slot:headers="{ props }"//-->
    <v-data-table
      :headers="headers"
      :items="resultset"
      item-key="rowids"
      :loading="table_loading"
      loading-text="Loading... Please wait"
      :options.sync="pagination_resultset"
      :server-items-length="total_resultsets"
      :items-per-page="rows_per_page_items"
      id="datatbl1"
      class="elevation-6 mt-4"
      hide-default-header
    >
      <template v-slot:header="{ props: { headers } }">
        <thead>
          <tr>
            <th style="width: 500px; font-size:15px;">
              <br/>
              <v-select
                :items="questionnaire_categories"
                item-text="label"
                item-value="value"
                :label="headers[0].text"
                v-model="QuestionnaireCategoryID"
                dense
              >
              </v-select>
            </th>
            <th style="width: 180px; font-size:15px;">
              <v-autocomplete
                id="user_contact_name"
                :loading="user_contact_name_loading"
                :items="user_contact_names"
                item-text="label"
                item-value="value"
                :search-input.sync="user_contact_name_search"
                clearable
                return-object
                v-model="UserContactName"
                class="pb-3"
                text
                hide-no-data
                hide-details
                :label="headers[1].text"
                @keyup.enter="populateSubmissionList(false)"
                @click:clear="clearAuto('user_contact_names')"
              ></v-autocomplete>
            </th>
            <th style="max-width: 90px; font-size:15px;">
              {{ headers[2].text }}
            </th>
            <th style="max-width: 160px; font-size:15px;">
              <v-autocomplete
                id="ticket_number"
                :loading="ticket_number_loading"
                :items="ticket_numbers"
                item-text="label"
                item-value="value"
                :search-input.sync="ticket_number_search"
                clearable
                return-object
                v-model="TicketNumber"
                class="pb-3"
                text
                hide-no-data
                hide-details
                :label="headers[3].text"
                @keyup.enter="populateSubmissionList(false)"
                @click:clear="clearAuto('ticket_numbers')"
              ></v-autocomplete>
            </th>
          </tr>
        </thead>
      </template>
      <!-- showPDF(props.item.ticketnumber); v-slot:items="{ props: { items } }"  v-slot:item="{ item }"  v-slot:items="props"//-->
      <template v-slot:body="{ items }">
        <tbody>
          <tr class="pointer" v-if="items.length == 0">
            <td colspan="4" class="text-xs-left">There are no submissions with current filter settings.</td>
          </tr>
          <tr class="pointer" v-for="item in items" :key="item.id" @click="showPDF(item.ticketnumber)">
            <td class="text-xs-left">{{ item.questionnaire }}</td>
            <td class="text-xs-left">{{ item.contactname }}</td>
            <td class="text-xs-left">{{ item.createdat }}</td>
            <td class="text-xs-left">{{ item.ticketnumber }}</td>
          </tr>
        </tbody>
      </template>
      <!--<template v-slot:no-data>
        There are no submissions with current filter settings.
      </template>//-->
    </v-data-table>

    <!-- Report //-->
    <v-dialog
      v-model="dlgLoadPDF"
      persistent
      width="760px">
      <v-card>
        <v-card-title>
          <iframe width="740" height="520" ref="ifra" id="ifra" src=""></iframe>
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            @click="dlgLoadPDF = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-app>
</template>

<script>
import axios from 'axios'
import querystring from 'query-string'
import moment from 'moment'

const CancelToken = axios.CancelToken
const source = CancelToken.source()

export default {
  name: 'Submissions',
  mixins: [ ],
  data () {
    return {
      table_loading: false,
      headers: [
        { text: 'Questionnaire', value: 'questionnaire' },
        { text: 'Submitted By', value: 'contactname' },
        { text: 'Submitted On', value: 'createdat' },
        { text: 'Ticket Number', value: 'ticketnumber' }
      ],
      dlgChangeSubmissionStatus: false,
      loading: false,
      error_message: '',
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      download_submissions_url: document.getElementById('download_submissions_url').value,
      is_success: false,
      is_error: false,
      disabled_download: true,
      resultset: [],
      /* Filter fields */
      //Date Filters
      date_items: ['Today', 'Last 7 Days', 'Last 30 Days', 'Last 12 Months', 'Custom'],
      DateItemsSelected: 'Custom',
      datefr: '',
      dateto: '',
      datefr_raw: '',
      dateto_raw: '',
      date_min:'',
      date_max: '',
      menu: false,
      menu2: false,
      show_dates: false,
      tmp_date_from: '',
      tmp_date_to: '',
      // Questionnaire
      questionnaire_categories: [
        { label : ' --- Select --- ', value : '0' },
        { label : 'Cat A', value : '-1' },
        { label : 'Cat B', value : '-2' },
        { label : 'Cat C', value : '-3' },
        { label : 'Cat D', value : '-4' },
        { label : 'Cat E', value : '-5' },
        { label : 'Cat F', value : '-6' },
        { label : 'Cat G', value : '-7' },
        { label : 'Cat H', value : '-8' },
        { label : 'Cat I', value : '-9' },
        { label : 'Cat J', value : '-10' },
      ],
      dlgLoadPDF: false,
      QuestionnaireCategoryID: '',
      tmp_questionnaire_cat_id: '',
      // User contact name
      user_contact_name_loading: false,
      user_contact_names: [],
      user_contact_name_search: '',
      UserContactName: {},
      tmp_user_contact_name: '',
      // Ticket number
      ticket_number_loading: false,
      ticket_numbers: [],
      ticket_number_search: '',
      TicketNumber: {},
      tmp_ticket_number: '',
      // Pagination
      array_resultset: [],
      current_page_number: 0,
      visited_max_page_number: -1,
      previous_items_per_page: 0,
      rows_per_page_items: 10,
      rows_per_ajax_items: 200,
      ajax_rotate_page_gap: 20,
      total_resultsets: 0,
      pagination_resultset: {},
      cancel_source_auto: null,
      cancel_source_list: null,
      // http://localhost/test/laravel/laravel/
      site_url: 'http://xxx.xxx.xx/callcentre/'
    }
  },
  mounted () {

    this.cancel_source_auto = axios.CancelToken.source()
    this.cancel_source_list = axios.CancelToken.source()
    this.DateItemsSelected = 'Last 7 Days'

  },
  beforeDestroy () {

    console.log('Page Destruct Cancel ajax')

    try {
      this.cancel_source_auto.cancel()
      console.log('Cancel ajax cancel_source_auto')
    } catch(err) {
      // Do nothing
    }

    try {
      this.cancel_source_list.cancel()
      console.log('Cancel ajax cancel_source_list')
    } catch(err) {
      // Do nothing
    }

  },
  methods: {
    resetfilters () {

      this.initAjaxCall('all')

      this.current_page_number = 0
      this.pagination_resultset.page = 1
      this.visited_max_page_number = -1
      this.total_resultsets = 0
      this.array_resultset = []
      this.resultset = []

      this.disabled_download = true

      this.QuestionnaireCategoryID = ''

      // Initialise v-autocomplete search properties
      this.user_contact_name_search = ''
      this.tmp_ticket_number = ''

      this.tmp_date_from = ''
      this.tmp_date_to = ''
      this.tmp_questionnaire_cat_id = ''
      this.tmp_user_contact_name = ''

      // Initialise v-autocomplete v-models
      this.UserContactName = {}
      this.TicketNumber = {}
      this.DateItemsSelected = 'Last 7 Days'

      let self = this
      this.$nextTick()
      .then(function () {
        // DOM updated
        self.populateSubmissionList(false)
      })
    },
    clearDateFr () {

      this.datefr_raw = ''
      this.menu = false
    },
    formatDateFr (date) {

      if ( ! date) return null

      const [year, month, day] = date.split('-')
      let date_fr = day.padStart(2, '0') + '-' + month.padStart(2, '0') + '-' + year

      return date_fr
    },
    clearDateTo () {

      this.dateto_raw = ''
      this.menu2 = false
    },
    formatDateTo (date) {

      if ( ! date) return null

      const [year, month, day] = date.split('-')
      let date_to = day.padStart(2, '0') + '-' + month.padStart(2, '0') + '-' + year

      return date_to
    },
    _post: function(path, params, method) {

      //path = path.substr(0, path.length - 1)
      method = method || 'post'
      var form = document.createElement('form')
      form.setAttribute('method', method)
      form.setAttribute('action', path)

      var Token = document.createElement('input')
      Token.setAttribute('type', 'hidden')
      Token.setAttribute('name', '_token')
      Token.setAttribute('value', this.csrf)
      form.appendChild(Token)

      for (var key in params) {
        if (params.hasOwnProperty(key)) {
            var hiddenField = document.createElement('input')
            hiddenField.setAttribute('type', 'hidden')
            hiddenField.setAttribute('name', key)
            hiddenField.setAttribute('value', params[key])
            form.appendChild(hiddenField);
         }
      }
      document.body.appendChild(form)
      form.submit();

    },
    download: function() {

      let date_from = ''
      let date_to = ''
      let questionnaire_cat_id
      let user_contact_name = ''
      let ticket_number = ''
      questionnaire_cat_id = this.QuestionnaireCategoryID
      if (typeof this.UserContactName === 'undefined' || this.UserContactName === null) {
        // Do nothing
      } else if (Object.keys(this.UserContactName).length === 0 && this.UserContactName.constructor === Object) {
        // Do nothing
      } else {
        user_contact_name = this.UserContactName.value
      }
      if (typeof this.TicketNumber === 'undefined' || this.TicketNumber === null) {
        // Do nothing
      } else if (Object.keys(this.TicketNumber).length === 0 && this.TicketNumber.constructor === Object) {
        // Do nothing
      } else {
        ticket_number = this.TicketNumber.value
      }
      if (this.datefr === null || this.datefr === '') {
        // Do nothing
      } else {
        var array_date_from = this.datefr.split('-')
        date_from = array_date_from[2] + '-' + array_date_from[1] + '-' + array_date_from[0]
      }
      if (this.dateto === null || this.dateto === '') {
        // Do nothing
      } else {
        var array_date_to = this.dateto.split('-')
        date_to = array_date_to[2] + '-' + array_date_to[1] + '-' + array_date_to[0]
      }
      this._post(
        this.download_submissions_url, {
          date_from: date_from,
          date_to: date_to,
          questionnaire_cat_id: questionnaire_cat_id,
          user_contact_name: user_contact_name,
          ticket_number: ticket_number,
          is_csv: true
        }
      )
    },
    resetLoad () {

      this.current_page_number = 0
      this.pagination_resultset.page = 1
      this.total_resultsets = 0
      this.resultset = []
    },
    // Initialize all ajax calls
    initAjaxCall: function(param) {

      if (param === 'all' || param === 'auto') {
        try {
          this.cancel_source_auto.cancel()
          console.log('Cancel ajax cancel_source_auto')
        } catch(err) {
          // Do nothing
        }
        this.cancel_source_auto = axios.CancelToken.source()
      }

      if (param === 'all' || param === 'list') {
        try {
          this.cancel_source_list.cancel()
          console.log('Cancel ajax cancel_source_list')
        } catch(err) {
          // Do nothing
        }
        this.cancel_source_list = axios.CancelToken.source()
      }

    },
    showPDF: function(ticket_number) {

      this.dlgLoadPDF = true
      let self = this
      this.$nextTick(() => {
        self.$refs.ifra.src = self.site_url + 'submissions/pdf/' + ticket_number
        //document.getElementById('ifra').src = this.site_url + '/submissions/pdf/' + ticket_number
      })

    },
    /* Clear autocompletion */
    clearAuto: function(control) {
      switch (control) {
        case 'user_contact_names':
          this.user_contact_names = []
          break
        case 'ticket_numbers':
          this.ticket_numbers = []
      }
    },
    /* Filter queries */
    filterQuery: function(search_type, search_string) {

      this.initAjaxCall('auto')

      let self = this
      let headers = {
          'Content-Type': 'application/json'
      }
      // Set autocompletion loader
      switch (search_type) {
        case 'user_contact_names':
          //self.user_contact_name_loading = true
          self.user_contact_names = []
          var array_field = new Object()
          array_field['label'] = search_string
          array_field['value'] = search_string
          self.user_contact_names.push(array_field)
          self.UserContactName.value = search_string
          break
        case 'ticket_numbers':
          self.ticket_numbers = []
          var array_field = new Object()
          array_field['label'] = search_string
          array_field['value'] = search_string
          self.ticket_numbers.push(array_field)
          self.TicketNumber.value = search_string
      }
      // Ajax call for filter's query by search type
      axios.post(this.site_url + 'submissions/auto', querystring.stringify(
        { search_type: search_type, search_string: search_string }
      ), { cancelToken: this.cancel_source_auto.token }, headers)
      .then(response => {
        switch (search_type) {
          case 'user_contact_names':
            self.user_contact_names = []
            if (response.data.length) {
              self.user_contact_names = response.data
            }
            var array_field = new Object()
            array_field['label'] = search_string
            array_field['value'] = search_string
            self.user_contact_names.push(array_field)
            self.UserContactName.value = search_string
            self.user_contact_name_loading = false
            break
          case 'ticket_numbers':
            self.ticket_numbers = []
            if (response.data.length) {
              self.ticket_numbers = response.data
            }
            var array_field = new Object()
            array_field['label'] = search_string
            array_field['value'] = search_string
            self.ticket_numbers.push(array_field)
            self.TicketNumber.value = search_string
            self.ticket_number_loading = false
        }
      })
      .catch(e => {
        if (axios.isCancel(e)) {
          console.log('Request canceled');
        } else {
          switch (search_type) {
            case 'user_contact_names':
              this.error_message = 'System error, could not load contact name auto. Please try again.'
              this.is_error = true
              setTimeout(() => this.is_error = false, 5000);
              break
            case 'ticket_numbers':
              this.error_message = 'System error, could not load ticket number auto. Please try again.'
              this.is_error = true
              setTimeout(() => this.is_error = false, 5000);
          }
        }
        switch (search_type) {
          case 'user_contact_names':
            self.user_contact_name_loading = false
            break
          case 'ticket_numbers':
            self.ticket_number_loading = false
        }
      })
    },
    populateSubmissionList: function(is_load) {

      this.table_loading = true

      this.initAjaxCall('list')

      let self = this
      let headers = {
        'Content-Type': 'application/json'
      }

      // Get filter fields
      let date_from = ''
      let date_to = ''
      let questionnaire_cat_id = ''
      let user_contact_name = ''
      let ticket_number = ''

      if ( ! is_load) {
        this.current_page_number = 0
        this.visited_max_page_number = -1
        this.array_resultset = []
        this.pagination_resultset.page = 1

        this.tmp_user_contact_name = ''
        this.tmp_ticket_number = ''

        if (this.datefr === null || this.datefr === '') {
          // Do nothing
        } else {
          var array_date_from = this.datefr.split('-')
          date_from = array_date_from[2] + '-' + array_date_from[1] + '-' + array_date_from[0]
          this.tmp_date_from = this.datefr
        }
        if (this.dateto === null || this.dateto === '') {
          // Do nothing
        } else {
          var array_date_to = this.dateto.split('-')
          date_to = array_date_to[2] + '-' + array_date_to[1] + '-' + array_date_to[0]
          this.tmp_date_to = this.dateto
        }
        questionnaire_cat_id = this.QuestionnaireCategoryID
        this.tmp_questionnaire_cat_id = this.QuestionnaireCategoryID
        if (typeof this.UserContactName === 'undefined' || this.UserContactName === null) {
          // Do nothing
        } else if (Object.keys(this.UserContactName).length === 0 && this.UserContactName.constructor === Object) {
          // Do nothing
        } else {
          user_contact_name = this.UserContactName.value
          this.tmp_user_contact_name = this.UserContactName.value
        }
        if (typeof this.TicketNumber === 'undefined' || this.TicketNumber === null) {
          // Do nothing
        } else if (Object.keys(this.TicketNumber).length === 0 && this.TicketNumber.constructor === Object) {
          // Do nothing
        } else {
          ticket_number = this.TicketNumber.value
          this.tmp_ticket_number = this.TicketNumber.value
        }
        // Close the autocompletion
        if (document.getElementsByClassName('v-menu__content theme--light menuable__content__active v-autocomplete__content')[0] != null) {
          document.getElementsByClassName('v-menu__content theme--light menuable__content__active v-autocomplete__content')[0].style.display = 'none'
        }
      } else {
        var array_date_from = this.tmp_date_from.split('-')
        date_from = array_date_from[2] + '-' + array_date_from[1] + '-' + array_date_from[0]
        var array_date_to = this.tmp_date_to.split('-')
        date_to = array_date_to[2] + '-' + array_date_to[1] + '-' + array_date_to[0]
        questionnaire_cat_id = this.tmp_questionnaire_cat_id
        user_contact_name = this.tmp_user_contact_name
        ticket_number = this.tmp_ticket_number
      }
      if ((this.current_page_number == 0 && this.visited_max_page_number == -1) || (((this.current_page_number % this.ajax_rotate_page_gap) == 0) && (this.current_page_number > this.visited_max_page_number))) {
        try {
          this.cancel_source_list.cancel()
        } catch(err) {
          // Do nothing
        }

        this.cancel_source_list = axios.CancelToken.source()
        axios.post(this.site_url + 'submissions/list', querystring.stringify(
          { date_from: date_from, date_to: date_to, questionnaire_cat_id: questionnaire_cat_id, user_contact_name: user_contact_name, ticket_number: ticket_number, current_page_number: (this.current_page_number / this.ajax_rotate_page_gap), rows_per_ajax_items: this.rows_per_ajax_items }
        ), { cancelToken: this.cancel_source_list.token }, headers)
        .then(response => {
          let data = response.data
          if (data[0].total_records) {
            for (var i = 0, length = data.length; i < length; i++) {
              self.array_resultset.push(data[i])
            }
            self.disabled_download = false
          } else {
            self.array_resultset = []
            self.disabled_download = true
          }
          if (self.current_page_number == 0) {
            self.total_resultsets = data[0].total_records
          }
          self.resultset = []
          if (self.array_resultset.length) {
            var start_index = self.current_page_number * self.rows_per_page_items
            if ((start_index + self.rows_per_page_items) > self.array_resultset.length) {
              var end_index = start_index + (self.array_resultset.length - start_index)
            } else {
              var end_index = start_index + self.rows_per_page_items
            }
            for (var i = start_index; i < end_index; i++) {
              self.resultset.push(self.array_resultset[i])
            }
          }
          if (self.current_page_number == 0) {
            self.pagination_resultset.page = 1
          }
          self.visited_max_page_number = self.current_page_number
          self.table_loading = false
        })
        .catch(e => {
          if (axios.isCancel(e)) {
            console.log('Request canceled.');
          } else {
            //self.$store.commit('setFailMessage', 'Failed to load user list.')
          }
          self.table_loading = false
        })
      } else {
        self.resultset = []
        if (self.array_resultset.length) {
          var start_index = self.current_page_number * self.rows_per_page_items
          if ((start_index + self.rows_per_page_items) > self.array_resultset.length) {
            var end_index = start_index + (self.array_resultset.length - start_index)
          } else {
            var end_index = start_index + self.rows_per_page_items
          }
          for (var i = start_index; i < end_index; i++) {
            self.resultset.push(self.array_resultset[i])
          }
        }
        if (self.current_page_number == 0) {
          self.pagination_resultset.page = 1
        }
        self.resultset_loading = false
        self.table_loading = false
      }
    }
  },
  watch: {
    user_contact_name_search (val) {
      if (val == null || val == '') {
        this.user_contact_names = []
        this.UserContactName = {}
      } else {
        val && val !== this.UserContactName && this.filterQuery('user_contact_names', val)
      }
    },
    ticket_number_search (val) {
      if (val == null || val == '') {
        this.ticket_numbers = []
        this.TicketNumber = {}
      } else {
        val && val !== this.TicketNumber && this.filterQuery('ticket_numbers', val)
      }
    },
    pagination_resultset: {
      handler () {
//alert(this.pagination_resultset.itemsPerPage)
        if (this.pagination_resultset.itemsPerPage == -1) {
          this.rows_per_page_items = 10000000
          this.rows_per_ajax_items = 200000000
          this.ajax_rotate_page_gap = 20000000
          this.array_resultset = []
          this.resultset = []
          this.array_resultset = []
          this.previous_items_per_page = -1
          this.visited_max_page_number = -1
          this.current_page_number = 0
          this.populateSubmissionList(true)
          return
        } else {
//alert('this.previous_items_per_page='+this.previous_items_per_page)
          if (this.previous_items_per_page == -1) {
            this.rows_per_page_items = this.pagination_resultset.itemsPerPage
            this.rows_per_ajax_items = this.rows_per_page_items * 10 * 2
            this.ajax_rotate_page_gap = this.rows_per_page_items * 2
            this.previous_items_per_page = 0
            this.resultset = []
            this.array_resultset = []
            this.visited_max_page_number = -1
            this.current_page_number = 0
            this.populateSubmissionList(true)
            return
          }
        }
        this.current_page_number = this.pagination_resultset.page - 1
        if (this.current_page_number < 0) {
          this.current_page_number = 0
          this.visited_max_page_number = -1
        }
        if (this.current_page_number >= 0 && this.visited_max_page_number > -1) {
          this.populateSubmissionList(true)
        }
      },
      deep: true,
    },
/*
    pagination_resultset: {
      handler () {
        this.current_page_number = this.pagination_resultset.page - 1
        if (this.current_page_number < 0) {
          this.current_page_number = 0
        }
        this.populateSubmissionList(true)
      },
      deep: true
    }
*/
    DateItemsSelected () {
      let self = this
      this.show_dates = false
      if (this.DateItemsSelected == 'Today') {
        this.datefr_raw = moment().format('YYYY-MM-DD')
        this.dateto_raw = moment().format('YYYY-MM-DD')
        this.$nextTick()
        .then(function () {
          // DOM updated
          self.populateSubmissionList(false)
        })
      } else if (this.DateItemsSelected == 'Last 7 Days') {
        this.datefr_raw = moment().subtract(1, 'weeks').format('YYYY-MM-DD')
        this.dateto_raw = moment().format('YYYY-MM-DD')
        this.$nextTick()
        .then(function () {
          // DOM updated
          self.populateSubmissionList(false)
        })
      } else if (this.DateItemsSelected == 'Last 30 Days') {
        this.datefr_raw = moment().subtract(1, 'months').format('YYYY-MM-DD')
        this.dateto_raw = moment().format('YYYY-MM-DD')
        this.$nextTick()
        .then(function () {
          // DOM updated
          self.populateSubmissionList(false)
        })
      } else if (this.DateItemsSelected == 'Last 12 Months') {
        this.datefr_raw = moment().subtract(1, 'years').format('YYYY-MM-DD')
        this.dateto_raw = moment().format('YYYY-MM-DD')
        this.$nextTick()
        .then(function () {
          // DOM updated
          self.populateSubmissionList(false)
        })
      } else if (this.DateItemsSelected == 'Custom') {
        this.show_dates = true
      }
    },
    datefr_raw () {
      this.datefr = this.formatDateFr(this.datefr_raw)
      /*
      if (this.loading) {
        return
      }
      this.loading = true
      let datefr_raw = ''
      let dateto_raw = ''
      if (this.datefr_raw != null) {
        datefr_raw = this.formatDateFr(this.datefr_raw)
      }
      if (this.dateto_raw != null) {
        dateto_raw = this.formatDateTo(this.dateto_raw)
      }
      if (datefr_raw == '' || dateto_raw == '') {
        // Do nothing
      } else {
        let array_start_date = datefr_raw.split('-')
        let array_end_date = dateto_raw.split('-')
        let date_start = new Date(array_start_date[2], array_start_date[1] - 1, array_start_date[0])
        let date_end = new Date(array_end_date[2], array_end_date[1] - 1, array_end_date[0])
        if (date_end < date_start) {
          this.dateto = this.formatDateTo(this.datefr_raw)
        }
      }
      this.loading = false
      */
    },
    dateto_raw () {
      this.dateto = this.formatDateTo(this.dateto_raw)

      /*
      if (this.loading) {
        return
      }
      this.loading = true
      let datefr_raw = ''
      let dateto_raw = ''
      if (this.datefr_raw != null) {
        datefr_raw = this.formatDateFr(this.datefr_raw)
      }
      if (this.dateto_raw != null) {
        dateto_raw = this.formatDateTo(this.dateto_raw)
      }
      if (datefr_raw == '' || dateto_raw == '') {
        // Do nothing
      } else {
        let array_start_date = datefr_raw.split('-')
        let array_end_date = dateto_raw.split('-')
        let date_start = new Date(array_start_date[2], array_start_date[1] - 1, array_start_date[0])
        let date_end = new Date(array_end_date[2], array_end_date[1] - 1, array_end_date[0])
        if (date_end < date_start) {
          this.datefr = this.formatDateFr(this.dateto_raw)
        }
      }
      this.loading = false
      */
    },
  }
}
</script>
<style lang="scss" scoped>
  .container #app .v-application--wrap .layout, .container #app .v-application--wrap .row {
    flex: 0 1 auto;
    margin-top: 10px;
  }
  ::v-deep .v-data-table__empty-wrapper {
    text-align: left;
  }
  .completeMessage {
    padding: 10px;
    border: 1px solid green;
    color: green;
    background-color: #f0fedd;
  }
  .errorMessage {
    padding: 10px;
    border: 1px solid red;
    color: red;
    background-color: #feded3;
  }
</style>