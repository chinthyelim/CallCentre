<template>
  <transition name="slide">
    <div class="divTransition" v-show="q.show && DDQuestionnaire == qt">
      <h3>{{ q.question }}</h3>
      <div class="showNextButtonOnly" v-show="q.show_next_button_only">
        <button class="button" @click="flowQuestion(q, q.y_value, qt)">Next</button>
      </div>
      <div class="optionList" v-show="q.is_options != 0">
        <select :ref="'option_' + qt + '_' + q.id" :id="'option_' + qt + '_' + q.id" v-model="q.txt" :class="{ 'cbo' : true, 'error2' : is_dd_empty }" @change="triggerInitDDEmpty">
          <option value=""> --- Select --- </option>
          <option v-for="(option, index) in option_list" :value="option.name" :key="index">{{ option.name }}</option>
        </select>
        <div class="optionListButton">
          <button class="button" @click="flowValidate2Question(q, q.y_value, qt)">Next</button>
        </div>
      </div>
      <div class="guide" v-show="q.is_ticket_number">
        <v-text-field
          :ref="'txtMask_' + qt + '_' + q.id" :id="'txt_' + qt + '_' + q.id"
          :class="{ 'error2' : is_txt_empty }" @keyup="triggerInitTXTEmpty"
          placeholder="00000000"
          minlength="8"
          maxlength="8"
          pattern="[0-9]*"
          @keydown.native="constrainKeyPress($event, q, q.y_value, qt)"
          v-model="q.mask_txt"
          label="Ticket Number (8 digits)">
        </v-text-field>
        <br/>
        <div class="optionListButton">
          <button :class="{ 'button' : true, 'submitBtn' : true }" @click="flowValidate3Question(q, q.y_value, qt)">Submit</button>
        </div>
      </div>
      <div class="guide" v-show="q.is_guide !== ''">
        <p><b>Please make sure you follow the instructions from the <a target="_blank" :href="site_url + 'src/assets/pdf/' + q.is_guide">guide</a> for the customer then click <u>NEXT</u></b>.</p>
        <div class="optionListButton">
          <button class="button" @click="flowQuestion(q, q.y_value, qt)">Next</button>
        </div>
      </div>
      <div class="rdo" v-show=" ! q.make_note && q.is_guide === '' && q.is_options === 0 && ! q.show_next_button_only && ! q.is_ticket_number">
        <p v-show="q.downloadable_guide != ''"><b>Please make sure you follow the instructions from the <a target="_blank" :href="site_url + 'src/assets/pdf/' + q.downloadable_guide">guide</a> for the customer.</b></p>
        <ul>
          <li>
            <input type="radio" :id="'a_yes_' + qt + '_' + q.id" :ref="'a_yes_' + qt + '_' + q.id" :name="'a_' + qt + '_' + q.id" :value="q.y_value" v-model="q.rdo" @click="flowQuestionRDO(q, q.y_value, qt)">
            <label :for="'a_yes_' + qt + '_' + q.id">{{ q.y_value }}</label>
          </li>
          <li>
            <input type="radio" :id="'a_no_' + qt + '_' + q.id" :ref="'a_no_' + qt + '_' + q.id" :name="'a_' + qt + '_' + q.id" :value="q.n_value" v-model="q.rdo" @click="flowQuestionRDO(q, q.n_value, qt)">
            <label :for="'a_no_' + qt + '_' + q.id">{{ q.n_value }}</label>
          </li>
        </ul>
      </div>
      <div :class="{ 'text' : true, 'error3' : is_txt_empty }" v-show="q.make_note || q.make_note_n_only && q.rdo == q.n_value">
        <p :class="{ 'smallItalic' : true, 'errorFont' : is_txt_empty }">Please fill in the note below</p>
        <v-textarea
          outlined
          :ref="'txt_' + qt + '_' + q.id" :id="'txt_' + qt + '_' + q.id" v-model="q.txt" @keyup="triggerInitTXTEmpty"
        ></v-textarea>
        <br/>
        <div class="optionListButton">
          <button :class="{ 'button' : true, 'submitBtn' : is_submit_btn }" @click="flowValidateQuestion(q, q.y_value, qt)">{{ txt_button_caption }}</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { serverBus } from '../app'
export default {
  name: 'QuestionAndAnswer',
  props: ['DDQuestionnaire', 'qt', 'q', 'site_url', 'is_txt_empty' , 'is_dd_empty', 'option_list1' ],
  data () {
    return {
      option_list: [],
      txt_button_caption: 'Next',
      is_submit_btn: false
    }
  },
  created() {
    serverBus.$on('focusInput', this.focusInput)
    serverBus.$on('focusNote', this.focusNote)
    serverBus.$on('focusMaskText', this.focusMaskText)
    serverBus.$on('focusOption', this.focusOption)
  },
  mounted() {
    let self = this
    switch (this.q.is_options) {
      case 1:
        this.option_list = this.option_list1
        break
    }
    if (this.q.make_note) {
      if (this.q.y_goto == '0' && this.q.y_goto == this.q.n_goto) {
        this.txt_button_caption = 'Submit'
        this.is_submit_btn = true
      } else {
        this.txt_button_caption = 'Next'
        this.is_submit_btn = false
      }
      this.focusNote()
    } else if (this.q.is_options != 0) {
      this.focusOption()
    } else if (this.q.make_note_n_only && this.q.rdo == this.q.n_value) {
      this.focusNote()
    } else if (this.q.is_ticket_number) {
      this.focusMaskText()
    } else {
/*
      if (this.q.rdo.toLowerCase() == this.q.y_value.toLowerCase()) {
        this.$nextTick(() => {
          self.$refs['a_yes_' + self.qt + '_' + self.q.id].click()
        })
      } else if (this.q.rdo.toLowerCase() == this.q.n_value.toLowerCase()) {
        this.$nextTick(() => {
          self.$refs['a_no_' + self.qt + '_' + self.q.id].click()
        })
      }
*/
    }
  },
  methods: {
    // Make input numeric only
    constrainKeyPress: function(event, q, a, qt) {

      if ( ! event) event = window.event
      if (event.shiftKey) {
        // Reject Shift key
        event.preventDefault()
      } else {
        let key = event.keyCode
        if ( ! key) key = event.keyCode
        if ( ! key) key = event.charCode
        key = parseInt(key)
        if (key == 13) {
          this.flowValidate3Question(q, a, qt)
          event.preventDefault()
          return
        }
        if (((key >= 48) && (key <= 57)) || ((key >= 96) && (key <= 105)) || (key == 9) || (key == 11) || (key == 8)) {
          // OK
        } else {
          // Reject
          event.preventDefault()
        }
      }
    },
    focusInput: function() {
      if (this.q.make_note) {
        this.focusNote()
      } else if (this.q.is_options != 0) {
        this.focusOption()
      } else if (this.q.make_note_n_only && this.q.rdo == this.q.n_value) {
        this.focusNote()
      } else if (this.q.is_ticket_number) {
        this.focusMaskText()
      }
    },
    triggerInitDDEmpty: function() {
      this.$emit('initDDEmpty', { })
    },
    triggerInitTXTEmpty: function() {
      if (this.q.mask_txt.length == 0) {
        this.$emit('initTXTEmpty', { })
      }
    },
    focusNote: function() {
      let self = this
      if (typeof self.$refs['txt_' + self.qt + '_' + self.q.id] === 'undefined' || self.$refs['txt_' + self.qt + '_' + self.q.id] == null) {
        return
      }
      self.$nextTick(() => {
        // Set textarea focus
        self.$refs['txt_' + self.qt + '_' + self.q.id].focus()
        //document.getElementById('txt_' + this.qt + '_' + this.q.id).focus()
      })
    },
    focusMaskText: function() {
      let self = this
      if (typeof self.$refs['txtMask_' + self.qt + '_' + self.q.id] === 'undefined' || self.$refs['txtMask_' + self.qt + '_' + self.q.id] == null) {
        return
      }
      self.$nextTick(() => {
        // Set textarea focus
        self.$refs['txtMask_' + self.qt + '_' + self.q.id].focus()
        //document.getElementById('txt_' + this.qt + '_' + this.q.id).focus()
      })
    },
    focusOption: function() {
      let self = this
      if (typeof self.$refs['option_' + self.qt + '_' + self.q.id] === 'undefined' || self.$refs['option_' + self.qt + '_' + self.q.id] == null) {
        return
      }
      self.$nextTick(() => {
        // Set textarea focus
        self.$refs['option_' + self.qt + '_' + self.q.id].focus()
        //document.getElementById('txt_' + this.qt + '_' + this.q.id).focus()
      })
    },
    flowQuestion: function(q, a, qt) {
      this.$emit('flowQuestion', { q, a, qt })
    },
    flowValidate2Question: function(q, a, qt) {
      this.$emit('flowValidate2Question', { q, a, qt })
    },
    flowValidate3Question: function(q, a, qt) {
      this.$emit('flowValidate3Question', { q, a, qt })
    },
    flowQuestionRDO: function(q, a, qt) {
      this.$emit('flowQuestionRDO', { q, a, qt })
    },
    flowValidateQuestion: function(q, a, qt) {
      this.$emit('flowValidateQuestion', { q, a, qt })
    }
  }
}
</script>

<style>
  .submitBtn {
    position: absolute;
    right: 65px;
    width: 180px;
    height: 60px;
    font-size: 2.2em !important;
  }
</style>