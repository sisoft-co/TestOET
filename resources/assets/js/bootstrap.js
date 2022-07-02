
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    //window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
//window.moment = require('moment');
//import Swal from 'sweetalert2';
//const Swal = require('sweetalert2');

//import Swal from 'sweetalert2/dist/sweetalert2.js'

//import 'sweetalert2/src/sweetalert2.scss'

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

axios.interceptors.request.use(
    function (config) {
        if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
            jQuery('body').append('<div id="resultLoading" style="display:none"><div><img src="img/loading.gif"><div>'+'Procesando Información... Espere...'+'</div></div><div class="bg"></div></div>');
        }
        jQuery('#resultLoading').css({
            'width':'100%',
            'height':'100%',
            'position':'fixed',
            'z-index':'10000000',
            'top':'0',
            'left':'0',
            'right':'0',
            'bottom':'0',
            'margin':'auto'
        });
        jQuery('#resultLoading .bg').css({
            'background':'#AFEAFF40',
            'opacity':'0.4',
            'width':'100%',
            'height':'100%',
            'position':'absolute',
            'top':'0'
        });
        jQuery('#resultLoading>div:first').css({
            'width': '250px',
            'height':'75px',
            'text-align': 'center',
            'position': 'fixed',
            'top':'0',
            'left':'0',
            'right':'0',
            'bottom':'0',
            'margin':'auto',
            'font-size':'16px',
            'z-index':'10',
            'color':'#000000'
        });
        jQuery('#resultLoading .bg').height('100%');
        jQuery('#resultLoading').fadeIn(300);
        jQuery('body').css('cursor', 'wait');

        return config;
    },
    function(error) {

    }
);


axios.interceptors.response.use(
    function(response) { 
        jQuery('#resultLoading .bg').height('100%');
        jQuery('#resultLoading').fadeOut(300);
        jQuery('body').css('cursor', 'default');
        return response;
    },
    function(error) {
        jQuery('#resultLoading .bg').height('100%');
        jQuery('#resultLoading').fadeOut(300);
        jQuery('body').css('cursor', 'default');
        if (error.response) {
            //console.log('Error RESPONSE ===> ', error.response.status)
            if (error.response.status === 401) {
                swal({
                    title: '¡ Tu sesión ha Expirado !',
                    type: 'info',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Reingresar a la Aplicación',
                }).then((result) => {
                    if (result.value) {
                        window.location.href = '/login'
                    }
                })            
            } else {
                if (error.response.status < 500) {
                    swal({
                            title: '¡ Error !',
                            type: 'error',
                            text: 'Factible: Campos mal Digitados / Llaves Duplicadas / Recurso Solicitado Inexistente / Servidor no Disponible / etc....',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#499b72',
                            confirmButtonText: 'Ok',
                            cancelButtonText: 'Ver Detalles',
                    }).then((result) => {
                        if (!result.value) {
                            swal(
                                'Detalle Error :: ' + error.response.status,
                                error.response.data.message,
                            )
                        }
                    })
                } else {
                    if (error.response.status == 500) {
                        swal({
                                title: '¡ Ningún Registro para esta Consulta !',
                                type: 'info',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#499b72',
                                confirmButtonText: 'Ok',
                                cancelButtonText: 'Ver Detalles',
                        }).then((result) => {
                            if (!result.value) {
                                swal(
                                    'Detalle Error :: ' + error.response.status,
                                    error.response.data.message,
                                )
                            }
                        })
                    }
                }
            }
        } 
        else if (error.request) {
            //console.log('Error REQUEST ===> ', error.request);
            swal({
                title: '¡ Error on Request !',
                type: 'error',
                text: 'Repita el Comando ó Refresque el Navegador.',
                showConfirmButton: true,
                allowOutsideClick: false
            })
        } else {
                swal({
                    title: '¡ Error Desconocido !',
                    type: 'error',
                    text: 'Por favor cierre la ventana y vuelva a Ingresar a la Aplicación.',
                    showConfirmButton: true,
                    allowOutsideClick: false
                })
               }
        return Promise.reject(error);
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

 import Echo from 'laravel-echo'

 window.Pusher = require('pusher-js');

 window.Echo = new Echo({
     broadcaster: 'pusher',
     key: '3b4db495fac700ec7bdc',
     cluster: 'us2',
     encrypted: true
 });
