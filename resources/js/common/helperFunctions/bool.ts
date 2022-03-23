const anyTo01 = (any: unknown): 0 | 1 => {
  if (typeof any === 'string') {
    if (any === 'false' || any === '0') {
      return 0;
    }
  }
  return any ? 1 : 0;
};

const spaceshipEval = (a: number | string, b: number | string): -1 | 0 | 1 => {
  if (a < b) {
    return -1;
  }
  if (a > b) {
    return 1;
  }
  return 0;
};

export { anyTo01, spaceshipEval };
