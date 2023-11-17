<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Spatie\Color\Color;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3a;

class DisplayP3a2DisplayP3a extends AbstractCommand {
  /**
   * Configures the command
   */
  public function configure(): void {
    $this->setName('display-p3a2display-p3a')
        ->setDescription('Apply palette and/or alpha transformation to Display-P3a color values.')
        ->setHelp('Applies palette and/or alpha transformation to Display-P3a color values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)").')
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
    return preg_replace_callback(Regex::DISPLAYP3A, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3a::fromString('color(display-p3 ' . $matches[1] . ')');
        $original_alpha = $original->alpha();
        $aligned = $this->applyPalette($input, $original)->toRgba($original_alpha);
        $alpha = $this->alpha($input, $aligned);
        $display_p3a = new DisplayP3a($aligned->red(), $aligned->green(), $aligned->blue(), $alpha);

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
