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
            <v-icon size="16" aria-hidden="true">mdi-map-marker-outline</v-icon>
            <span>Brasília, DF</span>
          </div>

          <div class="footer-socials" aria-label="Contatos do Grêmio">
            <a :href="contatos.gremioInstagram" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="Instagram do Grêmio" title="Instagram do Grêmio">
              <v-icon size="19" aria-hidden="true">mdi-instagram</v-icon>
            </a>
            <a :href="contatos.gremioEmail" class="footer-social" aria-label="E-mail do Grêmio" title="E-mail do Grêmio">
              <v-icon size="19" aria-hidden="true">mdi-email-outline</v-icon>
            </a>
          </div>
        </section>

        <section class="footer-column footer-column-creator">
          <p class="footer-label">Desenvolvido por</p>
          <h2 class="footer-creator-name">Kauã Guilherme Almeida Melo</h2>

          <div class="footer-socials" aria-label="Contatos do criador">
            <a :href="contatos.linkedin" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="LinkedIn do criador" title="LinkedIn">
              <v-icon size="19" aria-hidden="true">mdi-linkedin</v-icon>
            </a>
            <a :href="contatos.github" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="GitHub do criador" title="GitHub">
              <v-icon size="19" aria-hidden="true">mdi-github</v-icon>
            </a>
            <a :href="contatos.criadorInstagram" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="Instagram do criador" title="Instagram">
              <v-icon size="19" aria-hidden="true">mdi-instagram</v-icon>
            </a>
            <a :href="contatos.criadorEmail" class="footer-social" aria-label="E-mail do criador" title="E-mail">
              <v-icon size="19" aria-hidden="true">mdi-email-outline</v-icon>
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
  padding: 30px 32px calc(18px + env(safe-area-inset-bottom, 0));
  box-sizing: border-box;
}

.footer-grid {
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
  gap: 56px;
  align-items: center;
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
  font-size: 16px;
  line-height: 1.4;
  font-weight: 600;
  letter-spacing: -0.01em;
}

.footer-location {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 7px;
  color: #cbd5e1;
  font-size: 12px;
}

.footer-label {
  margin: 0 0 2px;
  color: #94a3b8;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.07em;
}

.footer-creator-name {
  margin: 0;
  color: #ffffff;
  font-size: 16px;
  line-height: 1.4;
  font-weight: 700;
}

.footer-socials {
  display: flex;
  align-items: center;
  gap: 7px;
  margin-top: 9px;
}

.footer-social {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 31px;
  height: 31px;
  border-radius: 50%;
  color: #cbd5e1;
  text-decoration: none;
  transition: color 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
}

.footer-social:hover,
.footer-social:focus-visible {
  color: #60a5fa;
  background-color: rgba(96, 165, 250, 0.1);
  transform: translateY(-1px);
}

.footer-social:focus-visible {
  outline: 2px solid #60a5fa;
  outline-offset: 3px;
}

.footer-bottom {
  display: flex;
  justify-content: center;
  margin-top: 24px;
  padding-top: 14px;
  border-top: 1px solid #1e293b;
}

.footer-bottom p {
  margin: 0;
  color: #94a3b8;
  font-size: 11px;
  line-height: 1.5;
  text-align: center;
}

@media (max-width: 720px) {
  .footer-container {
    padding: 26px 20px calc(88px + env(safe-area-inset-bottom, 0));
  }

  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 18px;
  }

  .footer-column-creator {
    padding-left: 0;
  }

  .footer-title,
  .footer-creator-name {
    font-size: 14px;
  }

  .footer-label {
    font-size: 10px;
  }

  .footer-location {
    font-size: 11px;
  }

  .footer-social {
    width: 30px;
    height: 30px;
  }

  .footer-bottom {
    margin-top: 20px;
  }
}

@media (max-width: 430px) {
  .footer-grid {
    gap: 12px;
  }

  .footer-title,
  .footer-creator-name {
    font-size: 13px;
  }
}
</style>