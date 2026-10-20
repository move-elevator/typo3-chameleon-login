import { snow } from './presets/snow.js';

const presets = { snow };

const MAX_PARTICLES = 200;
const PIXELS_PER_PARTICLE = 10000;
const MAX_FRAME_SECONDS = 0.1;
const OUTSIDE_MARGIN = 50;

const isOutside = (particle, width, height) =>
  particle.y > height + OUTSIDE_MARGIN
  || particle.y < -OUTSIDE_MARGIN
  || particle.x > width + OUTSIDE_MARGIN
  || particle.x < -OUTSIDE_MARGIN;

const createCanvas = (container) => {
  const canvas = document.createElement('canvas');
  canvas.className = 'chameleon-login-effect';
  canvas.setAttribute('aria-hidden', 'true');
  container.prepend(canvas);
  return canvas;
};

const init = (effect) => {
  const preset = presets[effect];
  const container = document.querySelector('.typo3-login');
  const motionQuery = window.matchMedia('(prefers-reduced-motion: no-preference)');
  if (!preset || !container || !motionQuery.matches) {
    return;
  }

  const canvas = createCanvas(container);
  const context = canvas.getContext('2d');
  let width = 0;
  let height = 0;
  let particles = [];
  let frame = 0;
  let lastTime = 0;

  const resize = () => {
    const ratio = window.devicePixelRatio || 1;
    width = window.innerWidth;
    height = window.innerHeight;
    canvas.width = Math.round(width * ratio);
    canvas.height = Math.round(height * ratio);
    context.setTransform(ratio, 0, 0, ratio, 0, 0);
    const count = Math.min(MAX_PARTICLES, Math.round((width * height) / PIXELS_PER_PARTICLE));
    particles = Array.from({ length: count }, () => preset.spawn(width, height, true));
  };

  const render = (time) => {
    const delta = Math.min((time - lastTime) / 1000, MAX_FRAME_SECONDS);
    lastTime = time;
    particles = particles.map((particle) => {
      const next = preset.update(particle, delta);
      return isOutside(next, width, height) ? preset.spawn(width, height, false) : next;
    });
    context.clearRect(0, 0, width, height);
    for (const particle of particles) {
      preset.draw(context, particle);
    }
    frame = requestAnimationFrame(render);
  };

  const start = () => {
    lastTime = performance.now();
    frame = requestAnimationFrame(render);
  };

  const stop = () => cancelAnimationFrame(frame);

  const listeners = new AbortController();
  const { signal } = listeners;

  document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()), { signal });
  window.addEventListener('resize', resize, { signal });
  motionQuery.addEventListener('change', () => {
    stop();
    listeners.abort();
    canvas.remove();
  }, { signal });

  resize();
  start();
};

export default { init };
