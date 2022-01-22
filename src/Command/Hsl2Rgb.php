<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Hsl;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;

class Hsl2Rgb extends AbstractCommand {
	public function configure() {
		$this->setName('hsl2rgb')
		    ->setDescription('Convert hsl color values to rgb.')
		    ->setHelp('Converts hsl color values (e.g. "hsl(207,22%,10%)") to decimal rgb values (e.g. "rgb(121,14,212)").')
		    ->addDefaultOptions();
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::HSL, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsl::fromString('hsl(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toRgb();
			}

			return $matches[0];
		}, $source);
	}
}