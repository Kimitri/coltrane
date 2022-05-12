<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Rgb;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;
use Coltrane\Color\DisplayP3;
use Coltrane\Color\DisplayP3a;

class DisplayP3a2DisplayP3 extends AbstractCommand {
  public function configure() {
    $this->setName('display-p3a2display-p3')
        ->setDescription('Convert Display-P3a color values to Display-P3.')
        ->setHelp('Converts Display-P3a color values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)") to Display-P3 values (e.g. "color(display-p3 0.1765 0.3059 0.4353)").')
        ->addDefaultOptions()
        ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0);
  }

  public function transform(InputInterface $input, string $source) {
    return preg_replace_callback(Regex::DISPLAYP3A, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3a::fromString('color(display-p3 ' . $matches[1] . ')');
        $aligned = $this->applyPalette($input, $original)->toRgb();
        $display_p3 = new DisplayP3($aligned->red(), $aligned->green(), $aligned->blue());

        if (intval($input->getOption('precision')) > 0) {
          $display_p3->setPrecision(intval($input->getOption('precision')));
        }
        
        return $display_p3;
      }

      return $matches[0];
    }, $source);
  }
}