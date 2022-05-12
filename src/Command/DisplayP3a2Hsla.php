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

class DisplayP3a2Hsla extends AbstractCommand {
  public function configure() {
    $this->setName('display-p3a2hsla')
        ->setDescription('Convert Display-P3a color values to hsla.')
        ->setHelp('Converts Display-P3a color values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)") to hsl values (e.g. "hsla(207,22%,10%,0.8)").')
        ->addDefaultOptions()
        ->addAlphaOption();
  }

  public function transform(InputInterface $input, string $source) {
    return preg_replace_callback(Regex::DISPLAYP3A, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3a::fromString('color(display-p3 ' . $matches[1] . ')');
        $original_alpha = $original->alpha();
        $aligned = $this->applyPalette($input, $original)->toRgba($original_alpha);
        $alpha = $this->alpha($input, $aligned);
        
        return $aligned->toHsla($alpha);
      }

      return $matches[0];
    }, $source);
  }
}