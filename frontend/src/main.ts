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
import { checkSession, clearSession } from '@/stores/auth'
import { setupAuthInterceptor } from '@/services/api'

// Styles
import 'vuetify/styles'

import '@/assets/theme.css'


setupAuthInterceptor(clearSession)

async function bootstrap() {
	const app = createApp(App)
	registerPlugins(app)
	app.use(router)

	// initialize app data from backend (non-fatal)
	await initAppData().catch(() => {})

	// Confirma com o backend se a sessão de admin ainda é válida assim que o
	// app carrega (cobre o caso da aba ter ficado aberta além do tempo
	// limite, ou a sessão ter expirado no servidor).
	await checkSession().catch(() => {})

	// Revalida periodicamente em segundo plano, para que a expiração
	// aconteça mesmo se o usuário deixar a aba aberta sem navegar.
	setInterval(() => {
		checkSession().catch(() => {})
	}, 5 * 60 * 1000) // a cada 5 minutos

	app.mount('#app')
}

bootstrap()