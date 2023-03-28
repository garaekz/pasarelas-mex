import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { OhVueIcon, addIcons } from "oh-vue-icons";
import {
    BiCircle,
    BiCheck2Circle,
    FaShoppingCart
 } from "oh-vue-icons/icons";

addIcons(BiCircle, BiCheck2Circle, FaShoppingCart);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .component("v-icon", OhVueIcon)
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(OhVueIcon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
