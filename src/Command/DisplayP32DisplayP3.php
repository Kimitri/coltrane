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

class DisplayP32DisplayP3 extends AbstractCommand {
  public function configure() {
    $this->setName('display-p32display-p3')
        ->setDescription('Apply palette transformation to Display-P3 color values.')
        ->setHelp('Applies palette transformation to Display-P3 color values.')
        ->addDefaultOptions()
        ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0);
  }

  public function transform(InputInterface $input, string $source) {
    return preg_replace_callback(Regex::DISPLAYP3, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3::fromString('color(display-p3 ' . $matches[1] . ')');
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