<?php

namespace VersionPress\ChangeInfos;

use Nette\Utils\Strings;
use VersionPress\Actions\ActionsInfo;
use VersionPress\Actions\ActionsInfoProvider;
use VersionPress\Database\DbSchemaInfo;
use VersionPress\Git\CommitMessage;

class ChangeInfoFactory
{
    /** @var DbSchemaInfo */
    private $dbSchema;
    /** @var ActionsInfoProvider */
    private $actionsInfoProvider;

    public function __construct(DbSchemaInfo $dbSchema, ActionsInfoProvider $actionsInfoProvider)
    {
        $this->dbSchema = $dbSchema;
        $this->actionsInfoProvider = $actionsInfoProvider;
    }

    public function createEntityChangeInfo($entity, $entityName, $action, $customTags = [], $customFiles = [])
    {
        $entityInfo = $this->dbSchema->getEntityInfo($entityName);
        $vpid = $entity[$entityInfo->vpidColumnName];

        $actionsInfo = $this->actionsInfoProvider->getActionsInfo($entityName);

        $automaticallySavedTags = $actionsInfo->getTags();
        $tags = ChangeInfoUtils::extractTags($automaticallySavedTags, $entity, $entity);
        $tags = array_merge($tags, $customTags);

        return new EntityChangeInfo($entityInfo, $actionsInfo, $action, $vpid, $tags, $customFiles);
    }

    public function createTrackedChangeInfo($scope, $action, $entityId = null, $tags = [], $files = [])
    {
        return new TrackedChangeInfo($scope, $this->actionsInfoProvider->getActionsInfo($scope), $action, $entityId, $tags, $files);
    }
}
