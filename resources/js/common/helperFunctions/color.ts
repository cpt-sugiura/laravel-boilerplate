/** * 渡されたrgbに対して目立つ色を返す */
export const blackOrWhiteAboutRgb = (rgb: string | number[]): 'white' | 'black' | undefined => {
  let rgbArr: number[] | undefined = undefined;
  if (Array.isArray(rgb)) {
    rgbArr = rgb;
  } else {
    let match: RegExpMatchArray | null;
    if ((match = rgb.match(/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/))) {
      rgbArr = [match[1], match[2], match[3]].map((n) => Number.parseInt(n, 16));
    } else if ((match = rgb.match(/rgba?\((\d+\s*),\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*\d+\s*)?\)/))) {
      rgbArr = [+match[1], +match[2], +match[3]];
    }
  }

  if (rgbArr === undefined || rgbArr.length < 3) {
    console.error(`rgb形式でない値が渡されました in blackOrWhite, ${rgb}, ${JSON.stringify(rgb, null, 2)}`);
    return undefined;
  }
  return (rgbArr[0] * 299 + rgbArr[1] * 587 + rgbArr[2] * 114) / 1000 < 128 ? 'white' : 'black';
};

/**
 * 渡されたhslに対して目立つ色を返す
 */
export const blackOrWhiteAboutHsl = (hslStr: string): 'white' | 'black' | null => {
  const match = hslStr.match(/hsla?\(([\d.+-]+)\s*,\s*([\d.+-]+)%\s*,\s*([\d.+-]+)%\s*[,.\d]*\)/);

  if (match === null || match.length < 4) {
    console.error(`hsl形式でない値が渡されました in blackOrWhite, ${hslStr}, ${JSON.stringify(match, null, 2)}`);
    return null;
  }
  const backRGB = hsl2rgb(+match[1], +match[2] / 100, +match[3] / 100);

  return (backRGB[0] * 299 + backRGB[1] * 587 + backRGB[2] * 114) / 1000 < 128 ? 'white' : 'black';
};

/**
 * hslをrgbに変換
 * @param {number} hue 0-360
 * @param {number} saturation 0-1
 * @param {number} lightness 0-1
 * @return {[number, number, number]}
 */
export const hsl2rgb = (hue: number, saturation: number, lightness: number): number[] => {
  const max = lightness + (saturation * (1 - Math.abs(2 * lightness - 1))) / 2;
  const min = lightness - (saturation * (1 - Math.abs(2 * lightness - 1))) / 2;

  let rgb;
  const i = Number.parseInt(`${hue / 60}`);

  switch (i) {
    case 0:
    case 6:
      rgb = [max, min + (max - min) * (hue / 60), min];
      break;

    case 1:
      rgb = [min + (max - min) * (120 - hue / 60), max, min];
      break;

    case 2:
      rgb = [min, max, min + (max - min) * (hue - 120 / 60)];
      break;

    case 3:
      rgb = [min, min + (max - min) * (240 - hue / 60), max];
      break;

    case 4:
      rgb = [min + (max - min) * (hue - 240 / 60), min, max];
      break;

    case 5:
    default:
      rgb = [max, min, min + (max - min) * (360 - hue / 60)];
      break;
  }

  return rgb.map(function (value) {
    return value * 255;
  });
};
