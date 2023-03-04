// import libraries
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import VTooltip from 'v-tooltip'
import VueSweetalert2 from 'vue-sweetalert2'
import VueLibraries from './lib'

// import fontawesome and used icons
import { library } from '@fortawesome/fontawesome-svg-core';
import { faEye, faTrash, faEdit } from '@fortawesome/free-solid-svg-icons';

require('./bootstrap');
window.Vue = require('vue').default;

// register used icons in fontawesome
library.add(faEye, faTrash, faEdit);

// libraries usage
Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VTooltip)
Vue.use(VueSweetalert2)

// load custom libraries
Vue.mixin(VueLibraries)

// load custom event : triggered when user click outside of the current element
Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
        el.clickOutsideEvent = function (event) {
            if (!(el === event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
});

// App
const App = () => import(/* webpackChunkName: "vueApp" */ './components/AppComponent')

// Public components
const Home = () => import(/* webpackChunkName: "home" */ './components/home/HomeComponent')
const Login = () => import(/* webpackChunkName: "login" */ './components/auth/LoginComponent')
const Signin = () => import(/* webpackChunkName: "signin" */ './components/auth/SigninComponent')
const Verify = () => import(/* webpackChunkName: "verify" */ './components/auth/VerifyComponent')
const SendResetPasswordLink = () => import(/* webpackChunkName: "SendResetPasswordLink" */ './components/auth/PasswordEmailComponent')
const ResetPassword = () => import(/* webpackChunkName: "ResetPassword" */ './components/auth/PasswordResetComponent')

// Protected components
const AccountSettings = () => import(/* webpackChunkName: "AccountSettings" */ './components/settings/AccountSettings/AccountSettingsComponent')
const Moderation = () => import(/* webpackChunkName: "Moderation" */ './components/settings/Moderation/ModerationComponent')
const Dashboard = () => import(/* webpackChunkName: "Dashboard" */ './components/settings/Dashboard/DashboardComponent')

// Swal components
let swalWarning = Vue.component('swalWarning', require('./components/general/swal/WarningComponent').default);

// Instantiate vue router
const router = new VueRouter({
    mode: 'history',
    routes: [

        // Public path
        {
            path: '/',
            component: Home,
            meta: {
                title: VueLibraries.methods.__('Jackpot Realm')
            }
        },

        {
            path: '/app/login',
            component: Login,
            meta: {
                title: VueLibraries.methods.__('Log In')
            }
        },

        {
            path: '/app/password/email',
            component: SendResetPasswordLink,
            meta: {
                title: VueLibraries.methods.__('Reset password')
            }
        },

        {
            path: '/app/password/reset/:email/:token',
            component: ResetPassword,
            meta: {
                title: VueLibraries.methods.__('Reset password')
            }
        },

        {
            path: '/app/register',
            component: Signin,
            meta: {
                title: VueLibraries.methods.__('Sign In')
            }
        },

        {
            path: '/app/verify',
            component: Verify,
            meta: {
                title: VueLibraries.methods.__('Verify your email address')
            }
        },

        // Protected path
        {
            path: '/settings',
            component: AccountSettings,
            meta: {
                title: VueLibraries.methods.__('Jackpot Realm') + ' - ' + VueLibraries.methods.__('Account settings')
            }
        },

        {
            path: '/settings/moderation',
            component: Moderation,
            meta: {
                title: VueLibraries.methods.__('Jackpot Realm') + ' - ' + VueLibraries.methods.__('Moderation')
            }
        },

        {
            path: '/settings/dashboard',
            component: Dashboard,
            meta: {
                title: VueLibraries.methods.__('Jackpot Realm') + ' - ' + VueLibraries.methods.__('Dashboard')
            }
        }
    ]
});

// Replace page title
router.beforeEach((to, from, next) => {
    document.title = to.meta.title

    next()
});

// Define vue global variables
Vue.prototype.$appURL = "http://local.jackpotrealm.com"
Vue.prototype.$apiURL = "http://local.jackpotrealm.com/api"
Vue.prototype.$swalRouter = router

// Instantiate vue app
const app = new Vue({
    el: '#app',
    components: {
        App
    },
    router
});
