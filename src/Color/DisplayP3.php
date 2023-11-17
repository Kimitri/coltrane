<?php
namespace Coltrane\Color;

use Coltrane\Regex;
use Spatie\Color\Rgb;

class DisplayP3 extends Rgb {
  protected int $precision = 0;

  /**
   * Creates a new DisplayP3 instance
   * @param  string $string   A color string
   * @return DisplayP3        A new DisplayP3 instance
   */
  public static function fromString(string $string): self {
    $matches = null;
    preg_match(Regex::DISPLAYP3, $string, $matches);

    $channels = array_values(array_filter(explode(' ', $matches[1])));
    [$red, $green, $blue] = array_map(function($value) {
      $float = floatval(trim($value));
      return $float > 1 ? 1 : $float;
    }, $channels);

    return new static(round(floatval($red) * 255), round(floatval($green) * 255), round(floatval($blue) * 255));
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

    if ($this->precision > 0) {
      $r = number_format($r, $this->precision);
      $g = number_format($g, $this->precision);
      $b = number_format($b, $this->precision);
    }

    return "color(display-p3 {$r} {$g} {$b})";
  }
}
