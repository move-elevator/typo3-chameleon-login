export const random = (min, max) => min + Math.random() * (max - min);

export const pick = (items) => items[Math.floor(Math.random() * items.length)];
