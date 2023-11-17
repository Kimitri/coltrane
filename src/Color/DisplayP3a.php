<?php
namespace Coltrane\Color;

use Coltrane\Regex;
use Spatie\Color\Rgba;

class DisplayP3a extends Rgba {
  protected int $precision = 0;

  /**
   * Creates a new DisplayP3 instance
   * @param  string $string   A color string
   * @return DisplayP3a       A new DisplayP3a instance
   */
  public static function fromString(string $string): self {
    $matches = null;
    preg_match(Regex::DISPLAYP3A, $string, $matches);
    $channels = array_values(array_filter(explode(' ', str_replace('/', '', $matches[1]))));
    [$red, $green, $blue, $alpha] = array_map(function($value) {
      $float = floatval(trim($value));
      return $float > 1 ? 1 : $float;
    }, $channels);

    return new static(round(floatval($red) * 255), round(floatval($green) * 255), round(floatval($blue) * 255), floatval($alpha));
  }

  /**
   * Sets the precision of the color string
   *
   * @param int $precision  The number of decimal places to round to. Set to 0 to disable rounding (default).
   */
  public function setPrecision(int $precision): void {
    $this->precision = $precision;
  }

  /**
   * Returns the color as a string
   *
   * @return string
   */
  public function __toString(): string {
    $r = $this->red / 255;
    $g = $this->green / 255;
    $b = $this->blue / 255;
    $a = $this->alpha;

    if ($this->precision > 0) {
      $r = number_format($r, $this->precision);
      $g = number_format($g, $this->precision);
      $b = number_format($b, $this->precision);
      $a = number_format($a, $this->precision);
    }

    return "color(display-p3 {$r} {$g} {$b} / {$a})";
  }
}
