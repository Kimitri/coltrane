<?php

namespace Coltrane;

use Spatie\Color\Color;
use Spatie\Color\Hex;
use Spatie\Color\Distance;

class Palette {
  protected $colors;

  public function __construct(string $file_path) {
    $lines = array_filter(file($file_path));
    $this->colors = array_map(function($hex) {
      return Hex::fromString('#' . trim($hex));
    }, $lines);
  }

  public function nearest(Color $color) {
    $distances = array_map(function($p_color) use ($color) {
      return Distance::CIE76($color, $p_color);
    }, $this->colors);

    $by_distance = array_combine($distances, $this->colors);
    ksort($by_distance);
    return reset($by_distance);
  }
}