<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;


use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3a;

class DisplayP3a2Rgb extends AbstractCommand {
  /**
   * Configures the command
   */
  public function configure(): void {
    $this->setName('display-p3a2rgb')
        ->setDescription('Convert Display-P3a color values to rgb.')
        ->setHelp('Converts Display-P3a color values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)") to rgb values (e.g. "rgb(121,14,212)").')
        ->addDefaultOptions();
  }

  /**
   * Transforms the input source code.
   *
   * @param  InputInterface $input  Command input.
   * @param  string         $source Input source code.
   * @return string                 Transformed source code.
   */
  public function transform(InputInterface $input, string $source): string {
    return preg_replace_callback(Regex::DISPLAYP3A, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3a::fromString('color(display-p3 ' . $matches[1] . ')');
        
        return $this->applyPalette($input, $original)->toRgb();
      }

      return $matches[0];
    }, $source);
  }
}
