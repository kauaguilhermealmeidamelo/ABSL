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
                <span>
                    Criador:
                    <strong>Kauã Guilherme Almeida Melo</strong>
                </span>

                <div class="footer-socials">

                    <a :href="instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                        title="Instagram">
                        <v-icon size="18">mdi-instagram</v-icon>
                    </a>

                    <a :href="linkedin" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"
                        title="LinkedIn">
                        <v-icon size="18">mdi-linkedin</v-icon>
                    </a>

                    <a :href="gmail" target="_blank" rel="noopener noreferrer" aria-label="E-mail"
                        title="Enviar e-mail pelo Gmail">
                        <v-icon size="18">mdi-email-outline</v-icon>
                    </a>

                </div>
            </div>

        </div>
    </footer>
</template>

<style scoped>
.footer {
    background-color: #0F2038;
    width: 100%;
    margin-top: 50px;
    padding: 30px 50px;
    box-sizing: border-box;
}

.footer-content {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 100px;
}

.footer-text,
.footer-creator {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    color: #6B7C93;
    font-size: 11px;
    line-height: 1.4;
}

.footer-creator {
    margin-left: 10px;
}

.footer-creator strong {
    color: #fff;
    font-weight: 600;
}

.footer-socials {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-top: 10px;
}

.footer-socials a {
    color: #6B7C93;
    text-decoration: none;
    transition: 0.2s ease;
}

.footer-socials a:hover {
    color: #ffffff;
    transform: translateY(-2px);
}

/* Responsividade */
@media (max-width: 600px) {
    .footer {
        padding: 25px 20px;
    }

    .footer-content {
        flex-direction: column;
        gap: 15px;
    }

    .footer-creator {
        margin-left: 0;
    }
}
</style>