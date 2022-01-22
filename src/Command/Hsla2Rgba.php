<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Hsla;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;

class Hsla2Rgba extends AbstractCommand {
	public function configure() {
		$this->setName('hsla2rgba')
		    ->setDescription('Convert hsla color values to rgba.')
		    ->setHelp('Converts hsla color values (e.g. "hsla(207,22%,10%,.8)") to decimal rgba values (e.g. "rgba(121,14,212,.8)").')
		    ->addDefaultOptions()
		    ->addAlphaOption();
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::HSLA, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsla::fromString('hsla(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				$rgba = $aligned->toRgba();
				$alpha = $this->alpha($input, $rgba);

				return $rgba->toRgba($alpha);
			}

			return $matches[0];
		}, $source);
	}
}