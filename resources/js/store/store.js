import Vue from 'vue'
import Vuex from 'vuex'
import _ from 'lodash'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    submission_data: [],
    submission_data_log: [],
    feedback: '',
    return_id: '0'
  },
  mutations: {
    SET_SUBMISSION(state, data) {
      state.submission_data_log = data;
    },
    SET_SUBMISSION_DATA(state, data) {
      state.submission_data = data;
    },
    SET_FEEDBACK(state, feedback) {
      state.feedback = feedback
    },
    SET_RETURN_ID(state, return_id) {
      state.return_id = return_id
    }
  },
  actions: {
    saveSubmission({commit, state}) {
      return new Promise((resolve, reject) => {
        axios.post('http://comcons.com.au/callcentre/submissions/insert', {
          submission_data: state.submission_data
        })
        .then((res) => {
          if (res.data.success) {
            commit('SET_FEEDBACK', 'Well Done! You have successfully completed the questionnaire!')
            commit('SET_RETURN_ID', res.data.return_id)
            //setTimeout(() => commit('SET_FEEDBACK', ''), 2000)
          } else {
            if (res.data.return_id != 0) {
              commit('SET_FEEDBACK', 'Sorry, the ticket number already existed. Please try again.')
              commit('SET_RETURN_ID', '0')
            } else {
              commit('SET_FEEDBACK', 'Oh no! Submission was failed to be added. Please try again.')
              commit('SET_RETURN_ID', '0')
            }
          }
          resolve()
        })
      })
    }
  }
});