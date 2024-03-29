<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Spatie\Color\Hsl;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3a;

class Hsl2DisplayP3a extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('hsl2display-p3a')
		    ->setDescription('Convert hsl color values to Display-P3a.')
		    ->setHelp('Converts hsl color values (e.g. "hsl(207,22%,10%)") to Display-P3a values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)").')
		    ->addDefaultOptions()
		    ->addOption('precision', 'd', InputOption::VALUE_OPTIONAL, 'Component value decimal precision. 0 = no precision limit', 0)
		    ->addAlphaOption();
	}

  /**
   * Transforms the input source code.
   *
   * @param  InputInterface $input  Command input.
   * @param  string         $source Input source code.
   * @return string                 Transformed source code.
   */
	public function transform(InputInterface $input, string $source): string {
		return preg_replace_callback(Regex::HSL, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsl::fromString('hsl(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
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
