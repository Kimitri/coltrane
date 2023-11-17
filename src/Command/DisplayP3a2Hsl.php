<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3a;

class DisplayP3a2Hsl extends AbstractCommand {
  /**
   * Configures the command
   */
  public function configure(): void {
    $this->setName('display-p3a2hsl')
        ->setDescription('Convert Display-P3a color values to hsl.')
        ->setHelp('Converts Display-P3a color values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)") to hsl values (e.g. "hsl(207,22%,10%)").')
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
        
        return $this->applyPalette($input, $original)->toHsl();
      }

      return $matches[0];
    }, $source);
  }
}
