<?php
namespace Coltrane;

class StringManipulator {
  /**
   * Duplicates each character in a string
   *
   * @param string $string
   * @return string
   */
	public static function dupeChars(string $string): string {
		return array_reduce(str_split($string), function($carry, $chr) { return $carry . $chr . $chr; }, '');
	}
}
