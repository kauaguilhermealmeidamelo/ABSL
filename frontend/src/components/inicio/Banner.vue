<template>
  <section class="hero">
    <div class="hero-pattern" aria-hidden="true"></div>
    <div class="hero-content">
      <span class="eyebrow">GESTÃO 2026 · ABSL</span>
      <h1 class="hero-title">
        GRÊMIO<br />
        <span class="accent">ATHOS</span><br />
        BULCÃO <span class="sparkle">✦</span>
      </h1>
      <p class="hero-text">
        Não é apenas representação estudantil. É a <strong>voz</strong> de cada aluno
        virando decisão, projeto e mudança real dentro da escola.
      </p>
      <div class="hero-actions">
        <button class="btn-primary">
          CONHEÇA O GRÊMIO <span class="arrow">→</span>
        </button>
        <button class="btn-ghost">
          <span class="play-icon">▶</span> Ver Vídeo
        </button>
      </div>
    </div>
  </section>
</template>

<script setup></script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap');

.hero {
  position: relative;
  border-radius: 18px;
  overflow: hidden;
  background-image: url('@/components/icons/iniciobanner.png');
  background-size: cover;
  background-position: center;
  padding: 56px 48px;
  isolation: isolate;
  font-family: 'Inter', system-ui, sans-serif;
}

.hero-pattern {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    90deg,
    rgba(20, 43, 134, 0.88) 0%,
    rgba(16, 32, 97, 0.72) 20%,
    rgba(18, 39, 122, 0.35) 60%,
    rgba(22, 43, 128, 0.05) 80%
  );
  z-index: 0;
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 560px;
}

.eyebrow {
  display: inline-block;
  font-size: 12px;
  letter-spacing: 0.12em;
  font-weight: 600;
  color: #ffd479;
  border-bottom: 2px solid #ffd479;
  padding-bottom: 4px;
  margin-bottom: 18px;
}

.hero-title {
  font-family: 'Fraunces', serif;
  font-weight: 700;
  font-size: 52px;
  line-height: 1.05;
  color: #ffffff;
  margin: 0 0 20px;
}

.hero-title .accent {
  font-style: italic;
  color: #f5a623;
}

.sparkle {
  font-size: 0.6em;
  color: #ffd479;
}

.hero-text {
  border-left: 3px solid #f5a623;
  padding-left: 16px;
  color: #dbe1f5;
  font-size: 15px;
  line-height: 1.6;
  margin-bottom: 28px;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 22px;
}

.btn-primary {
  background: #ffffff;
  color: #1c2f63;
  border: none;
  border-radius: 999px;
  padding: 13px 26px;
  font-weight: 700;
  font-size: 13px;
  letter-spacing: 0.03em;
  cursor: pointer;
  transition: transform 0.15s ease;
}
.btn-primary:hover {
  transform: translateY(-2px);
}
.arrow {
  margin-left: 4px;
}

.btn-ghost {
  background: transparent;
  border: none;
  color: #ffffff;
  font-weight: 600;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}
.play-icon {
  width: 26px;
  height: 26px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.5);
  font-size: 10px;
}

@media (max-width: 720px) {
  .hero {
    padding: 36px 24px;
  }
  .hero-title {
    font-size: 36px;
  }
}
</style>