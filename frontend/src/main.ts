/**
 * main.ts
 *
 * Bootstraps Vuetify and other plugins then mounts the App`
 */

// Composables
import { createApp } from 'vue'

// Plugins
import { registerPlugins } from '@/plugins'

// Components
import App from './App.vue'

import router from './router'
import { initAppData } from '@/stores/appData'

// Styles
import 'vuetify/styles'

import '@/assets/theme.css'

//Bootstrap
import { createBootstrap } from 'bootstrap-vue-next'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'


async function bootstrap() {
	const app = createApp(App)
	registerPlugins(app)
	app.use(createBootstrap())
	app.use(router)

	// initialize app data from backend (non-fatal)
	await initAppData().catch(() => {})

	app.mount('#app')
}

bootstrap()
