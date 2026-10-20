import { pick, random } from '@move-elevator/typo3-chameleon-login/random.js';

const COLORS = ['#c0392b', '#d35400', '#e67e22', '#f1c40f', '#8e5a2b'];

export const leaves = {
  density: 0.25,
  spawn: (width, height, initial) => {
    const size = random(6, 12);
    return {
      x: random(0, width),
      y: initial ? random(0, height) : -size,
      size,
      color: pick(COLORS),
      speed: random(30, 60),
      sway: random(30, 70),
      phase: random(0, Math.PI * 2),
      rotation: random(0, Math.PI * 2),
      spin: random(-2, 2),
    };
  },
  update: (leaf, delta) => ({
    ...leaf,
    x: leaf.x + Math.cos(leaf.phase) * leaf.sway * delta,
    y: leaf.y + leaf.speed * delta,
    phase: leaf.phase + delta * 1.2,
    rotation: leaf.rotation + leaf.spin * delta,
  }),
  draw: (context, leaf) => {
    context.globalAlpha = 0.9;
    context.fillStyle = leaf.color;
    context.beginPath();
    context.ellipse(leaf.x, leaf.y, leaf.size, leaf.size / 2.5, leaf.rotation, 0, Math.PI * 2);
    context.fill();
  },
};
