<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Hex;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;
use Coltrane\Color\DisplayP3a;

class Hex2DisplayP3a extends AbstractCommand {
	public function configure() {
		$this->setName('hex2display-p3a')
		    ->setDescription('Convert hexadecimal color values to Display-P3a.')
		    ->setHelp('Converts hexadecimal color values (e.g. "#3d4" or "#2d4e6f") to Display-P3a values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)").')
		    ->addDefaultOptions()
		    ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0)
		    ->addAlphaOption();
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::HEX, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$value = substr($matches[1], 1);
				$value = (strlen($value) < 4) ? StringManipulator::dupeChars($value) : $value;

				$hex = Hex::fromString('#' . $value);
				$aligned = $this->applyPalette($input, $hex);
				$alpha = $this->alpha($input, $aligned);
				$rgba = $aligned->toRgba($alpha);

				$display_p3a = new DisplayP3a($rgba->red(), $rgba->green(), $rgba->blue(), $rgba->alpha());
				
				if (intval($input->getOption('precision')) > 0) {
					$display_p3a->setPrecision(intval($input->getOption('precision')));
				}

				return $display_p3a;
			}

			return $matches[0];
		}, $source);
	}
}