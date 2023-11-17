<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Rgb;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3;

class DisplayP32Hex extends AbstractCommand {
  /**
   * Configures the command
   */
  public function configure(): void {
    $this->setName('display-p32hex')
        ->setDescription('Convert Display-P3 color values to hexadecimal.')
        ->setHelp('Converts Display-P3 color values (e.g. "color(display-p3 0.1765 0.3059 0.4353)") to hexadecimal values (e.g. "#2d4e6f").')
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
    return preg_replace_callback(Regex::DISPLAYP3, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $display_p3 = DisplayP3::fromString('color(display-p3 ' . $matches[1] . ')');
        $aligned = $this->applyPalette($input, $display_p3);
        $rgb = new Rgb($aligned->red(), $aligned->green(), $aligned->blue());

        return $rgb->toHex();
      }

      return $matches[0];
    }, $source);
  }
}
