import { pick, random } from '@move-elevator/typo3-chameleon-login/random.js';

const COLORS = ['#ffd1dc', '#ffb7c5', '#f8c8dc', '#fde2e4'];

export const blossoms = {
  density: 0.4,
  spawn: (width, height, initial) => {
    const size = random(4, 8);
    return {
      x: random(0, width),
      y: initial ? random(0, height) : -size,
      size,
      color: pick(COLORS),
      speed: random(20, 40),
      sway: random(20, 50),
      phase: random(0, Math.PI * 2),
      rotation: random(0, Math.PI * 2),
      spin: random(-1, 1),
    };
  },
  update: (petal, delta) => ({
    ...petal,
    x: petal.x + (Math.cos(petal.phase) * petal.sway + 15) * delta,
    y: petal.y + petal.speed * delta,
    phase: petal.phase + delta * 0.8,
    rotation: petal.rotation + petal.spin * delta,
  }),
  draw: (context, petal) => {
    context.globalAlpha = 0.85;
    context.fillStyle = petal.color;
    context.beginPath();
    context.ellipse(petal.x, petal.y, petal.size, petal.size * 0.6, petal.rotation, 0, Math.PI * 2);
    context.fill();
  },
};
