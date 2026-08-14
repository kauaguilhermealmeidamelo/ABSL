<script setup lang="ts">
const instagram = 'https://instagram.com/mello.gk_'
const linkedin = 'https://linkedin.com/in/kauã-guilherme-15711026b'
const gmail = 'https://mail.google.com/mail/u/0/?fs=1&to=kauagmelotrabalho%40gmail.com&su=Contato%20pelo%20site&tf=cm'
</script>

<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-text">
        <span>Grêmio Estudantil Athos Bulcão · ABSL</span>
        <span>Brasília, DF</span>
      </div>

      <div class="footer-creator">
        <span class="footer-creator-label">Criador: <strong>Kauã Guilherme Almeida Melo</strong></span>

        <div class="footer-socials">
          <a :href="instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><v-icon size="18">mdi-instagram</v-icon></a>
          <a :href="linkedin" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" title="LinkedIn"><v-icon size="18">mdi-linkedin</v-icon></a>
          <a :href="gmail" target="_blank" rel="noopener noreferrer" aria-label="Enviar e-mail pelo Gmail" title="Enviar e-mail pelo Gmail"><v-icon size="18">mdi-email-outline</v-icon></a>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  background-color: #0f2038;
  width: 100%;
  margin-top: 50px;
  padding: 28px 50px;
  box-sizing: border-box;
}

.footer-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 64px;
  max-width: 1180px;
  margin: 0 auto;
}

.footer-text,
.footer-creator {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  color: #6b7c93;
  font-size: 11px;
  line-height: 1.4;
}

.footer-creator-label {
  white-space: normal;
}

.footer-creator strong {
  color: #fff;
  font-weight: 600;
}

.footer-socials {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 14px;
  margin-top: 10px;
}

.footer-socials a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 999px;
  color: #6b7c93;
  text-decoration: none;
  transition: color 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
}

.footer-socials a:hover,
.footer-socials a:focus-visible {
  color: #ffffff;
  background-color: rgba(255, 255, 255, 0.08);
  transform: translateY(-2px);
}

@media (max-width: 900px) {
  .footer {
    padding: 24px 32px;
  }
  .footer-content {
    gap: 32px;
  }
}

@media (max-width: 600px) {
  .footer {
    padding: 20px 20px calc(20px + env(safe-area-inset-bottom, 0));
  }
  .footer-content {
    flex-direction: column;
    gap: 16px;
  }
}

@media (max-width: 360px) {
  .footer-socials a {
    width: 36px;
    height: 36px;
  }
}
</style>