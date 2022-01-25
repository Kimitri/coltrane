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

class DisplayP32Rgb extends AbstractCommand {
  public function configure() {
    $this->setName('display-p32rgb')
        ->setDescription('Convert Display-P3 color values to rgb.')
        ->setHelp('Converts Display-P3 color values (e.g. "color(display-p3 0.1765 0.3059 0.4353)") to decimal rgb values (e.g. "rgb(121,14,212)").')
        ->addDefaultOptions();
  }

  public function transform(InputInterface $input, string $source) {
    return preg_replace_callback(Regex::DISPLAYP3, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $display_p3 = DisplayP3::fromString('color(display-p3 ' . $matches[1] . ')');
        $aligned = $this->applyPalette($input, $display_p3);
        
        return new Rgb($aligned->red(), $aligned->green(), $aligned->blue());
      }

      return $matches[0];
    }, $source);
  }
}