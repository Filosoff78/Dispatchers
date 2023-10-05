<?php
namespace NB\TestTask\Integration\UI\EntitySelector;

use Bitrix\UI\EntitySelector;
use NB\TestTask\ORM\ObjectTable;

class ObjectsProvider extends EntitySelector\BaseProvider {
    private const ELEMENT_LIMIT = 100;

    protected const ENTITY_ID = 'object';

    protected $options = [];

    public function __construct()
    {
        parent::__construct();
        $this->prepareOptions();
    }

    protected function prepareOptions(array $options = []): void
    {
        $this->options['selectedItemIds'] = $options['selectedItemIds'] ?: [];
    }

    public function isAvailable(): bool
    {
        global $USER;
        return $USER->isAuthorized();
    }

    public function getItems(array $ids): array
    {
        foreach (self::getEntityRows(['ID' => $ids]) as $item) {
            $items[] = $this->makeItem($item);
        }
        return $items;
    }

    public function getSelectedItems(array $ids): array
    {
        return $this->getItems($ids);
    }

    public function fillDialog(EntitySelector\Dialog $dialog): void
    {
        foreach ($this->getElements() as $element) {
            $dialog->addItem(
                $this->makeItem($element)
            );
        }
    }

    public function doSearch(EntitySelector\SearchQuery $searchQuery, EntitySelector\Dialog $dialog): void
    {
        $items = $this->getElements($searchQuery->getQuery());

        if(!empty($items)) {
            foreach ($items as $item) {
                $dialog->addItem(
                    $this->makeItem($item)
                );
            }
        }
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function makeItem(array $element): EntitySelector\Item
    {
        $item = new EntitySelector\Item([
            'id' => $element['ID'],
            'entityId' => static::ENTITY_ID,
            'title' => htmlspecialcharsback($element['NAME']),
            'tabs' => static::getTabsNames()
        ]);

        if(in_array($element['ID'], $this->options['selectedItemIds'])){
            $item->setSelected();
        }
        return $item;
    }

    protected function getElements(string $searchString = ''): array
    {
        $filter = [];
        $searchString = trim($searchString);
        if($searchString !== '') {
            $filter['NAME'] = '%' . $searchString .'%';
        }
        return self::getEntityRows($filter);
    }

    protected static function getEntityRows(array $filter = []): array
    {
        return ObjectTable::query()
            ->setFilter($filter)
            ->setSelect(['ID', 'NAME'])
            ->setLimit(self::ELEMENT_LIMIT)
            ->fetchAll();
    }

    protected static function getTabsNames(): array
    {
        return [static::ENTITY_ID];
    }
}
