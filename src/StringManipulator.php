<?php
namespace Coltrane;

class StringManipulator {
	public static function dupeChars(string $string) {
		return array_reduce(str_split($string), function($carry, $chr) { return $carry . $chr . $chr; }, '');
	}
}