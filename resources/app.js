import Vue from 'vue'

window.$ = require('jquery')
window.axios = require('axios')

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.mixin({
    data() {
        return {
            csrfToken: $('[name="X-CSRF-TOKEN"]').attr('content'),
        }
    }
})

new Vue({
    el: '#app',
    components: {
        App: require('./components/App').default
    },
    template: '<App />'
})