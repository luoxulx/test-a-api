<template>
  <div>
    <form id="form-contact" :model="feedbackForm" class="clearfix ts-form ts-form-email">
    <div class="row">
      <div class="col-md-6 col-sm-6">
        <div class="form-group">
          <label for="form-contact-name">Name *</label>
          <input type="text" class="form-control" id="form-contact-name" v-model="feedbackForm.nickname" placeholder="Name" required>
        </div>
      </div>
      <div class="col-md-6 col-sm-6">
        <div class="form-group">
          <label for="form-contact-email">Email *</label>
          <input type="email" class="form-control" id="form-contact-email" v-model="feedbackForm.email" placeholder="Email" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="form-contact-message">Message *</label>
          <textarea class="form-control" id="form-contact-message" rows="5" v-model="feedbackForm.content" placeholder="Message" required></textarea>
        </div>
      </div>
    </div>
    <div class="form-group clearfix">
      <button @click="submitGuestFeedback" type="button" class="btn btn-primary float-right ts-btn-arrow">SUBMIT</button>
    </div>
    <div class="form-contact-status"></div>
  </form>
  </div>
</template>

<script>
  import { Message } from 'element-ui'
  import 'element-ui/lib/theme-chalk/index.css'
  Vue.component(Message.name, Message);
  export default {
    name: "Feedback",
    components: {},
    data () {
      return {
        feedbackForm: {
          nickname: '',
          email: '',
          content: '',
        }
      }
    },
    mounted() {
      // this.getVerifyCodeBase64()
    },
    methods: {
      submitGuestFeedback() {
          if (this.verifyFormField() !== true) {
              return false
          }
          window.axios.post('/api/v1/open/feedback', this.feedbackForm).then((response) => {
              if (response.status === true) {
                  this.feedbackForm.nickname = ''
                  this.feedbackForm.email = ''
                  this.feedbackForm.content = ''
                  Message.success('successful')
                  return true
              } else {
                  Message.error(response.message || response.debug)
                  return false
              }
          }).catch((error) => {
              Message.error('Client error')
              console.error(error)
              return false
          })
      },
      verifyFormField() {
            if (this.feedbackForm.nickname.length === 0) {
                Message.error('Nickname is required. ')
                return false
            }
            if (this.feedbackForm.email.length === 0) {
                Message.error('Email is required. ')
                return false
            }
            if (this.feedbackForm.content.length === 0) {
                Message.error('Content is required. ')
                return false
            }
            if (this.feedbackForm.content.length > 225) {
                Message.error('Content is more than 225. ')
                return false
            }
            return true
        }
    }
  }
</script>
