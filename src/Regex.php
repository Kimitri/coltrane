<?php
namespace Coltrane;

class Regex {
  public const DISPLAYP3 = '/color\( *display-p3 *([\.\d]* *[\.\d]* *[\.\d]* *) *\)/i';
  public const DISPLAYP3A = '/color\( *display-p3 *([\.\d]* *[\.\d]* *[\.\d]* *\/ *[\.\d]* *) *\)/i';
  public const HEX = '/(#[0-9a-f]{6}|#[0-9a-f]{3})/i';
  public const HSL = '/hsl\( *(-?\d{1,3} *, *\d{1,3}%? *, *\d{1,3}%?) *\)/i';
  public const HSLA = '/hsla\( *(\d{1,3} *, *\d{1,3}%? *, *\d{1,3}%? *, *[0-1]?\.\d{1,2}?) *\)/i';
  public const RGB = '/rgb\( *(\d{1,3} *, *\d{1,3} *, *\d{1,3}) *\)/i';
  public const RGBA = '/rgba\( *(\d{1,3} *, *\d{1,3} *, *\d{1,3} *, *[0-1]?\.\d{1,2}?) *\)/i';
}