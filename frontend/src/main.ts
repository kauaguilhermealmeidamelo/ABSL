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

//Bootstrap
import { createBootstrap } from 'bootstrap-vue-next'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'

// Se qualquer chamada à API vier com 401/419, encerra a sessão local — o
// admin volta a ser tratado como usuário comum automaticamente, sem
// precisar recarregar a página.
setupAuthInterceptor(clearSession)

async function bootstrap() {
	const app = createApp(App)
	registerPlugins(app)
	app.use(createBootstrap())
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