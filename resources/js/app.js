require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';

// optional for styling
import VueChartkick from 'vue-chartkick'
import 'chartkick/chart.js'

import VueApexCharts from "vue3-apexcharts"
import ModuleMixin from '@/Mixins/modules'

import VTooltip from 'v-tooltip'
import 'v-tooltip/dist/v-tooltip.css'

import VueTippy from 'vue-tippy'
import 'tippy.js/dist/tippy.css'
import 'tippy.js/themes/light.css'

import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Appetite.link';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {

        const appH = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(VueChartkick, { 
                library: {
                    animation: {
                        duration: 3000
                    }
                }
            })
            .use(VTooltip)
            .use(VueApexCharts)
            .use(VueTippy, {
                defaultProps: { theme: 'light', allowHTML: true },
            })
            .use(Toast, {
                position: "bottom-center",
                hideProgressBar: true,
                icon: false,
                closeButton: false
            })
            .mixin(ModuleMixin)
            .mixin({ methods: { route } });

        appH.config.globalProperties.$api = axios.create({
            //baseURL: 'https://dashtest.good-day-win.ch/api/query/'
            baseURL: 'http://localhost:8000/api/query/'
        })

        appH.mount(el);

    },
});

InertiaProgress.init({
    // The delay after which the progress bar will
    // appear during navigation, in milliseconds.
    delay: 50,
  
    // The color of the progress bar.
    color: '#fff',
  
    // Whether to include the default NProgress styles.
    includeCSS: true,
  
    // Whether the NProgress spinner will be shown.
    showSpinner: true,
})