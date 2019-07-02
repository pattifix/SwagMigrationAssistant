<?php declare(strict_types=1);

namespace SwagMigrationAssistant\Migration\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\ShopwareHttpException;
use SwagMigrationAssistant\Migration\EnvironmentInformation;
use SwagMigrationAssistant\Migration\Gateway\GatewayRegistryInterface;
use SwagMigrationAssistant\Migration\Logging\LoggingServiceInterface;
use SwagMigrationAssistant\Migration\MigrationContextInterface;

class MigrationDataFetcher implements MigrationDataFetcherInterface
{
    /**
     * @var GatewayRegistryInterface
     */
    private $gatewayRegistry;

    /**
     * @var LoggingServiceInterface
     */
    private $loggingService;

    public function __construct(
        GatewayRegistryInterface $gatewayRegistry,
        LoggingServiceInterface $loggingService
    ) {
        $this->gatewayRegistry = $gatewayRegistry;
        $this->loggingService = $loggingService;
    }

    public function fetchData(MigrationContextInterface $migrationContext, Context $context): array
    {
        try {
            $gateway = $this->gatewayRegistry->getGateway($migrationContext);

            return $gateway->read($migrationContext);
        } catch (\Exception $exception) {
            $code = $exception->getCode();
            if (is_subclass_of($exception, ShopwareHttpException::class, false)) {
                $code = $exception->getErrorCode();
            }

            $dataSet = $migrationContext->getDataSet();
            $this->loggingService->addError($migrationContext->getRunUuid(), (string) $code, '', $exception->getMessage(), ['entity' => $dataSet::getEntity()]);
            $this->loggingService->saveLogging($context);
        }

        return [];
    }

    public function getEnvironmentInformation(MigrationContextInterface $migrationContext): EnvironmentInformation
    {
        $gateway = $this->gatewayRegistry->getGateway($migrationContext);

        return $gateway->readEnvironmentInformation($migrationContext);
    }

    public function fetchTotals(MigrationContextInterface $migrationContext): array
    {
        $gateway = $this->gatewayRegistry->getGateway($migrationContext);

        return $gateway->readTotals($migrationContext);
    }
}
