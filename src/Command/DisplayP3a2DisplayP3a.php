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
use Coltrane\Color\DisplayP3a;

class DisplayP3a2DisplayP3a extends AbstractCommand {
  public function configure() {
    $this->setName('display-p3a2display-p3a')
        ->setDescription('Apply palette transformation to Display-P3a color values.')
        ->setHelp('Applies palette transformation to Display-P3a color values.')
        ->addDefaultOptions()
        ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0)
        ->addAlphaOption();
  }

  public function transform(InputInterface $input, string $source) {
    return preg_replace_callback(Regex::DISPLAYP3A, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3a::fromString('color(display-p3 ' . $matches[1] . ')');
        $original_alpha = $original->alpha();
        $aligned = $this->applyPalette($input, $original)->toRgba($original_alpha);
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