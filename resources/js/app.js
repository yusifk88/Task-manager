import './bootstrap';

import {aliases, mdi} from 'vuetify/iconsets/mdi'
import '@mdi/font/css/materialdesignicons.css'

import 'vuetify/styles'
import projectsComponent from "./components/ProjectsComponent";
import {createApp} from 'vue'
import {createVuetify} from 'vuetify';
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

createApp({
    components: {projectsComponent}
})
    .use(createVuetify({
        ssr: true,
        components,
        directives,
        icons: {
            defaultSet: 'mdi',
            aliases,
            sets: {
                mdi
            }
        }
    }))
    .mount('#app')
