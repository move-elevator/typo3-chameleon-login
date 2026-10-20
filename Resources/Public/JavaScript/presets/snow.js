import { random } from '@move-elevator/typo3-chameleon-login/random.js';

export const snow = {
  spawn: (width, height, initial) => {
    const radius = random(1.5, 5);
    return {
      x: random(0, width),
      y: initial ? random(0, height) : -radius,
      radius,
      speed: 15 + radius * 12,
      sway: random(10, 40),
      phase: random(0, Math.PI * 2),
      opacity: random(0.4, 1),
    };
  },
  update: (flake, delta) => ({
    ...flake,
    x: flake.x + Math.cos(flake.phase) * flake.sway * delta,
    y: flake.y + flake.speed * delta,
    phase: flake.phase + delta,
  }),
  draw: (context, flake) => {
    context.globalAlpha = flake.opacity;
    context.fillStyle = '#fff';
    context.beginPath();
    context.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);
    context.fill();
  },
};
