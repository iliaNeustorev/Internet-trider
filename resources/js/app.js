/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap")

window.Vue = require("vue").default

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
)

Vue.component(
    "navbar-component",
    require("./components/NavBarComponent.vue").default
)

Vue.component(
    "addProduct-component",
    require("./components/Forms/addProduct.vue").default
)

Vue.component(
    "editProduct-component",
    require("./components/Forms/editProduct.vue").default
)

Vue.component(
    "progress-bar-component",
    require("./components/ProgressBarComponent.vue").default
)

Vue.component(
    "button-send-form",
    require("./components/ButtonSendForm.vue").default
)

Vue.component(
    "button-mass-delete",
    require("./components/ButtonMassDelete.vue").default
)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import store from "./store/store.js"
import router from "./router/router.js"
import Vue from "vue"

const app = new Vue({
    el: "#app",
    store,
    router,
})
