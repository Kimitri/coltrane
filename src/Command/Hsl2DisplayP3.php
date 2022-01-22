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
use Coltrane\Color\DisplayP3;

class Hsl2DisplayP3 extends AbstractCommand {
	public function configure() {
		$this->setName('hsl2display-p3')
		    ->setDescription('Convert hsl color values to Display P3.')
		    ->setHelp('Converts hsl color values (e.g. "hsl(207,22%,10%)") to Display P3 values (e.g. "color(display-p3 0.1765 0.3059 0.4353)").')
		    ->addDefaultOptions()
		    ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0);
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::HSL, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsl::fromString('hsl(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				$rgb = $aligned->toRgb();

				$display_p3 = new DisplayP3($rgb->red(), $rgb->green(), $rgb->blue());
				
				if (intval($input->getOption('precision')) > 0) {
					$display_p3->setPrecision(intval($input->getOption('precision')));
				}
				
				return $display_p3;
			}

			return $matches[0];
		}, $source);
	}
}