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

class Rgb2Hsla extends AbstractCommand {
	public function configure() {
		$this->setName('rgb2hsla')
		    ->setDescription('Convert rgb color values to hsla.')
		    ->setHelp('Converts rgb color values (e.g. "rgb(121,14,212)") to decimal hsla values (e.g. "hsla(207,22%,10%,0.8)").')
		    ->addDefaultOptions()
		    ->addAlphaOption();
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::RGB, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgb::fromString('rgb(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				$hsla = $aligned->toHsla();
				$alpha = $this->alpha($input, $hsla);

				return $hsla->toHsla($alpha);
			}

			return $matches[0];
		}, $source);
	}
}