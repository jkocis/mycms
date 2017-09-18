<?php

/**
 * MyCMS
 * 
 * @author Jan Kocis
 */
declare(strict_types = 1);

namespace MyCms\Product;

use MyCms;

/**
 * Text dostupnosti produktu
 */
class AvailabilityDateService
{
	/** @var Translator */
	private $translator;

	public function __construct(MyCms\Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Vrátí text doručení
	 * 
	 * situace doručení -> kolik dní trvá expedice v závislosti na víkendu nebo při zohlednění svátků:
	 * 
	 * 		1 den: "zítra u vás" -> sklad expeduje hned (do 15h)
	 * 		2 dny: "pozítří u vás" -> sklad expeduje druhý den (po 15h)
	 * 		3 a více: "ve [nejbližší možný pracovní den] u vás"
	 * 
	 * @return string
	 */
	public function getText(): string
	{
		$date = new MyCms\DateTime('15:00');

		if ($date->isFuture()) {
			$date->modify('+ 1 day'); // je < než 16:00 = doručení zítra u vás
			$date->getClosestWorkday(); // den doručení musí být pracovní den, pokud není, tak se najde ten nejbližší
		}
		else {
			$isTomorrowWorkingDay = $date->isTomorrowWorkingDay();
			$date->modify('+ 2 day'); // je > než time = doručení za 2 dny
			$date->getClosestWorkday(); // den doručení musí být pracovní den, pokud není, tak se najde ten nejbližší

			if (!$isTomorrowWorkingDay) {
				$date->modify('+ 1 day'); // když zítra není pracovní den, tak musíme příčíst ještě další den; př. je pátek odpoledně = dorečení je až v úterý
			}
		}

		if ($date->getDaysFromNow() === 1) { // pokud je den doručení pracovní a zítra
			return $this->translator->translate('_product_flag_availability_tommorow');
		}
		
		// jinak zobrazíme konkrétní den
		$day = $date->format('N'); // 1-7
		$type = $day === '3' || $day === '4' ? 2 : 1; // v | ve
		
		return $this->translator->translate('_product_flag_availability_next_days', $this->translator->translate('_product_flag_availability_next_days_pre_' . $type), $this->translator->translate('_global_text_cal_day_' . $day));

	}
}