import { pick, random } from '@move-elevator/typo3-chameleon-login/random.js';

const COLORS = ['#e74c3c', '#ff6b81', '#ff4d6d', '#ffb3c1'];

const traceHeart = (context, size) => {
  const top = size * 0.3;
  const half = size / 2;
  context.beginPath();
  context.moveTo(0, top);
  context.bezierCurveTo(0, 0, -half, 0, -half, top);
  context.bezierCurveTo(-half, size * 0.6, 0, size * 0.8, 0, size);
  context.bezierCurveTo(0, size * 0.8, half, size * 0.6, half, top);
  context.bezierCurveTo(half, 0, 0, 0, 0, top);
};

export const hearts = {
  density: 0.15,
  spawn: (width, height, initial) => {
    const size = random(10, 22);
    return {
      x: random(0, width),
      y: initial ? random(0, height) : height + size,
      size,
      color: pick(COLORS),
      speed: random(25, 50),
      sway: random(15, 35),
      phase: random(0, Math.PI * 2),
      opacity: random(0.4, 0.8),
    };
  },
  update: (heart, delta) => ({
    ...heart,
    x: heart.x + Math.cos(heart.phase) * heart.sway * delta,
    y: heart.y - heart.speed * delta,
    phase: heart.phase + delta,
  }),
  draw: (context, heart) => {
    context.globalAlpha = heart.opacity;
    context.fillStyle = heart.color;
    context.save();
    context.translate(heart.x, heart.y - heart.size / 2);
    traceHeart(context, heart.size);
    context.fill();
    context.restore();
  },
};
