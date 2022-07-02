
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.$ = window.jQuery = require('jquery'); 
window.Vue = require('vue');

import Vue from 'vue'

Vue.directive('uppercase', {
	update (el) {
		el.value = el.value.toUpperCase()
	},
});

Vue.mixin({
    methods: {
        mensajelog() {
            console.log('VEAMOS NEW MESSAGE MIXIN')
        },
    }
})


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('vehiculo', require('./components/Vehiculo.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('dashboard', require('./components/Dashboard.vue'));
Vue.component('notification', require('./components/Notification.vue'));
Vue.component('changepassword', require('./components/ChangePassword.vue'));
Vue.component('logocentral', require('./components/LogoCentral.vue'));

const app = new Vue({
    el: '#app',
    data :{
        menu : 0,
        notifications: [],
    },

    created() {
        let me = this;

        axios.post('notification/get').then(function(response) {
           me.notifications=response.data;    
        }).catch(function(error) {
            console.log(error);
        });

        var userId = $('meta[name="userId"]').attr('content');
        Echo.private('App.User.' + userId).notification((notification) => {
            me.notifications.unshift(notification); 
        });
    }        
})