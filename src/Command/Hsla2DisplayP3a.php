<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Spatie\Color\Hsla;
use Spatie\Color\Color;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3a;

class Hsla2DisplayP3a extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('hsla2display-p3a')
		    ->setDescription('Convert hsla color values to Display-P3a.')
		    ->setHelp('Converts hsla color values (e.g. "hsla(300,10%,50%,0.25)") to Display-P3a values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)").')
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
		return preg_replace_callback(Regex::HSLA, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsla::fromString('hsla(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				$alpha = $this->alpha($input, $color);
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

	/**
	 * Defines the alpha option used with hsla and rgba colors.
	 */
	protected function addAlphaOption(): self {
		return $this->addOption('alpha', 'a', InputOption::VALUE_OPTIONAL, 'Alpha value (between 0.0 and 1.0) or component (r, g, b or a) to use as alpha', null);
	}

	/**
	 * Gets the desired alpha value.
	 * 
	 * @param  InputInterface 	$input Command input.
	 * @param  Color          	$color Color.
	 * @return float 									 Alpha value (0..1).
	 */
	public function alpha(InputInterface $input, Color $color): float {
    if ($input->getOption('alpha')) {
      return parent::alpha($input, $color);
    }
    
    return floatval($color->alpha());
	}
}
