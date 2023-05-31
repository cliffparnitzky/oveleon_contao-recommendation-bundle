<?php

declare(strict_types=1);

namespace Oveleon\ContaoRecommendationBundle\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use Contao\CoreBundle\Framework\ContaoFramework;
use Oveleon\ContaoRecommendationBundle\RecommendationModel;
use Psr\Log\LoggerInterface;

#[AsCronJob('daily')]
class PurgeRecommendationsCron
{
    public function __construct(private ContaoFramework $framework, private LoggerInterface|null $logger)
    {
    }

    public function __invoke(): void
    {
        $this->framework->initialize();

        $recommendations = $this->framework->getAdapter(RecommendationModel::class)->findExpiredRecommendations();

        if (null === $recommendations)
        {
            return;
        }

        /** @var RecommendationModel $recommendation */
        foreach ($recommendations as $recommendation)
        {
            $recommendation->delete();
        }

        $this->logger?->info('Purged the unactivated recommendations');
    }
}
