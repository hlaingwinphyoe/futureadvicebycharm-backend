import "./bootstrap";
import "../css/app.css";
import "element-plus/theme-chalk/index.css";
import "element-plus/theme-chalk/dark/css-vars.css";
import "../css/el-theme.scss";

import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import ElementPlus from "element-plus";

const appName = import.meta.env.VITE_APP_NAME || "Tarot By Charm";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .component("Link", Link)
            .component("Head", Head)
            .use(plugin)
            .use(ZiggyVue)
            .use(ElementPlus)
            .mount(el);
    },
    progress: {
        color: "#4b338c",
        includeCSS: true,
        showSpinner: true,
    },
});
