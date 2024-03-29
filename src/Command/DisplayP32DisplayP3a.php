<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3;
use Coltrane\Color\DisplayP3a;

class DisplayP32DisplayP3a extends AbstractCommand {
  /**
   * Configures the command
   */
  public function configure(): void {
    $this->setName('display-p32display-p3a')
        ->setDescription('Convert Display-P3 color values to Display-P3a.')
        ->setHelp('Converts Display-P3 color values (e.g. "color(display-p3 0.1765 0.3059 0.4353)") to Display-P3a values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)").')
        ->addDefaultOptions()
        ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0)
        ->addAlphaOption();
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
        $original = DisplayP3::fromString('color(display-p3 ' . $matches[1] . ')');
        $aligned = $this->applyPalette($input, $original)->toRgba();
        $alpha = $this->alpha($input, $aligned);
        $display_p3a = new DisplayP3a($aligned->red(), $aligned->green(), $aligned->blue(), $alpha);

        if (intval($input->getOption('precision')) > 0) {
          $display_p3a->setPrecision(intval($input->getOption('precision')));
        }
        
        return $display_p3a;
      }

      return $matches[0];
    }, $source);
  }
}
