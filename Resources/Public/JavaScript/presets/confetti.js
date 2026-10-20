import { pick, random } from '@move-elevator/typo3-chameleon-login/random.js';

const COLORS = ['#e74c3c', '#f1c40f', '#2ecc71', '#3498db', '#9b59b6', '#ff6b9d'];

export const confetti = {
  density: 0.6,
  spawn: (width, height, initial) => {
    const size = random(5, 9);
    return {
      x: random(0, width),
      y: initial ? random(0, height) : -size,
      size,
      color: pick(COLORS),
      speed: random(50, 90),
      sway: random(10, 30),
      phase: random(0, Math.PI * 2),
      rotation: random(0, Math.PI * 2),
      spin: random(-4, 4),
    };
  },
  update: (piece, delta) => ({
    ...piece,
    x: piece.x + Math.sin(piece.phase) * piece.sway * delta,
    y: piece.y + piece.speed * delta,
    phase: piece.phase + delta * 3,
    rotation: piece.rotation + piece.spin * delta,
  }),
  draw: (context, piece) => {
    context.globalAlpha = 1;
    context.fillStyle = piece.color;
    context.save();
    context.translate(piece.x, piece.y);
    context.rotate(piece.rotation);
    // Scaling one axis with the phase makes the piece look like it is flipping over
    context.scale(1, Math.cos(piece.phase));
    context.fillRect(-piece.size / 2, -piece.size / 4, piece.size, piece.size / 2);
    context.restore();
  },
};
