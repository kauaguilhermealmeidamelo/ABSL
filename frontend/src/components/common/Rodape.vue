<script setup lang="ts">
const anoAtual = new Date().getFullYear()

const contatos = {
  gremioInstagram: 'https://www.instagram.com/gremioathosb/',
  gremioEmail: 'mailto:gremio.athosbulcao.sl@gmail.com',
  linkedin: 'https://www.linkedin.com/in/kau%C3%A3-guilherme-15711026b/?isSelfProfile=true',
  github: 'https://github.com/kauaguilhermealmeidamelo/',
  criadorInstagram: 'https://www.instagram.com/mello.gk_/',
  criadorEmail: 'mailto:kauagmelotrabalho@gmail.com',
}
</script>

<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-grid">
        <section class="footer-column">
          <h2 class="footer-title">Grêmio Estudantil Athos Bulcão · ABSL</h2>

          <div class="footer-location">
            <v-icon size="17" aria-hidden="true">mdi-map-marker-outline</v-icon>
            <span>Brasília, DF</span>
          </div>

          <div class="footer-contact-group">
            <a
              :href="contatos.gremioInstagram"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-link"
              aria-label="Instagram do Grêmio Estudantil Athos Bulcão"
            >
              <v-icon size="18" aria-hidden="true">mdi-instagram</v-icon>
              <span>Instagram do Grêmio</span>
            </a>

            <a
              :href="contatos.gremioEmail"
              class="footer-link"
              aria-label="Enviar e-mail para o Grêmio Estudantil Athos Bulcão"
            >
              <v-icon size="18" aria-hidden="true">mdi-email-outline</v-icon>
              <span>gremio.athosbulcao.sl@gmail.com</span>
            </a>
          </div>
        </section>

        <section class="footer-column footer-column-creator">
          <p class="footer-label">Desenvolvido por</p>
          <h2 class="footer-creator-name">Kauã Guilherme Almeida Melo</h2>

          <div class="footer-contact-group">
            <a
              :href="contatos.linkedin"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-link"
              aria-label="LinkedIn de Kauã Guilherme Almeida Melo"
            >
              <v-icon size="18" aria-hidden="true">mdi-linkedin</v-icon>
              <span>LinkedIn</span>
            </a>

            <a
              :href="contatos.github"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-link"
              aria-label="GitHub de Kauã Guilherme Almeida Melo"
            >
              <v-icon size="18" aria-hidden="true">mdi-github</v-icon>
              <span>GitHub</span>
            </a>

            <a
              :href="contatos.criadorInstagram"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-link"
              aria-label="Instagram de Kauã Guilherme Almeida Melo"
            >
              <v-icon size="18" aria-hidden="true">mdi-instagram</v-icon>
              <span>Instagram</span>
            </a>

            <a
              :href="contatos.criadorEmail"
              class="footer-link"
              aria-label="Enviar e-mail para Kauã Guilherme Almeida Melo"
            >
              <v-icon size="18" aria-hidden="true">mdi-email-outline</v-icon>
              <span>E-mail</span>
            </a>
          </div>
        </section>
      </div>

      <div class="footer-bottom">
        <p>© {{ anoAtual }} Grêmio Estudantil Athos Bulcão - Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  width: 100%;
  margin-top: 50px;
  background: #0b132b;
  color: #e2e8f0;
  border-top: 1px solid #1e293b;
  box-sizing: border-box;
}

.footer-container {
  width: min(100%, 1180px);
  margin: 0 auto;
  padding: 44px 32px calc(24px + env(safe-area-inset-bottom, 0));
  box-sizing: border-box;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 56px;
}

.footer-column {
  min-width: 0;
}

.footer-column-creator {
  padding-left: 8px;
}

.footer-title {
  margin: 0;
  color: #ffffff;
  font-size: 17px;
  line-height: 1.45;
  font-weight: 600;
  letter-spacing: -0.01em;
}

.footer-location {
  display: flex;
  align-items: center;
  gap: 7px;
  margin-top: 10px;
  color: #cbd5e1;
  font-size: 13px;
  line-height: 1.5;
}

.footer-label {
  margin: 0 0 4px;
  color: #94a3b8;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.07em;
}

.footer-creator-name {
  margin: 0;
  color: #ffffff;
  font-size: 17px;
  line-height: 1.45;
  font-weight: 700;
  letter-spacing: -0.01em;
}

.footer-contact-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px 20px;
  margin-top: 20px;
}

.footer-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 32px;
  color: #cbd5e1;
  font-size: 13px;
  font-weight: 500;
  line-height: 1.4;
  text-decoration: none;
  transition: color 0.2s ease, transform 0.2s ease;
}

.footer-link:hover,
.footer-link:focus-visible {
  color: #60a5fa;
  transform: translateY(-1px);
}

.footer-link:focus-visible {
  outline: 2px solid #60a5fa;
  outline-offset: 4px;
  border-radius: 4px;
}

.footer-bottom {
  display: flex;
  justify-content: center;
  margin-top: 36px;
  padding-top: 20px;
  border-top: 1px solid #1e293b;
}

.footer-bottom p {
  margin: 0;
  color: #94a3b8;
  font-size: 12px;
  line-height: 1.5;
  text-align: center;
}

@media (max-width: 720px) {
  .footer-container {
    padding: 36px 20px calc(92px + env(safe-area-inset-bottom, 0));
  }

  .footer-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .footer-column-creator {
    padding-left: 0;
  }

  .footer-title,
  .footer-creator-name {
    font-size: 16px;
  }

  .footer-contact-group {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    margin-top: 16px;
  }

  .footer-link {
    min-height: 36px;
  }

  .footer-bottom {
    margin-top: 30px;
  }
}
</style>