<?php

namespace OpenAds\Scoring;

use OpenAds\Contracts\ScoringInterface;

class QualityScore implements ScoringInterface
{
	public function score(array $ads): array
	{
		foreach ($ads as $ad) {
			$ad->score =
				$ad->bid *
				(
					($ad->ctr * 0.5) +
					($ad->relevance * 0.3) +
					($ad->landingScore * 0.2)
				);
		}
		return $ads;
	}
}
