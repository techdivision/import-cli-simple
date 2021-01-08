# Upgrade from 3.8.27 to 4.0.0


## Removed Methods

### techdivision/import

* `TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface::isUrlKeyOf()`

### techdivision/import-category

* `TechDivision\Import\Category\Services\CategoryBunchProcessorInterface::getUrlRewritesByEntityTypeAndEntityId()`
* `TechDivision\Import\Category\Services\CategoryBunchProcessorInterface::getUrlRewritesByEntityTypeAndEntityIdAndStoreId()`

## Removed Classes

### techdivision/import

* `TechDivision\Import\Loaders\StoreViewCodeLoader` has been replaced with `\TechDivision\Import\Loaders\GenericMemberNameLoader`
* `TechDivision\Import\Actions\Processors\ImportHistoryCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\ImportHistoryUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\ImportHistoryDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreGroupCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreGroupUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreWebsiteCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreWebsiteUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\UrlRewriteCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\UrlRewriteUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\UrlRewriteDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`

## Moved Classes

While adding addional functionality, e. g. the extenden URL handling, it has been necessary to
migrate some classes from the professional to the community edition.

### techdivision/import > techdivision/import-cache

* `TechDivision\Import\Cache\CacheAdapterInterface` > `TechDivision\Import\Cache\Listeners\CacheAdapterInterface`
* `TechDivision\Import\Connection\CachePoolFactoryInterface` > `TechDivision\Import\Cache\CachePoolFactoryInterface`
* `TechDivision\Import\Utils\CacheKeysInterface` > `TechDivision\Import\Cache\Utils\CacheKeysInterface`
* `TechDivision\Import\Utils\CacheKeyUtilInterface` > `TechDivision\Import\Cache\Utils\CacheKeyUtilInterface`
* `TechDivision\Import\Utils\CacheTypes` > `TechDivision\Import\Cache\Utils\CacheTypes`

### techdivision/import > techdivision/import-cache-collection

* `TechDivision\Import\Cache\CacheAdapterTrait` > `TechDivision\Import\Cache\Collection\CacheAdapterTrait`
* `TechDivision\Import\Cache\ConfigurableCacheAdapter` > `TechDivision\Import\Cache\Collection\ConfigurableCacheAdapter`
* `TechDivision\Import\Cache\LocalCacheAdapter` > `TechDivision\Import\Cache\Collection\LocalCacheAdapter`
* `TechDivision\Import\Utils\CacheKeyUtil` > `TechDivision\Import\Cache\Collection\Utils\CacheKeyUtil`

### techdivision/import-cli-extended > techdivision/import-dbal

* `TechDivision\Import\Cli\Extended\Utils\EventNames` > `TechDivision\Import\Dbal\Utils\EventNames`
* `TechDivision\Import\Cli\Extended\Utils\EntityStatus` > `TechDivision\Import\Dbal\Utils\EntityStatus`
* `TechDivision\Import\Cli\Extended\Actions\GenericEventAwareAction` > `TechDivision\Import\Dbal\Actions\GenericEventAwareAction`
* `TechDivision\Import\Cli\Extended\Actions\GenericEventAwareIdentifierAction` > `TechDivision\Import\Dbal\Actions\GenericEventAwareIdentifierAction`

### techdivision/import > techdivision/import-dbal

* `TechDivision\Import\Actions\Processors\AbstractBaseProcessor` > `TechDivision\Import\Dbal\Actions\Processors\AbstractBaseProcessor`
* `TechDivision\Import\Actions\Processors\AbstractProcessor` > `TechDivision\Import\Dbal\Actions\Processors\AbstractProcessor`
* `TechDivision\Import\Actions\Processors\ProcessorInterface` > `TechDivision\Import\Dbal\Actions\Processors\ProcessorInterface`
* `TechDivision\Import\Actions\AbstractAction` > `TechDivision\Import\Dbal\Actions\AbstractAction`
* `TechDivision\Import\Actions\ActionInterface` > `TechDivision\Import\Dbal\Actions\ActionInterface`
* `TechDivision\Import\Actions\CachedActionInterface` > `TechDivision\Import\Dbal\Actions\CachedActionInterface`
* `TechDivision\Import\Actions\GenericAction` > `TechDivision\Import\Dbal\Actions\GenericAction`
* `TechDivision\Import\Actions\GenericCachedEventAwareAction` > `TechDivision\Import\Dbal\Actions\GenericCachedEventAwareAction`
* `TechDivision\Import\Actions\GenericCachedEventAwareIdentifierAction` > `TechDivision\Import\Dbal\Actions\GenericCachedEventAwareIdentifierAction`
* `TechDivision\Import\Actions\GenericDynamicIdentifierAction` > `TechDivision\Import\Dbal\Actions\GenericDynamicIdentifierAction`
* `TechDivision\Import\Actions\GenericEventAwareAction` > `TechDivision\Import\Dbal\Actions\GenericEventAwareAction`
* `TechDivision\Import\Actions\GenericEventAwareIdentifierAction` > `TechDivision\Import\Dbal\Actions\GenericEventAwareIdentifierAction`
* `TechDivision\Import\Actions\GenericIdentifierAction` > `TechDivision\Import\Dbal\Actions\GenericIdentifierAction`
* `TechDivision\Import\Actions\IdentifierActionInterface` > `TechDivision\Import\Dbal\Actions\IdentifierActionInterface`
* `TechDivision\Import\Connection\ConnectionInterface` > `TechDivision\Import\Dbal\Connection\ConnectionInterface`
* `TechDivision\Import\Connection\PDOConnectionWrapper` > `TechDivision\Import\Dbal\Connection\PDOConnectionWrapper`
* `TechDivision\Import\Listeners\CacheUpdateListener` > `TechDivision\Import\Dbal\Listeners\CacheUpdateListener`
* `TechDivision\Import\Repositories\Finders\AbstractFinder` > `TechDivision\Import\Dbal\Repositories\Finders\AbstractFinder`
* `TechDivision\Import\Repositories\Finders\ConfigurableFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\ConfigurableFinderFactory`
* `TechDivision\Import\Repositories\Finders\FinderFactoryInterface` > `TechDivision\Import\Dbal\Repositories\Finders\FinderFactoryInterface`
* `TechDivision\Import\Repositories\Finders\FinderInterface` > `TechDivision\Import\Dbal\Repositories\Finders\FinderInterface`
* `TechDivision\Import\Repositories\Finders\SimpleFinder` > `TechDivision\Import\Dbal\Repositories\Finders\SimpleFinder`
* `TechDivision\Import\Repositories\Finders\SimpleFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\SimpleFinderFactory`
* `TechDivision\Import\Repositories\Finders\UniqueFinder` > `TechDivision\Import\Dbal\Repositories\Finders\UniqueFinder`
* `TechDivision\Import\Repositories\Finders\UniqueFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\UniqueFinderFactory`
* `TechDivision\Import\Repositories\Finders\YieldedFinder` > `TechDivision\Import\Dbal\Repositories\Finders\YieldedFinder`
* `TechDivision\Import\Repositories\Finders\YieldedFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\YieldedFinderFactory`
* `TechDivision\Import\Repositories\AbstractCachedRepository` > `TechDivision\Import\Dbal\Repositories\AbstractCachedRepository`
* `TechDivision\Import\Repositories\AbstractFinderRepository` > `TechDivision\Import\Dbal\Repositories\AbstractFinderRepository`
* `TechDivision\Import\Repositories\AbstractRepository` > `TechDivision\Import\Dbal\Repositories\AbstractRepository`
* `TechDivision\Import\Repositories\AbstractSqlStatementRepository` > `TechDivision\Import\Dbal\Repositories\AbstractSqlStatementRepository`
* `TechDivision\Import\Repositories\CachedRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\CachedRepositoryInterface`
* `TechDivision\Import\Repositories\FinderAwareEntityRepositoryRepository` > `TechDivision\Import\Dbal\Repositories\FinderAwareEntityRepositoryRepository`
* `TechDivision\Import\Repositories\FinderAwareRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\FinderAwareRepositoryInterface`
* `TechDivision\Import\Repositories\RepositoryInterface` > `TechDivision\Import\Dbal\Repositories\RepositoryInterface`
* `TechDivision\Import\Repositories\SqlStatementRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface`
* `TechDivision\Import\Utils\ColumnSanitizer` > `TechDivision\Import\Dbal\Utils\ColumnSanitizer`
* `TechDivision\Import\Utils\EntityStatus` > `TechDivision\Import\Dbal\Utils\EntityStatus`
* `TechDivision\Import\Utils\EventNames` > `TechDivision\Import\Dbal\Utils\EventNames`
* `TechDivision\Import\Utils\PrimaryKeyUtilInterface` > `TechDivision\Import\Dbal\Utils\PrimaryKeyUtilInterface`
* `TechDivision\Import\Utils\TablePrefixUtilInterface` > `TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface`
* `TechDivision\Import\Utils\SanitizerInterface` > `TechDivision\Import\Dbal\Utils\SanitizerInterface`
* `TechDivision\Import\Utils\SqlCompilerInterface` > `TechDivision\Import\Dbal\Utils\SqlCompilerInterface`

### techdivision/import-caching > techdivision/import-dbal

* `TechDivision\Import\Caching\Listeners\CacheUpdateListener` > `TechDivision\Import\Dbal\Listeners\CacheUpdateListener`
* `TechDivision\Import\Caching\Actions\CachedActionInterface` >  `TechDivision\Import\Dbal\Actions\CachedActionInterface`
* `TechDivision\Import\Caching\Actions\GenericCachedEventAwareAction` > `TechDivision\Import\Dbal\Actions\GenericCachedEventAwareAction`
* `TechDivision\Import\Caching\Actions\GenericCachedEventAwareIdentifierAction` > `TechDivision\Import\Dbal\Actions\GenericCachedEventAwareIdentifierAction`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueEntityFinder` >  `TechDivision\Import\Dbal\Repositories\Finders\CachedUniqueFinder`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueEntityFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\CachedUniqueFinderFactory`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueFinder` >  `TechDivision\Import\Dbal\Repositories\Finders\CachedUniqueFinder`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\CachedUniqueFinderFactory`
* `TechDivision\Import\Caching\Repositories\Finders\CachedYieldedFinder` >  `TechDivision\Import\Dbal\Repositories\Finders\CachedUniqueFinder`
* `TechDivision\Import\Caching\Repositories\Finders\CachedYieldedFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\CachedUniqueFinderFactory`
* `TechDivision\Import\Caching\Repositories\Finders\EntityFinderInterface` > `TechDivision\Import\Dbal\Repositories\Finders\EntityFinderInterface`
* `TechDivision\Import\Caching\Repositories\Finders\UniqueEntityFinder` > `TechDivision\Import\Dbal\Repositories\Finders\UniqueEntityFinder`

### techdivision/import-caching > techdivision/import-dbal

* `TechDivision\Import\Cli\Connection\ConnectionFactory` > `TechDivision\Import\Dbal\Connection\ConnectionFactory`

### Symfony DI

* `import_caching.listener.cache.update` > `import_cache.listener.cache.update`
* `import_caching.repository.finder.factory.unique.cached` > `import_cache.repository.finder.factory.unique.cached`
