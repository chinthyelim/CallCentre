<template>
  <v-app>
    <v-layout row wrap>
      <div class="telephoneIssue">
        <div class="message">
          <div class="completeMessage" v-show="is_complete">{{ $store.state.feedback }}</div>
          <div class="errorMessage" v-show="is_not_complete">{{ $store.state.feedback }}</div>
          <div class="errorMessage" v-show="is_txt_empty">Please fill in the required field!</div>
          <div class="errorMessage" v-show="is_dd_empty">Please select a selection!</div>
        </div>
        <div class="telephoneIssueBody">
          <h1>Acsess Job{{ selected_questionnaire }} </h1>
          <label class="subjectLabel" for="ddQuestionnaire">Questionnaire:</label>
          <v-select
            id="ddQuestionnaire"
            :items="option_questionnaire_groups"
            item-text="name"
            item-value="id"
            v-model="DDQuestionnaire"
            @change="initQuestionnaire"
            dense
          >
            <!-- Template for render data when the select is expanded -->
            <template
              slot="item"
              slot-scope="data"
            >
              <!-- Divider and Header-->
              <template v-if="typeof data.item !== 'object'">
                <v-list-item-content class="select-header" v-text="data.item"/>
              </template>
              <!-- Normal item -->
              <template v-else>
                <v-list-item-content>
                  <v-list-item-title class="select-option" v-html="data.item.name"/>
                  <!--<v-list-tile-sub-title class="select-group" v-html="data.item.group"/>//-->
                </v-list-item-content>
              </template>
            </template>
          </v-select>
          <!--<select class="cbo" id="ddQuestionnaire" v-model="DDQuestionnaire" @change="initQuestionnaire">
            <option value=""> --- Select --- </option>
            <optgroup v-for="(group, name) in option_questionnaire_groups" v-bind:key="name" v-bind:label="name">
              <option class="subjectCaption" v-for="option in group" :value="option.id" v-bind:key="option.id">
                {{ option.name }}
              </option>
            </optgroup>
          </select>//-->
          <div class="divNavigation">
            <div :class="{ 'divNavRight' : true, 'enabled' : (questionnaire_log.length > 1) && (current_seq <= questionnaire_log.length - 1) }">
              <button class="button" @click="navigateQuestion('forward')">Forward</button>
            </div>
            <div :class="{ 'divNavLeft' : true, 'enabled' : (questionnaire_log.length > 1) && (current_seq > 1) }">
              <button class="button" @click="navigateQuestion('backward')">Backward</button>
            </div>
          </div>
          <div class="divStart" v-show="is_complete">
            <button class="button" @click="confirmInitQuestionnaire">Start</button>
          </div>
          <ol>
            <li v-for="(q, index) in questionnaire_cat1" :key="index + '-1'" v-show="q.show && DDQuestionnaire == '-1'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-1'" v-model="questionnaire_cat1[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-1" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat2" :key="index + '-2'" v-show="q.show && DDQuestionnaire == '-2'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-2'" v-model="questionnaire_cat2[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-2" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat3" :key="index + '-3'" v-show="q.show && DDQuestionnaire == '-3'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-3'" v-model="questionnaire_cat3[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-3" :q="q" @flowValidate2Question="flowValidate2Question" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat4" :key="index + '-4'" v-show="q.show && DDQuestionnaire == '-4'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-4'" v-model="questionnaire_cat4[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-4" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat5" :key="index + '-5'" v-show="q.show && DDQuestionnaire == '-5'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-5'" v-model="questionnaire_cat5[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-5" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat6" :key="index + '-6'" v-show="q.show && DDQuestionnaire == '-6'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-6'" v-model="questionnaire_cat6[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-6" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat7" :key="index + '-7'" v-show="q.show && DDQuestionnaire == '-7'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-7'" v-model="questionnaire_cat7[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-7" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat8" :key="index + '-8'" v-show="q.show && DDQuestionnaire == '-8'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-8'" v-model="questionnaire_cat8[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-8" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat9" :key="index + '-9'" v-show="q.show && DDQuestionnaire == '-9'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-9'" v-model="questionnaire_cat9[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-9" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
            <li v-for="(q, index) in questionnaire_cat10" :key="index + '-10'" v-show="q.show && DDQuestionnaire == '-10'">
              <QuestionAndAnswer v-if="q.show && DDQuestionnaire == '-10'" v-model="questionnaire_cat10[index]" :DDQuestionnaire="DDQuestionnaire" :qt="-10" :q="q" :site_url="site_url" :is_txt_empty="is_txt_empty" :is_dd_empty="is_dd_empty" :option_list1="option_list1" @initTXTEmpty="initTXTEmpty" @initDDEmpty="initDDEmpty" @flowQuestion="flowQuestion" @flowValidate2Question="flowValidate2Question" @flowValidate3Question="flowValidate3Question" @flowQuestionRDO="flowQuestionRDO" @flowValidateQuestion="flowValidateQuestion"/>
            </li>
          </ol>
        </div><!-- End of telephoneIssueBody//-->
        <!-- Confirm switch questionnaire popup //-->
        <modal name="ConfirmSwitchQuestionnaire" width="360" height="160">
          <div class="divModal">
            <p>Confirm switch questionnaire?</p>
            <div class="divConfirmSwitchQuestionnaire">
              <button class="button" @click="confirmSwitchQuestionnaire('ok')">OK</button>
              &nbsp;&nbsp;
              <button class="button norigtmargin" @click="confirmSwitchQuestionnaire('cancel')">Cancel</button>
            </div>
          </div>
        </modal>

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
      </div>
    </v-layout row wrap>
  </v-app>
</template>

<script>
import { serverBus } from '../app'
import QuestionAndAnswer from './QuestionAndAnswer'
import { store } from '../store/store'

export default {
  name: 'telephone-issue',
  components: {
    QuestionAndAnswer
  },
  data () {
    return {
      // Cat 1
      questionnaire_cat1: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '3', n_goto: '11', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '12', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: "Question bla bla 5?", id: '5', make_note: false, make_note_n_only: false, y_goto: '6', n_goto: '13', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: false, make_note_n_only: false, y_goto: '7', n_goto: '13', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 7?', id: '7', make_note: false, make_note_n_only: false, y_goto: '14', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 8?', id: '8', make_note: false, make_note_n_only: false, y_goto: '9', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 9?', id: '9', make_note: false, make_note_n_only: false, y_goto: '10', n_goto: '13', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 10?', id: '10', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 11?', id: '11', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 11?', id: '12', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 12?', id: '13', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 13?', id: '14', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 2
      questionnaire_cat2: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '-1', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '3', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: false, make_note_n_only: false, y_goto: '11', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: false, make_note_n_only: false, y_goto: '6', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: true, make_note_n_only: false, y_goto: '7', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 7?', id: '7', make_note: false, make_note_n_only: false, y_goto: '8', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide: 'rd.docx', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 8?', id: '8', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '9', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 9?', id: '9', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 10?', id: '10', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 11?', id: '11', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 3
      questionnaire_cat3: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '3', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: true, make_note_n_only: false, y_goto: '6', n_goto: '6', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 7?', id: '7', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 8?', id: '8', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 4
      questionnaire_cat4: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '2', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 1, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '3', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '17', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: true, make_note_n_only: false, y_goto: '7', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        /*
        { question: 'Question bla bla 5?', id: '4', make_note: true, make_note_n_only: false, y_goto: '6', n_goto: '6', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '5', make_note: true, make_note_n_only: false, y_goto: '8', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 7?', id: '6', make_note: false, make_note_n_only: false, y_goto: '16', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 8?', id: '7', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide:  '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        */
        { question: 'Question bla bla 9?', id: '7', make_note: false, make_note_n_only: false, y_goto: '8', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide:  '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 10?', id: '8', make_note: false, make_note_n_only: false, y_goto: '9', n_goto: '9', rdo: '', txt: '', mask_txt: '', is_guide: 'pcr.pdf', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 11?', id: '9', make_note: false, make_note_n_only: false, y_goto: '14', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 12?', id: '10', make_note: true, make_note_n_only: false, y_goto: '11', n_goto: '11', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 13?', id: '11', make_note: false, make_note_n_only: false, y_goto: '12', n_goto: '13', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 14?', id: '12', make_note: false, make_note_n_only: false, y_goto: '13', n_goto: '13', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: 'bar.pdf' },
        { question: 'Question bla bla 15?', id: '13', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 16?', id: '14', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 17?', id: '15', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 18?', id: '16', make_note: false, make_note_n_only: false, y_goto: '7', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Online', n_value: 'Offline', downloadable_guide: '' },
        // Added on 02/12/2020 by Chin
        { question: 'Question bla bla 19?', id: '17', make_note: false, make_note_n_only: false, y_goto: '18', n_goto: '19', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: "Question bla bla 20?", id: '18', make_note: true, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 21?', id: '19', make_note: true, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 5
      questionnaire_cat5: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '2', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 1, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: true, y_goto: '3', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: 'pcr.pdf', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: false, make_note_n_only: false, y_goto: '6', n_goto: '6', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: false, make_note_n_only: false, y_goto: '8', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 7?', id: '7', make_note: false, make_note_n_only: false, y_goto: '8', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide: 'rast.pdf', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 8?', id: '8', make_note: true, make_note_n_only: false, y_goto: '14', n_goto: '14', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        /* Removed on 02/12/2020 by Chin
        { question: 'Question bla bla 9?', id: '9', make_note: false, make_note_n_only: false, y_goto: '11', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 10?', id: '10', make_note: true, make_note_n_only: false, y_goto: '12', n_goto: '12', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 11?', id: '11', make_note: false, make_note_n_only: false, y_goto: '13', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        */
        { question: 'Question bla bla 12?', id: '12', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        /* Removed on 02/12/2020 by Chin
        { question: 'Question bla bla 13?', id: '13', make_note: true, make_note_n_only: false, y_goto: '10', n_goto: '10', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
        */
        // Added on 02/12/2020 by Chin
        { question: 'Question bla bla 14?', id: '14', make_note: false, make_note_n_only: false, y_goto: '15', n_goto: '12', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 15?', id: '15', make_note: false, make_note_n_only: false, y_goto: '16', n_goto: '17', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: "Question bla bla 16?", id: '16', make_note: true, make_note_n_only: false, y_goto: '12', n_goto: '12', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 17?', id: '17', make_note: true, make_note_n_only: false, y_goto: '12', n_goto: '12', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 6
      questionnaire_cat6: [
        // Added on 02/12/2020 by Chin
        { question: 'Question bla bla 1?', id: '1', make_note: true, make_note_n_only: false, y_goto: '8', n_goto: '8', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: false, make_note_n_only: false, y_goto: '6', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: true, make_note_n_only: false, y_goto: '7', n_goto: '7', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 7?', id: '7', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 8?', id: '8', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }

      ],
      // Cat 7
      questionnaire_cat7: [
        // Added on 02/12/2020 by Chin
        { question: 'Question bla bla 1?', id: '1', make_note: true, make_note_n_only: false, y_goto: '6', n_goto: '6', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: true, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 8
      questionnaire_cat8: [
        { question: 'Question bla bla 1?', id: '1', make_note: true, make_note_n_only: false, y_goto: '2', n_goto: '2', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '3', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: true, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 9
      questionnaire_cat9: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '2', n_goto: '2', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: true, make_note_n_only: false, y_goto: '3', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' }
      ],
      // Cat 10
      questionnaire_cat10: [
        { question: 'Question bla bla 1?', id: '1', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '2', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: true, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 2?', id: '2', make_note: false, make_note_n_only: false, y_goto: '3', n_goto: '3', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 3?', id: '3', make_note: false, make_note_n_only: false, y_goto: '4', n_goto: '4', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 4?', id: '4', make_note: false, make_note_n_only: false, y_goto: '5', n_goto: '5', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: false, show_next_button_only: true, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 5?', id: '5', make_note: false, make_note_n_only: false, y_goto: '6', n_goto: '6', rdo: '', txt: '', mask_txt: '', is_guide: 'PowerCyclingAFoxtelBox.pdf', is_options: 0, is_ticket_number: false, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },
        { question: 'Question bla bla 6?', id: '6', make_note: false, make_note_n_only: false, y_goto: '0', n_goto: '0', rdo: '', txt: '', mask_txt: '', is_guide: '', is_options: 0, is_ticket_number: true, show_next_button_only: false, show: false, y_value: 'Yes', n_value: 'No', downloadable_guide: '' },

      ],
      dlgLoadPDF: false,
      option_questionnaire_groups: [
        { id: '', name: ' --- Select --- ', group: '' },
        { header: 'Category 1' },
        { id: '-1', name: 'Sub Category 1', group: 'Category 1' },
        { id: '-2', name: 'Sub Category  2', group: 'Category 1' },
        { id: '-3', name: 'Sub Category  3', group: 'Category 1' },
        { divider: true },
        { header: 'Category  2' },
        { id: '-4', name: 'Sub Category 1', group: 'Category  2' },
        { id: '-5', name: 'Sub Category 2', group: 'Category  2' },
        { header: 'Category 3' },
        { id: '-6', name: 'Sub Category 1', group: 'Category 3' },
        { id: '-7', name: 'Sub Category 2', group: 'Category 3' },
        { header: 'Category 4' },
        { id: '-8', name: 'Sub Category 1', group: 'FOXTEL' },
        { id: '-9', name: 'Sub Category 2', group: 'FOXTEL' },
        { id: '-10', name: 'Sub Category 3', group: 'FOXTEL' }
      ],
      option_list1: [
        { name: 'Cat 1'},
        { name: 'Cat 2'},
        { name: 'Cat 2'},
        { name: 'Cat 4'},
        { name: 'Cat 5'},
        { name: 'Cat 6'},
        { name: 'Cat 7' }
      ],
      DDQuestionnaire: '',
      selected_questionnaire: '',
      is_complete: false,
      is_not_complete: false,
      is_txt_empty: false,
      is_dd_empty: false,
      prior_questionnaire_log: [],
      questionnaire_log: [],
      current_seq: 0,
      // http://localhost/test/laravel/laravel/
      site_url: 'http://xxx.xxx.xx/callcentre/',
      site_localstorage_id: 'ah_telephoneissue',
      questionnaires_start_question: [],
      start_question_id: '1'
    }
  },
  mounted() {
    // Initialise - Find the start question of the questionnaire
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat1, '-1')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat2, '-2')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat3, '-3')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat4, '-4')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat5, '-5')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat6, '-6')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat7, '-7')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat8, '-8')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat9, '-9')
    this.initStartQuestionOfQuestionnaire(this.questionnaire_cat10, '-10')

    // Check if localstorage has content, eg. a screnario that somehow the browser is refreshed by accident so load previous data
    let state_questionnaire_id = localStorage.getItem(this.site_localstorage_id + '.state_questionnaire_id')
    if (state_questionnaire_id) {
      this.DDQuestionnaire = state_questionnaire_id

      let state_questionnaire_log = localStorage.getItem(this.site_localstorage_id + '.state_questionnaire_log')
      if (state_questionnaire_log) {
        this.questionnaire_log = JSON.parse(state_questionnaire_log)
        //if (this.questionnaire_log.length > 1) {
        //  this.questionnaire_log.splice(-1, 1)
        //}
      }

      let state_questionnaire_questions = localStorage.getItem(this.site_localstorage_id + '.state_questionnaire_questions')
      if (state_questionnaire_questions !== null) {
        let array_state_questionnaire_questions = JSON.parse(state_questionnaire_questions)
        for (var i = 0, length = array_state_questionnaire_questions.length; i < length; i++) {
          if (array_state_questionnaire_questions[i].id == this.questionnaire_log[this.questionnaire_log.length - 1].QuestionID) {
            array_state_questionnaire_questions[i].show = true
          } else {
            array_state_questionnaire_questions[i].show = false
          }
        }
        this.current_seq = this.questionnaire_log.length

        switch (this.DDQuestionnaire) {
          case '-1':
            this.selected_questionnaire = ' - CAT 1'
            this.questionnaire_cat1 = array_state_questionnaire_questions
            break
          case '-2':
            this.selected_questionnaire = ' - CAT 2'
            this.questionnaire_cat2 = array_state_questionnaire_questions
            break
          case '-3':
            this.selected_questionnaire = ' - CAT 3'
            this.questionnaire_cat3 = array_state_questionnaire_questions
            break
          case '-4':
            this.selected_questionnaire = ' - CAT 4'
            this.questionnaire_cat4 = array_state_questionnaire_questions
            break
          case '-5':
            this.selected_questionnaire = ' - CAT 5'
            this.questionnaire_cat5 = array_state_questionnaire_questions
            break
          case '-6':
            this.selected_questionnaire = ' - CAT 6'
            this.questionnaire_cat6 = array_state_questionnaire_questions
            break
          case '-7':
            this.selected_questionnaire = ' - CAT 7'
            this.questionnaire_cat7 = array_state_questionnaire_questions
            break
          case '-8':
            this.selected_questionnaire = ' - CAT 8'
            this.questionnaire_cat8 = array_state_questionnaire_questions
            break
          case '-9':
            this.selected_questionnaire = ' - CAT 9'
            this.questionnaire_cat9 = array_state_questionnaire_questions
            break
          case '-10':
            this.selected_questionnaire = ' - CAT 10'
            this.questionnaire_cat10 = array_state_questionnaire_questions
            break
          default:
            this.selected_questionnaire = ''
        }
      }
    }
    if (this.selected_questionnaire == '') {
      this.DDQuestionnaire = ''
    }
  },
  methods: {
    showPDF: function(ticket_number) {

      this.dlgLoadPDF = true
      let self = this
      this.$nextTick(() => {
        self.$refs.ifra.src = self.site_url +'submissions/pdf/' + ticket_number
        //document.getElementById('ifra').src = this.site_url + '/submissions/pdf/' + ticket_number
      })

    },
    initTXTEmpty: function() {
      this.is_txt_empty = false
    },
    initDDEmpty: function() {
      this.is_dd_empty = false
    },
    setFocusInput(array_question) {
/*
      let start_index = 0
      for (var i = 0, length = array_question.length; i < length; i++) {
        if (array_question[i].show) {
          start_index = i
          break
        }
      }

      if (array_question[start_index].make_note) {
        serverBus.$emit('focusInput')
      }
      if (array_question[start_index].is_options != 0) {
        serverBus.$emit('focusInput')
      }
      if (array_question[start_index].make_note_n_only && array_question[start_index].rdo === array_question[start_index].n_value) {
        serverBus.$emit('focusInput')
      }
      if (array_question[start_index].is_ticket_number) {
        serverBus.$emit('focusInput')
      }
*/
      serverBus.$emit('focusInput')
    },
    // Proceed switching questionnaire
    confirmSwitchQuestionnaire: function(response) {
      if (response == 'ok') {
        this.prior_questionnaire_log = []
        this.confirmInitQuestionnaire()
      } else {
        if (this.questionnaire_log.length) {
          let array_field = this.questionnaire_log[0]
          this.DDQuestionnaire = array_field['QuestionnaireID']
        } else {
          this.DDQuestionnaire = ''
        }
      }
      this.$modal.hide('ConfirmSwitchQuestionnaire')
      // Check if focus control?
      switch (this.DDQuestionnaire) {
        case '-1':
          // Cat 1
          this.setFocusInput(this.questionnaire_cat1)
          break
        case '-2':
          // Cat 2
          this.setFocusInput(this.questionnaire_cat2)
          break
        case '-3':
          // Cat 3
          this.setFocusInput(this.questionnaire_cat3)
          break
        case '-4':
          // Cat 4
          this.setFocusInput(this.questionnaire_cat4)
          break
        case '-5':
          // Cat 5
          this.setFocusInput(this.questionnaire_cat5)
          break
        case '-6':
          // Cat 6
          this.setFocusInput(this.questionnaire_cat6)
          break
        case '-7':
          // Cat 7
          this.setFocusInput(this.questionnaire_cat7)
          break
        case '-8':
          // Cat 8
          this.setFocusInput(this.questionnaire_cat8)
          break
        case '-9':
          // Cat 9
          this.setFocusInput(this.questionnaire_cat9)
          break
        case '-10':
          // Cat 10
          this.setFocusInput(this.questionnaire_cat10)
      }

    },
    // Find the original start question ID of the questionnaire
    initStartQuestionOfQuestionnaire: function(array_question, questionnaire_id) {
      let start_question_id = 1
      for (var i = 0, length = array_question.length; i < length; i++) {
        if (array_question[i].show) {
          start_question_id = array_question[i].id
          break
        }
      }
      let array_field = new Object()
      array_field['QuestionnaireID'] = questionnaire_id
      array_field['QuestionID'] = start_question_id
      this.questionnaires_start_question.push(array_field)
    },
    // Show current question
    showCurrentQuestion: function(array_question, question_id) {
      for (var i = 0, length = array_question.length; i < length; i++) {
        if (array_question[i].id == question_id) {
          array_question[i].show = true
        } else {
          array_question[i].show = false
        }
      }
      return array_question
    },
    // Initialise note and dropdown for default first question
    initCurrentQuestionnaire: function(array_question, questionnaire_id) {
      let start_questionnaire_id = 1
      // Find the original start question of the questionnaire
      for (var j = 0, jlength = this.questionnaires_start_question.length; j < jlength; j++) {
        if (this.questionnaires_start_question[j].QuestionnaireID == questionnaire_id) {
          start_questionnaire_id = this.questionnaires_start_question[j].QuestionID
          break
        }
      }
      this.start_question_id = start_questionnaire_id
      for (var i = 0, length = array_question.length; i < length; i++) {
        array_question[i].rdo = ''
        array_question[i].txt = ''
        array_question[i].mask_txt = ''
        if (array_question[i].id == start_questionnaire_id) {
          array_question[i].show = true
        } else {
          array_question[i].show = false
        }
      }
      return array_question
    },
    // Validate note and dropdown
    validateCurrentQuestion: function(array_question, question_id) {
      for (var i = 0, length = array_question.length; i < length; i++) {
        if (array_question[i].id == question_id) {
          if (array_question[i].make_note && array_question[i].txt.trim() === '') {
            this.is_txt_empty = true
            serverBus.$emit('focusInput')
          }
          if (array_question[i].is_options != 0 && array_question[i].txt.trim() === '') {
            this.is_dd_empty = true
            serverBus.$emit('focusInput')
          }
          if (array_question[i].make_note_n_only && array_question[i].rdo === array_question[i].n_value && array_question[i].txt.trim() === '') {
            this.is_txt_empty = true
            serverBus.$emit('focusInput')
          }
          if (array_question[i].is_ticket_number && array_question[i].mask_txt.length < 8) {
            this.is_txt_empty = true
            serverBus.$emit('focusInput')
          }
          break
        }
      }
    },
    // Goto next question
    navigateQuestion: function(direction) {

      if (direction == 'backward' && (this.questionnaire_log.length <= 1 || ! ((this.questionnaire_log.length > 1) && (this.current_seq > 1)))) {
        return
      } else if (direction == 'forward' && (this.questionnaire_log.length <= 1 || ! ((this.questionnaire_log.length > 1) && (this.current_seq <= this.questionnaire_log.length - 1)))) {
        return
      }
      // Validate note and dropdown
      this.is_txt_empty = false
      this.is_dd_empty = false
      let x
      if (this.current_seq >= 1) {
        if (this.current_seq >= this.questionnaire_log.length) {
          x = this.current_seq - 1
        } else {
          x = this.current_seq
        }
        x -= 1
      } else {
        x = this.current_seq
      }
      var array_field2 = this.questionnaire_log[x]

      switch (array_field2['QuestionnaireID']) {
        case '-1':
          // Cat 1
          this.validateCurrentQuestion(this.questionnaire_cat1, array_field2['Question'])
          break
        case '-2':
          // Cat 2
          this.validateCurrentQuestion(this.questionnaire_cat2, array_field2['Question'])
          break
        case '-3':
          // Cat 3
          this.validateCurrentQuestion(this.questionnaire_cat3, array_field2['Question'])
          break
        case '-4':
          // Cat 4
          this.validateCurrentQuestion(this.questionnaire_cat4, array_field2['Question'])
          break
        case '-5':
          // Cat 5
          this.validateCurrentQuestion(this.questionnaire_cat5, array_field2['Question'])
          break
        case '-6':
          // Cat 6
          this.validateCurrentQuestion(this.questionnaire_cat6, array_field2['Question'])
          break
        case '-7':
          // Cat 7
          this.validateCurrentQuestion(this.questionnaire_cat7, array_field2['Question'])
          break
        case '-8':
          // Cat 8
          this.validateCurrentQuestion(this.questionnaire_cat8, array_field2['Question'])
          break
        case '-9':
          // Cat 9
          this.validateCurrentQuestion(this.questionnaire_cat9, array_field2['Question'])
          break
        case '-10':
          // Cat 10
          this.validateCurrentQuestion(this.questionnaire_cat10, array_field2['Question'])
      }

      if (this.is_txt_empty || this.is_dd_empty) {
        return
      }
      if (direction === 'backward') {
        this.current_seq -= 1
      } else {
        this.current_seq += 1
      }

      var array_field = this.questionnaire_log[this.current_seq - 1]

      switch (array_field['QuestionnaireID']) {
        case '-1':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat1 = this.showCurrentQuestion(this.questionnaire_cat1, array_field['QuestionID'])
          break
        case '-2':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat2 = this.showCurrentQuestion(this.questionnaire_cat2, array_field['QuestionID'])
          break
        case '-3':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat3 = this.showCurrentQuestion(this.questionnaire_cat3, array_field['QuestionID'])
          break
        case '-4':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat4 = this.showCurrentQuestion(this.questionnaire_cat4, array_field['QuestionID'])
          break
        case '-5':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat5 = this.showCurrentQuestion(this.questionnaire_cat5, array_field['QuestionID'])
          break
        case '-6':
          this.selected_questionnaire = ' - Cat B'
          this.questionnaire_cat6 = this.showCurrentQuestion(this.questionnaire_cat6, array_field['QuestionID'])
          break
        case '-7':
          this.selected_questionnaire = ' - Cat B'
          this.questionnaire_cat7 = this.showCurrentQuestion(this.questionnaire_cat7, array_field['QuestionID'])
          break
        case '-8':
          this.selected_questionnaire = ' - Cat C'
          this.questionnaire_cat8 = this.showCurrentQuestion(this.questionnaire_cat8, array_field['QuestionID'])
          break
        case '-9':
          this.selected_questionnaire = '- Cat C'
          this.questionnaire_cat9 = this.showCurrentQuestion(this.questionnaire_cat9, array_field['QuestionID'])
          break
        case '-10':
          this.selected_questionnaire = ' - Cat C'
          this.questionnaire_cat10 = this.showCurrentQuestion(this.questionnaire_cat10, array_field['QuestionID'])
      }

      this.setQuestionnaireLocalstorage(array_field['QuestionnaireID'], {}, '')
    },
    // Initialise selected questionnaire
    confirmInitQuestionnaire: function() {
      this.is_complete = false
      this.is_not_complete = false
      this.is_txt_empty = false
      this.is_dd_empty = false
      this.selected_questionnaire = ''
      this.questionnaire_log = []
      this.current_seq = 0
      this.start_question_id = '1'
      switch (this.DDQuestionnaire) {
        case '-1':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat1 = this.initCurrentQuestionnaire(this.questionnaire_cat1, '-1')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat1))
          break
        case '-2':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat2 = this.initCurrentQuestionnaire(this.questionnaire_cat2, '-2')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat2))
          break
        case '-3':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat3 = this.initCurrentQuestionnaire(this.questionnaire_cat3, '-3')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat3))
          break
        case '-4':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat4 = this.initCurrentQuestionnaire(this.questionnaire_cat4, '-4')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat4))
          break
        case '-5':
          this.selected_questionnaire = ' - Cat A'
          this.questionnaire_cat5 = this.initCurrentQuestionnaire(this.questionnaire_cat5, '-5-')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat5))
          break
        case '-6':
          this.selected_questionnaire = ' - Cat B'
          this.questionnaire_cat6 = this.initCurrentQuestionnaire(this.questionnaire_cat6, '-6')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat6))
          break
        case '-7':
          this.selected_questionnaire = ' - Cat B'
          this.questionnaire_cat7 = this.initCurrentQuestionnaire(this.questionnaire_cat7, '-7')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat7))
          break
        case '-8':
          this.selected_questionnaire = ' - Cat C'
          this.questionnaire_cat8 = this.initCurrentQuestionnaire(this.questionnaire_cat8, '-8')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat8))
          break
        case '-9':
          this.selected_questionnaire = ' - Cat C'
          this.questionnaire_cat9 = this.initCurrentQuestionnaire(this.questionnaire_cat9, '-9')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat9))
          break
        case '-10':
          this.selected_questionnaire = ' - Cat C'
          this.questionnaire_cat10 = this.initCurrentQuestionnaire(this.questionnaire_cat10, '-10')
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat10))
          break
        default:
          this.selected_questionnaire = ''
      }
      if (this.DDQuestionnaire != '') {
        // Add log
        let array_field = new Object()
        array_field['QuestionnaireID'] = this.DDQuestionnaire
        array_field['QuestionID'] = this.start_question_id
        this.questionnaire_log.push(array_field)
        this.$store.commit('SET_SUBMISSION', _.cloneDeep(this.questionnaire_log))
        // Add localstorage
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_id', this.DDQuestionnaire)
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_log', JSON.stringify(this.questionnaire_log))
        this.current_seq += 1
      }
    },
    // Check whether to prompt for confirmation of switching questionnaire
    initQuestionnaire: function() {
      if (this.questionnaire_log.length > 1) {
        this.$modal.show('ConfirmSwitchQuestionnaire')
      } else {
        this.confirmInitQuestionnaire()
      }
    },
    // Prepare JSON submission data
    doSubmissionData: function(target_question_id, array_submission_data, array_questionnaire_questions, questionnaire_category, questionnaire_name, questionnaire_id) {
      for (var iy = 0, iylength = array_questionnaire_questions.length; iy < iylength; iy++) {
        if (target_question_id == array_questionnaire_questions[iy].id) {
          //if (array_questionnaire_questions[iy].is_guide || array_questionnaire_questions[iy].show_next_button_only) {
            // Ignore
          //  continue
          //}
          let array_field = new Object()
          array_field['QuestionnaireID'] = questionnaire_id
          array_field['QuestionnaireCategory'] = questionnaire_category
          array_field['QuestionnaireName'] = questionnaire_name
          array_field['QuestionID'] = array_questionnaire_questions[iy].id
          array_field['Question'] = array_questionnaire_questions[iy].question
          if (array_questionnaire_questions[iy].is_guide || array_questionnaire_questions[iy].show_next_button_only) {
            array_field['Answer'] = ''
          } else if (array_questionnaire_questions[iy].is_options != 0) {
            array_field['Answer'] = array_questionnaire_questions[iy].txt
          } else if (array_questionnaire_questions[iy].rdo != '') {
            if (array_questionnaire_questions[iy].make_note) {
              array_field['Answer'] = ''
            } else {
              array_field['Answer'] = array_questionnaire_questions[iy].rdo
            }
          } else {
            array_field['Answer'] = ''
          }
          if (array_questionnaire_questions[iy].make_note) {
            array_field['Note'] = array_questionnaire_questions[iy].txt
          } else if (array_questionnaire_questions[iy].make_note_n_only && array_questionnaire_questions[iy].rdo == array_questionnaire_questions[iy].n_value) {
            array_field['Note'] = array_questionnaire_questions[iy].txt
          } else {
            array_field['Note'] = ''
          }
          if (array_questionnaire_questions[iy].is_ticket_number) {
            array_field['TicketNumber'] = array_questionnaire_questions[iy].mask_txt
          } else {
            array_field['TicketNumber'] = ''
          }
          array_submission_data.push(array_field)
        }
      }
      return array_submission_data
    },
    // Onclick event for Note question
    flowValidateQuestion: function(obj) {
      let qu = obj.q
      this.is_txt_empty = false
      if (qu.txt.trim() === '') {
        this.is_txt_empty = true
        serverBus.$emit('focusNote')
        return
      }
      this.flowQuestion(obj)
    },
    // Onclick event for dropdown question
    flowValidate2Question: function(obj) {
      let qu = obj.q
      this.is_dd_empty = false
      if (qu.txt.trim() === '') {
        this.is_dd_empty = true
        serverBus.$emit('focusOption')
        return
      }
      this.flowQuestion(obj)
    },
    // Onclick event for mask ref question
    flowValidate3Question: function(obj) {
      let qu = obj.q
      this.is_complete = false
      this.is_not_complete = false
      this.is_dd_empty = false
      this.is_txt_empty = false
      if (qu.mask_txt.length < 8) {
        this.is_txt_empty = true
        serverBus.$emit('focusMaskText')
        return
      }
      this.flowQuestion(obj)
    },
    setQuestionnaireLocalstorage(s, qu, a) {

      switch (s) {
        case '-1':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ia = 0, ialength = this.questionnaire_cat1.length; ia < ialength; ia++) {
              if (this.questionnaire_cat1[ia].id == qu.id) {
                this.questionnaire_cat1[ia].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat1))
          break
        case '-2':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ib = 0, iblength = this.questionnaire_cat2.length; ib < iblength; ib++) {
              if (this.questionnaire_cat2[ib].id == qu.id) {
                this.questionnaire_cat2[ib].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat2))
          break
        case '-3':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ic = 0, iclength = this.questionnaire_cat3.length; ic < iclength; ic++) {
              if (this.questionnaire_cat3[ic].id == qu.id) {
                this.questionnaire_cat3[ic].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat3))
          break
        case '-4':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var id = 0, idlength = this.questionnaire_cat4.length; id < idlength; id++) {
              if (this.questionnaire_cat4[id].id == qu.id) {
                this.questionnaire_cat4[id].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat4))
          break
        case '-5':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ie = 0, ielength = this.questionnaire_cat5.length; ie < ielength; ie++) {
              if (this.questionnaire_cat5[ie].id == qu.id) {
                this.questionnaire_cat5[ie].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat5))
          break
        case '-6':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var if1 = 0, if1length = this.questionnaire_cat6.length; if1 < if1length; if1++) {
              if (this.questionnaire_cat6[if1].id == qu.id) {
                this.questionnaire_cat6[if1].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat6))
          break
        case '-7':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ig = 0, iglength = this.questionnaire_cat7.length; ig < iglength; ig++) {
              if (this.questionnaire_cat7[ig].id == qu.id) {
                this.questionnaire_cat7[ig].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat7))
          break
        case '-8':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ih = 0, ihlength = this.questionnaire_cat8.length; ih < ihlength; ih++) {
              if (this.questionnaire_cat8[ih].id == qu.id) {
                this.questionnaire_cat8[ih].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat8))
          break
        case '-9':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ii = 0, iilength = this.questionnaire_cat9.length; ii < iilength; ii++) {
              if (this.questionnaire_cat9[ii].id == qu.id) {
                this.questionnaire_cat9[ii].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat9))
          break
        case '-10':
          if (Object.keys(qu).length === 0 && qu.constructor === Object) {
            // Do nothing
          } else {
            for (var ij = 0, ijlength = this.questionnaire_cat10.length; ij < ijlength; ij++) {
              if (this.questionnaire_cat10[ij].id == qu.id) {
                this.questionnaire_cat10[ij].rdo = a
                break
              }
            }
          }
          localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_questions', JSON.stringify(this.questionnaire_cat10))
      }

    },
    // Onclick event for radio button question
    flowQuestionRDO: function(obj) {
      let qu = obj.q
      let a = obj.a
      let s = obj.qt
//console.log('----'+JSON.stringify(obj))
      let array_field = this.questionnaire_log[this.current_seq - 1]
      if (array_field['QuestionnaireID'].toString() == s.toString() && array_field['QuestionID'].toString() != a.toString()) {
        this.questionnaire_log.splice(this.current_seq, this.questionnaire_log.length - this.current_seq + 1)
        this.$store.commit('SET_SUBMISSION', _.cloneDeep(this.questionnaire_log))
        // Add localstorage
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_id', this.DDQuestionnaire)
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_log', JSON.stringify(this.questionnaire_log))
      }
      if (qu.make_note_n_only && a === qu.n_value) {
        return
      }
      this.flowQuestion(obj)
    },
    // Onclick event for goto next question
    flowQuestion: function(obj) {
      let self = this
      let qu = obj.q
      let a = obj.a
      let s = obj.qt.toString()
//console.log('----'+JSON.stringify(obj))
      this.is_txt_empty = false

      this.setQuestionnaireLocalstorage(s, qu, a)

      var goto_q
      if (a === qu.y_value) {
        goto_q = qu.y_goto
      } else {
        goto_q = qu.n_goto
      }

      if (goto_q === '0') {
        // Prepare JSON data for data saving
        let array_submission_data = []
        // Any prior questionnaire's redirect?
        for (var iz = 0, izlength = this.prior_questionnaire_log.length; iz < izlength; iz++) {
          switch (this.prior_questionnaire_log[iz].QuestionnaireID) {
            case '-1':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat1, 'Cat A', 'Desc 1', '-1')
              break
            case '-2':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat2, 'Cat A', 'Desc 2', '-2')
              break
            case '-3':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat3, 'Cat A', 'Desc 3', '-3')
              break
            case '-4':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat4, 'Cat B', 'Desc 4', '-4')
              break
            case '-5':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat5, 'Cat B', 'Desc 5', '-5')
              break
            case '-6':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat6, 'Cat C', 'Desc 6', '-6')
              break
            case '-7':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat7, 'Cat D', 'Desc 7', '-7')
              break
            case '-8':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat8, 'Cat E', 'Desc 8', '-8')
              break
            case '-9':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat9, 'Cat F', 'Desc 9', '-9')
              break
            case '-10':
              array_submission_data = this.doSubmissionData(this.prior_questionnaire_log[iz].QuestionID, array_submission_data, this.questionnaire_cat10, 'Cat G', 'Desc 10', '-10')
          }
        }
        // Current questionnaire
        for (var ix = 0, ixlength = this.questionnaire_log.length; ix < ixlength; ix++) {
          switch (this.questionnaire_log[ix].QuestionnaireID) {
            case '-1':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat1, 'Cat A', 'desc 1', '-1')
              break
            case '-2':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat2, 'Cat A', 'Desc 2', '-2')
              break
            case '-3':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat3, 'Cat A', 'Desc 3', '-3')
              break
            case '-4':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat4, 'Cat B', 'Desc 4', '-4')
              break
            case '-5':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat5, 'Cat B', 'Desc 5', '-5')
              break
            case '-6':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat6, 'Cat C', 'Desc 6', '-6')
              break
            case '-7':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat7, 'Cat C', 'Desc 7', '-7')
              break
            case '-8':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat8, 'Cat D', 'Desc 8', '-8')
              break
            case '-9':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat9, 'Cat D', 'Desc 9', '-9')
              break
            case '-10':
              array_submission_data = this.doSubmissionData(this.questionnaire_log[ix].QuestionID, array_submission_data, this.questionnaire_cat10, 'Cat D', 'Desc 10', '-10')
          }
        }
        // Save data to database
        this.$store.commit('SET_SUBMISSION_DATA', _.cloneDeep(array_submission_data))
        // Add localstorage
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_id', this.DDQuestionnaire)
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_log', JSON.stringify(this.questionnaire_log))
        this.$store.dispatch('saveSubmission').then(() => {
          if (self.$store.state.feedback.indexOf('success') >= 0) {
            // Initialise and set flags
            self.prior_questionnaire_log = []
            self.questionnaire_log = []
            qu.show = false
            localStorage.removeItem(this.site_localstorage_id + '.state_questionnaire_id')
            localStorage.removeItem(this.site_localstorage_id + '.state_questionnaire_questions')
            localStorage.removeItem(this.site_localstorage_id + '.state_questionnaire_log')
            self.is_complete = true
            self.showPDF(self.$store.state.return_id)
          } else {
            self.is_not_complete = true
          }
        })
        return
      } else if (goto_q.indexOf('-') >= 0) {
        // Temporary store prior questionanaire questions before redirect questionnaire
        for (var iy = 0, iylength = this.questionnaire_log.length; iy < iylength; iy++) {
          this.prior_questionnaire_log[this.prior_questionnaire_log.length] = this.questionnaire_log[iy]
        }
        qu.show = false
        this.DDQuestionnaire = goto_q
        this.confirmInitQuestionnaire()
        return
      }
      switch (s) {
        case '-1':
          this.questionnaire_cat1 = this.showCurrentQuestion(this.questionnaire_cat1, goto_q)
          break
        case '-2':
          this.questionnaire_cat2 = this.showCurrentQuestion(this.questionnaire_cat2, goto_q)
          break
        case '-3':
          this.questionnaire_cat3 = this.showCurrentQuestion(this.questionnaire_cat3, goto_q)
          break
        case '-4':
          this.questionnaire_cat4 = this.showCurrentQuestion(this.questionnaire_cat4, goto_q)
          break
        case '-5':
          this.questionnaire_cat5 = this.showCurrentQuestion(this.questionnaire_cat5, goto_q)
          break
        case '-6':
          this.questionnaire_cat6 = this.showCurrentQuestion(this.questionnaire_cat6, goto_q)
          break
        case '-7':
          this.questionnaire_cat7 = this.showCurrentQuestion(this.questionnaire_cat7, goto_q)
          break
        case '-8':
          this.questionnaire_cat8 = this.showCurrentQuestion(this.questionnaire_cat8, goto_q)
          break
        case '-9':
          this.questionnaire_cat9 = this.showCurrentQuestion(this.questionnaire_cat9, goto_q)
          break
        case '-10':
          this.questionnaire_cat10 = this.showCurrentQuestion(this.questionnaire_cat10, goto_q)
          break
        default:
          s = ''
      }
      if (s != '') {
        // Check if this question has already been logged
        let found = false
        for (var xa = 0, xalength = this.questionnaire_log.length; xa < xalength; xa++) {
          if (this.questionnaire_log[xa].QuestionnaireID == s && this.questionnaire_log[xa].QuestionID == goto_q) {
            found = true
            break
          }
        }
        if (found) {
          this.current_seq += 1
          return
        }

        // Add log
        let array_field = new Object()
        array_field['QuestionnaireID'] = s
        array_field['QuestionID'] = goto_q
        this.questionnaire_log.push(array_field)
        this.$store.commit('SET_SUBMISSION', _.cloneDeep(this.questionnaire_log))
        // Add localstorage
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_id', this.DDQuestionnaire)
        localStorage.setItem(this.site_localstorage_id + '.state_questionnaire_log', JSON.stringify(this.questionnaire_log))
        this.current_seq += 1
      }
    }
  }
}
</script>

<style>
  h1 {
    font-size: 2.2em;
    font-weight: bold;
    color: #0aa6d0;
    margin-top: 5px;
  }
  h3 {
    font-size: 1.6em;
    font-weight: bold;
    padding: 0;
    margin-top: 0;
    margin-bottom: 0;
  }

  .divTransition {
    height: 320px;
    margin-top: 10px;
    margin-bottom: 10px;
    padding: 0;
  }
  .subjectLabel, .cbo {
    font-size: 1.8em;
    margin-bottom: 15px;
    color: #0aa6d0;
  }
  label {
    font-weight: bold;
  }
  label.subjectLabel {
    font-size: 1.6em;
  }
  .telephoneIssueBody .v-select__selection, .telephoneIssueBody .v-select__selection--comma {
    font-size: 1.6em;
    font-weight: bold;
  }
  .v-list--dense .v-subheader, .select-header {
    font-size: 1.6em  !important;
    font-weight: bold;
    color: blue;
  }
  .v-list--dense .v-list-item__content, .select-option {
    font-size: 1.2em !important;
    color: #0aa6d0;
  }
  .telephoneIssue {
    width: 100%;
    margin: 0 auto;
    background-color: #fff;
    min-height: 560px;
  }
  .telephoneIssueBody {
    padding: 0 40px;
  }
  .v-application ol {
    margin-left: 0;
    padding-left: 0;
  }
  .v-application p {
    margin-bottom: 0;
  }
  ol, ul {
    list-style-type: none;
    padding: 0;
  }
  ol li {
    clear: both;
    margin-bottom: 20px;
  }
  ul li {
    display: inline-block;
    margin-right: 30px;
    font-size: 1.6em;
  }
  .message {
    width: 100%;
    height: 40px;
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
  .error2 {
    border: 1px solid red;
    background-color: #feded3;
  }
  .error2 .v-label, .error2 .v-label--active, .error2 .theme--light, .error2 .primary--text  {
    color: red !important;
  }
  .cbo {
    margin-left: 15px;
    border: 1px solid #2c3e50;
  }
  select.error2 {
    border: 1px solid red !important;
  }
  .optionList {
    margin-top: 15px;
  }
  .optionList .cbo {
    margin-left: 0;
  }
  .button {
    background-color: #0aa6d0;
    border: 2px solid #0aa6d0;
    color: whitesmoke;
    padding: 0.25em 0.5em;
    text-align: center;
    text-decoration: none;
    font-size: 1.1em;
    /* display: inline-block; */
    border-radius: 5px;
    box-shadow: 3px 3px 8px 0 #0aa6d0; /* h-offset v-offset blur spread color */
    margin-left: 600px;
    cursor: pointer;
  }
  .divNavigation {
    text-align: right;
  }
  .divNavigation .button {
    background-color: grey;
    border: 2px solid grey;
    box-shadow: 3px 3px 8px 0 grey;
    cursor: default;
    width: 100px !important;
  }
  .divNavigation .enabled .button {
    background-color: #0aa6d0;
    border: 2px solid #0aa6d0;
    box-shadow: 3px 3px 8px 0 #0aa6d0;
    cursor: pointer;
  }
  .divNavLeft {
    width: 120px;
    /*display: inline-flex;*/
    float: right;
    text-align: right;
  }
  .divNavRight {
    width: 100px !important;
    /*display: inline-flex;*/
    float: right;
    text-align: right;
  }
  .divStart {
    margin-top: 30px;
  }
  .divStart .button {
    margin-left: 0;
    width: 180px !important;
    height: 60px !important;
    font-size: 2.2em !important;
  }
  .showNextButtonOnly .button{
    position: absolute;
    right: 25px !important;
    width: 180px !important;
    height: 60px !important;
    font-size: 2.2em !important;
  }
  .guide .button {
    position: absolute;
    right: 25px !important;
    width: 180px !important;
    height: 60px !important;
    font-size: 2.2em !important;
  }
  .optionList .button {
    position: absolute;
    right: 25px !important;
    width: 180px !important;
    height: 60px !important;
    font-size: 2.2em !important;
  }
  .text .button {
    position: absolute;
    right: 25px !important;
    width: 180px !important;
    height: 60px !important;
    font-size: 2.2em !important;
  }
  .divModal {
    padding: 20px;
  }
  .divModal p {
    font-size: 1.4em;
  }
  .divNavigation {
    height: 45px;
    margin-bottom: 50px;
  }
  .norigtmargin {
    margin-right: 0 !important;
  }
  .divConfirmSwitchQuestionnaire {
    width: 100%;
    height: 30px;
    text-align: right;
  }
  .divNavigation .button, .divConfirmSwitchQuestionnaire .button {
    margin: 25px 40px 50px 0;
    width: 140px;
  }
  .divConfirmSwitchQuestionnaire .button {
    width: 90px;
  }
  select optgroup {
    color: blue !important;
  }
  textarea {
    width: 855px;
    height: 200px;
    margin-bottom: 10px;
    font-size: 1.2em;
  }
  .error3 .v-text-field fieldset {
    border: 1px solid red !important;
  }
  .smallItalic {
    font-size: 0.9em;
    font-style: italic;
    color: #8f8f8f;
    padding-bottom: 0;
    margin-bottom: 5px;
  }
  .errorFont {
    color: red;
  }
  /* Slide */
  .slide-enter-active {
    -moz-transition-duration: 0.3s;
    -webkit-transition-duration: 0.3s;
    -o-transition-duration: 0.3s;
    transition-duration: 0.3s;
    -moz-transition-timing-function: ease-in;
    -webkit-transition-timing-function: ease-in;
    -o-transition-timing-function: ease-in;
    transition-timing-function: ease-in;
  }

  .slide-leave-active {
    -moz-transition-duration: 0.3s;
    -webkit-transition-duration: 0.3s;
    -o-transition-duration: 0.3s;
    transition-duration: 0.3s;
    -moz-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
    -webkit-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
    -o-transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
    transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
  }

  .slide-enter-to, .slide-leave {
    max-height: 320px;
    overflow: hidden;
  }

  .slide-enter, .slide-leave-to {
    overflow: hidden;
    max-height: 0;
  }
</style>
