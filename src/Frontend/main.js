import Vue from 'vue';

import router from './router';
import store from './store/index';
import api from './services/api';
import {mapActions} from 'vuex';

import App from './App.vue';

import VueCookie from 'vue-cookie';

Vue.use(VueCookie);

new Vue({
    el: '#app',
    render: h => h(App),
    router,
    store,
    components: {
        App
    },
    methods: {
        ...mapActions(['loadProfile'])
    },
    created () {
        this.loadProfile();
    }
});
