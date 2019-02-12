<?php
declare(strict_types=1);

namespace Firegento\ContentProvisioning\Model\Query;

class HasStoreMatches
{
    /**
     * @param array $entryStoreIds
     * @param array $entityStoreIds
     * @return bool
     */
    public function execute(array $entryStoreIds, array $entityStoreIds): bool
    {
        if (in_array(0, $entryStoreIds)) {
            return true;
        }
        if (in_array(0, $entityStoreIds)) {
            return true;
        }
        foreach ($entityStoreIds as $entityStoreId) {
            if (in_array($entityStoreId, $entryStoreIds)) {
                return true;
            }
        }

        return false;
    }
}
