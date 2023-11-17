<?php

namespace Coltrane;

use Spatie\Color\Color;
use Spatie\Color\Hex;
use Spatie\Color\Distance;

class Palette {
  protected $colors;

  /**
   * Palette constructor.
   * @param string $file_path
   */
  public function __construct(string $file_path) {
    $lines = array_filter(file($file_path));
    $this->colors = array_map(function($hex) {
      return Hex::fromString('#' . trim($hex));
    }, $lines);
  }

  /**
   * Finds the nearest color in the palette to the given color
   *
   * @param Color $color
   * @return Hex
   */
  public function nearest(Color $color): Hex {
    $distances = array_map(function($p_color) use ($color) {
      return Distance::CIE76($color, $p_color);
    }, $this->colors);

    $by_distance = array_combine($distances, $this->colors);
    ksort($by_distance);
    return reset($by_distance);
  }
}
