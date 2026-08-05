<template>
  <div class="hero">
    <!-- Azulejo background -->
    <img :src="heroBg" alt="" aria-hidden class="hero-bg" />
    <!-- Dark overlay so text stays readable -->
    <div class="hero-overlay" />
    <div class="hero-content">
      <p class="eyebrow">GESTÃO 2026 · ABSL</p>
      <h1 class="title title-white">GRÊMIO</h1>
      <h1 class="title title-gold">ATHOS</h1>
      <h1 class="title title-white title-sm">BULCÃO ✦</h1>

      <div class="hero-body">
        <div class="hero-text-row">
          <div class="accent-bar" />
          <p class="hero-text">
            Não é apenas representação estudantil. É a <strong>voz</strong> de cada aluno
            virando decisão, projeto e mudança real dentro da escola.
          </p>
        </div>
        <div class="hero-actions">
          <button class="btn-primary">
            CONHEÇA O GRÊMIO <span class="arrow">→</span>
          </button>
          <button
            v-if="inicioMedia.videoUrl"
            class="btn-ghost"
            type="button"
            @click="openVideo"
          >
            <span class="play-icon">▶</span> Ver Vídeo
          </button>
        </div>
      </div>
    </div>

    <div v-if="videoDialog" class="video-modal" @click.self="videoDialog = false">
      <div class="video-modal-card">
        <button type="button" class="modal-close" @click="videoDialog = false">×</button>
        <video
          class="modal-video"
          :src="inicioMedia.videoUrl"
          controls
          autoplay
          playsinline
        ></video>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import heroBg from '@/components/inicio/icons/iniciobanner.png'
import { inicioMedia } from '@/stores/appData'

const videoDialog = ref(false)
function openVideo() {
  if (inicioMedia.videoUrl) {
    videoDialog.value = true
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700;1,900&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap');

.hero {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 40px;
  min-height: 360px;
  font-family: 'DM Sans', sans-serif;
}

.hero-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(13, 31, 60, 0.82) 0%,
    rgba(26, 63, 143, 0.72) 60%,
    rgba(37, 99, 235, 0.6) 100%
  );
}

.hero-content {
  position: relative;
  z-index: 10;
  padding: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 360px;
}

.eyebrow {
  color: #f5c518;
  font-size: 12px;
  font-family: 'DM Mono', monospace;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  margin-bottom: 16px;
}

.title {
  font-family: 'Playfair Display', serif;
  font-weight: 900;
  line-height: 1;
  font-size: 60px;
  margin: 0 0 4px;
}
.title-white {
  color: #ffffff;
}
.title-gold {
  color: #f5c518;
  font-style: italic;
}
.title-sm {
  font-size: 48px;
  font-style: italic;
  margin-bottom: 32px;
}

.hero-body {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 480px;
}

.hero-text-row {
  display: flex;
  gap: 12px;
}
.accent-bar {
  width: 4px;
  border-radius: 999px;
  flex-shrink: 0;
  background: #f5c518;
}
.hero-text {
  color: rgba(255, 255, 255, 0.85);
  font-size: 14px;
  line-height: 1.6;
  margin: 0;
}
.hero-text strong {
  color: #ffffff;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
}

.btn-primary {
  padding: 10px 20px;
  border-radius: 999px;
  background: #ffffff;
  color: #0d1f3c;
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.02em;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.15s ease;
}
.btn-primary:hover {
  background: #f5c518;
}
.arrow {
  font-size: 14px;
}

.btn-ghost {
  display: flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.8);
  font-size: 14px;
  cursor: pointer;
  transition: color 0.15s ease;
}
.btn-ghost:hover {
  color: #ffffff;
}
.play-icon {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
}

@media (max-width: 720px) {
  .title {
    font-size: 40px;
  }
  .title-sm {
    font-size: 32px;
  }
  .hero-content {
    padding: 24px;
  }
}

@media (max-width: 420px) {
  .hero {
    min-height: unset;
  }
  .hero-content {
    padding: 20px;
    min-height: unset;
  }
  .title {
    font-size: 30px;
  }
  .title-sm {
    font-size: 24px;
    margin-bottom: 20px;
  }
  .hero-actions {
    flex-direction: column;
    align-items: flex-start;
  }
  .btn-primary {
    width: 100%;
    justify-content: center;
  }
}

.video-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  z-index: 1000;
}
.video-modal-card {
  position: relative;
  width: min(900px, 100%);
  max-width: 100%;
}
.modal-close {
  position: absolute;
  top: -10px;
  right: -10px;
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 999px;
  background: #ffffff;
  color: #0d1f3c;
  font-size: 20px;
  cursor: pointer;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
}
.modal-video {
  width: 100%;
  aspect-ratio: 16 / 9;
  border-radius: 20px;
  background: #000;
}
</style>